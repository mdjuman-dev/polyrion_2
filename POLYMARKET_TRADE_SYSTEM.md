# Polymarket-Style Trade System Documentation

## Overview
Complete Polymarket-style trading system for Laravel with token-based trading, automatic settlement, and wallet management.

## Database Structure

### Markets Table
- `id` - Primary key
- `outcome_prices` - JSON array: `["NO_price", "YES_price"]`
  - Index 0 = NO price (e.g., "0.0545")
  - Index 1 = YES price (e.g., "0.9455")
- `final_outcome` - ENUM('YES', 'NO') - Set when market closes
- `close_time` - DateTime when market closes

### Trades Table
- `id` - Primary key
- `user_id` - Foreign key to users
- `market_id` - Foreign key to markets
- `outcome` - ENUM('YES', 'NO') - What user bet on
- `amount_invested` - Decimal(15,2) - Amount user invested
- `token_amount` - Decimal(20,8) - Calculated: amount_invested / price_at_buy
- `price_at_buy` - Decimal(8,6) - Price when trade was placed
- `status` - ENUM('PENDING', 'WON', 'LOST')
- `payout` - Decimal(15,2) - Payout amount (token_amount × 1.00 if WON)
- `settled_at` - Timestamp when trade was settled

### Users/Wallets
- Users have wallets with `balance` column
- Balance is deducted immediately when trade is placed
- Payout is added to balance when trade is settled as WON

## Trade Logic

### Token Calculation
```
token_amount = amount_invested / selected_outcome_price
```

**Example:**
- User invests $100
- YES price = 0.9455
- Token amount = 100 / 0.9455 = 105.76 tokens

### Outcome Prices
The `outcome_prices` JSON array structure:
```json
["0.0545", "0.9455"]
```
- Index 0 = NO price = 0.0545 (5.45¢)
- Index 1 = YES price = 0.9455 (94.55¢)

## API Endpoints

### Buy Trade
**POST** `/market/{marketId}/buy`

**Request:**
```json
{
    "outcome": "YES",
    "amount": 100.00
}
```

**Response:**
```json
{
    "success": true,
    "message": "Trade placed successfully",
    "trade": {
        "id": 1,
        "outcome": "YES",
        "amount_invested": 100.00,
        "token_amount": 105.76,
        "price_at_buy": 0.9455,
        "status": "PENDING"
    },
    "wallet": {
        "balance": 900.00
    }
}
```

### Settle Market
**POST** `/market/{marketId}/settle`

**Response:**
```json
{
    "success": true,
    "message": "Settled 5 trades",
    "settled_count": 5,
    "total_pending": 5
}
```

## Settlement Logic

### When Market Closes
1. Admin sets `final_outcome` to "YES" or "NO" in markets table
2. Settlement process runs (manual or via command)

### Settlement Rules
- If `trade.outcome == market.final_outcome`:
  - Status = `WON`
  - Payout = `token_amount × 1.00`
  - Add payout to user wallet
- Else:
  - Status = `LOST`
  - Payout = 0
  - No wallet credit

**Example:**
- User bought YES with 105.76 tokens at $0.9455
- Market closes with final_outcome = "YES"
- Payout = 105.76 × 1.00 = $105.76
- User wallet credited $105.76

## TradeService Methods

### validateBalance(User $user, float $amount): bool
Checks if user has sufficient balance.

### calculateTokens(float $amount, float $outcomePrice): float
Calculates token amount: `amount / outcomePrice`

### getOutcomePrice(Market $market, string $outcome): float
Gets the current price for YES or NO from outcome_prices array.

### createTrade(User $user, Market $market, string $outcome, float $amount): Trade
- Validates balance
- Gets outcome price
- Calculates tokens
- Deducts balance
- Creates trade record
- Creates wallet transaction

### settleTrade(Trade $trade): void
- Checks if trade.outcome == market.final_outcome
- If WON: Sets payout = token_amount × 1.00, adds to wallet
- If LOST: Sets payout = 0
- Updates trade status and settled_at

## Commands

### Settle Markets Command
```bash
php artisan markets:settle
```

This command:
1. Finds all markets with `final_outcome` set
2. Finds all PENDING trades for those markets
3. Settles each trade using TradeService
4. Logs results

**Schedule in app/Console/Kernel.php:**
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('markets:settle')->everyMinute();
}
```

## Validation Rules

### Buy Trade
- `outcome`: required, in:['YES', 'NO']
- `amount`: required, numeric, min:0.01

### Market Requirements
- Market must be `active = true`
- Market must not be `closed = true`
- Market must not be `archived = true`
- Market `close_time` must be in future (if set)

## Example Usage

### 1. User Buys YES
```php
$market = Market::find(1);
$user = Auth::user();

// outcome_prices = ["0.0545", "0.9455"]
// User invests $100 on YES

$trade = $tradeService->createTrade($user, $market, 'YES', 100.00);

// Result:
// - amount_invested = 100.00
// - price_at_buy = 0.9455
// - token_amount = 100 / 0.9455 = 105.76
// - User balance deducted $100
```

### 2. Market Closes with YES
```php
$market->final_outcome = 'YES';
$market->save();

// Settle all trades
$tradeService->settleTrade($trade);

// Result:
// - status = 'WON'
// - payout = 105.76 × 1.00 = 105.76
// - User balance credited $105.76
```

### 3. Market Closes with NO (User bet YES)
```php
$market->final_outcome = 'NO';
$market->save();

$tradeService->settleTrade($trade);

// Result:
// - status = 'LOST'
// - payout = 0
// - User balance unchanged (already deducted)
```

## Sample JSON for outcome_prices

```json
["0.0545", "0.9455"]
```

This means:
- NO costs $0.0545 (5.45¢)
- YES costs $0.9455 (94.55¢)
- Prices should always sum to approximately 1.00

## Error Handling

### Insufficient Balance
```json
{
    "success": false,
    "message": "Insufficient balance"
}
```

### Market Closed
```json
{
    "success": false,
    "message": "Market is closed for trading"
}
```

### Invalid Outcome
```json
{
    "success": false,
    "message": "Outcome must be YES or NO"
}
```

## Testing

### Test Trade Creation
```php
$user = User::find(1);
$market = Market::find(1);
$market->outcome_prices = json_encode(["0.0545", "0.9455"]);

$trade = $tradeService->createTrade($user, $market, 'YES', 100.00);
// Assert: trade->token_amount = 105.76
```

### Test Settlement
```php
$market->final_outcome = 'YES';
$market->save();

$tradeService->settleTrade($trade);
// Assert: trade->status = 'WON'
// Assert: trade->payout = 105.76
// Assert: user->wallet->balance increased by 105.76
```

## Notes

- All amounts are in USDT (or your base currency)
- Token amounts are calculated with 8 decimal precision
- Prices are stored with 6 decimal precision
- Balance is deducted immediately on trade creation
- Payout is added only when trade is settled as WON
- Multiple trades per user per market are allowed
- Each trade is stored as a separate record


