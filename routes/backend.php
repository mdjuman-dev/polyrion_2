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
use App\Http\Controllers\Backend\AdminController;
use App\Http\Controllers\Backend\PaymentSettingsController;

// Admin Login routes
Route::prefix('/admin')->name('admin.')->group(function () {

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');

    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');

    // Return to admin route (accessible without admin auth when impersonating)
    Route::post('/users/return-to-admin', [UserController::class, 'returnToAdmin'])->name('users.return-to-admin');

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [HomeController::class, 'dashboard'])->middleware('permission:view dashboard,admin')->name('backend.dashboard');
        Route::post('/search', [HomeController::class, 'search'])->middleware('permission:view dashboard,admin')->name('search');

        Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

        // Market Management Routes
        Route::controller(MarketController::class)->middleware('permission:view markets,admin')->group(function () {
            Route::get('/market', 'index')->name('market.index');
            Route::post('/market/search', 'search')->name('market.search');
            Route::get('/market/list', 'marketList')->name('market.list');
            Route::get('/market/show/{id}', 'show')->name('market.show');
            Route::get('/market/save/{slug}', 'marketSave')->name('market.save');
            Route::post('/market/store', 'store')->middleware('permission:create markets,admin')->name('market.store');
            Route::get('/market/edit/{id}', 'edit')->middleware('permission:edit markets,admin')->name('market.edit');
            Route::put('/market/update/{id}', 'update')->middleware('permission:edit markets,admin')->name('market.update');
            Route::delete('/market/delete/{id}', 'delete')->middleware('permission:delete markets,admin')->name('market.delete');

            // Trading management
            Route::post('/market/{id}/set-result', 'setResult')->middleware('permission:settle markets,admin')->name('market.set-result');
            Route::post('/market/{id}/settle-trades', 'settleTrades')->middleware('permission:settle markets,admin')->name('market.settle-trades');

            Route::get('/event/fetch', 'storeEvents')->middleware('permission:create events,admin')->name('event.fetch');
        });

        // Event Management Routes
        Route::controller(EventController::class)->middleware('permission:view events,admin')->group(function () {
            Route::get('/events', 'index')->name('events.index');
            Route::get('/events/create', 'create')->middleware('permission:create events,admin')->name('events.create');
            Route::get('/events/create-with-markets', 'createWithMarkets')->middleware('permission:create events,admin')->name('events.create-with-markets');
            Route::post('/events', 'store')->middleware('permission:create events,admin')->name('events.store');
            Route::post('/events/with-markets', 'storeWithMarkets')->middleware('permission:create events,admin')->name('events.store-with-markets');
            Route::get('/events/{event}', 'show')->name('events.show');
            Route::get('/events/{event}/edit', 'edit')->middleware('permission:edit events,admin')->name('events.edit');
            Route::put('/events/{event}', 'update')->middleware('permission:edit events,admin')->name('events.update');
            Route::delete('/events/{event}', 'destroy')->middleware('permission:delete events,admin')->name('events.destroy');
            Route::get('/events/{event}/add-markets', 'addMarkets')->middleware('permission:create markets,admin')->name('events.add-markets');
            Route::post('/events/{event}/markets', 'storeMarkets')->middleware('permission:create markets,admin')->name('events.store-markets');
        });

        // Comment Management Routes
        Route::controller(CommentController::class)->middleware('permission:manage events,admin')->group(function () {
            Route::delete('/comments/{id}', 'destroy')->name('comments.destroy');
            Route::post('/comments/{id}/toggle-status', 'toggleStatus')->name('comments.toggle-status');
        });

        //Global Setting Management Routes
        Route::controller(GlobalSettingsController::class)->middleware('permission:manage global settings,admin')->group(function () {
            Route::get('/setting', 'setting')->name('setting');
            Route::post('/setting/update', 'settingUpdate')->name('setting.update');
        });

        // Payment Settings Routes
        Route::controller(PaymentSettingsController::class)->prefix('payment')->name('payment.')->middleware('permission:manage payment settings,admin')->group(function () {
            Route::get('/settings', 'index')->name('settings');
            Route::post('/settings', 'update')->name('settings.update');
        });

        // Binance Pay Management Routes
        Route::controller(BinancePayController::class)->middleware('permission:approve deposits,admin')->group(function () {
            Route::post('/deposit/{depositId}/process', 'manualProcess')->name('deposit.manual.process');
        });

        // Deposit Management Routes
        Route::controller(\App\Http\Controllers\Backend\DepositController::class)->prefix('deposits')->name('deposits.')->middleware('permission:view deposits,admin')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}/approve', 'approve')->middleware('permission:approve deposits,admin')->name('approve');
            Route::post('/{id}/reject', 'reject')->middleware('permission:reject deposits,admin')->name('reject');
        });

        // Withdrawal Management Routes
        Route::controller(\App\Http\Controllers\Backend\WithdrawalController::class)->prefix('withdrawal')->name('withdrawal.')->middleware('permission:view withdrawals,admin')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}/approve', 'approve')->middleware('permission:approve withdrawals,admin')->name('approve');
            Route::post('/{id}/reject', 'reject')->middleware('permission:reject withdrawals,admin')->name('reject');
            Route::post('/{id}/processing', 'processing')->middleware('permission:process withdrawals,admin')->name('processing');
        });

        // Admin User Management Routes
        Route::controller(AdminController::class)->prefix('admins')->name('admins.')->middleware('permission:manage roles,admin')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->middleware('permission:create roles,admin')->name('create');
            Route::post('/', 'store')->middleware('permission:create roles,admin')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->middleware('permission:edit roles,admin')->name('edit');
            Route::put('/{id}', 'update')->middleware('permission:edit roles,admin')->name('update');
            Route::delete('/{id}', 'destroy')->middleware('permission:delete roles,admin')->name('destroy');
        });

        // User Management Routes
        Route::controller(UserController::class)->prefix('users')->name('users.')->middleware('permission:view users,admin')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}/login-as', 'loginAsUser')->middleware('permission:edit users,admin')->name('login-as');
            Route::post('/{id}/update-status', 'updateStatus')->middleware('permission:edit users,admin')->name('update-status');
            Route::post('/{id}/test-deposit', 'addTestDeposit')->middleware('permission:approve deposits,admin')->name('test-deposit');
            Route::delete('/{id}', 'destroy')->middleware('permission:delete users,admin')->name('destroy');
        });

        // Roles and Permissions Management Routes
        Route::controller(RolePermissionController::class)->group(function () {
            // Roles Routes
            Route::prefix('roles')->name('roles.')->middleware('permission:view roles,admin')->group(function () {
                Route::get('/', 'roles')->name('index');
                Route::get('/create', 'createRole')->middleware('permission:create roles,admin')->name('create');
                Route::post('/', 'storeRole')->middleware('permission:create roles,admin')->name('store');
                Route::get('/{id}/edit', 'editRole')->middleware('permission:edit roles,admin')->name('edit');
                Route::put('/{id}', 'updateRole')->middleware('permission:edit roles,admin')->name('update');
                Route::delete('/{id}', 'destroyRole')->middleware('permission:delete roles,admin')->name('destroy');
            });

            // Permissions Routes
            Route::prefix('permissions')->name('permissions.')->middleware('permission:view permissions,admin')->group(function () {
                Route::get('/', 'permissions')->name('index');
                Route::get('/create', 'createPermission')->middleware('permission:create permissions,admin')->name('create');
                Route::post('/', 'storePermission')->middleware('permission:create permissions,admin')->name('store');
                Route::get('/{id}/edit', 'editPermission')->middleware('permission:edit permissions,admin')->name('edit');
                Route::put('/{id}', 'updatePermission')->middleware('permission:edit permissions,admin')->name('update');
                Route::delete('/{id}', 'destroyPermission')->middleware('permission:delete permissions,admin')->name('destroy');
            });
        });
    });
});

