<?php

namespace App\Console\Commands;

use App\Http\Controllers\Backend\MarketController;
use Illuminate\Console\Command;

class StoreEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:store';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store events from Polymarket API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fetch events from Polymarket API...');

        try {
            $controller = new MarketController();
            $controller->storeEvents();

            $this->info('Events fetch completed successfully!');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Failed to fetch events: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
