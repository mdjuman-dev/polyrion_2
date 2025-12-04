<?php

namespace App\Console\Commands;

use App\Models\Market;
use App\Services\TradeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SettleMarkets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'markets:settle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Settle all markets that have a final outcome but pending trades';

    protected $tradeService;

    public function __construct(TradeService $tradeService)
    {
        parent::__construct();
        $this->tradeService = $tradeService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting market settlement...');

        // Get all markets with final_outcome set
        $markets = Market::whereNotNull('final_outcome')
            ->whereHas('trades', function ($query) {
                $query->where('status', 'PENDING');
            })
            ->get();

        if ($markets->isEmpty()) {
            $this->info('No markets to settle.');
            return 0;
        }

        $this->info("Found {$markets->count()} markets to settle.");

        $totalSettled = 0;
        $totalMarkets = 0;

        foreach ($markets as $market) {
            $pendingTrades = $market->trades()->where('status', 'PENDING')->count();

            if ($pendingTrades === 0) {
                continue;
            }

            $this->info("Settling market ID {$market->id}: {$market->question} ({$pendingTrades} pending trades)");

            $settledCount = 0;
            $trades = $market->trades()->where('status', 'PENDING')->get();

            foreach ($trades as $trade) {
                try {
                    $this->tradeService->settleTrade($trade);
                    $settledCount++;
                    $totalSettled++;
                } catch (\Exception $e) {
                    $this->error("Failed to settle trade ID {$trade->id}: {$e->getMessage()}");
                    Log::error('Settlement command failed for trade', [
                        'trade_id' => $trade->id,
                        'market_id' => $market->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $totalMarkets++;
            $this->info("Settled {$settledCount} trades for market ID {$market->id}");
        }

        $this->info("Settlement complete: {$totalSettled} trades settled across {$totalMarkets} markets.");

        return 0;
    }
}

