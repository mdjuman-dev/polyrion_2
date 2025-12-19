<?php

namespace App\Services;

use App\Models\Market;
use App\Models\Trade;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
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

            // Get outcome result - check multiple fields and normalize
            $outcomeResult = null;
            if ($market->outcome_result) {
                $outcomeResult = strtolower($market->outcome_result);
            } elseif ($market->final_outcome) {
                $outcomeResult = strtolower($market->final_outcome);
            } elseif ($market->final_result) {
                $outcomeResult = strtolower($market->final_result);
            }

            if (!$outcomeResult || !in_array($outcomeResult, ['yes', 'no'])) {
                Log::warning("Market settlement skipped: No valid outcome result", [
                    'market_id' => $marketId,
                    'outcome_result' => $market->outcome_result,
                    'final_outcome' => $market->final_outcome,
                    'final_result' => $market->final_result,
                ]);
                DB::rollBack();
                return false;
            }

            // Fetch all pending trades for this market (handle both PENDING and pending)
            $trades = Trade::where('market_id', $marketId)
                ->whereIn('status', ['PENDING', 'pending'])
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
                // Get trade outcome (normalize to lowercase)
                // Support both 'outcome' (YES/NO) and legacy 'side'/'option' fields
                $tradeOutcome = null;
                if ($trade->outcome) {
                    $tradeOutcome = strtolower($trade->outcome);
                } elseif ($trade->side) {
                    $tradeOutcome = strtolower($trade->side);
                } elseif ($trade->option) {
                    $tradeOutcome = strtolower($trade->option);
                }

                if (empty($tradeOutcome) || !in_array($tradeOutcome, ['yes', 'no'])) {
                    Log::warning("Trade has invalid outcome, skipping", [
                        'trade_id' => $trade->id,
                        'market_id' => $marketId,
                        'outcome' => $trade->outcome,
                        'side' => $trade->side,
                        'option' => $trade->option,
                    ]);
                    continue;
                }

                // Calculate shares/token_amount if not set
                $shares = $trade->shares ?? $trade->token_amount ?? 0;
                if (!$shares || $shares <= 0) {
                    // Calculate from amount and price
                    if ($trade->price && $trade->price > 0 && $trade->amount) {
                        $shares = $trade->amount / $trade->price;
                    } elseif ($trade->price_at_buy && $trade->price_at_buy > 0 && $trade->amount_invested) {
                        $shares = $trade->amount_invested / $trade->price_at_buy;
                    } else {
                        Log::warning("Trade has no shares/token_amount and cannot calculate, skipping", [
                            'trade_id' => $trade->id,
                        ]);
                        continue;
                    }
                }

                if ($tradeOutcome === $outcomeResult) {
                    // Trade WON - calculate payout
                    $payout = $shares * 1.00; // Full payout at $1.00 per share/token
                    
                    // Update trade status (support both formats)
                    $trade->status = 'WON'; // Use uppercase for consistency
                    $trade->payout = $payout;
                    $trade->payout_amount = $payout; // Legacy field
                    $trade->settled_at = now();
                    $trade->save();

                    // Update user's balance
                    $user = User::find($trade->user_id);
                    if ($user) {
                        $wallet = Wallet::firstOrCreate(
                            ['user_id' => $user->id],
                            ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
                        );

                        $balanceBefore = $wallet->balance;
                        $wallet->balance += $payout;
                        $wallet->save();

                        // Create wallet transaction for payout
                        try {
                            WalletTransaction::create([
                                'user_id' => $user->id,
                                'wallet_id' => $wallet->id,
                                'type' => 'trade_payout',
                                'amount' => $payout,
                                'balance_before' => $balanceBefore,
                                'balance_after' => $wallet->balance,
                                'reference_type' => \App\Models\Trade::class,
                                'reference_id' => $trade->id,
                                'description' => "Trade payout: {$trade->outcome} on market: {$market->question}",
                                'metadata' => [
                                    'trade_id' => $trade->id,
                                    'market_id' => $market->id,
                                    'outcome' => $trade->outcome,
                                    'payout' => $payout,
                                    'shares' => $shares,
                                    'profit' => $payout - ($trade->amount_invested ?? $trade->amount ?? 0),
                                ]
                            ]);
                        } catch (\Exception $e) {
                            Log::error('Failed to create wallet transaction for trade payout', [
                                'trade_id' => $trade->id,
                                'error' => $e->getMessage(),
                            ]);
                            // Don't fail settlement if transaction creation fails
                        }

                        Log::info("Trade settled as WON - balance updated", [
                            'trade_id' => $trade->id,
                            'user_id' => $user->id,
                            'payout' => $payout,
                            'shares' => $shares,
                            'balance_before' => $balanceBefore,
                            'balance_after' => $wallet->balance
                        ]);

                        $winCount++;
                    }
                } else {
                    // Trade LOST
                    $trade->status = 'LOST'; // Use uppercase for consistency
                    $trade->payout = 0;
                    $trade->payout_amount = 0; // Legacy field
                    $trade->settled_at = now();
                    $trade->save();

                    Log::info("Trade settled as LOST", [
                        'trade_id' => $trade->id,
                        'user_id' => $trade->user_id,
                        'trade_outcome' => $tradeOutcome,
                        'market_result' => $outcomeResult
                    ]);

                    $lossCount++;
                }

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
        // Find markets that are closed, not settled, and have a result
        $markets = Market::where(function($query) {
                $query->where('is_closed', true)
                      ->orWhere('closed', true);
            })
            ->where('settled', false)
            ->where(function($query) {
                $query->whereNotNull('outcome_result')
                      ->orWhereNotNull('final_outcome')
                      ->orWhereNotNull('final_result');
            })
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
