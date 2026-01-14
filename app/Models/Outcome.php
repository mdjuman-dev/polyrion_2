<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Outcome extends Model
{
    protected $fillable = [
        'market_id',
        'name',
        'order_index',
        'total_traded_amount',
        'total_shares',
        'current_price',
        'is_winning',
        'active',
    ];

    protected $casts = [
        'total_traded_amount' => 'decimal:8',
        'total_shares' => 'decimal:8',
        'current_price' => 'decimal:6',
        'is_winning' => 'boolean',
        'active' => 'boolean',
        'order_index' => 'integer',
    ];

    /**
     * Get the market this outcome belongs to
     */
    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    /**
     * Get all trades for this outcome
     */
    public function trades()
    {
        return $this->hasMany(Trade::class);
    }

    /**
     * Get pending trades for this outcome
     */
    public function pendingTrades()
    {
        return $this->hasMany(Trade::class)->where('status', 'PENDING');
    }

    /**
     * Get winning trades for this outcome
     */
    public function winningTrades()
    {
        return $this->hasMany(Trade::class)->where('status', 'WON');
    }

    /**
     * Calculate current price based on total traded amounts
     * Polymarket-style: price = outcome_total / market_total
     */
    public function calculatePrice(): float
    {
        $market = $this->market;
        if (!$market) {
            return 0.5; // Default 50%
        }

        // Get all active outcomes for this market
        $allOutcomes = $market->outcomes()->where('active', true)->get();
        
        if ($allOutcomes->isEmpty()) {
            return 0.5;
        }

        // Calculate total traded amount across all outcomes
        $totalMarketAmount = $allOutcomes->sum('total_traded_amount');

        if ($totalMarketAmount <= 0) {
            // If no trades yet, return equal distribution
            return 1.0 / $allOutcomes->count();
        }

        // Price = this outcome's total / market total
        $price = $this->total_traded_amount / $totalMarketAmount;

        // Ensure price is between 0.001 and 0.999
        return max(0.001, min(0.999, $price));
    }

    /**
     * Recalculate price and update
     */
    public function recalculatePrice(): bool
    {
        $newPrice = $this->calculatePrice();
        
        if (abs($this->current_price - $newPrice) > 0.0001) {
            $this->current_price = $newPrice;
            return $this->save();
        }

        return true;
    }

    /**
     * Increment traded amount and shares (atomic operation)
     */
    public function incrementTradeData(float $amount, float $shares): bool
    {
        try {
            DB::beginTransaction();

            // Use lock to prevent race conditions
            $outcome = self::lockForUpdate()->find($this->id);
            
            if (!$outcome) {
                DB::rollBack();
                return false;
            }

            // Increment amounts
            $outcome->increment('total_traded_amount', $amount);
            $outcome->increment('total_shares', $shares);

            // Recalculate price
            $outcome->recalculatePrice();

            // Also recalculate prices for all other outcomes in the market
            $market = $outcome->market;
            if ($market) {
                $market->outcomes()
                    ->where('id', '!=', $outcome->id)
                    ->where('active', true)
                    ->get()
                    ->each(function ($otherOutcome) {
                        $otherOutcome->recalculatePrice();
                    });
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to increment outcome trade data', [
                'outcome_id' => $this->id,
                'amount' => $amount,
                'shares' => $shares,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Get total number of trades for this outcome
     */
    public function getTradeCountAttribute(): int
    {
        return $this->trades()->count();
    }

    /**
     * Get pending trade count
     */
    public function getPendingTradeCountAttribute(): int
    {
        return $this->pendingTrades()->count();
    }

    /**
     * Scope: Get active outcomes
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope: Get outcomes ordered by order_index
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order_index', 'asc');
    }

    /**
     * Scope: Get winning outcome
     */
    public function scopeWinning($query)
    {
        return $query->where('is_winning', true);
    }
}
