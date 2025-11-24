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
    ->withoutOverlapping(1);
