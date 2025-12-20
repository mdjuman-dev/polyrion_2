<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Models\Market;
use Illuminate\Console\Command;

class CleanClosedEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:clean-closed 
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove closed, archived, or inactive events from database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        // Find events to delete
        $closedEvents = Event::where('closed', true)->get();
        $archivedEvents = Event::where('archived', true)->get();
        $inactiveEvents = Event::where('active', false)->get();

        // Combine and get unique events
        $eventsToDelete = $closedEvents->merge($archivedEvents)->merge($inactiveEvents)->unique('id');

        $count = $eventsToDelete->count();

        if ($count === 0) {
            $this->info('No closed, archived, or inactive events found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$count} events to delete:");
        $this->line("  - Closed: {$closedEvents->count()}");
        $this->line("  - Archived: {$archivedEvents->count()}");
        $this->line("  - Inactive: {$inactiveEvents->count()}");

        if ($dryRun) {
            $this->warn('DRY RUN MODE - No events will be deleted.');
            $this->line('');
            $this->line('Events that would be deleted:');
            foreach ($eventsToDelete->take(10) as $event) {
                $this->line("  - [{$event->id}] {$event->title} (closed: " . ($event->closed ? 'yes' : 'no') . ", archived: " . ($event->archived ? 'yes' : 'no') . ", active: " . ($event->active ? 'yes' : 'no') . ")");
            }
            if ($count > 10) {
                $this->line("  ... and " . ($count - 10) . " more");
            }
            return Command::SUCCESS;
        }

        if (!$force) {
            if (!$this->confirm("Are you sure you want to delete {$count} events? This will also delete all associated markets.", false)) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        $deleted = 0;
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($eventsToDelete as $event) {
            try {
                // Delete event (markets will be deleted via cascade)
                $event->delete();
                $deleted++;
            } catch (\Exception $e) {
                $this->error("Failed to delete event {$event->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully deleted {$deleted} events.");

        return Command::SUCCESS;
    }
}

