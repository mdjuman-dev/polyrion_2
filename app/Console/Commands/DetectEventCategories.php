<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Services\CategoryDetector;
use Illuminate\Console\Command;

class DetectEventCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:detect-categories 
                            {--force : Force update even if category already exists}
                            {--category= : Only process events with specific category}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detect and update categories for events based on their titles';

    /**
     * Execute the console command.
     */
    public function handle(CategoryDetector $detector): int
    {
        $this->info('Starting category detection...');

        $query = Event::query();

        // Filter by category if specified
        if ($this->option('category')) {
            $query->where('category', $this->option('category'));
        }

        // Only process events without category if --force is not used
        if (!$this->option('force')) {
            $query->whereNull('category')->orWhere('category', '');
        }

        $events = $query->get();
        $total = $events->count();

        if ($total === 0) {
            $this->warn('No events found to process.');
            return Command::FAILURE;
        }

        $this->info("Found {$total} event(s) to process.");

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updated = 0;
        $unchanged = 0;
        $stats = [];

        foreach ($events as $event) {
            if (empty($event->title)) {
                $bar->advance();
                continue;
            }

            $oldCategory = $event->category;
            $newCategory = $detector->detect($event->title);

            if ($oldCategory !== $newCategory) {
                $event->category = $newCategory;
                $event->save();
                $updated++;

                // Track category changes
                if (!isset($stats[$newCategory])) {
                    $stats[$newCategory] = 0;
                }
                $stats[$newCategory]++;
            } else {
                $unchanged++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Display results
        $this->info("Processing complete!");
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Processed', $total],
                ['Updated', $updated],
                ['Unchanged', $unchanged],
            ]
        );

        if (!empty($stats)) {
            $this->info("\nCategory Distribution:");
            $this->table(
                ['Category', 'Count'],
                collect($stats)->map(fn($count, $category) => [$category, $count])->toArray()
            );
        }

        return Command::SUCCESS;
    }
}

