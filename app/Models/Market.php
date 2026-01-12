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
        'outcomePrices' => 'array',
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
}
