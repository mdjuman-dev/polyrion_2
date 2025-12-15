<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class GlobalSettingsController extends Controller
{
    public function setting()
    {
        // Get all settings grouped by category
        $settings = GlobalSetting::getAllSettings();

        // General Settings
        $generalSettings = [
            'app_name' => $settings['app_name'] ?? config('app.name'),
            'app_url' => $settings['app_url'] ?? config('app.url'),
            'app_locale' => $settings['app_locale'] ?? config('app.locale'),
            'app_timezone' => $settings['app_timezone'] ?? config('app.timezone'),
            'app_theme' => $settings['app_theme'] ?? 'all',
            'commission_type' => $settings['commission_type'] ?? 'commission',
            'commission_percentage' => $settings['commission_percentage'] ?? '0',
            'contact_email' => $settings['contact_email'] ?? '',
            'logo' => $settings['logo'] ?? null,
            'favicon' => $settings['favicon'] ?? null,
        ];

        // reCaptcha Settings
        $recaptchaSettings = [
            'site_key' => $settings['recaptcha_site_key'] ?? '',
            'secret_key' => $settings['recaptcha_secret_key'] ?? '',
        ];

        // Tawk Settings
        $tawkSettings = [
            'widget_code' => $settings['tawk_widget_code'] ?? '',
        ];

        // Analytics Settings
        $analyticsSettings = [
            'tracking_id' => $settings['ga_tracking_id'] ?? '',
        ];

        // Facebook Pixel Settings
        $facebookPixelSettings = [
            'pixel_id' => $settings['fb_pixel_id'] ?? '',
        ];

        // Binance API Settings
        $binanceSettings = [
            'binance_api_key' => $settings['binance_api_key'] ?? config('services.binance.api_key'),
            'binance_secret_key' => $settings['binance_secret_key'] ?? config('services.binance.secret_key'),
            'binance_base_url' => $settings['binance_base_url'] ?? config('services.binance.base_url'),
        ];

        // Google OAuth Settings
        $googleSettings = [
            'google_client_id' => $settings['google_client_id'] ?? config('services.google.client_id'),
            'google_client_secret' => $settings['google_client_secret'] ?? config('services.google.client_secret'),
            'google_redirect' => $settings['google_redirect'] ?? config('services.google.redirect'),
        ];

        // Facebook OAuth Settings
        $facebookSettings = [
            'facebook_client_id' => $settings['facebook_client_id'] ?? config('services.facebook.client_id'),
            'facebook_client_secret' => $settings['facebook_client_secret'] ?? config('services.facebook.client_secret'),
            'facebook_redirect' => $settings['facebook_redirect'] ?? config('services.facebook.redirect'),
        ];

        // Mail Settings
        $mailSettings = [
            'mail_mailer' => $settings['mail_mailer'] ?? env('MAIL_MAILER', config('mail.default')),
            'mail_host' => $settings['mail_host'] ?? env('MAIL_HOST', config('mail.mailers.smtp.host', '127.0.0.1')),
            'mail_port' => $settings['mail_port'] ?? env('MAIL_PORT', config('mail.mailers.smtp.port', 2525)),
            'mail_username' => $settings['mail_username'] ?? env('MAIL_USERNAME', config('mail.mailers.smtp.username')),
            'mail_password' => $settings['mail_password'] ?? env('MAIL_PASSWORD', config('mail.mailers.smtp.password')),
            'mail_encryption' => $settings['mail_encryption'] ?? env('MAIL_ENCRYPTION', config('mail.mailers.smtp.scheme')),
            'mail_from_address' => $settings['mail_from_address'] ?? env('MAIL_FROM_ADDRESS', config('mail.from.address', 'hello@example.com')),
            'mail_from_name' => $settings['mail_from_name'] ?? env('MAIL_FROM_NAME', config('mail.from.name', 'Example')),
        ];

        // AWS SES Settings
        $awsSettings = [
            'aws_access_key_id' => $settings['aws_access_key_id'] ?? config('services.ses.key'),
            'aws_secret_access_key' => $settings['aws_secret_access_key'] ?? config('services.ses.secret'),
            'aws_default_region' => $settings['aws_default_region'] ?? config('services.ses.region'),
        ];

        // Postmark Settings
        $postmarkSettings = [
            'postmark_api_key' => $settings['postmark_api_key'] ?? config('services.postmark.key'),
        ];

        // Resend Settings
        $resendSettings = [
            'resend_api_key' => $settings['resend_api_key'] ?? config('services.resend.key'),
        ];

        return view('backend.settings.global_settings', compact(
            'generalSettings',
            'binanceSettings',
            'googleSettings',
            'facebookSettings',
            'mailSettings',
            'awsSettings',
            'postmarkSettings',
            'resendSettings',
            'recaptchaSettings',
            'tawkSettings',
            'analyticsSettings',
            'facebookPixelSettings',
            'settings'
        ));
    }

    public function settingUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'app_name' => 'nullable|string|max:255',
            'app_url' => 'nullable|url',
            'app_locale' => 'nullable|string|max:10',
            'app_timezone' => 'nullable|string|max:50',
            'app_theme' => 'nullable|string|in:all,light,dark',
            'commission_type' => 'nullable|string|in:commission,fixed,hybrid',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',
            'contact_email' => 'nullable|email|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg,jpeg|max:1024',
            'site_logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
            'binance_api_key' => 'nullable|string',
            'binance_secret_key' => 'nullable|string',
            'binance_base_url' => 'nullable|url',
            'google_client_id' => 'nullable|string',
            'google_client_secret' => 'nullable|string',
            'google_redirect' => 'nullable|url',
            'facebook_client_id' => 'nullable|string',
            'facebook_client_secret' => 'nullable|string',
            'facebook_redirect' => 'nullable|url',
            'mail_mailer' => 'nullable|string|in:smtp,ses,postmark,resend,sendmail,log,array',
            'mail_host' => 'nullable|string',
            'mail_port' => 'nullable|integer',
            'mail_username' => 'nullable|string',
            'mail_password' => 'nullable|string',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_from_address' => 'nullable|email',
            'mail_from_name' => 'nullable|string|max:255',
            'aws_access_key_id' => 'nullable|string',
            'aws_secret_access_key' => 'nullable|string',
            'aws_default_region' => 'nullable|string',
            'postmark_api_key' => 'nullable|string',
            'resend_api_key' => 'nullable|string',
            'recaptcha_site_key' => 'nullable|string',
            'recaptcha_secret_key' => 'nullable|string',
            'tawk_widget_code' => 'nullable|string',
            'ga_tracking_id' => 'nullable|string',
            'fb_pixel_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = 'logo.' . $logo->getClientOriginalExtension();
            $logoPath = $logo->storeAs('settings', $logoName, 'public');

            // Delete old logo if exists
            $oldLogo = GlobalSetting::getValue('logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            GlobalSetting::setValue('logo', $logoPath);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon');
            $faviconName = 'favicon.' . $favicon->getClientOriginalExtension();
            $faviconPath = $favicon->storeAs('settings', $faviconName, 'public');

            // Delete old favicon if exists
            $oldFavicon = GlobalSetting::getValue('favicon');
            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                Storage::disk('public')->delete($oldFavicon);
            }

            GlobalSetting::setValue('favicon', $faviconPath);
        }

        // Handle site_logo upload
        if ($request->hasFile('site_logo')) {
            $siteLogo = $request->file('site_logo');
            $siteLogoName = 'site_logo.' . $siteLogo->getClientOriginalExtension();
            $siteLogoPath = $siteLogo->storeAs('settings', $siteLogoName, 'public');

            // Delete old site logo if exists
            $oldSiteLogo = GlobalSetting::getValue('site_logo');
            if ($oldSiteLogo && Storage::disk('public')->exists($oldSiteLogo)) {
                Storage::disk('public')->delete($oldSiteLogo);
            }

            GlobalSetting::setValue('site_logo', $siteLogoPath);
        }

        // Update all settings
        $settingsToUpdate = [
            // General
            'app_name',
            'app_url',
            'app_locale',
            'app_timezone',
            'app_theme',
            'commission_type',
            'commission_percentage',
            'contact_email',
            // Binance
            'binance_api_key',
            'binance_secret_key',
            'binance_base_url',
            // Google
            'google_client_id',
            'google_client_secret',
            'google_redirect',
            // Facebook
            'facebook_client_id',
            'facebook_client_secret',
            'facebook_redirect',
            // Mail
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
            // AWS
            'aws_access_key_id',
            'aws_secret_access_key',
            'aws_default_region',
            // Postmark
            'postmark_api_key',
            // Resend
            'resend_api_key',
            // reCaptcha
            'recaptcha_site_key',
            'recaptcha_secret_key',
            // Tawk
            'tawk_widget_code',
            // Analytics
            'ga_tracking_id',
            // Facebook Pixel
            'fb_pixel_id',
        ];

        foreach ($settingsToUpdate as $key) {
            if ($request->has($key) && $request->input($key) !== null) {
                GlobalSetting::setValue($key, $request->input($key));
            }
        }

        return redirect()->route('admin.setting')
            ->with('success', 'Settings updated successfully!');
    }
}
