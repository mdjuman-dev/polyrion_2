# Automatic Payout System Documentation

## Overview
This system ensures that when a market is resolved, all user payouts are automatically calculated and processed without any manual intervention. The system is fully transaction-safe, concurrency-safe, and prevents duplicate payouts.

## Key Features

### 1. Automatic Settlement Triggers
- **API Resolution**: When markets are resolved from Polymarket API, settlement is automatically triggered
- **Admin Resolution**: When admin sets market result via `setResult()` endpoint, settlement runs immediately
- **Scheduled Settlement**: Scheduler runs every minute to catch any missed settlements

### 2. Duplicate Prevention
- **Market-level check**: `settled` flag prevents re-processing already settled markets
- **Trade-level check**: Only processes trades with `PENDING` status
- **Database locking**: Uses `lockForUpdate()` to prevent concurrent settlement attempts
- **Double-check pattern**: Verifies trade status after lock to catch race conditions

### 3. Transaction Safety
- All settlement operations wrapped in database transactions
- Automatic rollback on any error
- Market marked as `settled` only after successful completion
- Wallet updates are atomic

### 4. Market Locking
- Markets are automatically locked when resolved (no new trades allowed)
- `isOpenForTrading()` checks for:
  - Winning outcome exists
  - Market is settled
  - Market has result
  - Market is closed

## Settlement Flow

### Step 1: Market Resolution
```php
// When market is resolved (from API or admin):
1. Winning outcome is marked (is_winning = true)
2. Market is closed (closed = true, is_closed = true)
3. Market is queued for settlement
```

### Step 2: Settlement Process
```php
// SettlementService::settleMarket()
1. Lock market row (lockForUpdate)
2. Check if already settled (early exit if yes)
3. Get winning outcome
4. Lock all pending trades
5. Process each trade:
   - Check if trade outcome matches winning outcome
   - If WON: Calculate payout, credit wallet, mark as WON
   - If LOST: Mark as LOST, no payout
6. Mark market as settled
7. Commit transaction
```

### Step 3: Payout Calculation
```php
// Polymarket-style payout:
payout = shares * 1.00

// Where:
shares = amount_invested / price_at_buy
// OR
shares = token_amount (if already calculated)
```

## Safety Mechanisms

### 1. Duplicate Settlement Prevention
```php
// In SettlementService::settleMarket()
if ($market->settled) {
    // Already settled, skip immediately
    return true;
}
```

### 2. Trade Status Verification
```php
// Double-check after lock
$trade->refresh();
if (!in_array(strtoupper($trade->status), ['PENDING'])) {
    // Already processed, skip
    continue;
}
```

### 3. Concurrency Protection
```php
// Market-level lock
$market = Market::lockForUpdate()->find($marketId);

// Trade-level lock
$trades = Trade::where('market_id', $marketId)
    ->whereIn('status', ['PENDING', 'pending'])
    ->lockForUpdate()
    ->get();
```

### 4. Wallet Locking
```php
// Prevent concurrent wallet updates
$wallet = Wallet::where('user_id', $user->id)
    ->where('wallet_type', Wallet::TYPE_EARNING)
    ->lockForUpdate()
    ->first();
```

## Automatic Settlement Triggers

### 1. API Resolution (storeEvents)
```php
// When market is resolved from Polymarket API:
if ($outcomeResult && $isClosed && !$market->settled) {
    // Mark winning outcome
    $winningOutcome->is_winning = true;
    
    // Queue for settlement
    $marketsToSettle[] = $market->id;
}

// Batch settlement (every 50 markets or at end)
foreach ($marketsToSettle as $marketId) {
    $settlementService->settleMarket($marketId);
}
```

### 2. Admin Resolution (setResult)
```php
// When admin sets result:
$market->winningOutcome = $outcome;
$market->save();

// Immediate settlement
$settlementService->settleMarket($market->id);
```

### 3. Scheduled Settlement
```php
// routes/console.php - Runs every minute
Schedule::call(function () {
    $settlementService = app(\App\Services\SettlementService::class);
    $results = $settlementService->settleClosedMarkets();
})->everyMinute()->withoutOverlapping(5);
```

## Payout Process

### Winning Trades
1. Calculate payout: `shares * 1.00`
2. Get or create earning wallet
3. Lock wallet row
4. Increment balance
5. Create wallet transaction record
6. Mark trade as `WON`
7. Set `payout` and `settled_at`

### Losing Trades
1. Mark trade as `LOST`
2. Set `payout = 0`
3. Set `settled_at`

## Audit Trail

All settlement operations are logged:
- Market settlement start/completion
- Trade-by-trade processing
- Payout amounts
- Wallet balance changes
- Errors and warnings

Logs include:
- Market ID
- Trade IDs
- User IDs
- Payout amounts
- Balance before/after
- Winning outcome details

## Error Handling

### Transaction Rollback
- Any exception during settlement triggers rollback
- Market remains `settled = false` if settlement fails
- Trades remain `PENDING` if settlement fails
- Wallet balances are not modified on failure

### Retry Mechanism
- Scheduler runs every minute
- Failed settlements are retried automatically
- Markets remain in queue until successfully settled

### Error Logging
- All errors are logged with full context
- Includes stack traces for debugging
- Separate logs for different error types

## Testing

### Manual Settlement Test
```php
php artisan tinker

$service = app(\App\Services\SettlementService::class);
$result = $service->settleMarket($marketId);
dd($result);
```

### Check Settlement Status
```php
$market = Market::find($marketId);
echo "Settled: " . ($market->settled ? 'Yes' : 'No') . "\n";
echo "Winning Outcome: " . ($market->winningOutcome ? $market->winningOutcome->name : 'None') . "\n";
echo "Pending Trades: " . $market->pendingTrades()->count() . "\n";
```

### Verify Payouts
```php
$trades = Trade::where('market_id', $marketId)
    ->where('status', 'WON')
    ->get();

foreach ($trades as $trade) {
    echo "Trade {$trade->id}: Payout = $" . $trade->payout . "\n";
}
```

## Configuration

### Scheduler Frequency
```php
// routes/console.php
->everyMinute()  // Change to ->everyThirtySeconds() for faster processing
```

### Batch Size
```php
// MarketController::storeEvents()
if (count($marketsToSettle) >= 50) {
    // Process batch
}
```

## Monitoring

### Check Scheduler
```bash
php artisan schedule:list
php artisan schedule:run
```

### View Logs
```bash
tail -f storage/logs/laravel.log | grep "settlement"
tail -f storage/logs/laravel.log | grep "payout"
```

### Database Queries
```sql
-- Check unsettled markets with results
SELECT id, question, settled, outcome_result, final_outcome 
FROM markets 
WHERE settled = 0 
AND (outcome_result IS NOT NULL OR final_outcome IS NOT NULL)
AND (closed = 1 OR is_closed = 1);

-- Check pending trades
SELECT COUNT(*) 
FROM trades 
WHERE status = 'PENDING' 
AND market_id IN (
    SELECT id FROM markets WHERE settled = 0
);
```

## Summary

✅ **Fully Automatic** - No manual intervention required
✅ **Transaction Safe** - All operations in transactions
✅ **Concurrency Safe** - Database locking prevents duplicates
✅ **Audit Trail** - Complete logging of all operations
✅ **Error Resilient** - Automatic retry on failures
✅ **Market Locking** - Prevents new trades after resolution
✅ **Instant Payouts** - Credited immediately to earning wallet

**System is production-ready and handles all edge cases.**

