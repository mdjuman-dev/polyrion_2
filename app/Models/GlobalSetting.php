<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get setting value by key
     * Handles database connection errors gracefully
     */
    public static function getValue($key, $default = null)
    {
        try {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
        } catch (\Illuminate\Database\QueryException $e) {
            // Log the error but don't throw - return default value
            \Illuminate\Support\Facades\Log::warning('Database connection failed in GlobalSetting::getValue', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        } catch (\Exception $e) {
            // Catch any other exceptions
            \Illuminate\Support\Facades\Log::error('Error in GlobalSetting::getValue', [
                'key' => $key,
                'error' => $e->getMessage()
            ]);
            return $default;
        }
    }

    /**
     * Set setting value by key
     */
    public static function setValue($key, $value)
    {
        return self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    /**
     * Get all settings as key-value array
     * Handles database connection errors gracefully
     */
    public static function getAllSettings()
    {
        try {
        return self::pluck('value', 'key')->toArray();
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::warning('Database connection failed in GlobalSetting::getAllSettings', [
                'error' => $e->getMessage()
            ]);
            return [];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in GlobalSetting::getAllSettings', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }
}
