<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\GlobalSetting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Share global settings with all views
        View::composer('*', function ($view) {
            $view->with([
                'appName' => GlobalSetting::getValue('app_name') ?? config('app.name', 'Polyrion'),
                'appUrl' => GlobalSetting::getValue('app_url') ?? config('app.url', url('/')),
                'favicon' => GlobalSetting::getValue('favicon'),
                'logo' => GlobalSetting::getValue('logo'),
                'gaTrackingId' => GlobalSetting::getValue('ga_tracking_id'),
                'fbPixelId' => GlobalSetting::getValue('fb_pixel_id'),
                'tawkWidgetCode' => GlobalSetting::getValue('tawk_widget_code'),
            ]);
        });
    }
}
