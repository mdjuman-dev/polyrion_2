<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Http\Controllers\Backend\MarketController;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Schedule the storeEvents method to run every minute
// The method is optimized to complete within 25 seconds, so it will update frequently
// For true 30-second intervals, you would need a custom daemon or queue-based solution
Schedule::call(function () {
    $controller = new MarketController();
    $controller->storeEvents();
})
    ->name('store-events')
    ->everyMinute()
    ->withoutOverlapping(10) // Increased to 10 minutes to prevent overlapping
    ->onOneServer(); // Ensure only one server runs this if using multiple servers

// Schedule category detection for events
// Runs daily at 2 AM to detect/update categories for new events
Schedule::command('events:detect-categories')
    ->name('detect-event-categories')
    ->dailyAt('02:00')
    ->withoutOverlapping(10)
    ->onOneServer()
    ->appendOutputTo(storage_path('logs/category-detection.log'));