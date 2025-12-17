<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
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

        // Dynamically configure mail settings from database
        $this->configureMailSettings();

        // Share global settings with all views
        View::composer('*', function ($view) {
            try {
                $view->with([
                    'appName' => GlobalSetting::getValue('app_name') ?? config('app.name', 'Polyrion'),
                    'appUrl' => GlobalSetting::getValue('app_url') ?? config('app.url', url('/')),
                    'favicon' => GlobalSetting::getValue('favicon'),
                    'logo' => GlobalSetting::getValue('logo'),
                    'gaTrackingId' => GlobalSetting::getValue('ga_tracking_id'),
                    'fbPixelId' => GlobalSetting::getValue('fb_pixel_id'),
                    'tawkWidgetCode' => GlobalSetting::getValue('tawk_widget_code'),
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                // If database connection fails, use default values
                Log::warning('Database connection failed in View composer, using defaults', [
                    'error' => $e->getMessage()
                ]);
                $view->with([
                    'appName' => config('app.name', 'Polyrion'),
                    'appUrl' => config('app.url', url('/')),
                    'favicon' => null,
                    'logo' => null,
                    'gaTrackingId' => null,
                    'fbPixelId' => null,
                    'tawkWidgetCode' => null,
                ]);
            } catch (\Exception $e) {
                // Catch any other exceptions
                Log::error('Error loading global settings in View composer', [
                    'error' => $e->getMessage()
                ]);
                $view->with([
                    'appName' => config('app.name', 'Polyrion'),
                    'appUrl' => config('app.url', url('/')),
                    'favicon' => null,
                    'logo' => null,
                    'gaTrackingId' => null,
                    'fbPixelId' => null,
                    'tawkWidgetCode' => null,
                ]);
            }
        });
    }

    /**
     * Configure mail settings from database
     */
    protected function configureMailSettings(): void
    {
        try {
            // Read SMTP settings from database
            $mailMailer = GlobalSetting::getValue('mail_mailer');
            $mailHost = GlobalSetting::getValue('mail_host');
            $mailPort = GlobalSetting::getValue('mail_port');
            $mailUsername = GlobalSetting::getValue('mail_username');
            $mailPassword = GlobalSetting::getValue('mail_password');
            $mailEncryption = GlobalSetting::getValue('mail_encryption');
            $mailFromAddress = GlobalSetting::getValue('mail_from_address');
            $mailFromName = GlobalSetting::getValue('mail_from_name');

            // Load SMTP settings from database
            Log::info('Loading SMTP settings from database', [
                'mailer' => $mailMailer,
                'host' => $mailHost,
                'port' => $mailPort,
                'username' => $mailUsername ? '***' : null,
                'encryption' => $mailEncryption,
                'from_address' => $mailFromAddress,
                'from_name' => $mailFromName,
            ]);

            // Validate and set mailer
            if ($mailMailer && in_array($mailMailer, ['smtp', 'ses', 'postmark', 'resend', 'mailgun', 'sendmail', 'log'])) {
                config(['mail.default' => $mailMailer]);
                Log::info('Mailer set from database', ['mailer' => $mailMailer]);
            } else {
                Log::info('Using default mailer (no valid mailer in database)', ['mailer' => $mailMailer]);
            }

            // Only configure SMTP settings if mailer is SMTP and settings are valid
            if ($mailMailer === 'smtp' || !$mailMailer) {
                // Validate host - must be a valid hostname/IP, not placeholder text
                // Only set config if host is valid, otherwise fallback to 'log' mailer
                if ($mailHost && $this->isValidHost($mailHost)) {
                    // Extract hostname part (remove port if included in host field)
                    $hostParts = explode(':', trim($mailHost));
                    $hostname = trim($hostParts[0]);
                    config(['mail.mailers.smtp.host' => $hostname]);
                    Log::info('SMTP host set from database', ['host' => $hostname]);
                } else {
                    // If host is invalid and mailer is set to SMTP, fallback to 'log' mailer
                    // This prevents connection errors from invalid hosts
                    if ($mailMailer === 'smtp' || (!$mailMailer && $mailHost)) {
                        Log::warning('Invalid SMTP host detected, falling back to log mailer', [
                            'host' => $mailHost,
                            'mailer' => $mailMailer
                        ]);
                        config(['mail.default' => 'log']);
                    }
                    // Don't override existing SMTP config if host is invalid
                }

                // Validate port - must be numeric and between 1-65535
                if ($mailPort && is_numeric($mailPort)) {
                    $port = (int)$mailPort;
                    if ($port > 0 && $port <= 65535) {
                        config(['mail.mailers.smtp.port' => $port]);
                        Log::info('SMTP port set from database', ['port' => $port]);
                    }
                }

                // Set username if provided
                if ($mailUsername && trim($mailUsername) !== '') {
                    config(['mail.mailers.smtp.username' => trim($mailUsername)]);
                    Log::info('SMTP username set from database');
                }

                // Set password if provided
                if ($mailPassword && trim($mailPassword) !== '') {
                    config(['mail.mailers.smtp.password' => trim($mailPassword)]);
                    Log::info('SMTP password set from database');
                }

                // Validate encryption - must be 'tls', 'ssl', or empty/null
                if ($mailEncryption !== null && $mailEncryption !== '') {
                    if (in_array(strtolower($mailEncryption), ['tls', 'ssl'])) {
                        config(['mail.mailers.smtp.encryption' => strtolower($mailEncryption)]);
                        Log::info('SMTP encryption set from database', ['encryption' => $mailEncryption]);
                    }
                } elseif ($mailEncryption === '') {
                    config(['mail.mailers.smtp.encryption' => null]);
                }
            }

            // Validate and set from address - must be valid email format
            if ($mailFromAddress && filter_var($mailFromAddress, FILTER_VALIDATE_EMAIL)) {
                config(['mail.from.address' => trim($mailFromAddress)]);
                Log::info('From address set from database', ['address' => $mailFromAddress]);
            }

            // Set from name if provided
            if ($mailFromName && trim($mailFromName) !== '') {
                config(['mail.from.name' => trim($mailFromName)]);
                Log::info('From name set from database', ['name' => $mailFromName]);
            }
        } catch (\Illuminate\Database\QueryException $e) {
            // If database connection fails, use default config
            Log::warning('Database connection failed while loading SMTP settings, using defaults', [
                'message' => $e->getMessage()
            ]);
        } catch (\Exception $e) {
            // If database is not ready or table doesn't exist, use default config
            // This prevents errors during migrations
            Log::error('Error loading SMTP settings from database', [
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Validate if a host is valid (not placeholder text)
     */
    protected function isValidHost(?string $host): bool
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

        // Check if it looks like placeholder text (contains common placeholder words)
        // Check both the full host and just the hostname part
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
        // Hostname should contain only alphanumeric, dots, and hyphens
        if (preg_match('/^[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?(\.[a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?)*$/', $hostname)) {
            // Additional check: must contain at least one dot for domain names (unless localhost)
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
