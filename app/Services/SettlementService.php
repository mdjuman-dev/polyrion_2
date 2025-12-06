<?php

namespace App\Services;

use App\Models\Market;
use App\Models\Trade;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettlementService
{
    /**
     * Settle a market - process all pending trades and update balances
     *
     * @param int $marketId
     * @return bool
     */
    public function settleMarket($marketId): bool
    {
        try {
            DB::beginTransaction();

            $market = Market::find($marketId);

            if (!$market || !$market->outcome_result) {
                Log::warning("Market settlement skipped: Market not found or outcome_result not set", [
                    'market_id' => $marketId
                ]);
                DB::rollBack();
                return false;
            }

            // Get outcome result (normalize to lowercase)
            $outcomeResult = strtolower($market->outcome_result);

            // Fetch all pending trades for this market
            $trades = Trade::where('market_id', $marketId)
                ->where('status', 'pending')
                ->get();

            if ($trades->isEmpty()) {
                Log::info("No pending trades to settle for market", [
                    'market_id' => $marketId
                ]);
                DB::commit();
                return true;
            }

            $settledCount = 0;
            $winCount = 0;
            $lossCount = 0;

            foreach ($trades as $trade) {
                // Get trade side (normalize to lowercase)
                // Support both 'side' and 'option' fields for backward compatibility
                $tradeSide = strtolower($trade->side ?? $trade->option ?? '');

                if (empty($tradeSide)) {
                    Log::warning("Trade has no side/option, skipping", [
                        'trade_id' => $trade->id,
                        'market_id' => $marketId
                    ]);
                    continue;
                }

                // Calculate shares if not set (amount / price)
                if (!$trade->shares && $trade->price && $trade->price > 0) {
                    $trade->shares = $trade->amount / $trade->price;
                } elseif (!$trade->shares && $trade->token_amount) {
                    // Fallback to token_amount if shares not set
                    $trade->shares = $trade->token_amount;
                }

                if ($tradeSide === $outcomeResult) {
                    // Trade won - calculate payout
                    $payout = $trade->shares * 1;
                    $trade->status = 'win';
                    $trade->payout = $payout;
                    $trade->settled_at = now();

                    // Update user's balance
                    $user = User::find($trade->user_id);
                    if ($user) {
                        $wallet = Wallet::firstOrCreate(
                            ['user_id' => $user->id],
                            ['balance' => 0, 'currency' => 'USD', 'status' => 'active']
                        );

                        $balanceBefore = $wallet->balance;
                        $wallet->balance += $payout;
                        $wallet->save();

                        Log::info("Trade settled as WIN - balance updated", [
                            'trade_id' => $trade->id,
                            'user_id' => $user->id,
                            'payout' => $payout,
                            'balance_before' => $balanceBefore,
                            'balance_after' => $wallet->balance
                        ]);

                        $winCount++;
                    }
                } else {
                    // Trade lost
                    $trade->status = 'loss';
                    $trade->payout = 0;
                    $trade->settled_at = now();

                    Log::info("Trade settled as LOSS", [
                        'trade_id' => $trade->id,
                        'user_id' => $trade->user_id,
                        'trade_side' => $tradeSide,
                        'market_result' => $outcomeResult
                    ]);

                    $lossCount++;
                }

                $trade->save();
                $settledCount++;
            }

            // Mark market as settled
            $market->settled = true;
            $market->save();

            DB::commit();

            Log::info("Market settlement completed", [
                'market_id' => $marketId,
                'total_trades' => $trades->count(),
                'settled_count' => $settledCount,
                'win_count' => $winCount,
                'loss_count' => $lossCount
            ]);

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Market settlement failed", [
                'market_id' => $marketId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Settle all closed markets that haven't been settled yet
     *
     * @return array
     */
    public function settleClosedMarkets(): array
    {
        $markets = Market::where('is_closed', true)
            ->where('settled', false)
            ->whereNotNull('outcome_result')
            ->get();

        $results = [
            'total' => $markets->count(),
            'success' => 0,
            'failed' => 0,
            'markets' => []
        ];

        foreach ($markets as $market) {
            $success = $this->settleMarket($market->id);

            $results['markets'][] = [
                'market_id' => $market->id,
                'success' => $success
            ];

            if ($success) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        return $results;
    }
}
