<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetaMaskController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Backend\BinancePayController;
use App\Http\Controllers\Frontend\HomeController;

Route::get('/test', function () {
    return ('hello');
});

// Google Login
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('auth/google/callback', [GoogleController::class, 'googleCallback'])->name('google.callback');

//Facebook Login
Route::get('auth/facebook', [FacebookController::class, 'redirectToFacebook'])->name('facebook.redirect');
Route::get('/auth/facebook/callback', [FacebookController::class, 'facebookCallback'])->name('facebook.callback');

//Home Page
Route::controller(HomeController::class)->group(function () {
    Route::get('/', 'index')->name('home');
});

