# Trading System Implementation Guide

## Overview
This trading system allows users to place Yes/No trades without admin approval. Trades automatically close when markets end, and user balances update automatically based on Win/Lose outcomes.

---

## 1. No Admin Approval Required ✅

### Current Implementation
**Status:** Already implemented - No admin approval needed.

### How It Works
Users can place trades directly through the `TradeController` without any approval workflow:

```php
// app/Http/Controllers/Frontend/TradeController.php
public function placeTrade(Request $request, $marketId)
{
    $user = Auth::user();
    
    // Direct trade creation - no approval needed
    $trade = $this->tradeService->createTrade($user, $market, $outcome, $amount);
    
    return response()->json(['success' => true, 'trade' => $trade]);
}
```

### Route Protection
```php
// routes/frontend.php
Route::post('/market/{marketId}', 'placeTrade')
    ->middleware(['auth'])  // Only requires authentication
    ->name('trades.place');
```

**Key Points:**
- Only requires user authentication (`auth` middleware)
- No admin permission checks
- No approval workflow
- Instant trade execution

---

## 2. Automatic Trade Closing Mechanism

### How It Works

#### Step 1: Scheduler Runs Every Minute
```php
// routes/console.php
Schedule::call(function () {
    $settlementService = app(\App\Services\SettlementService::class);
    $results = $settlementService->settleClosedMarkets();
})->name('settle-markets')->everyMinute()->withoutOverlapping(5);
```

#### Step 2: Find Closed Markets
```php
// app/Services/SettlementService.php - settleClosedMarkets()
public function settleClosedMarkets(): array
{
    // Find markets that are:
    // 1. Closed by flag (is_closed = true OR closed = true)
    // 2. OR have expired close_time
    // 3. AND not yet settled
    $markets = Market::where('settled', false)
        ->where(function($query) {
            $query->where('is_closed', true)
                  ->orWhere('closed', true)
                  ->orWhere(function($q) {
                      $q->whereNotNull('close_time')
                        ->where('close_time', '<=', now());
                  });
        })
        ->get();
    
    // Process each market
    foreach ($markets as $market) {
        $this->settleMarket($market->id);
    }
}
```

#### Step 3: Auto-Close Markets & Determine Results
```php
// app/Services/SettlementService.php - settleMarket()
public function settleMarket($marketId): bool
{
    $market = Market::find($marketId);
    
    // Auto-close if close_time expired
    if ($market->close_time && now() >= $market->close_time && !$market->is_closed) {
        $market->is_closed = true;
        $market->closed = true;
        $market->save();
    }
    
    // Auto-determine result if not set
    if (!$market->outcome_result && $market->isClosed()) {
        $autoOutcome = $market->determineOutcomeFromLastTradePrice();
        if ($autoOutcome) {
            $market->final_outcome = $autoOutcome;
            $market->outcome_result = strtolower($autoOutcome);
            $market->save();
        }
    }
    
    // Process all pending trades...
}
```

#### Step 4: Process Pending Trades
```php
// Get all pending trades for the market
$trades = Trade::where('market_id', $marketId)
    ->whereIn('status', ['PENDING', 'pending'])
    ->lockForUpdate()  // Prevent duplicate processing
    ->get();

foreach ($trades as $trade) {
    // Compare trade outcome with market result
    if ($tradeOutcome === $outcomeResult) {
        // WIN: Update status and credit balance
        $trade->status = 'WON';
        $wallet->balance += $payout;
    } else {
        // LOSS: Update status only
        $trade->status = 'LOST';
    }
    $trade->settled_at = now();
    $trade->save();
}
```

### Flow Diagram
```
Market closes (close_time reached)
    ↓
Scheduler detects closed market (every minute)
    ↓
Auto-determine result (if not set)
    ↓
Find all PENDING trades
    ↓
Compare trade.outcome with market.result
    ↓
Update trade status (WON/LOST)
    ↓
Credit balance if WON
    ↓
Mark market as settled
```

---

## 3. Automatic Balance Updates

### Win Scenario
```php
// app/Services/SettlementService.php
if ($tradeOutcome === $outcomeResult) {
    // Calculate payout: shares × $1.00
    $payout = $shares * 1.00;
    
    // Lock wallet to prevent race conditions
    $wallet = Wallet::where('user_id', $user->id)
        ->lockForUpdate()
        ->first();
    
    // Update balance atomically
    $balanceBefore = $wallet->balance;
    $wallet->balance += $payout;
    $wallet->save();
    
    // Update trade status
    $trade->status = 'WON';
    $trade->payout = $payout;
    $trade->settled_at = now();
    $trade->save();
    
    // Create transaction record
    WalletTransaction::create([
        'type' => 'trade_payout',
        'amount' => $payout,
        'balance_before' => $balanceBefore,
        'balance_after' => $wallet->balance,
    ]);
}
```

### Loss Scenario
```php
else {
    // No balance change, just update status
    $trade->status = 'LOST';
    $trade->payout = 0;
    $trade->settled_at = now();
    $trade->save();
}
```

### Transaction Safety
- Uses database transactions (`DB::beginTransaction()`)
- Row-level locking (`lockForUpdate()`) prevents concurrent updates
- Atomic balance updates
- Transaction rollback on errors

---

## 4. Low-Context UI/UX Implementation

### Frontend Approach: Minimal Information Display

#### A. Trade List - Compact View
```blade
<!-- Show minimal info by default -->
<div class="trade-item" data-trade-id="{{ $trade->id }}">
    <span class="outcome">{{ $trade->outcome }}</span>
    <span class="amount">${{ $trade->amount_invested }}</span>
    <span class="status-badge status-{{ strtolower($trade->status) }}">
        {{ $trade->status }}
    </span>
</div>

<!-- Detailed info on hover/click -->
<div class="trade-details" style="display: none;">
    <div>Market: {{ $trade->market->question }}</div>
    <div>Price: {{ $trade->price_at_buy }}</div>
    <div>Tokens: {{ $trade->token_amount }}</div>
    <div>Payout: ${{ $trade->payout ?? 0 }}</div>
    <div>Settled: {{ $trade->settled_at?->format('M d, Y H:i') }}</div>
</div>
```

#### B. JavaScript for Low-Context Display
```javascript
// Show details only on hover/click
document.querySelectorAll('.trade-item').forEach(item => {
    const tradeId = item.dataset.tradeId;
    const details = item.querySelector('.trade-details');
    
    // Hover to show details
    item.addEventListener('mouseenter', () => {
        if (!details.dataset.loaded) {
            loadTradeDetails(tradeId, details);
        }
        details.style.display = 'block';
    });
    
    item.addEventListener('mouseleave', () => {
        details.style.display = 'none';
    });
});

// Load details via API only when needed
async function loadTradeDetails(tradeId, container) {
    const response = await fetch(`/api/trades/${tradeId}`);
    const data = await response.json();
    
    container.innerHTML = `
        <div>Market: ${data.trade.market.question}</div>
        <div>Price: ${data.trade.price_at_buy}</div>
        <div>Tokens: ${data.trade.token_amount}</div>
        <div>Payout: $${data.trade.payout || 0}</div>
        <div>Profit: $${(data.trade.payout || 0) - data.trade.amount_invested}</div>
    `;
    container.dataset.loaded = 'true';
}
```

#### C. Status Badge Component
```blade
@php
    $status = strtoupper($trade->status ?? 'PENDING');
    $statusConfig = [
        'PENDING' => ['color' => '#f59e0b', 'icon' => '⏳', 'label' => 'Open'],
        'WON' => ['color' => '#10b981', 'icon' => '✓', 'label' => 'Won'],
        'LOST' => ['color' => '#ef4444', 'icon' => '✗', 'label' => 'Lost'],
    ];
    $config = $statusConfig[$status] ?? $statusConfig['PENDING'];
@endphp

<span class="status-badge" 
      style="background: {{ $config['color'] }}20; 
             color: {{ $config['color'] }};
             border: 1px solid {{ $config['color'] }}40;"
      title="Click for details">
    {{ $config['icon'] }} {{ $config['label'] }}
</span>
```

#### D. Real-Time Updates (Optional)
```javascript
// Poll for trade status updates (for pending trades)
function pollTradeStatus(tradeId) {
    const interval = setInterval(async () => {
        const response = await fetch(`/api/trades/${tradeId}`);
        const data = await response.json();
        
        if (data.trade.status !== 'PENDING') {
            // Trade settled - update UI
            updateTradeStatus(tradeId, data.trade);
            clearInterval(interval);
        }
    }, 5000); // Poll every 5 seconds
}

// Update UI when trade settles
function updateTradeStatus(tradeId, trade) {
    const badge = document.querySelector(`[data-trade-id="${tradeId}"] .status-badge`);
    badge.textContent = trade.status === 'WON' ? '✓ Won' : '✗ Lost';
    badge.style.color = trade.status === 'WON' ? '#10b981' : '#ef4444';
    
    // Show notification
    showNotification(
        trade.status === 'WON' 
            ? `Trade won! +$${trade.payout} added to balance`
            : 'Trade closed - No payout'
    );
}
```

---

## 5. Step-by-Step Implementation Checklist

### A. Verify No Admin Approval Required
- [x] Check `TradeController::placeTrade()` - No approval checks
- [x] Verify routes only require `auth` middleware
- [x] Confirm `TradeService::createTrade()` executes immediately

### B. Ensure Automatic Trade Closing
- [x] Verify scheduler in `routes/console.php` runs every minute
- [x] Check `SettlementService::settleClosedMarkets()` finds closed markets
- [x] Confirm auto-close logic when `close_time` expires
- [x] Verify auto-result determination from `last_trade_price`

### C. Verify Automatic Balance Updates
- [x] Check `SettlementService::settleMarket()` updates balances
- [x] Verify transaction safety with `DB::beginTransaction()`
- [x] Confirm row locking with `lockForUpdate()`
- [x] Test balance updates on WIN trades
- [x] Verify no balance change on LOSS trades

### D. Test the Complete Flow
```php
// Test scenario:
// 1. User places trade
$trade = $tradeService->createTrade($user, $market, 'YES', 100);
// Status: PENDING, Balance deducted: -$100

// 2. Market closes (close_time passes)
// Scheduler runs automatically

// 3. Market result determined
$market->outcome_result = 'yes'; // or auto-determined

// 4. Trade settled
// If trade.outcome === market.result:
//   Status: WON, Balance: +$payout
// Else:
//   Status: LOST, Balance: unchanged
```

---

## 6. Database Schema Reference

### Trades Table
```sql
- id
- user_id
- market_id
- outcome (YES/NO)
- amount_invested
- token_amount
- price_at_buy
- status (PENDING/WON/LOST)
- payout
- settled_at
```

### Markets Table
```sql
- id
- question
- close_time
- is_closed
- closed
- settled
- outcome_result (yes/no)
- final_outcome (YES/NO)
- last_trade_price
```

### Wallets Table
```sql
- id
- user_id
- balance
- currency
```

### Wallet Transactions Table
```sql
- id
- user_id
- wallet_id
- type (trade/trade_payout)
- amount
- balance_before
- balance_after
- reference_type (Trade)
- reference_id
```

---

## 7. API Endpoints

### Place Trade
```
POST /trades/market/{marketId}
Body: { option: 'yes'|'no', amount: float }
Response: { success: true, trade: {...}, wallet: { balance: ... } }
```

### Get Trade Details
```
GET /api/trades/{id}
Response: { success: true, trade: {...} }
```

### Get User Trades
```
GET /trades/my-trades
Response: { success: true, trades: [...], statistics: {...} }
```

---

## 8. Monitoring & Logging

### Key Log Points
```php
// Trade creation
Log::info('Trade created successfully', [
    'trade_id' => $trade->id,
    'user_id' => $user->id,
    'market_id' => $market->id,
]);

// Trade settlement
Log::info("Trade settled as WON - balance updated", [
    'trade_id' => $trade->id,
    'payout' => $payout,
    'balance_before' => $balanceBefore,
    'balance_after' => $wallet->balance
]);

// Market settlement
Log::info("Market settlement completed", [
    'market_id' => $marketId,
    'win_count' => $winCount,
    'loss_count' => $lossCount
]);
```

### Check Logs
```bash
# View settlement logs
tail -f storage/logs/laravel.log | grep "settlement"

# Check scheduler execution
tail -f storage/logs/laravel.log | grep "settle-markets"
```

---

## 9. Troubleshooting

### Trades Not Closing
1. Check scheduler is running: `php artisan schedule:list`
2. Verify `close_time` is set on markets
3. Check logs for settlement errors
4. Manually trigger: `php artisan tinker` → `app(\App\Services\SettlementService::class)->settleClosedMarkets()`

### Balance Not Updating
1. Verify market has `outcome_result` set
2. Check trade status is PENDING before settlement
3. Verify wallet exists for user
4. Check transaction logs for errors

### Duplicate Settlements
- Already prevented with `lockForUpdate()` on trades query
- Transaction rollback on errors prevents partial updates

---

## Summary

✅ **No Admin Approval:** Users can trade directly after authentication  
✅ **Auto Trade Closing:** Scheduler runs every minute, closes trades when markets end  
✅ **Auto Balance Updates:** Balances update automatically on WIN, no change on LOSS  
✅ **Transaction Safe:** Uses database transactions and row locking  
✅ **Low-Context UI:** Show minimal info, load details on demand  

The system is fully automated and requires no manual intervention for trade settlement.

