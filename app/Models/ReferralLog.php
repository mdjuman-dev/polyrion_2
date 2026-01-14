<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralLog extends Model
{
    protected $fillable = [
        'user_id',
        'from_user_id',
        'source_user_id', // Keep for backward compatibility
        'trade_id',
        'amount',
        'trade_amount',
        'level',
        'percentage_applied',
        'status',
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'trade_amount' => 'decimal:8',
        'percentage_applied' => 'decimal:2',
        'level' => 'integer',
        'trade_id' => 'integer',
    ];

    /**
     * Get the user who received the commission
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who made the deposit/trade (source/from)
     */
    public function sourceUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the user who made the trade (alias for sourceUser)
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Get the trade that generated this commission
     */
    public function trade(): BelongsTo
    {
        return $this->belongsTo(Trade::class);
    }

    /**
     * Scope to get commission history for a referrer
     */
    public function scopeForReferrer($query, $referrerId)
    {
        return $query->where('user_id', $referrerId);
    }

    /**
     * Scope to get commission history for a trade
     */
    public function scopeForTrade($query, $tradeId)
    {
        return $query->where('trade_id', $tradeId);
    }

    /**
     * Scope to get commission history for a referred user
     */
    public function scopeForReferredUser($query, $referredUserId)
    {
        return $query->where('from_user_id', $referredUserId);
    }
}
