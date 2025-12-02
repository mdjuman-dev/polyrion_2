<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $fillable = [
        'user_id',
        'market_id',
        'option',
        'amount',
        'price',
        'status',
        'payout',
        'payout_amount',
        'settled_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'price' => 'decimal:4',
        'payout' => 'decimal:2',
        'payout_amount' => 'decimal:2',
        'settled_at' => 'datetime',
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
        return $this->status === 'pending';
    }

    /**
     * Check if trade won
     */
    public function isWin(): bool
    {
        return $this->status === 'win';
    }

    /**
     * Check if trade lost
     */
    public function isLoss(): bool
    {
        return $this->status === 'loss';
    }
}
