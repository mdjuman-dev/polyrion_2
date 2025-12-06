<?php

use Livewire\Volt\Volt;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\BinancePayController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('profile', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')->middleware(
        when(
            Features::canManageTwoFactorAuthentication() && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
            ['password.confirm'],
            [],
        ),
    )
        ->name('two-factor.show');
});

Route::prefix('binance')->group(function () {
    Route::middleware('auth')->group(function () {
    Route::post('/payment/create', [BinancePayController::class, 'createPayment'])->name('binance.create');
        Route::post('/manual/verify', [BinancePayController::class, 'manualVerify'])->name('binance.manual.verify');
    });
    Route::post('/webhook', [BinancePayController::class, 'webhook'])->name('binance.webhook');
    Route::get('/return', [BinancePayController::class, 'return'])->name('binance.return');
    Route::get('/cancel', [BinancePayController::class, 'cancel'])->name('binance.cancel');
});
