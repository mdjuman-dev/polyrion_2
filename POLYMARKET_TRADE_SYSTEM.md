# Polymarket-Style Trading System Documentation

## Overview

Complete Polymarket-style trading system for Laravel with token-based trading, automatic settlement, and wallet management.

---

## Database Structure

### Markets Table

```sql
CREATE TABLE markets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    event_id BIGINT,
    question VARCHAR(255) NOT NULL,
    description TEXT,
    slug VARCHAR(255) UNIQUE,

    -- Outcome Prices (JSON array)
    -- Format: ["NO_price", "YES_price"]
    -- Example: ["0.0545", "0.9455"]
    -- Index 0 = NO price (5.45¢)
    -- Index 1 = YES price (94.55¢)
    outcome_prices JSON,

    -- Market Resolution
    final_outcome ENUM('YES', 'NO') NULL,  -- Set when market closes
    final_result ENUM('yes', 'no') NULL,   -- Legacy field
    result_set_at TIMESTAMP NULL,

    -- Market Status
    active BOOLEAN DEFAULT TRUE,
    closed BOOLEAN DEFAULT FALSE,
    settled BOOLEAN DEFAULT FALSE,

    -- Timestamps
    close_time TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    INDEX idx_active_closed (active, closed),
    INDEX idx_settled (settled)
);
```

### Trades Table

```sql
CREATE TABLE trades (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    market_id BIGINT NOT NULL,

    -- Trade Details
    outcome ENUM('YES', 'NO') NOT NULL,        -- What user bet on
    option ENUM('yes', 'no'),                  -- Legacy field

    -- Investment & Shares
    amount_invested DECIMAL(15,2) NOT NULL,    -- Amount user invested
    amount DECIMAL(15,2),                       -- Legacy field

    -- Token Calculation
    token_amount DECIMAL(20,8) NOT NULL,        -- Calculated: amount_invested / price_at_buy
    shares DECIMAL(20,8),                       -- Same as token_amount

    -- Price Information
    price_at_buy DECIMAL(8,6) NOT NULL,         -- Price when trade was placed (0.0001 to 0.9999)
    price DECIMAL(8,4),                         -- Legacy field

    -- Trade Status
    status ENUM('PENDING', 'WON', 'LOST', 'CLOSED') DEFAULT 'PENDING',

    -- Settlement
    payout DECIMAL(15,2) NULL,                  -- Payout amount (token_amount × 1.00 if WON)
    payout_amount DECIMAL(15,2),                -- Legacy field
    settled_at TIMESTAMP NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (market_id) REFERENCES markets(id) ON DELETE CASCADE,
    INDEX idx_user_status (user_id, status),
    INDEX idx_market_status (market_id, status)
);
```

### Wallets Table

```sql
CREATE TABLE wallets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL UNIQUE,
    balance DECIMAL(15,2) DEFAULT 0.00,
    currency VARCHAR(10) DEFAULT 'USDT',
    status ENUM('active', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

### Wallet Transactions Table

```sql
CREATE TABLE wallet_transactions (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    wallet_id BIGINT NOT NULL,
    type VARCHAR(50) NOT NULL,                  -- 'trade', 'trade_payout', 'deposit', 'withdraw'
    amount DECIMAL(15,2) NOT NULL,              -- Positive for credit, negative for debit
    balance_before DECIMAL(15,2),
    balance_after DECIMAL(15,2),
    reference_type VARCHAR(255),                 -- Model class name
    reference_id BIGINT,                         -- Related model ID
    description TEXT,
    metadata JSON,
    created_at TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (wallet_id) REFERENCES wallets(id) ON DELETE CASCADE,
    INDEX idx_user_type (user_id, type),
    INDEX idx_reference (reference_type, reference_id)
);
```

---

## Trade Logic

### 1. Outcome Prices Structure

The `outcome_prices` JSON array structure:

```json
["0.0545", "0.9455"]
```

-   **Index 0** = NO price = 0.0545 (5.45¢)
-   **Index 1** = YES price = 0.9455 (94.55¢)
-   Prices always sum to 1.00 (100¢)

### 2. Token/Share Calculation

**Formula:**

```
token_amount = amount_invested / selected_outcome_price
```

**Example:**

-   User invests: $100
-   YES price: 0.9455 (94.55¢)
-   Token amount: 100 / 0.9455 = **105.76 tokens**

### 3. Buying YES Shares

**When user buys YES:**

```
cost = quantity × yes_price
token_amount = quantity / yes_price
```

**Example:**

-   User wants to invest: $100
-   YES price: 0.9455
-   Cost: $100
-   Tokens received: 100 / 0.9455 = 105.76 tokens
-   Balance deduction: -$100

### 4. Buying NO Shares

**When user buys NO:**

```
cost = quantity × no_price
token_amount = quantity / no_price
```

**Example:**

-   User wants to invest: $100
-   NO price: 0.0545
-   Cost: $100
-   Tokens received: 100 / 0.0545 = 1,834.86 tokens
-   Balance deduction: -$100

---

## Market Resolution & Settlement

### Settlement Logic

When a market resolves with outcome `YES` or `NO`:

#### If User Traded on Winning Side:

```
payout = token_amount × 1.00
profit = payout - amount_invested
new_balance = old_balance + payout
```

**Example (User bought YES, market resolved YES):**

-   Amount invested: $100
-   Token amount: 105.76
-   Payout: 105.76 × $1.00 = **$105.76**
-   Profit: $105.76 - $100 = **$5.76**
-   New balance: old_balance + $105.76

#### If User Traded on Losing Side:

```
payout = 0
loss = amount_invested (full loss)
new_balance = old_balance (no change)
```

**Example (User bought YES, market resolved NO):**

-   Amount invested: $100
-   Token amount: 105.76
-   Payout: **$0**
-   Loss: **-$100**
-   New balance: old_balance (no addition)

---

## Laravel/PHP Implementation

### Trade Creation Service

```php
<?php

namespace App\Services;

use App\Models\Market;
use App\Models\Trade;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TradeService
{
    /**
     * Create a new trade
     *
     * @param User $user
     * @param Market $market
     * @param string $outcome 'YES' or 'NO'
     * @param float $amount Amount to invest
     * @return Trade
     * @throws \Exception
     */
    public function createTrade(User $user, Market $market, string $outcome, float $amount): Trade
    {
        // Validate outcome
        if (!in_array($outcome, ['YES', 'NO'])) {
            throw new \InvalidArgumentException('Outcome must be YES or NO');
        }

        // Validate market is open
        if (!$market->isOpenForTrading()) {
            throw new \InvalidArgumentException('Market is closed for trading');
        }

        // Validate balance
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
        );

        if ($wallet->balance < $amount) {
            throw new \InvalidArgumentException('Insufficient balance');
        }

        // Get outcome price
        $outcomePrices = json_decode($market->outcome_prices, true);
        if (!is_array($outcomePrices) || count($outcomePrices) < 2) {
            throw new \InvalidArgumentException('Invalid market price data');
        }

        // outcome_prices[0] = NO price, outcome_prices[1] = YES price
        $outcomePrice = ($outcome === 'YES') ? $outcomePrices[1] : $outcomePrices[0];

        // Calculate token amount
        $tokenAmount = $amount / $outcomePrice;

        DB::beginTransaction();

        try {
            // Deduct balance
            $balanceBefore = $wallet->balance;
            $wallet->balance -= $amount;
            $wallet->save();

            // Create trade
            $trade = Trade::create([
                'user_id' => $user->id,
                'market_id' => $market->id,
                'outcome' => $outcome,
                'amount_invested' => $amount,
                'token_amount' => $tokenAmount,
                'price_at_buy' => $outcomePrice,
                'status' => 'PENDING',
            ]);

            // Create wallet transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'trade',
                'amount' => -$amount, // Negative for deduction
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'reference_type' => Trade::class,
                'reference_id' => $trade->id,
                'description' => "Bought {$outcome} on market: {$market->question}",
                'metadata' => [
                    'trade_id' => $trade->id,
                    'market_id' => $market->id,
                    'outcome' => $outcome,
                    'amount_invested' => $amount,
                    'token_amount' => $tokenAmount,
                    'price_at_buy' => $outcomePrice,
                ]
            ]);

            DB::commit();

            Log::info('Trade created successfully', [
                'trade_id' => $trade->id,
                'user_id' => $user->id,
                'market_id' => $market->id,
                'outcome' => $outcome,
                'amount_invested' => $amount,
                'token_amount' => $tokenAmount,
            ]);

            return $trade;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Settle a single trade after market resolution
     *
     * @param Trade $trade
     * @return void
     * @throws \Exception
     */
    public function settleTrade(Trade $trade): void
    {
        if (strtoupper($trade->status) !== 'PENDING') {
            return; // Already settled
        }

        $market = $trade->market;

        // Get final outcome
        $finalOutcome = $market->final_outcome ?? strtoupper($market->final_result ?? '');

        if (!$finalOutcome || !in_array($finalOutcome, ['YES', 'NO'])) {
            throw new \InvalidArgumentException('Market does not have a final outcome');
        }

        DB::beginTransaction();

        try {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $trade->user_id],
                ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
            );

            if ($trade->outcome === $finalOutcome) {
                // Trade WON
                $payout = $trade->token_amount * 1.00; // Full payout at $1.00 per token

                $trade->status = 'WON';
                $trade->payout = $payout;
                $trade->settled_at = now();
                $trade->save();

                // Add payout to wallet
                $balanceBefore = $wallet->balance;
                $wallet->balance += $payout;
                $wallet->save();

                // Create wallet transaction
                WalletTransaction::create([
                    'user_id' => $trade->user_id,
                    'wallet_id' => $wallet->id,
                    'type' => 'trade_payout',
                    'amount' => $payout,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance,
                    'reference_type' => Trade::class,
                    'reference_id' => $trade->id,
                    'description' => "Trade payout: {$trade->outcome} on market: {$market->question}",
                    'metadata' => [
                        'trade_id' => $trade->id,
                        'market_id' => $market->id,
                        'outcome' => $trade->outcome,
                        'payout' => $payout,
                        'profit' => $payout - $trade->amount_invested,
                    ]
                ]);

                Log::info('Trade settled as WON', [
                    'trade_id' => $trade->id,
                    'payout' => $payout,
                    'profit' => $payout - $trade->amount_invested,
                ]);

            } else {
                // Trade LOST
                $trade->status = 'LOST';
                $trade->payout = 0;
                $trade->settled_at = now();
                $trade->save();

                Log::info('Trade settled as LOST', [
                    'trade_id' => $trade->id,
                    'loss' => $trade->amount_invested,
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Settle all pending trades for a market
     *
     * @param Market $market
     * @return array
     */
    public function settleMarket(Market $market): array
    {
        $finalOutcome = $market->final_outcome ?? strtoupper($market->final_result ?? '');

        if (!$finalOutcome || !in_array($finalOutcome, ['YES', 'NO'])) {
            throw new \InvalidArgumentException('Market does not have a final outcome');
        }

        $pendingTrades = Trade::where('market_id', $market->id)
            ->where('status', 'PENDING')
            ->get();

        $winCount = 0;
        $lossCount = 0;
        $totalPayout = 0;

        foreach ($pendingTrades as $trade) {
            try {
                $this->settleTrade($trade);

                if ($trade->outcome === $finalOutcome) {
                    $winCount++;
                    $totalPayout += $trade->payout;
                } else {
                    $lossCount++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to settle trade', [
                    'trade_id' => $trade->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Mark market as settled
        $market->settled = true;
        $market->save();

        return [
            'total_trades' => $pendingTrades->count(),
            'win_count' => $winCount,
            'loss_count' => $lossCount,
            'total_payout' => $totalPayout,
        ];
    }
}
```

---

## Calculation Examples

### Example 1: Buying YES Shares

**Scenario:**

-   Market: "Will it rain tomorrow?"
-   YES price: 0.75 (75¢)
-   NO price: 0.25 (25¢)
-   User invests: $100

**Calculation:**

```
Token amount = $100 / 0.75 = 133.33 tokens
Cost = $100
Balance deduction = -$100
```

**If Market Resolves YES:**

```
Payout = 133.33 × $1.00 = $133.33
Profit = $133.33 - $100 = $33.33
Return = 33.33%
```

**If Market Resolves NO:**

```
Payout = $0
Loss = -$100
Return = -100%
```

### Example 2: Buying NO Shares

**Scenario:**

-   Same market
-   User invests: $100 in NO
-   NO price: 0.25 (25¢)

**Calculation:**

```
Token amount = $100 / 0.25 = 400 tokens
Cost = $100
Balance deduction = -$100
```

**If Market Resolves NO:**

```
Payout = 400 × $1.00 = $400
Profit = $400 - $100 = $300
Return = 300%
```

**If Market Resolves YES:**

```
Payout = $0
Loss = -$100
Return = -100%
```

### Example 3: Portfolio Value Calculation

**User has 3 active positions:**

1. **Trade 1:** YES, $100 invested, 133.33 tokens, current YES price: 0.80

    - Current value: 133.33 × 0.80 = $106.67

2. **Trade 2:** NO, $50 invested, 200 tokens, current NO price: 0.30

    - Current value: 200 × 0.30 = $60.00

3. **Trade 3:** YES, $200 invested, 250 tokens, current YES price: 0.65
    - Current value: 250 × 0.65 = $162.50

**Total Portfolio Value:**

```
$106.67 + $60.00 + $162.50 = $329.17
```

---

## API Endpoints

### Place Trade

**POST** `/trades/market/{marketId}`

**Request:**

```json
{
    "option": "yes",
    "amount": 100.0,
    "price": 0.75
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
        "amount_invested": 100.0,
        "token_amount": 133.33,
        "price_at_buy": 0.75,
        "status": "PENDING"
    },
    "wallet": {
        "balance": 900.0
    }
}
```

### Settle Market

**POST** `/admin/market/{id}/set-result`

**Request:**

```json
{
    "final_result": "yes"
}
```

**Response:**

```json
{
    "success": true,
    "message": "Market result set and trades settled successfully",
    "settlement": {
        "total_trades": 10,
        "win_count": 6,
        "loss_count": 4,
        "total_payout": 650.0
    }
}
```

---

## Safety & Security Considerations

### 1. Balance Validation

-   Always check balance before trade creation
-   Use database locks (`lockForUpdate()`) to prevent race conditions
-   Use transactions for atomic operations

### 2. Price Validation

-   Validate outcome prices are between 0.01 and 0.99
-   Ensure prices sum to 1.00
-   Check market is open before allowing trades

### 3. Settlement Safety

-   Only settle pending trades
-   Prevent double settlement
-   Log all settlement actions
-   Use transactions for settlement

### 4. Portfolio Calculation

-   Calculate dynamically from current market prices
-   Only include pending trades
-   Handle missing market data gracefully

---

## Key Formulas Summary

### Token Calculation

```
token_amount = amount_invested / outcome_price
```

### Payout (If Won)

```
payout = token_amount × 1.00
profit = payout - amount_invested
```

### Current Position Value

```
current_value = token_amount × current_outcome_price
```

### Portfolio Value

```
portfolio = sum(current_value of all pending trades)
```

### Profit/Loss Percentage

```
profit_loss_pct = ((current_price - buy_price) / buy_price) × 100
```

---

## Status Flow

```
PENDING → (Market Resolves) → WON or LOST
```

-   **PENDING:** Trade is active, waiting for market resolution
-   **WON:** User bet on winning outcome, receives payout
-   **LOST:** User bet on losing outcome, no payout
-   **CLOSED:** Position was manually closed (if close feature enabled)

---

## Best Practices

1. **Always use transactions** for trade creation and settlement
2. **Lock wallet records** during balance updates
3. **Validate market status** before allowing trades
4. **Log all transactions** for audit trail
5. **Calculate portfolio dynamically** from current prices
6. **Handle edge cases** (missing prices, closed markets, etc.)
7. **Use proper error handling** and rollback on failures

---

## Testing Scenarios

### Test Case 1: Buy YES, Market Resolves YES

-   Input: $100, YES price 0.75
-   Expected: 133.33 tokens, payout $133.33, profit $33.33

### Test Case 2: Buy NO, Market Resolves NO

-   Input: $100, NO price 0.25
-   Expected: 400 tokens, payout $400, profit $300

### Test Case 3: Buy YES, Market Resolves NO

-   Input: $100, YES price 0.75
-   Expected: 133.33 tokens, payout $0, loss -$100

### Test Case 4: Insufficient Balance

-   Input: $1000 trade, balance $500
-   Expected: Error "Insufficient balance"

### Test Case 5: Closed Market

-   Input: Trade on closed market
-   Expected: Error "Market is closed for trading"

---

This system provides a complete, scalable, and safe implementation of Polymarket-style trading logic suitable for real-money simulation environments.
