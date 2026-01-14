<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Event;
use Illuminate\Database\Eloquent\Model;

class Market extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'liquidity' => 'float',
        'liquidityClob' => 'float',
        'volume' => 'float',
        'volume24hr' => 'float',
        'volume1wk' => 'float',
        'volume1mo' => 'float',
        'volume1yr' => 'float',
        'api_volume' => 'float',
        'api_liquidity' => 'float',
        'api_volume24hr' => 'float',
        'internal_volume' => 'float',
        'internal_liquidity' => 'float',
        'internal_volume24hr' => 'float',
        'outcome_prices' => 'array',
        'outcomes' => 'array',
        'best_bid' => 'float',
        'best_ask' => 'float',
        'last_trade_price' => 'float',
        'spread' => 'float',
        'one_day_price_change' => 'float',
        'one_week_price_change' => 'float',
        'one_month_price_change' => 'float',
        'competitive' => 'float',
        'active' => 'boolean',
        'closed' => 'boolean',
        'is_closed' => 'boolean',
        'settled' => 'boolean',
        'archived' => 'boolean',
        'new' => 'boolean',
        'restricted' => 'boolean',
        'startDate' => 'datetime',
        'endDate' => 'datetime',
        'close_time' => 'datetime',
        'result_set_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * Get all outcomes for this market
     */
    public function outcomes()
    {
        return $this->hasMany(Outcome::class)->orderBy('order_index', 'asc');
    }

    /**
     * Get active outcomes for this market
     */
    public function activeOutcomes()
    {
        return $this->hasMany(Outcome::class)->where('active', true)->orderBy('order_index', 'asc');
    }

    /**
     * Get winning outcome (if market is resolved)
     */
    public function winningOutcome()
    {
        return $this->hasOne(Outcome::class)->where('is_winning', true);
    }

    /**
     * Get all trades for this market
     */
    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * Get pending trades for this market
     */
    public function pendingTrades()
    {
        return $this->hasMany(Trade::class)->where('status', 'pending');
    }

    /**
     * Check if market is open for trading
     * Market is NOT open if:
     * - Not active
     * - Closed flag is set
     * - Archived
     * - Close time has passed
     * - Has final result (resolving/settled state) - prevents trading after result is determined
     * - Has winning outcome (new system) - prevents trading after resolution
     */
    public function isOpenForTrading(): bool
    {
        if (!$this->active || $this->closed || $this->archived) {
            return false;
        }

        if ($this->close_time && now() >= $this->close_time) {
            return false;
        }

        // Prevent trading if market has result (resolving or settled state)
        // This ensures users can't trade after result is determined but before settlement
        if ($this->hasResult() || $this->settled) {
            return false;
        }

        // CRITICAL: Prevent trading if market has winning outcome (new system)
        // This locks market immediately when resolved
        $winningOutcome = $this->winningOutcome()->first();
        if ($winningOutcome) {
            return false;
        }

        return true;
    }

    /**
     * Check if market is closed
     */
    public function isClosed(): bool
    {
        if ($this->close_time && now() >= $this->close_time) {
            return true;
        }

        return $this->closed;
    }

    /**
     * Check if market has final result
     */
    public function hasResult(): bool
    {
        return !is_null($this->final_result) || !is_null($this->final_outcome);
    }

    /**
     * Get final outcome (prefers final_outcome, falls back to final_result, then outcome_result)
     */
    public function getFinalOutcome()
    {
        if ($this->attributes['final_outcome'] ?? null) {
            return strtoupper($this->attributes['final_outcome']);
        }

        // Fallback to final_result if final_outcome is null
        if ($this->final_result) {
            return strtoupper($this->final_result);
        }

        // Fallback to outcome_result
        if ($this->outcome_result) {
            return strtoupper($this->outcome_result);
        }

        // Last resort: Try to determine from lastTradePrice and outcomePrices (Polymarket method)
        if ($this->last_trade_price !== null && $this->outcome_prices && $this->outcomes) {
            $outcome = $this->determineOutcomeFromLastTradePrice();
            if ($outcome) {
                return $outcome;
            }
        }

        return null;
    }

    /**
     * Determine outcome from lastTradePrice using Polymarket logic
     * If lastTradePrice matches a value in outcomePrices, the outcome at that index wins
     */
    public function determineOutcomeFromLastTradePrice()
    {
        if ($this->last_trade_price === null || !$this->outcome_prices || !$this->outcomes) {
            return null;
        }

        $outcomePrices = is_string($this->outcome_prices) ? json_decode($this->outcome_prices, true) : $this->outcome_prices;
        $outcomes = is_string($this->outcomes) ? json_decode($this->outcomes, true) : $this->outcomes;

        if (!is_array($outcomePrices) || !is_array($outcomes) || count($outcomePrices) === 0 || count($outcomes) === 0) {
            return null;
        }

        $lastTradePrice = floatval($this->last_trade_price);

        // Find which index in outcomePrices matches lastTradePrice
        $winningIndex = null;
        foreach ($outcomePrices as $index => $price) {
            if (abs(floatval($price) - $lastTradePrice) < 0.0001) {
                $winningIndex = $index;
                break;
            }
        }

        if ($winningIndex !== null && isset($outcomes[$winningIndex])) {
            $winningOutcome = $outcomes[$winningIndex];
            $winningOutcomeUpper = strtoupper(trim($winningOutcome));
            
            if ($winningOutcomeUpper === 'YES' || $winningOutcomeUpper === 'NO') {
                return $winningOutcomeUpper;
            } else {
                // For binary markets, usually first is YES, second is NO
                if ($winningIndex === 0 && count($outcomes) === 2) {
                    return 'YES';
                } elseif ($winningIndex === 1 && count($outcomes) === 2) {
                    return 'NO';
                }
            }
        }

        return null;
    }

    /**
     * Check if market is ready for settlement
     */
    public function isReadyForSettlement(): bool
    {
        return $this->is_closed 
            && !$this->settled 
            && !empty($this->outcome_result);
    }

    /**
     * Settle this market
     */
    public function settle(): bool
    {
        $settlementService = app(\App\Services\SettlementService::class);
        return $settlementService->settleMarket($this->id);
    }

    /**
     * Get total volume (API + Internal trades)
     * This is the value that should be displayed to users
     */
    public function getTotalVolumeAttribute(): float
    {
        $apiVolume = (float) ($this->api_volume ?? $this->volume ?? 0);
        $internalVolume = (float) ($this->internal_volume ?? 0);
        return $apiVolume + $internalVolume;
    }

    /**
     * Get total liquidity (API + Internal trades)
     * This is the value that should be displayed to users
     */
    public function getTotalLiquidityAttribute(): float
    {
        $apiLiquidity = (float) ($this->api_liquidity ?? $this->liquidity ?? 0);
        $internalLiquidity = (float) ($this->internal_liquidity ?? 0);
        return $apiLiquidity + $internalLiquidity;
    }

    /**
     * Get total 24hr volume (API + Internal trades)
     */
    public function getTotalVolume24hrAttribute(): float
    {
        $apiVolume24hr = (float) ($this->api_volume24hr ?? $this->volume24hr ?? 0);
        $internalVolume24hr = (float) ($this->internal_volume24hr ?? 0);
        return $apiVolume24hr + $internalVolume24hr;
    }

    /**
     * Safely increment internal volume and liquidity from a trade
     * Uses database lock to prevent race conditions
     * 
     * @param float $volume Volume to add (trade amount)
     * @param float $liquidity Liquidity to add (typically same as volume for simplicity)
     * @return bool Success status
     */
    public function incrementInternalTradeData(float $volume, float $liquidity = null): bool
    {
        if ($liquidity === null) {
            $liquidity = $volume; // Default: liquidity equals volume
        }

        try {
            // Use pessimistic locking to prevent concurrent update issues
            $market = self::lockForUpdate()->find($this->id);
            
            if (!$market) {
                \Log::error('Market not found for internal trade data increment', [
                    'market_id' => $this->id
                ]);
                return false;
            }

            // Increment internal values atomically
            $market->increment('internal_volume', $volume);
            $market->increment('internal_liquidity', $liquidity);
            
            // Also increment 24hr volume if trade is within last 24 hours
            // This is optional - you may want to track this separately
            $market->increment('internal_volume24hr', $volume);

            \Log::info('Internal trade data incremented', [
                'market_id' => $this->id,
                'volume_added' => $volume,
                'liquidity_added' => $liquidity,
                'new_internal_volume' => $market->fresh()->internal_volume,
                'new_internal_liquidity' => $market->fresh()->internal_liquidity,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to increment internal trade data', [
                'market_id' => $this->id,
                'volume' => $volume,
                'liquidity' => $liquidity,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Update API data while preserving internal trade data
     * This should be called during API sync operations
     * 
     * @param array $apiData API data containing volume, liquidity, etc.
     * @return bool Success status
     */
    public function updateApiDataPreservingInternal(array $apiData): bool
    {
        try {
            // Use pessimistic locking
            $market = self::lockForUpdate()->find($this->id);
            
            if (!$market) {
                \Log::error('Market not found for API data update', [
                    'market_id' => $this->id
                ]);
                return false;
            }

            // Store current internal values (they will be preserved)
            $currentInternalVolume = (float) ($market->internal_volume ?? 0);
            $currentInternalLiquidity = (float) ($market->internal_liquidity ?? 0);
            $currentInternalVolume24hr = (float) ($market->internal_volume24hr ?? 0);

            // Update API values only (these come from external API)
            $updateData = [];
            
            if (isset($apiData['volume'])) {
                $updateData['api_volume'] = (float) $apiData['volume'];
            }
            if (isset($apiData['liquidity'])) {
                $updateData['api_liquidity'] = (float) $apiData['liquidity'];
            }
            if (isset($apiData['volume24hr'])) {
                $updateData['api_volume24hr'] = (float) $apiData['volume24hr'];
            }

            // Update API fields only (internal fields remain unchanged)
            if (!empty($updateData)) {
                $market->update($updateData);
            }

            // Update legacy volume/liquidity fields for backward compatibility
            // These will be the total (API + Internal) for display
            $totalVolume = (float) ($updateData['api_volume'] ?? $market->api_volume ?? 0) + $currentInternalVolume;
            $totalLiquidity = (float) ($updateData['api_liquidity'] ?? $market->api_liquidity ?? 0) + $currentInternalLiquidity;
            $totalVolume24hr = (float) ($updateData['api_volume24hr'] ?? $market->api_volume24hr ?? 0) + $currentInternalVolume24hr;

            $market->update([
                'volume' => $totalVolume,
                'liquidity' => $totalLiquidity,
                'volume24hr' => $totalVolume24hr,
            ]);

            \Log::info('API data updated while preserving internal data', [
                'market_id' => $this->id,
                'api_volume' => $updateData['api_volume'] ?? null,
                'api_liquidity' => $updateData['api_liquidity'] ?? null,
                'internal_volume_preserved' => $currentInternalVolume,
                'internal_liquidity_preserved' => $currentInternalLiquidity,
                'total_volume' => $totalVolume,
                'total_liquidity' => $totalLiquidity,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to update API data while preserving internal', [
                'market_id' => $this->id,
                'api_data' => $apiData,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return false;
        }
    }

    /**
     * Sync outcomes from API data (outcomes array)
     * Creates or updates outcomes based on API response
     * 
     * @param array|null $outcomesArray Outcomes array from API (e.g., ["Yes", "No"] or ["Up", "Down"])
     * @return array Array of created/updated Outcome models
     */
    public function syncOutcomesFromApi(?array $outcomesArray): array
    {
        if (empty($outcomesArray) || !is_array($outcomesArray)) {
            // Default to Yes/No if no outcomes provided
            $outcomesArray = ['Yes', 'No'];
        }

        $syncedOutcomes = [];
        
        foreach ($outcomesArray as $index => $outcomeName) {
            if (empty($outcomeName)) {
                continue;
            }

            // Find or create outcome
            $outcome = Outcome::updateOrCreate(
                [
                    'market_id' => $this->id,
                    'name' => $outcomeName,
                ],
                [
                    'order_index' => $index,
                    'active' => true,
                ]
            );

            $syncedOutcomes[] = $outcome;
        }

        // Deactivate outcomes that are no longer in the API response
        $activeOutcomeNames = array_map('strval', $outcomesArray);
        Outcome::where('market_id', $this->id)
            ->whereNotIn('name', $activeOutcomeNames)
            ->update(['active' => false]);

        return $syncedOutcomes;
    }

    /**
     * Get outcome by name (case-insensitive)
     */
    public function getOutcomeByName(string $name): ?Outcome
    {
        return $this->outcomes()
            ->whereRaw('LOWER(name) = ?', [strtolower($name)])
            ->where('active', true)
            ->first();
    }

    /**
     * Recalculate prices for all outcomes in this market
     */
    public function recalculateAllOutcomePrices(): bool
    {
        try {
            $outcomes = $this->activeOutcomes()->get();
            
            foreach ($outcomes as $outcome) {
                $outcome->recalculatePrice();
            }

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to recalculate outcome prices', [
                'market_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
