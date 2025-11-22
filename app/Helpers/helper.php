<?php

use Carbon\Carbon;
if (!function_exists('toMysqlDate')) {
    function toMysqlDate($date)
    {
        return $date ? date('Y-m-d H:i:s', strtotime($date)) : null;
    }
}
if (!function_exists('format_date')) {
    /**
     * Format a date/time value
     *
     * @param mixed $date Can be Carbon instance, DateTime, or string
     * @param string $format Date format (default: 'd M Y, H:i')
     * @return string
     */
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