<?php

use Livewire\Volt\Volt;
use Laravel\Fortify\Features;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\BinancePayController;
use App\Http\Controllers\Backend\MetaMaskController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('profile', 'dashboard')->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');
    
    // Withdrawal request Livewire component (no route needed, used as component)

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
        Route::get('/diagnostic', [BinancePayController::class, 'diagnostic'])->name('binance.diagnostic');
    });
    Route::post('/webhook', [BinancePayController::class, 'webhook'])->name('binance.webhook');
    Route::get('/return', [BinancePayController::class, 'return'])->name('binance.return');
    Route::get('/cancel', [BinancePayController::class, 'cancel'])->name('binance.cancel');
});

Route::prefix('metamask')->middleware('auth')->group(function () {
    Route::post('/deposit/create', [MetaMaskController::class, 'createDeposit'])->name('metamask.deposit.create');
    Route::post('/transaction/verify', [MetaMaskController::class, 'verifyTransaction'])->name('metamask.transaction.verify');
    Route::post('/transaction/status', [MetaMaskController::class, 'checkTransactionStatus'])->name('metamask.transaction.status');
    Route::post('/transaction/test', [MetaMaskController::class, 'testTransaction'])->name('metamask.transaction.test');
});
