# Trading System - Quick Reference

## ✅ Current Status: Fully Automated

### 1. No Admin Approval ✅
- Users can trade immediately after login
- Route: `POST /trades/market/{marketId}`
- Only requires `auth` middleware
- **No approval workflow exists**

### 2. Automatic Trade Closing ✅
- **Scheduler:** Runs every minute (`routes/console.php`)
- **Service:** `SettlementService::settleClosedMarkets()`
- **Triggers:**
  - Market `close_time` expired
  - Market `is_closed` = true
  - Market `closed` = true
- **Auto-Result:** Determines from `last_trade_price` if not set

### 3. Automatic Balance Updates ✅
- **WIN Trades:**
  - Status: `PENDING` → `WON`
  - Balance: `+$payout` (payout = token_amount × $1.00)
  - Transaction record created
- **LOSS Trades:**
  - Status: `PENDING` → `LOST`
  - Balance: No change
- **Safety:** Database transactions + row locking

---

## Key Files

| File | Purpose |
|------|---------|
| `app/Http/Controllers/Frontend/TradeController.php` | Trade placement endpoint |
| `app/Services/TradeService.php` | Trade creation logic |
| `app/Services/SettlementService.php` | Auto-settlement logic |
| `routes/console.php` | Scheduler configuration |
| `app/Models/Trade.php` | Trade model |
| `app/Models/Market.php` | Market model with auto-close logic |

---

## Trade Status Flow

```
User Places Trade
    ↓
Status: PENDING
Balance: -$amount_invested
    ↓
Market Closes (close_time reached)
    ↓
Scheduler Detects (every minute)
    ↓
Market Result Determined
    ↓
Trade Compared with Result
    ↓
If Match: Status = WON, Balance += $payout
If No Match: Status = LOST, Balance unchanged
```

---

## API Endpoints

### Place Trade
```http
POST /trades/market/{marketId}
Content-Type: application/json

{
  "option": "yes" | "no",
  "amount": 100.00
}
```

### Get Trade
```http
GET /api/trades/{id}
```

### Get User Trades
```http
GET /trades/my-trades
```

---

## Testing

### Manual Settlement Test
```php
php artisan tinker

$service = app(\App\Services\SettlementService::class);
$results = $service->settleClosedMarkets();
dd($results);
```

### Check Scheduler
```bash
php artisan schedule:list
php artisan schedule:run
```

### View Logs
```bash
tail -f storage/logs/laravel.log | grep "settlement"
```

---

## Low-Context UI Component

Use: `resources/views/components/low-context-trade-card.blade.php`

```blade
<x-low-context-trade-card :trade="$trade" />
```

**Features:**
- Shows minimal info by default
- Expands on hover
- Loads details on demand
- Color-coded status badges
- Real-time profit/loss display

---

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Trades not closing | Check scheduler: `php artisan schedule:list` |
| Balance not updating | Verify market has `outcome_result` |
| Duplicate settlements | Already prevented with `lockForUpdate()` |
| Missing results | Auto-determines from `last_trade_price` |

---

## Database Fields

### Trade Status Values
- `PENDING` - Trade is open
- `WON` - Trade won, balance credited
- `LOST` - Trade lost, no payout

### Market Status Fields
- `close_time` - When market closes
- `is_closed` - Boolean flag
- `settled` - Boolean, all trades processed
- `outcome_result` - Final result (yes/no)
- `final_outcome` - Final outcome (YES/NO)

---

## Configuration

### Scheduler Frequency
```php
// routes/console.php
->everyMinute()  // Change to ->everyThirtySeconds() for faster processing
```

### Trade Limits
```php
// app/Services/TradeService.php
$minAmount = 0.01;
$maxAmount = 100000;
```

---

## Summary

✅ **No admin approval needed** - Users trade directly  
✅ **Auto-closing** - Scheduler runs every minute  
✅ **Auto-balance updates** - WIN trades credit automatically  
✅ **Transaction safe** - Uses DB transactions + locking  
✅ **Low-context UI** - Minimal info, expand on demand  

**System is production-ready and fully automated.**

