# Internal Trade Tracking System

## Overview
This system ensures that user-generated trade volume and liquidity are always preserved, even when external API data is synced. Internal trade data is never overwritten or lost during API updates.

## Architecture

### Database Schema
The system uses separate fields to track API data and internal trade data:

**API Fields (from external API):**
- `api_volume` - Base volume from external API
- `api_liquidity` - Base liquidity from external API
- `api_volume24hr` - 24-hour volume from external API

**Internal Fields (from user trades):**
- `internal_volume` - Volume contributed by user trades
- `internal_liquidity` - Liquidity contributed by user trades
- `internal_volume24hr` - 24-hour volume from user trades

**Legacy Fields (for backward compatibility):**
- `volume` - Total volume (API + Internal) - automatically calculated
- `liquidity` - Total liquidity (API + Internal) - automatically calculated
- `volume24hr` - Total 24hr volume (API + Internal) - automatically calculated

### Key Features

1. **Data Preservation**: Internal trade data is never overwritten during API sync
2. **Atomic Operations**: Uses database locks (`lockForUpdate()`) to prevent race conditions
3. **Automatic Calculation**: Total values are automatically calculated (API + Internal)
4. **Backward Compatibility**: Legacy fields are maintained for existing code

## Usage

### When a Trade is Placed

```php
// In TradeService::createTrade()
// After trade is created, increment internal data
$market->incrementInternalTradeData($amount, $amount);
```

This method:
- Uses database lock to prevent concurrent update issues
- Atomically increments `internal_volume` and `internal_liquidity`
- Also increments `internal_volume24hr` for 24-hour tracking
- Logs all operations for audit trail

### During API Sync

```php
// In MarketController::storeEvents()
// Update API data while preserving internal data
$apiData = [
    'volume' => $mk['volume'] ?? $mk['volumeNum'] ?? null,
    'liquidity' => $mk['liquidity'] ?? $mk['liquidityNum'] ?? null,
    'volume24hr' => $mk['volume24hr'] ?? $mk['volume24hrClob'] ?? null,
];
$market->updateApiDataPreservingInternal($apiData);
```

This method:
- Stores API values in `api_volume`, `api_liquidity`, `api_volume24hr`
- Preserves existing `internal_volume`, `internal_liquidity`, `internal_volume24hr`
- Calculates and updates total values in legacy fields
- Uses database lock to ensure consistency

### Accessing Total Values

```php
// Method 1: Use accessors (recommended)
$totalVolume = $market->total_volume;      // API + Internal
$totalLiquidity = $market->total_liquidity; // API + Internal
$totalVolume24hr = $market->total_volume24hr; // API + Internal

// Method 2: Use legacy fields (backward compatible)
$totalVolume = $market->volume;      // Already calculated
$totalLiquidity = $market->liquidity; // Already calculated
```

## Data Flow

### Trade Creation Flow
1. User places trade → `TradeService::createTrade()`
2. Trade is created in database
3. `$market->incrementInternalTradeData($amount, $amount)` is called
4. Internal volume/liquidity are atomically incremented
5. Total values are automatically updated

### API Sync Flow
1. External API data arrives → `MarketController::storeEvents()`
2. Market is created/updated (without volume/liquidity)
3. `$market->updateApiDataPreservingInternal($apiData)` is called
4. API values stored in `api_*` fields
5. Internal values remain unchanged
6. Total values recalculated and stored in legacy fields

## Safety Features

### 1. Database Locks
All critical operations use `lockForUpdate()` to prevent race conditions:
```php
$market = self::lockForUpdate()->find($this->id);
```

### 2. Atomic Increments
Uses Laravel's `increment()` method for atomic database operations:
```php
$market->increment('internal_volume', $volume);
$market->increment('internal_liquidity', $liquidity);
```

### 3. Transaction Safety
Trade creation happens within a database transaction:
```php
DB::beginTransaction();
try {
    // Create trade
    // Increment internal data
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

### 4. Error Handling
All operations include comprehensive error logging:
- Failed increments are logged with full context
- API sync failures preserve existing data
- All errors include stack traces for debugging

## Migration

Run the migration to add the new fields:
```bash
php artisan migrate
```

This will add:
- `api_volume`, `api_liquidity`, `api_volume24hr`
- `internal_volume`, `internal_liquidity`, `internal_volume24hr`
- Indexes on `internal_volume` and `internal_liquidity` for performance

## Example Scenario

**Initial State:**
- API Volume: $500
- Internal Volume: $0
- Total Volume: $500

**User places $100 trade:**
- API Volume: $500 (unchanged)
- Internal Volume: $100 (incremented)
- Total Volume: $600 (automatically calculated)

**API sync updates (API now shows $450):**
- API Volume: $450 (updated from API)
- Internal Volume: $100 (preserved!)
- Total Volume: $550 (automatically recalculated)

**Result:** Internal trade data ($100) is preserved even though API volume decreased.

## Testing

To test the system:

1. **Test Trade Increment:**
   ```php
   $market = Market::find($id);
   $initialVolume = $market->internal_volume;
   $market->incrementInternalTradeData(100, 100);
   $market->refresh();
   assert($market->internal_volume == $initialVolume + 100);
   ```

2. **Test API Sync Preservation:**
   ```php
   $market = Market::find($id);
   $initialInternal = $market->internal_volume;
   $market->updateApiDataPreservingInternal(['volume' => 1000]);
   $market->refresh();
   assert($market->internal_volume == $initialInternal); // Preserved!
   assert($market->api_volume == 1000); // Updated
   assert($market->volume == 1000 + $initialInternal); // Total calculated
   ```

## Notes

- Internal volume/liquidity are **always additive** - they only increase
- API sync can update API values up or down, but internal values remain
- Total values are always: `API + Internal`
- The system handles concurrent trades safely using database locks
- All operations are logged for audit and debugging

