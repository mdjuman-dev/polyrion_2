<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\GlobalSettingsController;
use App\Http\Controllers\Backend\HomeController;
use App\Http\Controllers\Backend\MarketController;
use App\Http\Controllers\Backend\EventController;
use App\Http\Controllers\Backend\CommentController;
use App\Http\Controllers\Backend\BinancePayController;
use App\Http\Controllers\Backend\RolePermissionController;
use App\Http\Controllers\Backend\UserController;

// Admin Login routes
Route::prefix('/admin')->name('admin.')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    // Return to admin route (accessible without admin auth when impersonating)
    Route::post('/users/return-to-admin', [UserController::class, 'returnToAdmin'])->name('users.return-to-admin');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('backend.dashboard');
        Route::post('/search', [HomeController::class, 'search'])->name('search');

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Market Management Routes
        Route::controller(MarketController::class)->group(function () {
            Route::get('/market', 'index')->name('market.index');
            Route::post('/market/search', 'search')->name('market.search');
            Route::get('/market/list', 'marketList')->name('market.list');
            Route::get('/market/show/{id}', 'show')->name('market.show');
            Route::get('/market/save/{slug}', 'marketSave')->name('market.save');
            Route::post('/market/store', 'store')->name('market.store');
            Route::get('/market/edit/{id}', 'edit')->name('market.edit');
            Route::put('/market/update/{id}', 'update')->name('market.update');
            Route::delete('/market/delete/{id}', 'delete')->name('market.delete');

            // Trading management
            Route::post('/market/{id}/set-result', 'setResult')->name('market.set-result');
            Route::post('/market/{id}/settle-trades', 'settleTrades')->name('market.settle-trades');

            Route::get('/event/fetch', 'storeEvents')->name('event.fetch');
        });

        // Event Management Routes
        Route::controller(EventController::class)->group(function () {
            Route::get('/events', 'index')->name('events.index');
            Route::get('/events/create-with-markets', 'createWithMarkets')->name('events.create-with-markets');
            Route::post('/events', 'store')->name('events.store');
            Route::post('/events/with-markets', 'storeWithMarkets')->name('events.store-with-markets');
            Route::get('/events/{event}', 'show')->name('events.show');
            Route::get('/events/{event}/edit', 'edit')->name('events.edit');
            Route::put('/events/{event}', 'update')->name('events.update');
            Route::delete('/events/{event}', 'destroy')->name('events.destroy');
            Route::get('/events/{event}/add-markets', 'addMarkets')->name('events.add-markets');
            Route::post('/events/{event}/markets', 'storeMarkets')->name('events.store-markets');
        });

        // Comment Management Routes
        Route::controller(CommentController::class)->group(function () {
            Route::delete('/comments/{id}', 'destroy')->name('comments.destroy');
            Route::post('/comments/{id}/toggle-status', 'toggleStatus')->name('comments.toggle-status');
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

        // Withdrawal Management Routes
        Route::controller(\App\Http\Controllers\Backend\WithdrawalController::class)->prefix('withdrawal')->name('withdrawal.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}/approve', 'approve')->name('approve');
            Route::post('/{id}/reject', 'reject')->name('reject');
            Route::post('/{id}/processing', 'processing')->name('processing');
        });

        // User Management Routes
        Route::controller(UserController::class)->prefix('users')->name('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}/login-as', 'loginAsUser')->name('login-as');
            Route::post('/{id}/update-status', 'updateStatus')->name('update-status');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Roles and Permissions Management Routes
        Route::controller(RolePermissionController::class)->group(function () {
            // Roles Routes
            Route::prefix('roles')->name('roles.')->middleware('role:admin,admin')->group(function () {
                Route::get('/', 'roles')->name('index');
                Route::get('/create', 'createRole')->name('create');
                Route::post('/', 'storeRole')->name('store');
                Route::get('/{id}/edit', 'editRole')->name('edit');
                Route::put('/{id}', 'updateRole')->name('update');
                Route::delete('/{id}', 'destroyRole')->name('destroy');
            });

            // Permissions Routes
            Route::prefix('permissions')->name('permissions.')->middleware('role:admin,admin')->group(function () {
                Route::get('/', 'permissions')->name('index');
                Route::get('/create', 'createPermission')->name('create');
                Route::post('/', 'storePermission')->name('store');
                Route::get('/{id}/edit', 'editPermission')->name('edit');
                Route::put('/{id}', 'updatePermission')->name('update');
                Route::delete('/{id}', 'destroyPermission')->name('destroy');
            });
        });
    });
});
