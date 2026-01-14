# Outcomes System Implementation

## Overview
This document describes the new flexible outcomes system that replaces the hardcoded YES/NO enum with a dynamic, scalable outcomes table. This allows markets to have any outcomes (e.g., "Up", "Down", "Over 2.5", "Under 2.5", etc.).

## Database Schema

### Outcomes Table
- `id` - Primary key
- `market_id` - Foreign key to markets table
- `name` - Outcome name (e.g., "Yes", "No", "Up", "Down", "Over 2.5")
- `order_index` - Display order (0, 1, 2, ...)
- `total_traded_amount` - Total amount traded on this outcome (calculated from trades)
- `total_shares` - Total shares/tokens for this outcome
- `current_price` - Current price (0-1 range, calculated from traded amounts)
- `is_winning` - Boolean flag set when market is resolved
- `active` - Whether outcome is active
- `created_at`, `updated_at` - Timestamps

### Trades Table Updates
- Added `outcome_id` - Foreign key to outcomes table (new system)
- `outcome` enum - Made nullable for backward compatibility
- `outcome_name` - Stores actual outcome name for display

## Key Features

### 1. Dynamic Outcome Creation
- Outcomes are automatically synced from API when markets are imported
- Each market can have any number of outcomes
- Outcomes are created/updated via `Market::syncOutcomesFromApi()`

### 2. Price Calculation
- Prices are calculated dynamically based on total traded amounts
- Formula: `price = outcome_total / market_total`
- Prices auto-update when new trades occur
- Falls back to API prices if no trades yet

### 3. Trade Execution
- Trades reference outcomes via `outcome_id`
- TradeService validates outcome exists before creating trade
- Outcome's `total_traded_amount` and `total_shares` are incremented atomically
- Prices are recalculated after each trade

### 4. Market Resolution
- Admin can resolve market by selecting winning outcome
- Supports:
  - `outcome_id` (new system - recommended)
  - `outcome_name` (by name)
  - `final_result` (legacy yes/no support)
- Winning outcome is marked with `is_winning = true`
- All other outcomes are marked as losing

### 5. Settlement & Payouts
- SettlementService uses `outcome_id` to determine winning trades
- Winning trades get full payout: `shares * 1.00`
- Losing trades get $0 payout
- Payouts are credited to user's earning wallet

## API Endpoints

### Resolve Market (Admin)
```
POST /admin/markets/{id}/set-result
{
    "outcome_id": 123,           // New system (recommended)
    // OR
    "outcome_name": "Over 2.5", // By name
    // OR
    "final_result": "yes"        // Legacy support
}
```

## Code Changes

### Models
- **Outcome.php** - New model with price calculation logic
- **Market.php** - Added `outcomes()`, `activeOutcomes()`, `winningOutcome()` relationships
- **Market.php** - Added `syncOutcomesFromApi()`, `getOutcomeByName()`, `recalculateAllOutcomePrices()`
- **Trade.php** - Added `outcome_id` and `outcome()` relationship

### Services
- **TradeService.php** - Updated to use Outcome model
  - `getOutcomeByName()` - Gets or creates outcome
  - `getOutcomePrice()` - Uses calculated price from Outcome model
  - `createTrade()` - Uses `outcome_id` instead of outcome string
- **SettlementService.php** - Updated to use `outcome_id` for settlement
  - Determines winning outcome from Outcome model
  - Compares trade's `outcome_id` with winning outcome

### Controllers
- **MarketController.php** - `storeEvents()` syncs outcomes from API
- **MarketController.php** - `setResult()` supports outcome_id, outcome_name, or legacy final_result

### Migrations
- `2026_01_14_203311_create_outcomes_table.php` - Creates outcomes table
- `2026_01_14_203314_update_trades_table_for_outcome_id.php` - Adds outcome_id to trades

## Frontend Integration

### Trading Panel
- Dynamically displays outcomes from market's outcomes array
- Uses `data-outcome` attribute to pass outcome name to JavaScript
- Prices are calculated from Outcome model's `current_price`

### Event Cards
- Displays dynamic outcome names (not hardcoded Yes/No)
- Shows winning outcome name when market is resolved

## Migration Path

### Existing Data
- Old trades with `outcome` enum are still supported
- SettlementService falls back to legacy fields if `outcome_id` is null
- Outcomes are automatically created when markets are synced from API

### Backward Compatibility
- `outcome` enum in trades table is nullable
- Legacy `option`, `side` fields still work
- `final_result` endpoint still accepts yes/no for backward compatibility

## Usage Examples

### Creating a Trade
```php
$tradeService = app(TradeService::class);
$trade = $tradeService->createTrade($user, $market, "Over 2.5", 100.00);
```

### Resolving a Market
```php
// Via API
POST /admin/markets/123/set-result
{
    "outcome_id": 456  // Outcome ID for "Over 2.5"
}

// Or by name
{
    "outcome_name": "Over 2.5"
}
```

### Getting Outcome Price
```php
$outcome = $market->getOutcomeByName("Over 2.5");
$price = $outcome->current_price; // 0.65 (65%)
```

## Benefits

1. **Flexibility** - Supports any outcome names, not just Yes/No
2. **Scalability** - Can add more outcomes per market in the future
3. **Accuracy** - Prices calculated from actual trade data
4. **Maintainability** - Clean separation of concerns
5. **Backward Compatible** - Existing code continues to work

## Future Enhancements

- Support for multi-outcome markets (more than 2 outcomes)
- Outcome-specific volume/liquidity tracking
- Outcome-specific charts
- Outcome-specific comments/discussions

