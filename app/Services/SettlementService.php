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

            // Lock market row to prevent concurrent settlement attempts
            $market = Market::lockForUpdate()->find($marketId);

            if (!$market) {
                Log::warning("Market settlement skipped: Market not found", [
                    'market_id' => $marketId
                ]);
                DB::rollBack();
                return false;
            }

            // CRITICAL: Prevent duplicate settlements
            // If market is already settled, skip immediately
            if ($market->settled) {
                Log::info("Market already settled, skipping duplicate settlement", [
                    'market_id' => $marketId,
                    'settled_at' => $market->updated_at
                ]);
                DB::rollBack();
                return true; // Return true because it's already settled (not an error)
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

            // Get winning outcome (new system: use Outcome model)
            $winningOutcome = $market->winningOutcome()->first();
            
            // Fallback: Try to determine from legacy fields
            if (!$winningOutcome) {
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
                        // Try to find matching outcome by name
                        $winningOutcome = $market->getOutcomeByName($autoOutcome);
                        if ($winningOutcome) {
                            $winningOutcome->is_winning = true;
                            $winningOutcome->save();
                        }
                        $market->final_outcome = $autoOutcome;
                        $market->outcome_result = $outcomeResult;
                        $market->final_result = $outcomeResult;
                        $market->result_set_at = $market->result_set_at ?? now();
                        $market->closed = true;
                        $market->is_closed = true;
                        $market->save();
                        Log::info("Market result auto-determined from lastTradePrice", [
                            'market_id' => $marketId,
                            'outcome' => $autoOutcome,
                            'last_trade_price' => $market->last_trade_price
                        ]);
                    }
                }

                // If still no winning outcome, try to find by name
                if (!$winningOutcome && $outcomeResult) {
                    $winningOutcome = $market->getOutcomeByName($outcomeResult);
                    if ($winningOutcome) {
                        $winningOutcome->is_winning = true;
                        $winningOutcome->save();
                    }
                }
            }

            if (!$winningOutcome) {
                Log::warning("Market settlement skipped: No winning outcome found", [
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
            // Use lockForUpdate to prevent duplicate settlement and ensure atomicity
            // CRITICAL: Only process trades that are still pending (double-check after lock)
            $trades = Trade::where('market_id', $marketId)
                ->whereIn('status', ['PENDING', 'pending'])
                ->lockForUpdate()
                ->get();

            // Double-check: Filter out any trades that might have been settled by another process
            $pendingTrades = $trades->filter(function($trade) {
                return in_array(strtoupper($trade->status), ['PENDING']);
            });

            if ($pendingTrades->isEmpty()) {
                // Mark as settled even if no trades (prevents re-processing)
                $market->settled = true;
                $market->save();
                
                Log::info("No pending trades to settle for market", [
                    'market_id' => $marketId,
                    'total_trades' => $trades->count(),
                    'already_settled' => $trades->count() - $pendingTrades->count()
                ]);
                DB::commit();
                return true;
            }

            $settledCount = 0;
            $winCount = 0;
            $lossCount = 0;
            $totalPayout = 0;

            foreach ($pendingTrades as $trade) {
                // CRITICAL: Double-check trade is still pending (race condition protection)
                $trade->refresh();
                if (!in_array(strtoupper($trade->status), ['PENDING'])) {
                    Log::info("Trade already settled, skipping", [
                        'trade_id' => $trade->id,
                        'status' => $trade->status
                    ]);
                    continue;
                }
                // Get trade outcome (new system: use outcome_id)
                $tradeOutcome = null;
                if ($trade->outcome_id) {
                    // New system: use outcome relationship
                    $tradeOutcome = $trade->outcome;
                    if (!$tradeOutcome) {
                        Log::warning("Trade has outcome_id but outcome not found, skipping", [
                            'trade_id' => $trade->id,
                            'outcome_id' => $trade->outcome_id,
                        ]);
                        continue;
                    }
                } else {
                    // Legacy system: try to find outcome by name
                    $outcomeName = $trade->outcome_name ?? $trade->outcome ?? $trade->option ?? $trade->side;
                    if ($outcomeName) {
                        $tradeOutcome = $market->getOutcomeByName($outcomeName);
                        if ($tradeOutcome) {
                            // Update trade to use outcome_id for future
                            $trade->outcome_id = $tradeOutcome->id;
                        }
                    }
                }

                if (!$tradeOutcome) {
                    Log::warning("Trade has invalid outcome, skipping", [
                        'trade_id' => $trade->id,
                        'market_id' => $marketId,
                        'outcome_id' => $trade->outcome_id,
                        'outcome_name' => $trade->outcome_name,
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

                // Check if trade outcome matches winning outcome
                if ($tradeOutcome->id === $winningOutcome->id) {
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

                    // Get or create earning wallet for trade winnings
                    $wallet = Wallet::where('user_id', $user->id)
                        ->where('wallet_type', Wallet::TYPE_EARNING)
                        ->lockForUpdate()
                        ->first();
                    if (!$wallet) {
                        try {
                            $wallet = Wallet::create([
                                'user_id' => $user->id,
                                'wallet_type' => Wallet::TYPE_EARNING,
                                'balance' => 0,
                                'currency' => 'USDT',
                                'status' => 'active'
                            ]);
                        } catch (\Illuminate\Database\QueryException $e) {
                            if ($e->getCode() == 23000) {
                                $wallet = Wallet::where('user_id', $user->id)
                                    ->where('wallet_type', Wallet::TYPE_EARNING)
                                    ->lockForUpdate()
                                    ->first();
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
                            'description' => "Trade payout: {$tradeOutcome->name} on market: {$market->question}",
                            'metadata' => [
                                'trade_id' => $trade->id,
                                'market_id' => $market->id,
                                'outcome_id' => $tradeOutcome->id,
                                'outcome_name' => $tradeOutcome->name,
                                'payout' => $payout,
                                'shares' => $shares,
                                'profit' => $payout - ($trade->amount_invested ?? $trade->amount ?? 0),
                                'wallet_type' => Wallet::TYPE_EARNING,
                            ]
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Failed to create wallet transaction for trade payout', [
                            'trade_id' => $trade->id,
                            'error' => $e->getMessage(),
                        ]);
                        throw $e;
                    }

                    $totalPayout += $payout;

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
                        'trade_outcome_id' => $tradeOutcome->id,
                        'trade_outcome_name' => $tradeOutcome->name,
                        'winning_outcome_id' => $winningOutcome->id,
                        'winning_outcome_name' => $winningOutcome->name,
                    ]);

                    $lossCount++;
                }

                $settledCount++;
            }

            // CRITICAL: Mark market as settled BEFORE commit to prevent race conditions
            // This ensures that even if commit fails, we won't try to settle again
            $market->settled = true;
            $market->closed = true; // Lock market from new trades
            $market->is_closed = true;
            $market->save();

            DB::commit();

            Log::info("Market settlement completed successfully", [
                'market_id' => $marketId,
                'total_trades_processed' => $pendingTrades->count(),
                'settled_count' => $settledCount,
                'win_count' => $winCount,
                'loss_count' => $lossCount,
                'total_payout' => $totalPayout,
                'winning_outcome_id' => $winningOutcome->id,
                'winning_outcome_name' => $winningOutcome->name,
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
        // Find markets that:
        // 1. Are NOT already settled (prevents duplicate processing)
        // 2. Have a winning outcome OR have result fields set
        // 3. Are closed (by flag or expired close_time)
        $markets = Market::where('settled', false)
            ->where(function($query) {
                // Market must have a result (winning outcome or legacy result fields)
                $query->whereHas('winningOutcome')
                      ->orWhereNotNull('outcome_result')
                      ->orWhereNotNull('final_outcome')
                      ->orWhereNotNull('final_result');
            })
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
