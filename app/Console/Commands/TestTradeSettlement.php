<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Market;
use App\Models\Trade;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\SettlementService;
use App\Services\TradeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TestTradeSettlement extends Command
{
    protected $signature = 'test:trade-settlement';
    protected $description = 'Test complete trade settlement flow: buy trade → close market → settle → verify wallet balance';

    public function handle()
    {
        $this->info('=== Trade Settlement Test ===');
        $this->newLine();

        DB::beginTransaction();

        try {
            // Step 1: Create or get test user
            $this->info('Step 1: Creating test user...');
            $userData = [
                'name' => 'Test Trader',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ];
            
            // Add username if field exists
            if (Schema::hasColumn('users', 'username')) {
                $userData['username'] = 'testtrader';
            }
            
            $user = User::firstOrCreate(
                ['email' => 'test-trader@example.com'],
                $userData
            );
            $this->info("✓ User created/found: {$user->name} (ID: {$user->id})");

            // Step 2: Add initial balance to main wallet
            $this->info('Step 2: Setting up main wallet...');
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $user->id, 'wallet_type' => Wallet::TYPE_MAIN],
                ['balance' => 0, 'currency' => 'USDT', 'status' => 'active']
            );
            $initialBalance = 100.00;
            $wallet->balance = $initialBalance;
            $wallet->save();
            $this->info("✓ Wallet balance set to: $" . number_format($initialBalance, 2));

            // Step 3: Create test event and market
            $this->info('Step 3: Creating test event and market...');
            
            // Create or get test event
            $event = Event::firstOrCreate(
                ['slug' => 'test-trade-settlement-event'],
                [
                    'title' => 'Test Trade Settlement Event',
                    'description' => 'Test event for trade settlement',
                    'active' => true,
                ]
            );
            $this->info("✓ Event created/found: {$event->title} (ID: {$event->id})");
            
            $market = Market::create([
                'event_id' => $event->id,
                'question' => 'Test Market: Will this trade settlement work?',
                'slug' => 'test-trade-settlement-' . time(),
                'active' => true,
                'closed' => false,
                'is_closed' => false,
                'settled' => false,
                'outcome_prices' => json_encode([0.50, 0.50]), // NO: 0.50, YES: 0.50
                'best_bid' => 0.50,
                'best_ask' => 0.50,
            ]);
            $this->info("✓ Market created: {$market->question} (ID: {$market->id})");

            // Step 4: Place a trade
            $this->info('Step 4: Placing trade...');
            $tradeService = app(TradeService::class);
            $tradeAmount = 20.00;
            $tradeOutcome = 'YES';

            $trade = $tradeService->createTrade($user, $market, $tradeOutcome, $tradeAmount);
            $this->info("✓ Trade placed:");
            $this->line("  - Outcome: {$trade->outcome}");
            $this->line("  - Amount invested: $" . number_format($trade->amount_invested, 2));
            $this->line("  - Token amount: " . number_format($trade->token_amount, 4));
            $this->line("  - Price at buy: " . number_format($trade->price_at_buy, 4));
            $this->line("  - Status: {$trade->status}");

            // Check wallet balance after trade
            $wallet->refresh();
            $balanceAfterTrade = $wallet->balance;
            $this->info("✓ Wallet balance after trade: $" . number_format($balanceAfterTrade, 2));
            $this->line("  - Expected: $" . number_format($initialBalance - $tradeAmount, 2));
            
            if (abs($balanceAfterTrade - ($initialBalance - $tradeAmount)) > 0.01) {
                $this->error("✗ Balance mismatch! Expected: $" . ($initialBalance - $tradeAmount) . ", Got: $" . $balanceAfterTrade);
                DB::rollBack();
                return 1;
            }

            // Step 5: Set market result (user wins)
            $this->info('Step 5: Setting market result (user wins)...');
            $market->outcome_result = 'yes';
            $market->final_outcome = 'YES';
            $market->final_result = 'yes';
            $market->closed = true;
            $market->is_closed = true;
            $market->settled = false;
            $market->result_set_at = now();
            $market->save();
            $this->info("✓ Market result set to: YES (User's trade outcome)");

            // Step 6: Settle the market
            $this->info('Step 6: Settling market...');
            $settlementService = app(SettlementService::class);
            $settlementResult = $settlementService->settleMarket($market->id);
            
            if (!$settlementResult) {
                $this->error('✗ Settlement failed!');
                DB::rollBack();
                return 1;
            }

            $this->info("✓ Market settled successfully");

            // Step 7: Verify trade status
            $this->info('Step 7: Verifying trade status...');
            $trade->refresh();
            $this->line("  - Trade status: {$trade->status}");
            $this->line("  - Payout: $" . number_format($trade->payout, 2));
            $this->line("  - Settled at: " . ($trade->settled_at ? $trade->settled_at->format('Y-m-d H:i:s') : 'N/A'));

            if ($trade->status !== 'WON') {
                $this->error("✗ Trade status should be WON, got: {$trade->status}");
                DB::rollBack();
                return 1;
            }

            // Step 8: Verify wallet balance
            $this->info('Step 8: Verifying wallet balance...');
            $wallet->refresh();
            $finalBalance = $wallet->balance;
            $expectedPayout = $trade->token_amount * 1.00;
            $expectedFinalBalance = $balanceAfterTrade + $expectedPayout;

            $this->line("  - Balance before settlement: $" . number_format($balanceAfterTrade, 2));
            $this->line("  - Payout amount: $" . number_format($expectedPayout, 2));
            $this->line("  - Expected final balance: $" . number_format($expectedFinalBalance, 2));
            $this->line("  - Actual final balance: $" . number_format($finalBalance, 2));

            if (abs($finalBalance - $expectedFinalBalance) > 0.01) {
                $this->error("✗ Balance mismatch!");
                $this->error("  Expected: $" . number_format($expectedFinalBalance, 2));
                $this->error("  Got: $" . number_format($finalBalance, 2));
                DB::rollBack();
                return 1;
            }

            $this->info("✓ Wallet balance updated correctly!");

            // Step 9: Verify wallet transaction
            $this->info('Step 9: Verifying wallet transaction...');
            $payoutTransaction = WalletTransaction::where('reference_type', Trade::class)
                ->where('reference_id', $trade->id)
                ->where('type', 'trade_payout')
                ->first();

            if (!$payoutTransaction) {
                $this->error('✗ Payout transaction not found!');
                DB::rollBack();
                return 1;
            }

            $this->info("✓ Payout transaction created:");
            $this->line("  - Type: {$payoutTransaction->type}");
            $this->line("  - Amount: $" . number_format($payoutTransaction->amount, 2));
            $this->line("  - Balance before: $" . number_format($payoutTransaction->balance_before, 2));
            $this->line("  - Balance after: $" . number_format($payoutTransaction->balance_after, 2));

            // Step 10: Calculate profit
            $this->info('Step 10: Calculating profit...');
            $profit = $expectedPayout - $tradeAmount;
            $profitPercent = ($profit / $tradeAmount) * 100;
            $this->line("  - Amount invested: $" . number_format($tradeAmount, 2));
            $this->line("  - Payout received: $" . number_format($expectedPayout, 2));
            $this->line("  - Profit: $" . number_format($profit, 2) . " ({$profitPercent}%)");

            // Refresh market to get latest settled status
            $market->refresh();

            // Summary
            $this->newLine();
            $this->info('=== Test Summary ===');
            $this->table(
                ['Item', 'Value'],
                [
                    ['Initial Balance', '$' . number_format($initialBalance, 2)],
                    ['Trade Amount', '$' . number_format($tradeAmount, 2)],
                    ['Balance After Trade', '$' . number_format($balanceAfterTrade, 2)],
                    ['Token Amount', number_format($trade->token_amount, 4)],
                    ['Payout', '$' . number_format($expectedPayout, 2)],
                    ['Final Balance', '$' . number_format($finalBalance, 2)],
                    ['Profit', '$' . number_format($profit, 2)],
                    ['Trade Status', $trade->status],
                    ['Market Settled', $market->settled ? 'Yes' : 'No'],
                    ['Wallet Transaction', $payoutTransaction ? 'Created' : 'Missing'],
                ]
            );

            DB::commit();

            $this->newLine();
            $this->info('✅ All tests passed! Trade settlement is working correctly.');
            $this->info("User: {$user->email}");
            $this->info("Market ID: {$market->id}");
            $this->info("Trade ID: {$trade->id}");

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('✗ Test failed with error: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}

