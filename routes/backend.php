<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\GlobalSettingsController;
use App\Http\Controllers\Backend\HomeController;
use App\Http\Controllers\Backend\MarketController;
use App\Http\Controllers\Backend\BinancePayController;

// Admin Login routes
Route::prefix('/admin')->name('admin.')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('backend.dashboard');

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Market Management Routes
        Route::controller(MarketController::class)->group(function () {
            Route::get('/market', 'index')->name('market.index');
            Route::post('/market/search', 'search')->name('market.search');
            Route::get('/market/list', 'marketList')->name('market.list');
            Route::get('/market/save/{slug}', 'marketSave')->name('market.save');
            Route::post('/market/store', 'store')->name('market.store');
            Route::get('/market/edit/{id}', 'edit')->name('market.edit');
            Route::put('/market/update/{id}', 'update')->name('market.update');
            Route::delete('/market/delete/{id}', 'delete')->name('market.delete');

            Route::get('/event', 'storeEvents');
        });

        //Global Setting Management Routes
        Route::controller(GlobalSettingsController::class)->group(function () {

            Route::get('/setting', 'setting')->name('setting');
            Route::post('/setting/update', 'settingUpdate')->name('setting.update');
        });

        // Binance Pay Management Routes
        Route::controller(BinancePayController::class)->group(function () {
            Route::post('/deposit/{depositId}/process', 'manualProcess')->name('deposit.manual.process');
        });
    });
});
