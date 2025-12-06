<?php

namespace App\Services;

use App\Models\Market;
use App\Models\Trade;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettlementService
{
    /**
     * Settle all pending trades for a market after result is set
     */
    public function settleMarket(Market $market): array
    {
        if (!$market->hasResult()) {
            throw new \Exception('Market does not have a final result yet');
        }

        $pendingTrades = $market->pendingTrades()->get();
        
        if ($pendingTrades->isEmpty()) {
            return [
                'success' => true,
                'message' => 'No pending trades to settle',
                'wins' => 0,
                'losses' => 0,
            ];
        }

        $wins = 0;
        $losses = 0;
        $totalPayout = 0;

        DB::beginTransaction();
        
        try {
            foreach ($pendingTrades as $trade) {
                $result = $this->settleTrade($trade, $market->final_result);
                
                if ($result['won']) {
                    $wins++;
                    $totalPayout += $result['payout'];
                } else {
                    $losses++;
                }
            }

            DB::commit();

            Log::info('Market settled successfully', [
                'market_id' => $market->id,
                'wins' => $wins,
                'losses' => $losses,
                'total_payout' => $totalPayout,
            ]);

            return [
                'success' => true,
                'message' => 'Market settled successfully',
                'wins' => $wins,
                'losses' => $losses,
                'total_payout' => $totalPayout,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Market settlement failed', [
                'market_id' => $market->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Settle a single trade
     */
    protected function settleTrade(Trade $trade, string $finalResult): array
    {
        $userWon = ($trade->option === $finalResult);

        if ($userWon) {
            // Calculate payout: return amount + profit
            // Profit = amount * (1 - price) for YES, or amount * price for NO
            if ($trade->option === 'yes') {
                $profit = $trade->amount * (1 - $trade->price);
            } else {
                $profit = $trade->amount * $trade->price;
            }
            
            $payout = $trade->amount + $profit;

            // Update trade
            $trade->status = 'win';
            $trade->payout = $payout;
            $trade->payout_amount = $payout; // Keep both in sync
            $trade->settled_at = now();
            $trade->save();

            // Add money to user's wallet
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $trade->user_id],
                ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
            );

            $balanceBefore = $wallet->balance;
            $wallet->balance += $payout;
            $wallet->save();

            // Create transaction record
            WalletTransaction::create([
                'user_id' => $trade->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'trade_payout',
                'amount' => $payout,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'reference_type' => Trade::class,
                'reference_id' => $trade->id,
                'description' => "Trade payout - Won {$trade->option} bet on market #{$trade->market_id}",
                'metadata' => [
                    'trade_id' => $trade->id,
                    'market_id' => $trade->market_id,
                    'option' => $trade->option,
                    'profit' => $profit,
                ]
            ]);

            return [
                'won' => true,
                'payout' => $payout,
            ];
        } else {
            // User lost - money already deducted, just mark as loss
            $trade->status = 'loss';
            $trade->settled_at = now();
            $trade->save();

            return [
                'won' => false,
                'payout' => 0,
            ];
        }
    }

    /**
     * Auto-settle markets that have results but pending trades
     * This can be called by a scheduled command
     */
    public function autoSettleMarkets(): array
    {
        $markets = Market::whereNotNull('final_result')
            ->whereHas('trades', function ($query) {
                $query->where('status', 'pending');
            })
            ->get();

        $results = [];
        
        foreach ($markets as $market) {
            try {
                $result = $this->settleMarket($market);
                $results[] = [
                    'market_id' => $market->id,
                    'success' => true,
                    ...$result,
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'market_id' => $market->id,
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }
}

