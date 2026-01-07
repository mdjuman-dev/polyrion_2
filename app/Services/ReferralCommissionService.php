<?php

namespace App\Services;

use App\Models\User;
use App\Models\Trade;
use App\Models\ReferralLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Referral Commission Service
 * 
 * Provides methods to query and manage referral commission history
 */
class ReferralCommissionService
{
    /**
     * Get commission history for a referrer
     * 
     * @param User $referrer
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getReferrerCommissionHistory(User $referrer, int $limit = 50)
    {
        return ReferralLog::forReferrer($referrer->id)
            ->with(['fromUser', 'trade'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get commission history for a specific trade
     * 
     * @param Trade $trade
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTradeCommissionHistory(Trade $trade)
    {
        return ReferralLog::forTrade($trade->id)
            ->with(['user', 'fromUser'])
            ->orderBy('level')
            ->get();
    }

    /**
     * Get commission history for a referred user's trades
     * 
     * @param User $referredUser
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getReferredUserCommissionHistory(User $referredUser, int $limit = 50)
    {
        return ReferralLog::forReferredUser($referredUser->id)
            ->with(['user', 'trade'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get total commission earned by a referrer
     * 
     * @param User $referrer
     * @return float
     */
    public function getTotalCommissionEarned(User $referrer): float
    {
        return (float) ReferralLog::forReferrer($referrer->id)
            ->sum('amount');
    }

    /**
     * Get commission statistics for a referrer
     * 
     * @param User $referrer
     * @return array
     */
    public function getReferrerStats(User $referrer): array
    {
        $stats = ReferralLog::forReferrer($referrer->id)
            ->selectRaw('
                COUNT(*) as total_commissions,
                SUM(amount) as total_earned,
                COUNT(DISTINCT from_user_id) as total_referred_users,
                COUNT(DISTINCT trade_id) as total_trades,
                AVG(amount) as avg_commission
            ')
            ->first();

        return [
            'total_commissions' => (int) ($stats->total_commissions ?? 0),
            'total_earned' => (float) ($stats->total_earned ?? 0),
            'total_referred_users' => (int) ($stats->total_referred_users ?? 0),
            'total_trades' => (int) ($stats->total_trades ?? 0),
            'avg_commission' => (float) ($stats->avg_commission ?? 0),
        ];
    }

    /**
     * Check if commission has been processed for a trade
     * 
     * @param Trade $trade
     * @return bool
     */
    public function isCommissionProcessed(Trade $trade): bool
    {
        return ReferralLog::forTrade($trade->id)->exists();
    }
}

