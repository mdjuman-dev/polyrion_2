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
    * Validate user has enough balance in main wallet
    */
   public function validateBalance(User $user, float $amount): bool
   {
      $wallet = Wallet::firstOrCreate(
         ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_MAIN],
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
    * Get outcome model by name from market
    * Creates outcome if it doesn't exist (for backward compatibility)
    */
   public function getOutcomeByName(Market $market, string $outcomeName): \App\Models\Outcome
   {
      // Try to find existing outcome
      $outcome = $market->getOutcomeByName($outcomeName);
      
      if ($outcome) {
         return $outcome;
      }

      // If not found, check if market has outcomes synced from API
      $outcomesArray = $market->outcomes;
      if (is_string($outcomesArray)) {
         $outcomesArray = json_decode($outcomesArray, true);
      }

      // If market has outcomes array, sync them first
      if (is_array($outcomesArray) && !empty($outcomesArray)) {
         $market->syncOutcomesFromApi($outcomesArray);
         $outcome = $market->getOutcomeByName($outcomeName);
         if ($outcome) {
            return $outcome;
         }
      }

      // Last resort: create default Yes/No outcomes if none exist
      if ($market->outcomes()->count() === 0) {
         $market->syncOutcomesFromApi(['Yes', 'No']);
         $outcome = $market->getOutcomeByName($outcomeName);
         if ($outcome) {
            return $outcome;
         }
      }

      throw new InvalidArgumentException("Outcome '{$outcomeName}' not found for market {$market->id}");
   }

   /**
    * Get outcome price from Outcome model
    * Uses calculated price from traded amounts (Polymarket-style)
    * Falls back to API prices if no trades yet
    */
   public function getOutcomePrice(Market $market, string $outcomeName): float
   {
      $outcome = $this->getOutcomeByName($market, $outcomeName);

      // Priority 1: Use calculated price from trades (most accurate for internal trading)
      if ($outcome->total_traded_amount > 0) {
         return (float) $outcome->current_price;
      }

      // Priority 2: Use best_ask/best_bid from API if available
      $outcomesArray = $market->outcomes;
      if (is_string($outcomesArray)) {
         $outcomesArray = json_decode($outcomesArray, true);
      }
      
      if (is_array($outcomesArray) && !empty($outcomesArray)) {
         $outcomeIndex = null;
         foreach ($outcomesArray as $index => $name) {
            if (strcasecmp($name, $outcomeName) === 0) {
               $outcomeIndex = $index;
               break;
            }
         }

         if ($outcomeIndex !== null) {
            // Use best_ask/best_bid if available
            if ($outcomeIndex === 1 && $market->best_ask !== null && $market->best_ask > 0) {
               return (float) $market->best_ask;
            } elseif ($outcomeIndex === 0 && $market->best_bid !== null && $market->best_bid > 0) {
               return 1 - (float) $market->best_bid;
            }

            // Fallback to outcome_prices
            $outcomePrices = $market->outcome_prices;
            if (is_string($outcomePrices)) {
               $outcomePrices = json_decode($outcomePrices, true);
            }
            if (is_array($outcomePrices) && isset($outcomePrices[$outcomeIndex === 0 ? 0 : 1])) {
               return (float) $outcomePrices[$outcomeIndex === 0 ? 0 : 1];
            }
         }
      }

      // Default: equal distribution
      $activeOutcomesCount = $market->activeOutcomes()->count();
      return $activeOutcomesCount > 0 ? (1.0 / $activeOutcomesCount) : 0.5;
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
    * Uses Outcome model for flexible outcomes (Up/Down, Yes/No, Over 2.5, etc.)
    */
   public function createTrade(User $user, Market $market, string $outcomeName, float $amount): Trade
   {
      // Get or create outcome model
      $outcome = $this->getOutcomeByName($market, $outcomeName);

      // Validate trade amount limits
      $this->validateTradeAmount($amount);

      // Validate market is open
      if (!$market->isOpenForTrading()) {
         throw new InvalidArgumentException('Market is closed for trading');
      }

      // Check market liquidity (warning only, doesn't block)
      $this->checkMarketLiquidity($market, $amount);

      // Validate balance in main wallet
      if (!$this->validateBalance($user, $amount)) {
         $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_MAIN],
            ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
         );
         throw new InvalidArgumentException('Insufficient balance. Your balance: $' . number_format($wallet->balance, 2) . ', Required: $' . number_format($amount, 2));
      }

      // Refresh market and outcome data
      $market->refresh();
      $outcome->refresh();

      // Get outcome price (calculated from trades or API fallback)
      $outcomePrice = $this->getOutcomePrice($market, $outcomeName);

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

      // Get or create main wallet
      $wallet = Wallet::firstOrCreate(
         ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_MAIN],
         ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
      );

      DB::beginTransaction();

      try {
         // Deduct balance
         $balanceBefore = $wallet->balance;
         $wallet->balance -= $amount;
         $wallet->save();

         // Create trade with outcome_id (new system)
         $trade = Trade::create([
            'user_id' => $user->id,
            'market_id' => $market->id,
            'outcome_id' => $outcome->id, // Use outcome_id (new system)
            'outcome' => null, // Keep nullable for backward compatibility
            'outcome_name' => $outcome->name, // Store actual outcome name
            'amount_invested' => $amount,
            'token_amount' => $tokenAmount,
            'price_at_buy' => $outcomePrice,
            'status' => 'PENDING',
            // Legacy fields for backward compatibility
            'option' => strtolower($outcome->name),
            'amount' => $amount,
            'price' => $outcomePrice,
            'shares' => $tokenAmount,
         ]);

         // Update outcome's traded amounts (atomic operation)
         $outcome->incrementTradeData($amount, $tokenAmount);

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
            'description' => "Bought {$outcome->name} on market: {$market->question}",
            'metadata' => [
               'trade_id' => $trade->id,
               'market_id' => $market->id,
               'outcome_id' => $outcome->id,
               'outcome_name' => $outcome->name,
               'amount_invested' => $amount,
               'token_amount' => $tokenAmount,
               'price_at_buy' => $outcomePrice,
            ]
         ]);

         // Increment internal volume and liquidity for this market
         // This ensures user trades contribute to market metrics
         // Uses database lock to prevent race conditions
         // Must be done within transaction to ensure consistency
         $market->incrementInternalTradeData($amount, $amount);

         DB::commit();

         // Process referral commission immediately when trade is placed
         // Commission is calculated from FULL trade amount (no deduction from user)
         // Process synchronously to ensure immediate execution
         try {
            $commissionJob = new \App\Jobs\ProcessTradeCommission($trade);
            $commissionJob->handle(); // Execute immediately, don't queue
            
            Log::info('ProcessTradeCommission processed synchronously', [
               'trade_id' => $trade->id,
            ]);
         } catch (\Exception $e) {
            // Log error but don't fail the trade creation
            Log::error('Failed to process trade commission', [
               'trade_id' => $trade->id,
               'error' => $e->getMessage(),
               'trace' => $e->getTraceAsString(),
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
         // Get or create earning wallet for trade winnings
         $earningWallet = Wallet::firstOrCreate(
            ['user_id' => $trade->user_id, 'wallet_type' => Wallet::TYPE_EARNING],
            ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
         );

         if ($trade->outcome === $finalOutcome) {
            // Trade WON - add payout to earning wallet
            $payout = $trade->token_amount * 1.00; // Full payout at $1.00 per token

            $trade->status = 'WON';
            $trade->payout = $payout;
            $trade->payout_amount = $payout; // Legacy field
            $trade->settled_at = now();
            $trade->save();

            // Add payout to earning wallet
            $balanceBefore = $earningWallet->balance;
            $earningWallet->balance += $payout;
            $earningWallet->save();

            // Create wallet transaction for earning wallet
            WalletTransaction::create([
               'user_id' => $trade->user_id,
               'wallet_id' => $earningWallet->id,
               'type' => 'trade_payout',
               'amount' => $payout,
               'balance_before' => $balanceBefore,
               'balance_after' => $earningWallet->balance,
               'reference_type' => Trade::class,
               'reference_id' => $trade->id,
               'description' => "Trade payout: {$trade->outcome} on market: {$market->question}",
               'metadata' => [
                  'trade_id' => $trade->id,
                  'market_id' => $market->id,
                  'outcome' => $trade->outcome,
                  'payout' => $payout,
                  'profit' => $payout - $trade->amount_invested,
                  'wallet_type' => Wallet::TYPE_EARNING,
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
   public function getTradePreview(Market $market, string $outcomeName, float $amount): array
   {
      // Get outcome model
      $outcome = $this->getOutcomeByName($market, $outcomeName);

      // Validate market is open
      if (!$market->isOpenForTrading()) {
         throw new InvalidArgumentException('Market is closed for trading');
      }

      // Get outcome price
      $outcomePrice = $this->getOutcomePrice($market, $outcomeName);

      // Calculate token amount
      $tokenAmount = $this->calculateTokens($amount, $outcomePrice);

      // Calculate potential payout (if win)
      $potentialPayout = $tokenAmount * 1.00;

      // Calculate potential profit/loss
      $potentialProfit = $potentialPayout - $amount;
      $potentialProfitPercent = $amount > 0 ? ($potentialProfit / $amount) * 100 : 0;

      return [
         'outcome_id' => $outcome->id,
         'outcome' => $outcome->name,
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

