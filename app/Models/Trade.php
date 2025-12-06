<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = [
        'user_id',
        'market_id',
        'outcome', // YES or NO
        'amount_invested', // Amount user invested
        'token_amount', // Calculated tokens: amount / price
        'price_at_buy', // Price when trade was placed
        'status', // PENDING, WON, LOST
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
}
