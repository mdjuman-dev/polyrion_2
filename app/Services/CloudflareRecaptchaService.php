<?php

namespace App\Services;

use App\Models\GlobalSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareRecaptchaService
{
    /**
     * Verify Cloudflare reCAPTCHA token
     *
     * @param string|null $token
     * @param string|null $ip
     * @return bool
     */
    public static function verify(?string $token, ?string $ip = null): bool
    {
        // Get secret key from settings
        $secretKey = GlobalSetting::getValue('recaptcha_secret_key');

        // If no secret key configured, skip verification (for development)
        if (empty($secretKey)) {
            return true;
        }

        // If no token provided, verification fails
        if (empty($token)) {
            return false;
        }

        try {
            // Cloudflare Turnstile verification endpoint
            $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => $secretKey,
                'response' => $token,
                'remoteip' => $ip ?? request()->ip(),
            ]);

            $result = $response->json();

            // Check if verification was successful
            if (isset($result['success']) && $result['success'] === true) {
                return true;
            }

            // Log errors for debugging
            if (isset($result['error-codes'])) {
                Log::warning('Cloudflare reCAPTCHA verification failed', [
                    'errors' => $result['error-codes'],
                    'ip' => $ip ?? request()->ip(),
                ]);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Cloudflare reCAPTCHA verification error: ' . $e->getMessage());
            // On error, allow the request (fail open) to prevent blocking legitimate users
            return true;
        }
    }

    /**
     * Get site key from settings
     *
     * @return string
     */
    public static function getSiteKey(): string
    {
        $siteKey = GlobalSetting::getValue('recaptcha_site_key', '');
        // Ensure we always return a string, even if null is returned
        return (string) ($siteKey ?? '');
    }

    /**
     * Check if reCAPTCHA is enabled
     *
     * @return bool
     */
    public static function isEnabled(): bool
    {
        $siteKey = self::getSiteKey();
        $secretKey = GlobalSetting::getValue('recaptcha_secret_key');

        return !empty($siteKey) && !empty($secretKey);
    }
}

