<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = [
        'user_id',
        'market_id',
        'outcome_id', // Foreign key to outcomes table (new system)
        'side', // yes or no (legacy field)
        'outcome', // YES or NO (legacy enum, nullable for backward compatibility)
        'outcome_name', // Actual outcome name (e.g., "Over 2.5", "Under 2.5", "Up", "Down")
        'amount_invested', // Amount user invested
        'token_amount', // Calculated tokens: amount / price
        'price_at_buy', // Price when trade was placed
        'shares', // amount / price (new field)
        'status', // pending, win, loss (lowercase) or PENDING, WON, LOST (uppercase)
        'payout', // Payout amount (token_amount * 1.00 if WON)
        'settled_at',
        // Legacy fields for backward compatibility
        'option',
        'amount',
        'price',
        'payout_amount',
    ];

    protected $casts = [
        'amount_invested' => 'decimal:2',
        'token_amount' => 'decimal:8',
        'price_at_buy' => 'decimal:6',
        'shares' => 'decimal:8',
        'payout' => 'decimal:2',
        'settled_at' => 'datetime',
        // Legacy casts
        'amount' => 'decimal:2',
        'price' => 'decimal:4',
        'payout_amount' => 'decimal:2',
    ];

    /**
     * Get the user that made this trade
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the market this trade is for
     */
    public function market()
    {
        return $this->belongsTo(Market::class);
    }

    /**
     * Get the outcome this trade is for (new system)
     */
    public function outcome()
    {
        return $this->belongsTo(Outcome::class);
    }

    /**
     * Check if trade is pending
     */
    public function isPending(): bool
    {
        return strtoupper($this->status) === 'PENDING';
    }

    /**
     * Check if trade won
     */
    public function isWin(): bool
    {
        return strtoupper($this->status) === 'WON';
    }

    /**
     * Check if trade lost
     */
    public function isLoss(): bool
    {
        return strtoupper($this->status) === 'LOST';
    }

    /**
     * Get the effective outcome (YES or NO)
     * Uses outcome field if available, otherwise derives from option
     */
    public function getEffectiveOutcome(): string
    {
        if ($this->outcome) {
            return strtoupper($this->outcome);
        }
        
        if ($this->option) {
            return strtoupper($this->option);
        }
        
        if ($this->side) {
            return strtoupper($this->side);
        }
        
        return 'YES'; // Default
    }

    /**
     * Get the display outcome name for frontend
     * Returns actual outcome name (e.g., "Over 2.5", "Under 2.5", "Up", "Down")
     * Falls back to YES/NO if outcome_name not available
     */
    public function getDisplayOutcomeName(): string
    {
        // If outcome_name exists, use it (actual outcome name from market)
        if ($this->outcome_name) {
            return $this->outcome_name;
        }

        // Fallback: Try to get from market outcomes if available
        if ($this->market) {
            $outcomes = $this->market->outcomes;
            if (is_string($outcomes)) {
                $outcomes = json_decode($outcomes, true);
            }
            if (is_array($outcomes) && !empty($outcomes)) {
                $effectiveOutcome = $this->getEffectiveOutcome();
                // Map YES to first outcome (index 1), NO to second outcome (index 0)
                if ($effectiveOutcome === 'YES' && isset($outcomes[1])) {
                    return $outcomes[1];
                } elseif ($effectiveOutcome === 'NO' && isset($outcomes[0])) {
                    return $outcomes[0];
                }
            }
        }

        // Final fallback: return YES/NO
        return $this->getEffectiveOutcome();
    }

    /**
     * Get the effective amount invested
     * Uses amount_invested if available, otherwise uses amount
     */
    public function getEffectiveAmount(): float
    {
        return floatval($this->amount_invested ?? $this->amount ?? 0);
    }

    /**
     * Get the effective price at buy
     * Uses price_at_buy if available, otherwise uses price
     */
    public function getEffectivePrice(): float
    {
        return floatval($this->price_at_buy ?? $this->price ?? 0);
    }

    /**
     * Get the effective shares/token amount
     * Uses token_amount or shares if available, otherwise calculates from amount/price
     */
    public function getEffectiveShares(): float
    {
        if ($this->token_amount) {
            return floatval($this->token_amount);
        }
        
        if ($this->shares) {
            return floatval($this->shares);
        }
        
        $amount = $this->getEffectiveAmount();
        $price = $this->getEffectivePrice();
        
        if ($price > 0) {
            return $amount / $price;
        }
        
        return 0;
    }

    /**
     * Get the effective payout
     * Uses payout if available, otherwise uses payout_amount
     */
    public function getEffectivePayout(): float
    {
        return floatval($this->payout ?? $this->payout_amount ?? 0);
    }

    /**
     * Calculate potential profit/loss
     */
    public function getProfitLoss(): float
    {
        if ($this->isPending()) {
            return 0; // No profit/loss for pending trades
        }
        
        $payout = $this->getEffectivePayout();
        $amount = $this->getEffectiveAmount();
        
        return $payout - $amount;
    }

    /**
     * Get profit/loss percentage
     */
    public function getProfitLossPercentage(): float
    {
        $amount = $this->getEffectiveAmount();
        if ($amount <= 0) {
            return 0;
        }
        
        $profitLoss = $this->getProfitLoss();
        return ($profitLoss / $amount) * 100;
    }

    /**
     * Scope: Get trades for a specific market
     */
    public function scopeForMarket($query, $marketId)
    {
        return $query->where('market_id', $marketId);
    }

    /**
     * Scope: Get trades for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Get pending trades
     */
    public function scopePending($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'pending')
              ->orWhere('status', 'PENDING');
        });
    }

    /**
     * Scope: Get settled trades
     */
    public function scopeSettled($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'win')
              ->orWhere('status', 'loss')
              ->orWhere('status', 'WON')
              ->orWhere('status', 'LOST');
        });
    }

    /**
     * Scope: Get winning trades
     */
    public function scopeWinning($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'win')
              ->orWhere('status', 'WON');
        });
    }

    /**
     * Scope: Get losing trades
     */
    public function scopeLosing($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'loss')
              ->orWhere('status', 'LOST');
        });
    }
}
