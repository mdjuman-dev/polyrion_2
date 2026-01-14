<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetaMaskController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\FacebookController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\WalletController;
use App\Http\Controllers\Frontend\TradeController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\FaqController;
use App\Http\Controllers\Frontend\ContactController;
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
    // Redirect /category/sports to /sports
    Route::get('/category/sports', function() {
        return redirect()->route('sports.index', request()->query());
    })->name('category.sports.redirect');
    // Redirect /category/politics to /politics
    Route::get('/category/politics', function() {
        return redirect()->route('politics.index', request()->query());
    })->name('category.politics.redirect');
    // Redirect /category/crypto to /crypto
    Route::get('/category/crypto', function() {
        return redirect()->route('crypto.index', request()->query());
    })->name('category.crypto.redirect');
    // Redirect /category/finance to /finance
    Route::get('/category/finance', function() {
        return redirect()->route('finance.index', request()->query());
    })->name('category.finance.redirect');
    Route::get('/category/{category}', 'eventsByCategory')->name('events.by.category');
    Route::get('/market/details/{slug}', 'marketDetails')->name('market.details');
    Route::get('/market/{slug}', 'singleMarket')->name('market.single');
    Route::get('/api/market/{slug}/price-data', 'getMarketPriceData')->name('api.market.price.data');
    Route::get('/api/market/{slug}/history-data', 'getMarketHistoryData')->name('api.market.history.data');
    Route::get('/api/market/{marketId}/live-price', 'getMarketLivePrice')->name('api.market.live.price');
    Route::get('/api/event/{eventId}/comments', 'fetchEventComments')->name('api.event.comments');
    Route::get('/tag/{slug}', 'eventsByTag')->name('events.by.tag');
    Route::get('/saved-events', 'savedEvents')->name('saved.events')->middleware(['auth']);
    Route::get('/profile', 'profile')->name('profile')->middleware(['auth']);
});
Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->middleware(['auth'])->group(function () {
    Route::get('/', 'profile')->name('index');
    Route::get('/settings', 'settings')->name('settings');
    Route::put('/update', 'update')->name('update');
});

// Password update routes
Route::post('/password/send-otp', [ProfileController::class, 'sendPasswordChangeOtp'])->middleware(['auth'])->name('password.send.otp');
Route::post('/password/update', [ProfileController::class, 'updatePassword'])->middleware(['auth'])->name('user.password.update');

// Wallet routes
Route::controller(WalletController::class)->prefix('wallet')->name('wallet.')->middleware(['auth'])->group(function () {
    Route::post('/deposit', 'deposit')->name('deposit');
    Route::post('/transfer-earning-to-main', 'transferEarningToMain')->name('transfer.earning.to.main');
    Route::get('/transfer-history', 'getTransferHistory')->name('transfer.history');
});

// Withdrawal routes - Redirect to profile page (withdrawal handled via modal)
Route::controller(\App\Http\Controllers\Frontend\WithdrawalController::class)->prefix('withdrawal')->name('withdrawal.')->middleware(['auth'])->group(function () {
    Route::get('/', function() {
        return redirect()->route('profile.index')->with('open_withdrawal_modal', true);
    })->name('index');
    Route::post('/', 'store')->name('store'); // Keep for API compatibility
    Route::get('/history', function() {
        return redirect()->route('profile.index');
    })->name('history');
});

// Trading routes
Route::controller(TradeController::class)->prefix('trades')->name('trades.')->middleware(['auth'])->group(function () {
    Route::post('/market/{marketId}', 'placeTrade')->name('place');
    Route::get('/my-trades', 'myTrades')->name('my');
    Route::get('/my-trades-page', 'myTradesPage')->name('my.page'); // View page
    Route::get('/market/{marketId}', 'marketTrades')->name('market');
    Route::get('/{id}', 'getTrade')->name('show'); // Get specific trade
});

// API Trading routes (as per guide specification)
Route::prefix('api')->middleware(['auth'])->group(function () {
    Route::get('/trades', [TradeController::class, 'myTrades'])->name('api.trades');
    Route::get('/trades/{id}', [TradeController::class, 'getTrade'])->name('api.trades.show');
    Route::post('/market/{marketId}/buy', [\App\Http\Controllers\Frontend\MarketController::class, 'buy'])->name('api.market.buy');
    Route::get('/market/{marketId}/trade-preview', [\App\Http\Controllers\Frontend\MarketController::class, 'getTradePreview'])->name('api.market.trade-preview');
    Route::get('/market/{marketId}/prices', [\App\Http\Controllers\Frontend\MarketController::class, 'getMarketPrices'])->name('api.market.prices');
});

// Market trading routes (Polymarket-style)
Route::controller(\App\Http\Controllers\Frontend\MarketController::class)->prefix('market')->name('market.')->middleware(['auth'])->group(function () {
    Route::post('/{marketId}/buy', 'buy')->name('buy');
    Route::post('/{marketId}/settle', 'settleMarket')->name('settle');
    Route::get('/{marketId}/trade-preview', 'getTradePreview')->name('trade-preview');
    Route::get('/{marketId}/prices', 'getMarketPrices')->name('prices');
});

// Public Pages
Route::controller(PageController::class)->group(function () {
    Route::get('/privacy-policy', 'privacyPolicy')->name('privacy-policy');
    Route::get('/terms-of-use', 'termsOfUse')->name('terms-of-use');
});

// FAQ
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

// Contact/Support
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/support', [ContactController::class, 'index'])->name('support');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Referral Routes
Route::get('/ref/{username}', [\App\Http\Controllers\Frontend\ReferralController::class, 'referralLink'])->name('referral.link');

// Sports Routes
Route::controller(\App\Http\Controllers\Frontend\SportsController::class)->group(function () {
    Route::get('/sports', 'index')->name('sports.index');
});

// Politics Routes
Route::controller(\App\Http\Controllers\Frontend\PoliticsController::class)->group(function () {
    Route::get('/politics', 'index')->name('politics.index');
});

// Crypto Routes
Route::controller(\App\Http\Controllers\Frontend\CryptoController::class)->group(function () {
    Route::get('/crypto', 'index')->name('crypto.index');
});

// Finance Routes
Route::controller(\App\Http\Controllers\Frontend\FinanceController::class)->group(function () {
    Route::get('/finance', 'index')->name('finance.index');
});
