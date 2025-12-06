<?php

namespace App\Services;

use App\Models\Market;
use App\Models\Trade;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class TradeService
{
    /**
     * Validate user has enough balance
     */
    public function validateBalance(User $user, float $amount): bool
    {
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
        );

        return $wallet->balance >= $amount;
    }

    /**
     * Calculate token amount based on investment and price
     * Formula: token_amount = amount / selected_outcome_price
     */
    public function calculateTokens(float $amount, float $outcomePrice): float
    {
        if ($outcomePrice <= 0 || $outcomePrice >= 1) {
            throw new InvalidArgumentException('Outcome price must be between 0 and 1');
        }

        return $amount / $outcomePrice;
    }

    /**
     * Get outcome price from market
     * outcome_prices[0] = NO price
     * outcome_prices[1] = YES price
     */
    public function getOutcomePrice(Market $market, string $outcome): float
    {
        // Try outcome_prices first, then outcomePrices (legacy)
        $outcomePrices = $market->outcome_prices ?? $market->outcomePrices;
        
        if (is_string($outcomePrices)) {
            $outcomePrices = json_decode($outcomePrices, true);
        }

        if (!is_array($outcomePrices) || count($outcomePrices) < 2) {
            throw new InvalidArgumentException('Invalid outcome_prices format. Expected array with 2 elements: [NO_price, YES_price]');
        }

        if ($outcome === 'YES') {
            return (float) $outcomePrices[1];
        } elseif ($outcome === 'NO') {
            return (float) $outcomePrices[0];
        }

        throw new InvalidArgumentException('Outcome must be YES or NO');
    }

    /**
     * Create a new trade
     */
    public function createTrade(User $user, Market $market, string $outcome, float $amount): Trade
    {
        // Validate outcome
        if (!in_array($outcome, ['YES', 'NO'])) {
            throw new InvalidArgumentException('Outcome must be YES or NO');
        }

        // Validate market is open
        if (!$market->isOpenForTrading()) {
            throw new InvalidArgumentException('Market is closed for trading');
        }

        // Validate balance
        if (!$this->validateBalance($user, $amount)) {
            throw new InvalidArgumentException('Insufficient balance');
        }

        // Get outcome price
        $outcomePrice = $this->getOutcomePrice($market, $outcome);

        // Calculate token amount
        $tokenAmount = $this->calculateTokens($amount, $outcomePrice);

        // Get or create wallet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
        );

        DB::beginTransaction();

        try {
            // Deduct balance
            $balanceBefore = $wallet->balance;
            $wallet->balance -= $amount;
            $wallet->save();

            // Create trade
            $trade = Trade::create([
                'user_id' => $user->id,
                'market_id' => $market->id,
                'outcome' => $outcome,
                'amount_invested' => $amount,
                'token_amount' => $tokenAmount,
                'price_at_buy' => $outcomePrice,
                'status' => 'PENDING',
            ]);

            // Create wallet transaction
            WalletTransaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'trade',
                'amount' => -$amount,
                'balance_before' => $balanceBefore,
                'balance_after' => $wallet->balance,
                'reference_type' => Trade::class,
                'reference_id' => $trade->id,
                'description' => "Bought {$outcome} on market: {$market->question}",
                'metadata' => [
                    'trade_id' => $trade->id,
                    'market_id' => $market->id,
                    'outcome' => $outcome,
                    'amount_invested' => $amount,
                    'token_amount' => $tokenAmount,
                    'price_at_buy' => $outcomePrice,
                ]
            ]);

            DB::commit();

            Log::info('Trade created successfully', [
                'trade_id' => $trade->id,
                'user_id' => $user->id,
                'market_id' => $market->id,
                'outcome' => $outcome,
                'amount_invested' => $amount,
                'token_amount' => $tokenAmount,
            ]);

            return $trade;

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Settle a single trade
     * If trade.outcome == final_outcome => WON, payout = token_amount × 1.00
     * Else => LOST, payout = 0
     */
    public function settleTrade(Trade $trade): void
    {
        if (strtoupper($trade->status) !== 'PENDING') {
            return; // Already settled
        }

        $market = $trade->market;

        // Get final_outcome (handles both final_outcome and final_result)
        $finalOutcome = $market->getFinalOutcome();
        
        if (!$finalOutcome) {
            throw new InvalidArgumentException('Market does not have a final outcome');
        }

        DB::beginTransaction();

        try {
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $trade->user_id],
                ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
            );

            if ($trade->outcome === $finalOutcome) {
                // Trade WON
                $payout = $trade->token_amount * 1.00; // Full payout at $1.00 per token

                $trade->status = 'WON';
                $trade->payout = $payout;
                $trade->settled_at = now();
                $trade->save();

                // Add payout to wallet
                $balanceBefore = $wallet->balance;
                $wallet->balance += $payout;
                $wallet->save();

                // Create wallet transaction
                WalletTransaction::create([
                    'user_id' => $trade->user_id,
                    'wallet_id' => $wallet->id,
                    'type' => 'trade_payout',
                    'amount' => $payout,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $wallet->balance,
                    'reference_type' => Trade::class,
                    'reference_id' => $trade->id,
                    'description' => "Trade payout: {$trade->outcome} on market: {$market->question}",
                    'metadata' => [
                        'trade_id' => $trade->id,
                        'market_id' => $market->id,
                        'outcome' => $trade->outcome,
                        'payout' => $payout,
                    ]
                ]);

                Log::info('Trade settled as WON', [
                    'trade_id' => $trade->id,
                    'payout' => $payout,
                ]);

            } else {
                // Trade LOST
                $trade->status = 'LOST';
                $trade->payout = 0;
                $trade->settled_at = now();
                $trade->save();

                Log::info('Trade settled as LOST', [
                    'trade_id' => $trade->id,
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

