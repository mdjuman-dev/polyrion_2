<?php

namespace App\Jobs;

use App\Models\Trade;
use App\Models\User;
use App\Models\ReferralLog;
use App\Models\ReferralSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProcessTradeCommission implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Cache key for referral settings
     */
    private const CACHE_KEY = 'referral_settings_trade_volume';
    
    /**
     * Cache duration in seconds (1 hour)
     */
    private const CACHE_DURATION = 3600;

    /**
     * The trade instance
     */
    public $trade;

    /**
     * Create a new job instance.
     */
    public function __construct(Trade $trade)
    {
        $this->trade = $trade;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('ProcessTradeCommission job started', [
                'trade_id' => $this->trade->id,
            ]);

            // Reload trade with relationships to ensure we have latest data
            $this->trade->refresh();
            $this->trade->load('user');
            
            // CRITICAL: Check if commission already processed for this trade (prevent duplicates)
            $existingCommission = ReferralLog::where('trade_id', $this->trade->id)->first();
            if ($existingCommission) {
                Log::info('Trade commission already processed, skipping duplicate', [
                    'trade_id' => $this->trade->id,
                    'existing_log_id' => $existingCommission->id,
                ]);
                return;
            }
            
            // Get the trader (user who made the trade)
            $trader = $this->trade->user;
            
            if (!$trader) {
                Log::warning('Trade commission processing skipped: Trader not found', [
                    'trade_id' => $this->trade->id,
                ]);
                return;
            }

            Log::info('Trader found', [
                'trade_id' => $this->trade->id,
                'trader_id' => $trader->id,
                'trader_referrer_id' => $trader->referrer_id,
            ]);

            // Check if trader has a referrer
            if (!$trader->referrer_id) {
                Log::info('Trade commission processing skipped: Trader has no referrer', [
                    'trade_id' => $this->trade->id,
                    'trader_id' => $trader->id,
                ]);
                return;
            }

            // Get trade amount (use amount_invested or amount field) - FULL AMOUNT, no deduction
            $tradeAmount = (float) ($this->trade->amount_invested ?? $this->trade->amount ?? 0);

            Log::info('Trade amount calculated', [
                'trade_id' => $this->trade->id,
                'trade_amount' => $tradeAmount,
                'amount_invested' => $this->trade->amount_invested,
                'amount' => $this->trade->amount,
            ]);

            if ($tradeAmount <= 0) {
                Log::warning('Trade commission processing skipped: Invalid trade amount', [
                    'trade_id' => $this->trade->id,
                    'trade_amount' => $tradeAmount,
                ]);
                return;
            }

            // Fetch active referral settings for trade_volume type (cached)
            $settings = $this->getReferralSettings();

            Log::info('Referral settings fetched', [
                'trade_id' => $this->trade->id,
                'settings_count' => count($settings),
                'settings' => $settings,
            ]);

            if (empty($settings)) {
                Log::warning('Trade commission processing skipped: No active referral settings found', [
                    'trade_id' => $this->trade->id,
                    'all_settings' => ReferralSetting::all()->toArray(),
                ]);
                return;
            }

            // Process commission distribution within a transaction with locking
            DB::transaction(function () use ($trader, $tradeAmount, $settings) {
                // Double-check for duplicate commission (race condition protection)
                $duplicateCheck = ReferralLog::where('trade_id', $this->trade->id)->lockForUpdate()->first();
                if ($duplicateCheck) {
                    Log::info('Trade commission already processed (duplicate check), skipping', [
                        'trade_id' => $this->trade->id,
                    ]);
                    return;
                }

                // OPTIMIZED: Build referral chain upfront to reduce database queries
                $referralChain = $this->buildReferralChain($trader);
                
                Log::info('Referral chain built', [
                    'trade_id' => $this->trade->id,
                    'trader_id' => $trader->id,
                    'chain_levels' => array_keys($referralChain),
                    'chain_count' => count($referralChain),
                ]);
                
                if (empty($referralChain)) {
                    Log::warning('No referral chain found for trader', [
                        'trade_id' => $this->trade->id,
                        'trader_id' => $trader->id,
                        'trader_referrer_id' => $trader->referrer_id,
                    ]);
                    return;
                }

                $commissionsDistributed = 0;
                $totalCommission = 0;
                $commissionLogs = [];

                // Process each level in the chain
                foreach ($referralChain as $level => $referrer) {
                    // Get commission percentage for this level
                    $commissionPercent = $settings[$level] ?? null;

                    // Skip if no setting found for this level
                    if ($commissionPercent === null || $commissionPercent <= 0) {
                        continue;
                    }

                    // Calculate commission amount
                    $commissionAmount = ($tradeAmount * $commissionPercent) / 100;

                    // Only process if commission amount is greater than 0
                    if ($commissionAmount > 0) {
                        // SAFE: Lock referrer's user record for update to prevent race conditions
                        $referrer = User::lockForUpdate()->find($referrer->id);
                        
                        if (!$referrer) {
                            Log::warning('Referrer not found during commission processing', [
                                'level' => $level,
                                'trade_id' => $this->trade->id,
                            ]);
                            continue;
                        }

                        // Get or create earning wallet for referral commission
                        $earningWallet = \App\Models\Wallet::lockForUpdate()
                            ->firstOrCreate(
                                ['user_id' => $referrer->id, 'wallet_type' => \App\Models\Wallet::TYPE_EARNING],
                                ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
                            );
                        
                        // Update earning wallet balance atomically
                        $balanceBefore = (float) $earningWallet->balance;
                        $earningWallet->balance += $commissionAmount;
                        $earningWallet->save();
                        $balanceAfter = (float) $earningWallet->balance;

                        // Prepare log entry (batch insert for better performance)
                        $logEntry = [
                            'user_id' => $referrer->id, // referrer_id
                            'from_user_id' => $trader->id, // referred_user_id
                            'trade_id' => $this->trade->id,
                            'amount' => $commissionAmount, // commission_amount
                            'level' => $level,
                            'percentage_applied' => $commissionPercent,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        
                        // Add optional columns if they exist
                        if (Schema::hasColumn('referral_logs', 'trade_amount')) {
                            $logEntry['trade_amount'] = $tradeAmount;
                        }
                        if (Schema::hasColumn('referral_logs', 'status')) {
                            $logEntry['status'] = 'completed'; // Commission is completed when trade is placed
                        }
                        
                        $commissionLogs[] = $logEntry;

                        $commissionsDistributed++;
                        $totalCommission += $commissionAmount;

                        Log::info('Trade referral commission distributed', [
                            'level' => $level,
                            'trade_id' => $this->trade->id,
                            'trader_id' => $trader->id,
                            'referrer_id' => $referrer->id,
                            'trade_amount' => $tradeAmount,
                            'commission_amount' => $commissionAmount,
                            'percentage' => $commissionPercent,
                            'balance_before' => $balanceBefore,
                            'balance_after' => $balanceAfter,
                        ]);
                    }
                }

                // Batch insert all commission logs for better performance
                if (!empty($commissionLogs)) {
                    ReferralLog::insert($commissionLogs);
                }

                Log::info('Trade commission distribution completed', [
                    'trade_id' => $this->trade->id,
                    'trader_id' => $trader->id,
                    'trade_amount' => $tradeAmount,
                    'commissions_distributed' => $commissionsDistributed,
                    'total_commission' => $totalCommission,
                ]);
            });

        } catch (\Exception $e) {
            Log::error('ProcessTradeCommission job failed', [
                'trade_id' => $this->trade->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Re-throw to mark job as failed (will be retried if configured)
            // Note: The duplicate check at the start will prevent duplicate processing on retry
            throw $e;
        }
    }

    /**
     * Build referral chain (up to 3 levels) efficiently
     * Optimized to reduce database queries
     * 
     * @param User $trader
     * @return array Array with level as key and User model as value
     */
    private function buildReferralChain(User $trader): array
    {
        $chain = [];
        $currentUserId = $trader->referrer_id;
        $level = 1;

        // Traverse up to 3 levels
        while ($level <= 3 && $currentUserId !== null) {
            // Load referrer (will be locked in transaction later)
            $referrer = User::find($currentUserId);
            
            if (!$referrer) {
                break;
            }

            $chain[$level] = $referrer;
            $currentUserId = $referrer->referrer_id;
            $level++;
        }

        return $chain;
    }

    /**
     * Get active referral settings for trade_volume type from cache or database
     * 
     * @return array Array with level as key and commission_percent as value
     */
    private function getReferralSettings(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_DURATION, function () {
            // First check if commission_type column exists
            $hasCommissionType = \Schema::hasColumn('referral_settings', 'commission_type');
            
            $query = ReferralSetting::where('is_active', true);
            
            // Only filter by commission_type if column exists
            if ($hasCommissionType) {
                $query->where('commission_type', 'trade_volume');
            } else {
                // If column doesn't exist, get all active settings (backward compatibility)
                Log::warning('commission_type column not found in referral_settings, using all active settings');
            }
            
            $settings = $query->orderBy('level')->get();

            Log::info('Referral settings fetched from database', [
                'has_commission_type_column' => $hasCommissionType,
                'settings_count' => $settings->count(),
                'settings' => $settings->toArray(),
            ]);

            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->level] = (float) $setting->commission_percent;
            }

            return $result;
        });
    }
    
    /**
     * Clear cache for referral settings
     * Call this when settings are updated
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Log::info('Referral settings cache cleared');
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessTradeCommission job permanently failed', [
            'trade_id' => $this->trade->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
