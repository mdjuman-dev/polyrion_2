<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Models\GlobalSetting;
use App\Models\SocialMediaLink;

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

      // Share global settings with all views (cached)
      View::composer('*', function ($view) {
         // Safely get authenticated user using helper function
         $authUser = safeAuthUser('web');

         try {
            // Cache social media links for 1 hour
            $socialMediaLinks = \Illuminate\Support\Facades\Cache::remember('social_media_links:active', 3600, function () {
               return SocialMediaLink::active()->get();
            });
            
            // Get all settings at once (cached) to reduce queries
            $allSettings = GlobalSetting::getAllSettings();
            
            $view->with([
               'appName' => $allSettings['app_name'] ?? config('app.name', 'Polyrion'),
               'appUrl' => $allSettings['app_url'] ?? config('app.url', url('/')),
               'favicon' => $allSettings['favicon'] ?? null,
               'logo' => $allSettings['logo'] ?? null,
               'gaTrackingId' => $allSettings['ga_tracking_id'] ?? null,
               'fbPixelId' => $allSettings['fb_pixel_id'] ?? null,
               'tawkWidgetCode' => $allSettings['tawk_widget_code'] ?? null,
               'authUser' => $authUser, // Safe user variable
               'socialMediaLinks' => $socialMediaLinks,
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
               'authUser' => $authUser, // Still include user if we got it before
               'socialMediaLinks' => collect([]),
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
               'authUser' => $authUser, // Still include user if we got it before
               'socialMediaLinks' => collect([]),
            ]);
         }
      });
   }

   /**
    * Configure mail settings from database (cached)
    */
   protected function configureMailSettings(): void
   {
      try {
         // Get all settings at once (cached) to reduce queries
         $allSettings = GlobalSetting::getAllSettings();
         
         // Read SMTP settings from cached array
         $mailMailer = $allSettings['mail_mailer'] ?? null;
         $mailHost = $allSettings['mail_host'] ?? null;
         $mailPort = $allSettings['mail_port'] ?? null;
         $mailUsername = $allSettings['mail_username'] ?? null;
         $mailPassword = $allSettings['mail_password'] ?? null;
         $mailEncryption = $allSettings['mail_encryption'] ?? null;
         $mailFromAddress = $allSettings['mail_from_address'] ?? null;
         $mailFromName = $allSettings['mail_from_name'] ?? null;

         // Only log if settings are actually configured (not all null)
         $hasSettings = $mailMailer || $mailHost || $mailPort || $mailUsername || $mailPassword;

         // Validate and set mailer
         if ($mailMailer && in_array($mailMailer, ['smtp', 'ses', 'postmark', 'resend', 'mailgun', 'sendmail', 'log'])) {
            config(['mail.default' => $mailMailer]);

         } else {
            // Only log if we expected settings but didn't find valid mailer
            if ($hasSettings && $mailMailer) {
               Log::warning('Invalid mailer in database, using default', ['mailer' => $mailMailer]);
            }
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
               $port = (int) $mailPort;
               if ($port > 0 && $port <= 65535) {
                  config(['mail.mailers.smtp.port' => $port]);
               }
            }

            // Set username if provided
            if ($mailUsername && trim($mailUsername) !== '') {
               config(['mail.mailers.smtp.username' => trim($mailUsername)]);
            }

            // Set password if provided
            if ($mailPassword && trim($mailPassword) !== '') {
               config(['mail.mailers.smtp.password' => trim($mailPassword)]);
            }

            // Validate encryption - must be 'tls', 'ssl', or empty/null
            if ($mailEncryption !== null && $mailEncryption !== '') {
               if (in_array(strtolower($mailEncryption), ['tls', 'ssl'])) {
                  config(['mail.mailers.smtp.encryption' => strtolower($mailEncryption)]);
               }
            } elseif ($mailEncryption === '') {
               config(['mail.mailers.smtp.encryption' => null]);
            }
         }

         // Validate and set from address - must be valid email format
         if ($mailFromAddress && filter_var($mailFromAddress, FILTER_VALIDATE_EMAIL)) {
            config(['mail.from.address' => trim($mailFromAddress)]);
         }

         // Set from name if provided
         if ($mailFromName && trim($mailFromName) !== '') {
            config(['mail.from.name' => trim($mailFromName)]);
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
