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

            if (!$market) {
                Log::warning("Market settlement skipped: Market not found", [
                    'market_id' => $marketId
                ]);
                DB::rollBack();
                return false;
            }

            // Mark market as closed if close_time has passed
            if ($market->close_time && now() >= $market->close_time && !$market->is_closed) {
                $market->is_closed = true;
                $market->closed = true;
                $market->save();
                Log::info("Market auto-closed due to expired close_time", [
                    'market_id' => $marketId,
                    'close_time' => $market->close_time
                ]);
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

            // Auto-determine result from lastTradePrice if market is closed but no result set
            if ((!$outcomeResult || !in_array($outcomeResult, ['yes', 'no'])) && $market->isClosed()) {
                $autoOutcome = $market->determineOutcomeFromLastTradePrice();
                if ($autoOutcome) {
                    $outcomeResult = strtolower($autoOutcome);
                    $market->final_outcome = $autoOutcome;
                    $market->outcome_result = $outcomeResult;
                    $market->final_result = $outcomeResult;
                    $market->result_set_at = $market->result_set_at ?? now();
                    $market->save();
                    Log::info("Market result auto-determined from lastTradePrice", [
                        'market_id' => $marketId,
                        'outcome' => $autoOutcome,
                        'last_trade_price' => $market->last_trade_price
                    ]);
                }
            }

            if (!$outcomeResult || !in_array($outcomeResult, ['yes', 'no'])) {
                Log::warning("Market settlement skipped: No valid outcome result", [
                    'market_id' => $marketId,
                    'outcome_result' => $market->outcome_result,
                    'final_outcome' => $market->final_outcome,
                    'final_result' => $market->final_result,
                    'is_closed' => $market->is_closed,
                    'close_time' => $market->close_time,
                ]);
                DB::rollBack();
                return false;
            }

            // Fetch all pending trades for this market (handle both PENDING and pending)
            // Use lockForUpdate to prevent duplicate settlement
            $trades = Trade::where('market_id', $marketId)
                ->whereIn('status', ['PENDING', 'pending'])
                ->lockForUpdate()
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
                    
                    // Update user's balance first (atomic operation)
                    $user = User::find($trade->user_id);
                    if (!$user) {
                        Log::error("Trade settlement failed: User not found", [
                            'trade_id' => $trade->id,
                            'user_id' => $trade->user_id
                        ]);
                        continue;
                    }

                    $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                    if (!$wallet) {
                        try {
                            $wallet = Wallet::create([
                                'user_id' => $user->id,
                                'balance' => 0,
                                'currency' => 'USDT',
                                'status' => 'active'
                            ]);
                        } catch (\Illuminate\Database\QueryException $e) {
                            if ($e->getCode() == 23000) {
                                $wallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                            } else {
                                throw $e;
                            }
                        }
                    }

                    $balanceBefore = $wallet->balance;
                    $wallet->balance += $payout;
                    $wallet->save();

                    // Update trade status (support both formats)
                    $trade->status = 'WON';
                    $trade->payout = $payout;
                    $trade->payout_amount = $payout;
                    $trade->settled_at = now();
                    $trade->save();

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
                        throw $e;
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
                } else {
                    // Trade LOST
                    $trade->status = 'LOST';
                    $trade->payout = 0;
                    $trade->payout_amount = 0;
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
        // Find markets that are closed (by flag or expired close_time) and not settled
        // Include markets without results - we'll try to auto-determine them
        $markets = Market::where('settled', false)
            ->where(function($query) {
                $query->where('is_closed', true)
                      ->orWhere('closed', true)
                      ->orWhere(function($q) {
                          $q->whereNotNull('close_time')
                            ->where('close_time', '<=', now());
                      });
            })
            ->get();

        Log::info("Settlement scheduler: Found markets to process", [
            'count' => $markets->count()
        ]);

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
