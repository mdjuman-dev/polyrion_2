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
            
            if ($diff < 60) {
                return $diff . 's';
            } elseif ($diff < 3600) {
                return floor($diff / 60) . 'm';
            } elseif ($diff < 604800) {
                return floor($diff / 3600) . 'h';
            } elseif ($diff < 2592000) {
                return floor($diff / 604800) . 'w';
            } elseif ($diff < 31536000) {
                return floor($diff / 2592000) . 'm';
            } else {
                return floor($diff / 31536000) . 'y';
            }
        } catch (\Exception $e) {
            return 'N/A';
        }
    }
}