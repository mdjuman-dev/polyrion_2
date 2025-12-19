# Market Trading System Guide (বাংলা/English)

## Overview (সারসংক্ষেপ)

এই সিস্টেমে Polymarket-style trading করা যায়। Market fetch করার পর, win/loss নির্ধারণ এবং trading করার পদ্ধতি নিচে দেওয়া আছে।

---

## 1. Market Win/Loss কিভাবে নির্ধারণ করব? (How to Determine Market Win/Loss)

### Method 1: Polymarket API থেকে Automatic (Recommended)

`storeEvents()` function এখন Polymarket API থেকে resolution data automatically save করবে:

```php
// Polymarket API থেকে যদি resolved data থাকে, তাহলে automatically save হবে:
- outcome_result (yes/no)
- final_outcome (YES/NO)  
- final_result (yes/no)
- result_set_at (timestamp)
```

**API থেকে data আসলে:**
- Market automatically `closed` এবং `outcome_result` set হবে
- Trades automatically settle হবে (যদি settlement service run করে)

### Method 2: Manual Admin Panel থেকে Set করা

Admin panel থেকে manually market result set করতে পারেন:

**Route:** `POST /admin/market/{id}/set-result`

**Request:**
```json
{
    "final_result": "yes"  // বা "no"
}
```

**Code Location:** `MarketController::setResult()`

**Process:**
1. Admin market result set করে (YES/NO)
2. `SettlementService` automatically সব pending trades settle করে
3. Winning trades → User wallet এ payout add হয়
4. Losing trades → Status "loss" হয়, payout = 0

---

## 2. Trading কিভাবে করব? (How to Place Trades)

### Frontend থেকে Trading

**Route:** `POST /api/market/{marketId}/buy`

**Request:**
```json
{
    "outcome": "YES",  // বা "NO"
    "amount": 10.50    // USD amount
}
```

**Process:**
1. User wallet balance check হয়
2. Balance থেকে amount deduct হয়
3. Market price অনুযায়ী shares/tokens calculate হয়
4. Trade create হয় status = "PENDING"
5. Market close হলে এবং result set হলে automatically settle হয়

### Trading Flow:

```
User → Place Trade → Wallet Deduct → Trade Created (PENDING)
                                           ↓
                                    Market Closes
                                           ↓
                                    Result Set (YES/NO)
                                           ↓
                                    Settlement Service
                                           ↓
                    ┌─────────────────────┴─────────────────────┐
                    ↓                                           ↓
            Trade Outcome = Market Result?              Trade Outcome ≠ Market Result?
                    ↓                                           ↓
                WIN (WON)                                  LOSS (LOST)
                    ↓                                           ↓
            Payout = Shares × $1.00                    Payout = $0
                    ↓                                           ↓
            Wallet Balance += Payout                   Status = LOST
```

---

## 3. Settlement Process (Win/Loss Settlement)

### Automatic Settlement

**Service:** `SettlementService::settleMarket()`

**When it runs:**
1. Market `outcome_result` set হলে
2. Market `closed` = true হলে
3. Market `settled` = false হলে

**What it does:**
```php
foreach (pending trades) {
    if (trade.outcome === market.outcome_result) {
        // WIN
        payout = trade.shares × 1.00
        user.wallet.balance += payout
        trade.status = "win"
    } else {
        // LOSS
        trade.status = "loss"
        payout = 0
    }
}
```

### Manual Settlement

**Route:** `POST /admin/market/{id}/settle-trades`

যদি market result already set থাকে কিন্তু trades settle হয়নি, তাহলে manually settle করতে পারেন।

---

## 4. Database Fields (Important)

### Markets Table

```php
'outcome_result' => 'yes' | 'no' | null  // Market result (lowercase)
'final_outcome' => 'YES' | 'NO' | null   // Market result (uppercase)
'final_result' => 'yes' | 'no' | null    // Legacy field
'result_set_at' => timestamp             // When result was set
'closed' => boolean                       // Is market closed?
'is_closed' => boolean                   // Alternative closed flag
'settled' => boolean                     // Are trades settled?
```

### Trades Table

```php
'outcome' => 'YES' | 'NO'               // What user bet on
'side' => 'yes' | 'no'                  // Alternative field
'amount_invested' => decimal            // Money user invested
'token_amount' => decimal              // Shares/tokens bought
'price_at_buy' => decimal              // Price when trade placed
'shares' => decimal                    // Number of shares
'status' => 'PENDING' | 'WON' | 'LOST'  // Trade status
'payout' => decimal                    // Payout amount (if won)
'settled_at' => timestamp              // When trade was settled
```

---

## 5. Code Examples

### Check if Market has Result

```php
$market = Market::find($id);

if ($market->hasResult()) {
    $result = $market->getFinalOutcome(); // Returns 'YES' or 'NO'
    echo "Market result: " . $result;
}
```

### Check Trade Status

```php
$trade = Trade::find($id);

if ($trade->isPending()) {
    echo "Trade is pending";
} elseif ($trade->isWin()) {
    echo "Trade won! Payout: $" . $trade->payout;
} elseif ($trade->isLoss()) {
    echo "Trade lost";
}
```

### Manually Set Market Result

```php
$market = Market::find($id);
$market->final_result = 'yes'; // or 'no'
$market->outcome_result = 'yes'; // or 'no'
$market->final_outcome = 'YES'; // or 'NO'
$market->result_set_at = now();
$market->closed = true;
$market->save();

// Settle trades
$settlementService = new SettlementService();
$settlementService->settleMarket($market->id);
```

### Get User's Trades

```php
$user = Auth::user();
$trades = Trade::where('user_id', $user->id)
    ->with('market')
    ->get();

foreach ($trades as $trade) {
    echo "Market: " . $trade->market->question;
    echo "Outcome: " . $trade->outcome;
    echo "Status: " . $trade->status;
    if ($trade->isWin()) {
        echo "Payout: $" . $trade->payout;
    }
}
```

---

## 6. API Endpoints Summary

### Trading Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/market/{id}/buy` | Place a trade |
| GET | `/api/trades` | Get user's trades |
| GET | `/api/trades/{id}` | Get specific trade |

### Admin Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/admin/market/{id}/set-result` | Set market result & settle |
| POST | `/admin/market/{id}/settle-trades` | Manually settle trades |
| GET | `/admin/market/{id}` | View market details |

---

## 7. Important Notes (গুরুত্বপূর্ণ নোট)

1. **Price Calculation:**
   - Market price = `outcome_prices[1]` for YES, `outcome_prices[0]` for NO
   - Shares = `amount_invested / price`
   - Payout if win = `shares × $1.00`

2. **Settlement Timing:**
   - Automatic: Market result set হলে immediately settle হয়
   - Manual: Admin panel থেকে manually settle করতে পারেন
   - Scheduled: Cron job দিয়ে closed markets automatically settle হয়

3. **Wallet Balance:**
   - Trade place করার সময় balance deduct হয়
   - Trade win হলে payout wallet এ add হয়
   - Trade loss হলে কোনো payout নেই

4. **Market Status:**
   - `active = true` → Market open for trading
   - `closed = true` → Market closed, no new trades
   - `settled = true` → All trades settled

---

## 8. Testing (টেস্টিং)

### Test Market Result Setting

```bash
# Set market result via API
curl -X POST http://your-domain/admin/market/1/set-result \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"final_result": "yes"}'
```

### Test Trading

```bash
# Place a trade
curl -X POST http://your-domain/api/market/1/buy \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer USER_TOKEN" \
  -d '{"outcome": "YES", "amount": 10.00}'
```

---

## 9. Troubleshooting (সমস্যা সমাধান)

### Problem: Market result set কিন্তু trades settle হয়নি

**Solution:**
```php
// Manually settle
$settlementService = new SettlementService();
$settlementService->settleMarket($marketId);
```

### Problem: Trade status "PENDING" কিন্তু market already closed

**Solution:**
- Check if `outcome_result` set আছে
- Check if `settled = false`
- Run settlement manually

### Problem: User wallet balance update হয়নি

**Solution:**
- Check trade status (should be "win" or "WON")
- Check `payout` field (should be > 0)
- Check wallet transaction logs
- Manually update if needed

---

## 10. Next Steps (পরবর্তী ধাপ)

1. ✅ Market fetch from Polymarket API - **DONE**
2. ✅ Save resolution data from API - **DONE** (updated in `storeEvents()`)
3. ✅ Trading system - **EXISTS** (via TradeService)
4. ✅ Settlement system - **EXISTS** (via SettlementService)
5. ⚠️ Test with real Polymarket data
6. ⚠️ Add UI for setting market results in admin panel
7. ⚠️ Add notifications for trade settlements

---

## Contact & Support

যদি কোনো সমস্যা হয় বা প্রশ্ন থাকে, code comments এবং service files দেখুন:
- `app/Services/TradeService.php`
- `app/Services/SettlementService.php`
- `app/Http/Controllers/Backend/MarketController.php`
- `app/Http/Controllers/Frontend/TradeController.php`

