# সম্পূর্ণ Trading Module - Complete Guide

## Overview

এই trading module সম্পূর্ণভাবে কাজ করে যেখানে:
1. ✅ User trade buy করতে পারে
2. ✅ Market close হলে automatically result set হয়
3. ✅ User জিতলে automatically account এ টাকা add হয়
4. ✅ সব transaction properly track হয়

## Complete Trading Flow

### 1. User Trade Buy করে

**Process:**
```
User → Market Select → Outcome Select (YES/NO) → Amount Enter → Buy
```

**What Happens:**
1. Market open আছে কিনা check হয়
2. User balance check হয়
3. Market price থেকে outcome price calculate হয়
4. Token amount calculate হয়: `token_amount = amount / price`
5. Wallet balance থেকে amount deduct হয়
6. Trade create হয় status = `PENDING`
7. Wallet transaction create হয় (type: `trade`)

**API Endpoint:**
```
POST /market/{marketId}/buy
{
    "outcome": "YES",  // or "NO"
    "amount": 10.50
}
```

**Response:**
```json
{
    "success": true,
    "message": "Trade placed successfully",
    "trade": {
        "id": 123,
        "outcome": "YES",
        "amount_invested": 10.50,
        "token_amount": 15.75,
        "price_at_buy": 0.67,
        "status": "PENDING"
    },
    "wallet": {
        "balance": 89.50
    }
}
```

### 2. Market Close এবং Result Set

**Automatic Result Setting:**
- Polymarket API থেকে market resolved হলে automatically result set হয়
- Admin manually result set করতে পারে

**When Market Closes:**
1. Market `closed` = true হয়
2. Market `is_closed` = true হয়
3. `outcome_result` set হয় (yes/no)
4. `final_outcome` set হয় (YES/NO)
5. `result_set_at` timestamp set হয়

**Admin Manual Result:**
```
POST /admin/market/{id}/set-result
{
    "final_result": "yes"  // or "no"
}
```

### 3. Automatic Settlement

**Settlement Triggers:**
1. **Immediate:** Market result set হলে immediately settlement trigger হয়
2. **Scheduled:** Every minute scheduled task runs এবং closed markets settle করে

**Settlement Process:**
```
Market Closed + Result Set
    ↓
Find All PENDING Trades
    ↓
For Each Trade:
    ↓
    Trade Outcome == Market Result?
        ↓                    ↓
       YES                  NO
        ↓                    ↓
    WON                  LOST
        ↓                    ↓
Payout = Shares × $1    Payout = $0
        ↓                    ↓
Wallet Balance += Payout  Status = LOST
        ↓
Wallet Transaction Created
        ↓
Trade Status = WON
```

**Settlement Logic:**
```php
if ($trade->outcome === $market->outcome_result) {
    // WIN
    $payout = $trade->token_amount * 1.00;
    $wallet->balance += $payout;
    $trade->status = 'WON';
    // Create wallet transaction
} else {
    // LOSS
    $trade->status = 'LOST';
    $trade->payout = 0;
}
```

### 4. Balance Update

**When User Wins:**
1. Payout calculate হয়: `token_amount × $1.00`
2. Wallet balance automatically add হয়
3. Wallet transaction create হয় (type: `trade_payout`)
4. Trade status update হয়: `WON`
5. Trade `settled_at` timestamp set হয়

**Example:**
```
User bought: YES at $0.67
Amount invested: $10.00
Token amount: 14.93 tokens

Market result: YES (User won!)

Payout: 14.93 × $1.00 = $14.93
Profit: $14.93 - $10.00 = $4.93

Wallet balance: $100.00 → $114.93
```

## Database Structure

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
- outcome_result (yes/no)
- final_outcome (YES/NO)
- final_result (yes/no)
- closed (boolean)
- is_closed (boolean)
- settled (boolean)
- result_set_at (timestamp)
```

### Wallet Transactions Table
```sql
- id
- user_id
- wallet_id
- type (trade, trade_payout)
- amount
- balance_before
- balance_after
- reference_type (Trade)
- reference_id
- description
- metadata
```

## Services

### TradeService
**Location:** `app/Services/TradeService.php`

**Key Methods:**
- `createTrade()` - Create new trade
- `settleTrade()` - Settle single trade
- `settleMarket()` - Settle all trades for a market
- `getTradePreview()` - Get trade estimate before buying
- `getMarketPrices()` - Get current market prices

### SettlementService
**Location:** `app/Services/SettlementService.php`

**Key Methods:**
- `settleMarket($marketId)` - Settle all pending trades for a market
- `settleClosedMarkets()` - Settle all closed markets (used by scheduler)

## Automatic Settlement

### Scheduled Task
**Location:** `routes/console.php`

```php
Schedule::call(function () {
    $settlementService = app(\App\Services\SettlementService::class);
    $results = $settlementService->settleClosedMarkets();
})->everyMinute();
```

**What it does:**
- Every minute runs
- Finds all closed markets with results but not settled
- Automatically settles all pending trades
- Updates wallet balances
- Creates wallet transactions

### Immediate Settlement
- When admin sets result → immediately settles
- When Polymarket API provides result → immediately settles

## API Endpoints

### Frontend Trading
```
POST /market/{marketId}/buy
GET  /market/{marketId}/trade-preview
GET  /market/{marketId}/prices
POST /market/{marketId}/settle
```

### Admin
```
POST /admin/market/{id}/set-result
POST /admin/market/{id}/settle-trades
```

## Status Values

### Trade Status
- `PENDING` - Trade placed, waiting for market result
- `WON` - Trade won, payout added to wallet
- `LOST` - Trade lost, no payout

### Market Status
- `active` - Market is open for trading
- `closed` - Market is closed
- `settled` - All trades have been settled

## Error Handling

### Common Errors
1. **Insufficient Balance**
   - Error: "Insufficient balance"
   - Solution: User needs to deposit funds

2. **Market Closed**
   - Error: "Market is closed for trading"
   - Solution: Market already closed, cannot trade

3. **Invalid Price**
   - Error: "Invalid market price"
   - Solution: Market price data issue, retry

4. **Settlement Failed**
   - Error: Logged in Laravel logs
   - Solution: Check logs, manually settle if needed

## Testing

### Test Trade Flow
1. **Create Test Market:**
   - Admin panel → Create market
   - Set question, outcomes
   - Mark as active

2. **Place Trade:**
   - Frontend → Select market
   - Choose outcome (YES/NO)
   - Enter amount
   - Click Buy

3. **Verify Trade:**
   - Check trade created (status: PENDING)
   - Check wallet balance deducted
   - Check wallet transaction created

4. **Set Market Result:**
   - Admin → Market → Set Result
   - Choose outcome (yes/no)

5. **Verify Settlement:**
   - Check trade status updated (WON/LOST)
   - If WON: Check wallet balance increased
   - Check wallet transaction created (trade_payout)
   - Check market settled = true

## Monitoring

### Logs
**Location:** `storage/logs/laravel.log`

**Key Log Entries:**
- "Trade created successfully"
- "Trade settled as WON"
- "Trade settled as LOST"
- "Market settlement completed"

### Database Queries
```sql
-- Check pending trades
SELECT * FROM trades WHERE status = 'PENDING';

-- Check unsettled markets
SELECT * FROM markets 
WHERE is_closed = 1 
AND settled = 0 
AND outcome_result IS NOT NULL;

-- Check user trades
SELECT * FROM trades 
WHERE user_id = ? 
ORDER BY created_at DESC;
```

## Features

✅ **Complete Trading System**
- Buy trades (YES/NO)
- Real-time price calculation
- Token amount calculation
- Balance deduction

✅ **Automatic Settlement**
- Immediate settlement when result set
- Scheduled settlement every minute
- Handles both uppercase/lowercase statuses
- Supports multiple outcome field formats

✅ **Wallet Integration**
- Automatic balance updates
- Transaction history
- Profit/loss tracking

✅ **Error Handling**
- Comprehensive validation
- Transaction rollback on errors
- Detailed logging

✅ **Admin Features**
- Manual result setting
- Manual settlement trigger
- Trade management

## Troubleshooting

### Trades Not Settling
1. Check market has result: `outcome_result` or `final_outcome` set
2. Check market is closed: `is_closed = true`
3. Check market not already settled: `settled = false`
4. Check trades are pending: `status = 'PENDING'`
5. Check scheduled task running: `php artisan schedule:run`

### Balance Not Updating
1. Check trade status is WON
2. Check wallet transaction created
3. Check Laravel logs for errors
4. Verify payout amount calculated correctly

### Settlement Service Issues
1. Check both `outcome_result` and `final_outcome` fields
2. Check trade `outcome` field matches market result
3. Check `token_amount` or `shares` field exists
4. Verify database transaction committed

## Summary

এই trading module সম্পূর্ণভাবে functional:
- ✅ User trade buy করতে পারে
- ✅ Balance automatically deduct হয়
- ✅ Market close হলে automatically result set হয়
- ✅ User জিতলে automatically balance add হয়
- ✅ সব transaction properly track হয়
- ✅ Automatic settlement every minute
- ✅ Immediate settlement when result set
- ✅ Complete error handling
- ✅ Comprehensive logging

**Everything works perfectly!** 🎉

