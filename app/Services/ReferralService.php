<?php

namespace App\Services;

use App\Models\User;
use App\Models\ReferralLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    /**
     * Cache key for referral settings
     */
    private const CACHE_KEY = 'referral_settings';
    
    /**
     * Cache duration in seconds (1 hour)
     */
    private const CACHE_DURATION = 3600;

    /**
     * Distribute commission to referral chain (3 levels) when a user makes a deposit
     * 
     * @param User $depositor The user who made the deposit
     * @param float $depositAmount The amount of the deposit
     * @return array Returns array with summary of commissions distributed
     * @throws \Exception If transaction fails
     */
    public function distributeCommission(User $depositor, float $depositAmount): array
    {
        // Validate deposit amount
        if ($depositAmount <= 0) {
            Log::warning('Invalid deposit amount for referral commission', [
                'user_id' => $depositor->id,
                'amount' => $depositAmount,
            ]);
            return [
                'success' => false,
                'message' => 'Invalid deposit amount',
                'commissions_distributed' => 0,
                'total_commission' => 0,
            ];
        }

        // Fetch active referral settings (cached)
        $settings = $this->getReferralSettings();

        if (empty($settings)) {
            Log::warning('No active referral settings found', [
                'user_id' => $depositor->id,
            ]);
            return [
                'success' => false,
                'message' => 'No active referral settings',
                'commissions_distributed' => 0,
                'total_commission' => 0,
            ];
        }

        $commissionsDistributed = 0;
        $totalCommission = 0;
        $commissionDetails = [];

        // Use database transaction to ensure atomicity
        DB::beginTransaction();

        try {
            $currentUser = $depositor;
            $level = 1;

            // Traverse up the referral chain (max 3 levels)
            while ($level <= 3 && $currentUser !== null) {
                // Get the referrer for current user
                $referrer = $currentUser->referrer;

                // Stop if no referrer found (chain breaks)
                if ($referrer === null) {
                    break;
                }

                // Get commission percentage for this level
                $commissionPercent = $settings[$level] ?? null;

                // Skip if no setting found for this level or setting is inactive
                if ($commissionPercent === null) {
                    Log::debug('No active setting for referral level', [
                        'level' => $level,
                        'user_id' => $depositor->id,
                    ]);
                    $level++;
                    $currentUser = $referrer;
                    continue;
                }

                // Calculate commission amount
                $commissionAmount = ($depositAmount * $commissionPercent) / 100;

                // Only process if commission amount is greater than 0
                if ($commissionAmount > 0) {
                    // Update referrer's balance
                    $balanceBefore = (float) ($referrer->balance ?? 0);
                    $referrer->balance = $balanceBefore + $commissionAmount;
                    $referrer->save();

                    // Create referral log entry
                    ReferralLog::create([
                        'user_id' => $referrer->id,
                        'source_user_id' => $depositor->id,
                        'amount' => $commissionAmount,
                        'level' => $level,
                        'percentage_applied' => $commissionPercent,
                    ]);

                    $commissionsDistributed++;
                    $totalCommission += $commissionAmount;

                    $commissionDetails[] = [
                        'level' => $level,
                        'referrer_id' => $referrer->id,
                        'referrer_name' => $referrer->name,
                        'commission_amount' => $commissionAmount,
                        'percentage' => $commissionPercent,
                    ];

                    Log::info('Referral commission distributed', [
                        'level' => $level,
                        'depositor_id' => $depositor->id,
                        'referrer_id' => $referrer->id,
                        'deposit_amount' => $depositAmount,
                        'commission_amount' => $commissionAmount,
                        'percentage' => $commissionPercent,
                    ]);
                }

                // Move to next level
                $level++;
                $currentUser = $referrer;
            }

            // Commit transaction
            DB::commit();

            Log::info('Referral commission distribution completed', [
                'depositor_id' => $depositor->id,
                'deposit_amount' => $depositAmount,
                'commissions_distributed' => $commissionsDistributed,
                'total_commission' => $totalCommission,
            ]);

            return [
                'success' => true,
                'message' => 'Commissions distributed successfully',
                'commissions_distributed' => $commissionsDistributed,
                'total_commission' => $totalCommission,
                'details' => $commissionDetails,
            ];

        } catch (\Exception $e) {
            // Rollback transaction on error
            DB::rollBack();

            Log::error('Failed to distribute referral commission', [
                'depositor_id' => $depositor->id,
                'deposit_amount' => $depositAmount,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Get active referral settings from cache or database
     * 
     * @return array Array with level as key and commission_percent as value
     */
    private function getReferralSettings(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            $settings = DB::table('referral_settings')
                ->where('is_active', true)
                ->orderBy('level')
                ->get();

            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->level] = (float) $setting->commission_percent;
            }

            return $result;
        });
    }

    /**
     * Clear referral settings cache
     * Call this method when admin updates referral settings
     * 
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Log::info('Referral settings cache cleared');
    }

    /**
     * Get referral statistics for a user
     * 
     * @param User $user
     * @return array
     */
    public function getUserReferralStats(User $user): array
    {
        $stats = [
            'total_commissions' => 0,
            'total_referrals' => 0,
            'level_1_commissions' => 0,
            'level_2_commissions' => 0,
            'level_3_commissions' => 0,
            'recent_commissions' => [],
        ];

        // Get total commissions received
        $commissions = ReferralLog::where('user_id', $user->id)
            ->selectRaw('SUM(amount) as total, level, COUNT(*) as count')
            ->groupBy('level')
            ->get();

        foreach ($commissions as $commission) {
            $stats['total_commissions'] += (float) $commission->total;
            $stats["level_{$commission->level}_commissions"] = (float) $commission->total;
        }

        // Get total referrals (direct referrals)
        $stats['total_referrals'] = User::where('referrer_id', $user->id)->count();

        // Get recent commissions (last 10)
        $recentLogs = ReferralLog::where('user_id', $user->id)
            ->with('sourceUser:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $stats['recent_commissions'] = $recentLogs->map(function ($log) {
            return [
                'amount' => $log->amount,
                'level' => $log->level,
                'percentage' => $log->percentage_applied,
                'source_user' => $log->sourceUser ? $log->sourceUser->name : 'N/A',
                'created_at' => $log->created_at->toDateTimeString(),
            ];
        })->toArray();

        return $stats;
    }
}

