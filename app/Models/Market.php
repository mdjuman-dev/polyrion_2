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
     */
    public function isOpenForTrading(): bool
    {
        if (!$this->active || $this->closed || $this->archived) {
            return false;
        }

        if ($this->close_time && now() >= $this->close_time) {
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
