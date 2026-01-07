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
    * Priority: best_ask/best_bid (most accurate) > outcome_prices
    * outcome_prices[0] = NO price
    * outcome_prices[1] = YES price
    */
   public function getOutcomePrice(Market $market, string $outcome): float
   {
      // Priority 1: Use best_ask/best_bid if available (most accurate from order book)
      if ($outcome === 'YES' && $market->best_ask !== null && $market->best_ask > 0) {
         return (float) $market->best_ask;
      } elseif ($outcome === 'NO' && $market->best_bid !== null && $market->best_bid > 0) {
         // For NO: price = 1 - best_bid (since best_bid is for YES)
         return 1 - (float) $market->best_bid;
      }

      // Priority 2: Use outcome_prices
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
    * Validate trade amount limits
    */
   public function validateTradeAmount(float $amount): void
   {
      $minAmount = 0.01; // Minimum $0.01
      $maxAmount = 100000; // Maximum $100,000 per trade

      if ($amount < $minAmount) {
         throw new InvalidArgumentException("Minimum trade amount is $" . number_format($minAmount, 2));
      }

      if ($amount > $maxAmount) {
         throw new InvalidArgumentException("Maximum trade amount is $" . number_format($maxAmount, 2));
      }
   }

   /**
    * Check if market has sufficient liquidity
    */
   public function checkMarketLiquidity(Market $market, float $amount): bool
   {
      // Check if market has minimum liquidity (optional check)
      $minLiquidity = 100; // Minimum $100 liquidity
      $liquidity = $market->liquidity_clob ?? $market->liquidity ?? 0;

      // If liquidity is very low, warn but don't block (market might still be tradeable)
      if ($liquidity < $minLiquidity && $amount > ($liquidity * 0.1)) {
         Log::warning('Low liquidity market trade', [
            'market_id' => $market->id,
            'liquidity' => $liquidity,
            'trade_amount' => $amount,
         ]);
      }

      return true; // Allow trade, just log warning
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

      // Validate trade amount limits
      $this->validateTradeAmount($amount);

      // Validate market is open
      if (!$market->isOpenForTrading()) {
         throw new InvalidArgumentException('Market is closed for trading');
      }

      // Check market liquidity (warning only, doesn't block)
      $this->checkMarketLiquidity($market, $amount);

      // Validate balance
      if (!$this->validateBalance($user, $amount)) {
         $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
         );
         throw new InvalidArgumentException('Insufficient balance. Your balance: $' . number_format($wallet->balance, 2) . ', Required: $' . number_format($amount, 2));
      }

      // Refresh market data if needed (reload from database to get latest prices)
      $market->refresh();

      // Get outcome price (uses best_ask/best_bid if available, otherwise outcome_prices)
      $outcomePrice = $this->getOutcomePrice($market, $outcome);

      // Validate price is valid
      if ($outcomePrice <= 0 || $outcomePrice >= 1) {
         throw new InvalidArgumentException('Invalid market price. Please try again.');
      }

      // Calculate token amount
      $tokenAmount = $this->calculateTokens($amount, $outcomePrice);

      // Validate token amount is reasonable (at least 0.0001 tokens)
      if ($tokenAmount < 0.0001) {
         throw new InvalidArgumentException('Trade amount too small. Minimum tokens: 0.0001');
      }

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

         // Create trade with both new and legacy fields for backward compatibility
         $trade = Trade::create([
            'user_id' => $user->id,
            'market_id' => $market->id,
            'outcome' => $outcome,
            'amount_invested' => $amount,
            'token_amount' => $tokenAmount,
            'price_at_buy' => $outcomePrice,
            'status' => 'PENDING',
            'option' => strtolower($outcome),
            'amount' => $amount,
            'price' => $outcomePrice,
            'shares' => $tokenAmount,
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

         // Dispatch job to process trade-based referral commission (async)
         try {
            \App\Jobs\ProcessTradeCommission::dispatch($trade);
         } catch (\Exception $e) {
            // Log error but don't fail the trade creation
            Log::error('Failed to dispatch ProcessTradeCommission job', [
               'trade_id' => $trade->id,
               'error' => $e->getMessage(),
            ]);
         }

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
            $trade->payout_amount = $payout; // Legacy field
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
                  'profit' => $payout - $trade->amount_invested,
               ]
            ]);

            Log::info('Trade settled as WON', [
               'trade_id' => $trade->id,
               'payout' => $payout,
               'profit' => $payout - $trade->amount_invested,
            ]);

         } else {
            // Trade LOST
            $trade->status = 'LOST';
            $trade->payout = 0;
            $trade->payout_amount = 0; // Legacy field
            $trade->settled_at = now();
            $trade->save();

            Log::info('Trade settled as LOST', [
               'trade_id' => $trade->id,
               'loss' => $trade->amount_invested,
            ]);
         }

         DB::commit();

      } catch (\Exception $e) {
         DB::rollBack();
         throw $e;
      }
   }

   /**
    * Settle all pending trades for a market
    *
    * @param Market $market
    * @return array
    */
   public function settleMarket(Market $market): array
   {
      $finalOutcome = $market->final_outcome ?? strtoupper($market->final_result ?? '');

      if (!$finalOutcome || !in_array($finalOutcome, ['YES', 'NO'])) {
         throw new InvalidArgumentException('Market does not have a final outcome');
      }

      $pendingTrades = Trade::where('market_id', $market->id)
         ->where('status', 'PENDING')
         ->get();

      $winCount = 0;
      $lossCount = 0;
      $totalPayout = 0;

      foreach ($pendingTrades as $trade) {
         try {
            $this->settleTrade($trade);

            // Refresh trade to get updated status
            $trade->refresh();

            if ($trade->outcome === $finalOutcome) {
               $winCount++;
               $totalPayout += $trade->payout;
            } else {
               $lossCount++;
            }
         } catch (\Exception $e) {
            Log::error('Failed to settle trade', [
               'trade_id' => $trade->id,
               'error' => $e->getMessage(),
            ]);
         }
      }

      // Mark market as settled
      $market->settled = true;
      $market->save();

      return [
         'total_trades' => $pendingTrades->count(),
         'win_count' => $winCount,
         'loss_count' => $lossCount,
         'total_payout' => $totalPayout,
      ];
   }

   /**
    * Get trade preview/estimate before placing trade
    * Returns: estimated tokens, cost, potential payout
    */
   public function getTradePreview(Market $market, string $outcome, float $amount): array
   {
      // Validate outcome
      if (!in_array($outcome, ['YES', 'NO'])) {
         throw new InvalidArgumentException('Outcome must be YES or NO');
      }

      // Validate market is open
      if (!$market->isOpenForTrading()) {
         throw new InvalidArgumentException('Market is closed for trading');
      }

      // Get outcome price
      $outcomePrice = $this->getOutcomePrice($market, $outcome);

      // Calculate token amount
      $tokenAmount = $this->calculateTokens($amount, $outcomePrice);

      // Calculate potential payout (if win)
      $potentialPayout = $tokenAmount * 1.00;

      // Calculate potential profit/loss
      $potentialProfit = $potentialPayout - $amount;
      $potentialProfitPercent = $amount > 0 ? ($potentialProfit / $amount) * 100 : 0;

      return [
         'outcome' => $outcome,
         'amount' => $amount,
         'price' => $outcomePrice,
         'price_percent' => $outcomePrice * 100,
         'token_amount' => $tokenAmount,
         'potential_payout' => $potentialPayout,
         'potential_profit' => $potentialProfit,
         'potential_profit_percent' => $potentialProfitPercent,
         'market' => [
            'id' => $market->id,
            'question' => $market->question,
            'slug' => $market->slug,
         ],
      ];
   }

   /**
    * Get current market prices for both YES and NO
    */
   public function getMarketPrices(Market $market): array
   {
      // Refresh market to get latest prices
      $market->refresh();

      $yesPrice = null;
      $noPrice = null;

      // Try best_ask/best_bid first (most accurate)
      if ($market->best_ask !== null && $market->best_ask > 0) {
         $yesPrice = (float) $market->best_ask;
      }
      if ($market->best_bid !== null && $market->best_bid > 0) {
         // best_bid is for YES, so NO = 1 - best_bid
         $noPrice = 1 - (float) $market->best_bid;
      }

      // Fallback to outcome_prices
      $outcomePrices = $market->outcome_prices ?? $market->outcomePrices;
      if (is_string($outcomePrices)) {
         $outcomePrices = json_decode($outcomePrices, true);
      }

      if (is_array($outcomePrices) && count($outcomePrices) >= 2) {
         if ($yesPrice === null) {
            $yesPrice = (float) $outcomePrices[1];
         }
         if ($noPrice === null) {
            $noPrice = (float) $outcomePrices[0];
         }
      }

      // Ensure prices are valid
      $yesPrice = $yesPrice ?? 0.5;
      $noPrice = $noPrice ?? 0.5;

      return [
         'yes_price' => $yesPrice,
         'no_price' => $noPrice,
         'yes_price_cents' => $yesPrice * 100,
         'no_price_cents' => $noPrice * 100,
         'best_ask' => $market->best_ask,
         'best_bid' => $market->best_bid,
         'last_trade_price' => $market->last_trade_price,
         'spread' => $market->spread,
      ];
   }
}

