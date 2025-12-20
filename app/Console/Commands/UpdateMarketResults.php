<?php

namespace App\Console\Commands;

use App\Models\Market;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateMarketResults extends Command
{
    protected $signature = 'markets:update-results {--dry-run : Show what would be updated without actually updating}';
    protected $description = 'Update market final results from lastTradePrice and outcomePrices (Polymarket method)';

    public function handle()
    {
        $this->info('=== Updating Market Results ===');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('DRY RUN MODE - No changes will be saved');
            $this->newLine();
        }

        // Get markets that are closed but don't have final_outcome set
        $markets = Market::where(function ($query) {
            $query->where('closed', true)
                  ->orWhere('is_closed', true)
                  ->orWhereNotNull('close_time');
        })
        ->where(function ($query) {
            $query->whereNull('final_outcome')
                  ->whereNull('final_result')
                  ->whereNull('outcome_result');
        })
        ->whereNotNull('last_trade_price')
        ->whereNotNull('outcome_prices')
        ->whereNotNull('outcomes')
        ->get();

        $this->info("Found {$markets->count()} markets to process");
        $this->newLine();

        if ($markets->count() === 0) {
            $this->info('No markets need updating.');
            return Command::SUCCESS;
        }

        $updated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($markets as $market) {
            try {
                $outcome = $market->determineOutcomeFromLastTradePrice();

                if ($outcome) {
                    $outcomeResult = strtolower($outcome);
                    
                    if ($dryRun) {
                        $this->line("Would update: Market #{$market->id} - {$market->question}");
                        $this->line("  → Final Outcome: {$outcome}");
                        $this->line("  → Last Trade Price: {$market->last_trade_price}");
                        $updated++;
                    } else {
                        $market->final_outcome = $outcome;
                        $market->outcome_result = $outcomeResult;
                        $market->final_result = $outcomeResult;
                        $market->result_set_at = $market->result_set_at ?? now();
                        $market->closed = true;
                        $market->is_closed = true;
                        $market->save();

                        $this->info("✓ Updated Market #{$market->id}: {$outcome}");
                        $updated++;
                    }
                } else {
                    $this->warn("⚠ Skipped Market #{$market->id}: Could not determine outcome");
                    $skipped++;
                }
            } catch (\Exception $e) {
                $this->error("✗ Error processing Market #{$market->id}: {$e->getMessage()}");
                Log::error('Failed to update market result', [
                    'market_id' => $market->id,
                    'error' => $e->getMessage(),
                ]);
                $errors++;
            }
        }

        $this->newLine();
        $this->info('=== Summary ===');
        $this->table(
            ['Status', 'Count'],
            [
                ['Updated', $updated],
                ['Skipped', $skipped],
                ['Errors', $errors],
                ['Total', $markets->count()],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('Run without --dry-run to apply changes');
        } else {
            $this->newLine();
            $this->info("✅ Successfully updated {$updated} markets");
        }

        return Command::SUCCESS;
    }
}

