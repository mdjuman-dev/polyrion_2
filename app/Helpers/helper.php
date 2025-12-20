<?php

use Carbon\Carbon;

if (!function_exists('formatVolume')) {
    function formatVolume($number)
    {
        if ($number >= 1000000000) {
            return round($number / 1000000000, 1) . 'B';
        } elseif ($number >= 1000000) {
            return round($number / 1000000, 1) . 'M';
        } elseif ($number >= 1000) {
            return round($number / 1000, 1) . 'K';
        } else {
            return $number;
        }
    }
}

if (!function_exists('toMysqlDate')) {
    function toMysqlDate($date)
    {
        return $date ? date('Y-m-d H:i:s', strtotime($date)) : null;
    }
}
if (!function_exists('format_date')) {

    function format_date($date, string $format = 'd M Y, H:i'): string
    {
        if (is_null($date)) {
            return 'N/A';
        }

        if ($date instanceof Carbon || $date instanceof \DateTime) {
            return $date->format($format);
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Exception $e) {
            return 'Invalid Date';
        }
    }
}

if (!function_exists('format_number')) {
    /**
     * Format a number with decimal places and optional currency symbol
     *
     * @param mixed $number The number to format
     * @param int $decimals Number of decimal places (default: 2)
     * @param string|null $currency Currency symbol (default: null, use '$' if null and $showCurrency is true)
     * @param bool $showCurrency Whether to show currency symbol (default: false)
     * @return string
     */
    function format_number($number, int $decimals = 2, ?string $currency = null, bool $showCurrency = false): string
    {
        $value = $number ?? 0;
        $formatted = number_format((float) $value, $decimals);

        if ($showCurrency) {
            $symbol = $currency ?? '$';
            return $symbol . $formatted;
        }

        return $formatted;
    }
}

if (!function_exists('format_time_ago')) {
    /**
     * Format time ago in compact format (s, m, h, w, m, y)
     *
     * @param mixed $date The date to format
     * @return string
     */
    function format_time_ago($date): string
    {
        if (is_null($date)) {
            return 'N/A';
        }

        try {
            $carbonDate = $date instanceof Carbon ? $date : Carbon::parse($date);
            $diff = $carbonDate->diffInSeconds(now());
            $diff = (int) $diff;

            if ($diff < 60) {
                return $diff . 's'; // seconds
            } elseif ($diff < 3600) {
                return floor($diff / 60) . 'm'; // minutes
            } elseif ($diff < 86400) {
                return floor($diff / 3600) . 'h'; // hours
            } elseif ($diff < 604800) {
                return floor($diff / 86400) . 'd'; // days
            } elseif ($diff < 2592000) {
                return floor($diff / 604800) . 'w'; // weeks
            } elseif ($diff < 31536000) {
                return floor($diff / 2592000) . 'mo'; // months
            } else {
                return floor($diff / 31536000) . 'y'; // years
            }
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}

if (!function_exists('cleanImageUrl')) {
    /**
     * Clean and fix multiple-encoded image URLs
     * Handles URLs that have been encoded multiple times
     *
     * @param string|null $url The URL to clean
     * @param int $maxLength Maximum URL length (default: 1000)
     * @return string|null
     */
    function cleanImageUrl(?string $url, int $maxLength = 1000): ?string
    {
        if (empty($url)) {
            return null;
        }

        // Decode multiple times until we get a stable URL
        $previousUrl = '';
        $currentUrl = $url;
        $maxIterations = 10;
        $iteration = 0;

        while ($currentUrl !== $previousUrl && $iteration < $maxIterations) {
            $previousUrl = $currentUrl;
            
            // Try to decode
            $decoded = urldecode($currentUrl);
            
            // If decoding didn't change anything, we're done
            if ($decoded === $currentUrl) {
                break;
            }
            
            // Check if decoded URL looks valid (starts with http)
            if (preg_match('/^https?:\/\//', $decoded)) {
                $currentUrl = $decoded;
            } else {
                // If decoded doesn't look like a URL, keep the previous one
                break;
            }
            
            $iteration++;
        }

        // Trim to max length if needed
        if (strlen($currentUrl) > $maxLength) {
            $currentUrl = substr($currentUrl, 0, $maxLength);
        }

        // Validate it's a proper URL
        if (!filter_var($currentUrl, FILTER_VALIDATE_URL)) {
            return null;
        }

        return $currentUrl;
    }
}

if (!function_exists('safeAuthUser')) {
    /**
     * Safely get the authenticated user, handling database connection failures
     *
     * @param string|null $guard The guard to use (default: 'web')
     * @return \App\Models\User|null
     */
    function safeAuthUser(?string $guard = 'web'): ?\App\Models\User
    {
        try {
            if (!auth($guard)->check()) {
                return null;
            }
            
            return auth($guard)->user();
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::warning('Database connection failed when getting authenticated user', [
                'error' => $e->getMessage(),
                'guard' => $guard
            ]);
            // Clear the session to prevent further errors
            try {
                auth($guard)->logout();
            } catch (\Exception $logoutException) {
                // Ignore logout errors
            }
            return null;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error getting authenticated user', [
                'error' => $e->getMessage(),
                'guard' => $guard
            ]);
            return null;
        }
    }
}