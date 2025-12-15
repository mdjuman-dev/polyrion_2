<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

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

        // Email Template Settings
        $emailTemplateSettings = [
            'reset_password_subject' => $settings['reset_password_subject'] ?? __('Reset Password Notification'),
            'reset_password_greeting' => $settings['reset_password_greeting'] ?? __('Hello :name!'),
            'reset_password_line1' => $settings['reset_password_line1'] ?? __('You are receiving this email because we received a password reset request for your account.'),
            'reset_password_action_text' => $settings['reset_password_action_text'] ?? __('Reset Password'),
            'reset_password_line2' => $settings['reset_password_line2'] ?? __('This password reset link will expire in :count minutes.'),
            'reset_password_line3' => $settings['reset_password_line3'] ?? __('If you did not request a password reset, no further action is required.'),
            'reset_password_salutation' => $settings['reset_password_salutation'] ?? __('Regards,'),
            // Welcome Email
            'welcome_email_subject' => $settings['welcome_email_subject'] ?? __('Welcome to :app_name!'),
            'welcome_email_greeting' => $settings['welcome_email_greeting'] ?? __('Hello :name!'),
            'welcome_email_message' => $settings['welcome_email_message'] ?? __('Thank you for joining :app_name! We are excited to have you on board.'),
            // Verification Email
            'verification_email_subject' => $settings['verification_email_subject'] ?? __('Verify Your Email Address'),
            'verification_email_greeting' => $settings['verification_email_greeting'] ?? __('Hello :name!'),
            'verification_email_message' => $settings['verification_email_message'] ?? __('Please click the button below to verify your email address.'),
            'verification_email_button_text' => $settings['verification_email_button_text'] ?? __('Verify Email Address'),
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
            'emailTemplateSettings',
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
        // Log file upload status BEFORE validation (to see if files are coming)
        Log::info('File upload check BEFORE validation', [
            'has_site_logo' => $request->hasFile('site_logo'),
            'has_favicon' => $request->hasFile('favicon'),
            'all_files' => array_keys($request->allFiles()),
            'request_method' => $request->method(),
            'content_type' => $request->header('Content-Type'),
            'all_input_keys' => array_keys($request->all()),
            'site_logo_size' => $request->hasFile('site_logo') ? $request->file('site_logo')->getSize() : null,
            'favicon_size' => $request->hasFile('favicon') ? $request->file('favicon')->getSize() : null,
            'post_max_size' => ini_get('post_max_size'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
        ]);

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
            'mail_host' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value && !$this->isValidSmtpHost($value)) {
                        $fail('The SMTP host must be a valid hostname or IP address. Placeholder text is not allowed.');
                    }
                },
            ],
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    // If SMTP mailer is selected, username should be a valid email
                    $mailer = $request->input('mail_mailer');
                    if (($mailer === 'smtp' || !$mailer) && $value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $fail('SMTP username must be a valid email address (e.g., yourname@gmail.com).');
                    }
                },
            ],
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
            // Email Template Settings
            'reset_password_subject' => 'nullable|string|max:255',
            'reset_password_greeting' => 'nullable|string|max:255',
            'reset_password_line1' => 'nullable|string|max:500',
            'reset_password_action_text' => 'nullable|string|max:100',
            'reset_password_line2' => 'nullable|string|max:500',
            'reset_password_line3' => 'nullable|string|max:500',
            'reset_password_salutation' => 'nullable|string|max:255',
            // Welcome Email
            'welcome_email_subject' => 'nullable|string|max:255',
            'welcome_email_greeting' => 'nullable|string|max:255',
            'welcome_email_message' => 'nullable|string|max:1000',
            // Verification Email
            'verification_email_subject' => 'nullable|string|max:255',
            'verification_email_greeting' => 'nullable|string|max:255',
            'verification_email_message' => 'nullable|string|max:1000',
            'verification_email_button_text' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            Log::error('Settings validation failed', ['errors' => $validator->errors()->all()]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Log file upload status AFTER validation
        Log::info('File upload check AFTER validation', [
            'has_site_logo' => $request->hasFile('site_logo'),
            'has_favicon' => $request->hasFile('favicon'),
            'all_files' => array_keys($request->allFiles()),
        ]);

        // Handle site_logo upload (save as 'logo' in database)
        $logoUploadError = null;
        if ($request->hasFile('site_logo')) {
            try {
                $siteLogo = $request->file('site_logo');
                
                \Log::info('Logo file received', [
                    'name' => $siteLogo->getClientOriginalName(),
                    'size' => $siteLogo->getSize(),
                    'mime' => $siteLogo->getMimeType(),
                    'isValid' => $siteLogo->isValid(),
                    'error' => $siteLogo->getError()
                ]);
                
                // Validate file
                if (!$siteLogo->isValid()) {
                    \Log::error('Invalid logo file uploaded', [
                        'error_code' => $siteLogo->getError(),
                        'error_message' => $siteLogo->getErrorMessage()
                    ]);
                    $logoUploadError = 'Invalid logo file. Please try again.';
                } else {
                    // Ensure settings directory exists
                    $settingsPath = storage_path('app/public/settings');
                    if (!File::exists($settingsPath)) {
                        File::makeDirectory($settingsPath, 0755, true);
                        \Log::info('Created settings directory', ['path' => $settingsPath]);
                    }
                    
                    $logoName = 'logo.' . $siteLogo->getClientOriginalExtension();
                    $logoPath = $siteLogo->storeAs('settings', $logoName, 'public');

                    \Log::info('Logo storage attempt', [
                        'logoName' => $logoName,
                        'logoPath' => $logoPath,
                        'storage_path' => storage_path('app/public/' . $logoPath),
                        'file_exists' => $logoPath ? Storage::disk('public')->exists($logoPath) : false
                    ]);

                    if (!$logoPath) {
                        \Log::error('Failed to store logo file');
                        $logoUploadError = 'Failed to upload logo. Please try again.';
                    } else {
                        // Verify file was actually stored
                        if (!Storage::disk('public')->exists($logoPath)) {
                            \Log::error('Logo file not found after storage', ['path' => $logoPath]);
                            $logoUploadError = 'Logo file was not saved properly. Please try again.';
                        } else {
                            // Delete old logo if exists
                            $oldLogo = GlobalSetting::getValue('logo');
                            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                                Storage::disk('public')->delete($oldLogo);
                                \Log::info('Deleted old logo', ['path' => $oldLogo]);
                            }

                            // Save as 'logo' (not 'site_logo') to match the display logic
                            GlobalSetting::setValue('logo', $logoPath);
                            
                            \Log::info('Logo uploaded successfully', [
                                'path' => $logoPath,
                                'full_path' => storage_path('app/public/' . $logoPath),
                                'url' => asset('storage/' . $logoPath)
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Logo upload error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                $logoUploadError = 'Error uploading logo: ' . $e->getMessage();
            }
        } else {
            \Log::info('No site_logo file in request', [
                'all_files' => array_keys($request->allFiles()),
                'has_file' => $request->hasFile('site_logo')
            ]);
        }

        // Handle favicon upload
        if ($request->hasFile('favicon')) {
            try {
                $favicon = $request->file('favicon');
                
                \Log::info('Favicon file received', [
                    'name' => $favicon->getClientOriginalName(),
                    'size' => $favicon->getSize(),
                    'mime' => $favicon->getMimeType(),
                    'isValid' => $favicon->isValid(),
                    'error' => $favicon->getError()
                ]);
                
                if (!$favicon->isValid()) {
                    \Log::error('Invalid favicon file uploaded', [
                        'error_code' => $favicon->getError(),
                        'error_message' => $favicon->getErrorMessage()
                    ]);
                } else {
                    // Ensure settings directory exists
                    $settingsPath = storage_path('app/public/settings');
                    if (!File::exists($settingsPath)) {
                        File::makeDirectory($settingsPath, 0755, true);
                        \Log::info('Created settings directory for favicon', ['path' => $settingsPath]);
                    }
                    
                    $faviconName = 'favicon.' . $favicon->getClientOriginalExtension();
                    $faviconPath = $favicon->storeAs('settings', $faviconName, 'public');

                    \Log::info('Favicon storage attempt', [
                        'faviconName' => $faviconName,
                        'faviconPath' => $faviconPath,
                        'file_exists' => $faviconPath ? Storage::disk('public')->exists($faviconPath) : false
                    ]);

                    if (!$faviconPath) {
                        \Log::error('Failed to store favicon file');
                    } else {
                        // Verify file was actually stored
                        if (!Storage::disk('public')->exists($faviconPath)) {
                            \Log::error('Favicon file not found after storage', ['path' => $faviconPath]);
                        } else {
                            // Delete old favicon if exists
                            $oldFavicon = GlobalSetting::getValue('favicon');
                            if ($oldFavicon && Storage::disk('public')->exists($oldFavicon)) {
                                Storage::disk('public')->delete($oldFavicon);
                                \Log::info('Deleted old favicon', ['path' => $oldFavicon]);
                            }

                            GlobalSetting::setValue('favicon', $faviconPath);
                            \Log::info('Favicon uploaded successfully', ['path' => $faviconPath]);
                        }
                    }
                }
            } catch (\Exception $e) {
                \Log::error('Favicon upload error: ' . $e->getMessage(), [
                    'trace' => $e->getTraceAsString()
                ]);
            }
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
            // Email Templates - Reset Password
            'reset_password_subject',
            'reset_password_greeting',
            'reset_password_line1',
            'reset_password_action_text',
            'reset_password_line2',
            'reset_password_line3',
            'reset_password_salutation',
            // Welcome Email
            'welcome_email_subject',
            'welcome_email_greeting',
            'welcome_email_message',
            // Verification Email
            'verification_email_subject',
            'verification_email_greeting',
            'verification_email_message',
            'verification_email_button_text',
        ];

        foreach ($settingsToUpdate as $key) {
            if ($request->has($key) && $request->input($key) !== null) {
                GlobalSetting::setValue($key, $request->input($key));
            }
        }

        $redirect = redirect()->route('admin.setting');
        
        if ($logoUploadError) {
            $redirect->with('error', $logoUploadError);
        } else {
            $redirect->with('success', 'Settings updated successfully!');
        }
        
        return $redirect;
    }

    /**
     * Validate SMTP host to prevent placeholder text
     */
    private function isValidSmtpHost(?string $host): bool
    {
        if (!$host || trim($host) === '') {
            return false;
        }

        $host = trim($host);

        // Extract hostname part (before port if present)
        $hostParts = explode(':', $host);
        $hostname = trim($hostParts[0]);

        // Reject hosts with spaces - invalid hostname format
        if (strpos($hostname, ' ') !== false) {
            return false;
        }

        // Check if it looks like placeholder text
        $placeholderPatterns = [
            '/placeholder/i',
            '/example/i',
            '/test/i',
            '/dummy/i',
            '/sample/i',
            '/lorem/i',
            '/ipsum/i',
            '/odit/i',
            '/duis/i',
            '/molestiae/i',
            '/sit amet/i',
            '/consectetur/i',
            '/adipiscing/i',
            '/elit/i',
        ];

        foreach ($placeholderPatterns as $pattern) {
            if (preg_match($pattern, $host) || preg_match($pattern, $hostname)) {
                return false;
            }
        }

        // Reject if hostname is too short (likely placeholder)
        if (strlen($hostname) < 3) {
            return false;
        }

        // Check if it's a valid IP address
        if (filter_var($hostname, FILTER_VALIDATE_IP)) {
            return true;
        }

        // Check if it's a valid hostname format
        if (preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/', $hostname)) {
            // Must contain at least one dot for domain names (unless localhost)
            if (strpos($hostname, '.') !== false || in_array(strtolower($hostname), ['localhost'])) {
                return true;
            }
        }

        // Allow localhost variants
        if (in_array(strtolower($hostname), ['localhost', '127.0.0.1', '::1'])) {
            return true;
        }

        return false;
    }
}
