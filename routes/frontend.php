<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetaMaskController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\WalletController;
use App\Http\Controllers\Backend\BinancePayController;

// Google Login
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'googleCallback'])->name('google.callback');

//Facebook Login
Route::get('auth/facebook', [FacebookController::class, 'redirectToFacebook'])->name('facebook.redirect');
Route::get('/auth/facebook/callback', [FacebookController::class, 'facebookCallback'])->name('facebook.callback');

//Home Page
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
    Route::get('/trending', 'trending')->name('trending');
    Route::get('/breaking', 'breaking')->name('breaking');
    Route::get('/new', 'newEvents')->name('new');
    Route::get('/category/{category}', 'eventsByCategory')->name('events.by.category');
    Route::get('/market/details/{slug}', 'marketDetails')->name('market.details');
    Route::get('/tag/{slug}', 'eventsByTag')->name('events.by.tag');
    Route::get('/saved-events', 'savedEvents')->name('saved.events')->middleware(['auth']);
    Route::get('/profile', 'profile')->name('profile')->middleware(['auth']);
});
Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->middleware(['auth'])->group(function () {
    Route::get('/', 'profile')->name('index');
    Route::get('/settings', 'settings')->name('settings');
});

// Wallet routes
Route::controller(WalletController::class)->prefix('wallet')->name('wallet.')->middleware(['auth'])->group(function () {
    Route::post('/deposit', 'deposit')->name('deposit');
});
