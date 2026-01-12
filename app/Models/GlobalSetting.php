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
     * Get setting value by key with caching
     * Handles database connection errors gracefully
     */
    public static function getValue($key, $default = null)
    {
        try {
            // Cache for 1 hour (3600 seconds)
            return \Illuminate\Support\Facades\Cache::remember("global_setting:{$key}", 3600, function () use ($key, $default) {
                $setting = self::where('key', $key)->first();
                return $setting ? $setting->value : $default;
            });
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
     * Set setting value by key and clear cache
     */
    public static function setValue($key, $value)
    {
        $result = self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        // Clear cache for this specific key
        \Illuminate\Support\Facades\Cache::forget("global_setting:{$key}");
        
        // Also clear all settings cache
        \Illuminate\Support\Facades\Cache::forget('global_settings:all');
        
        return $result;
    }

    /**
     * Get all settings as key-value array with caching
     * Handles database connection errors gracefully
     */
    public static function getAllSettings()
    {
        try {
            // Cache for 1 hour (3600 seconds)
            return \Illuminate\Support\Facades\Cache::remember('global_settings:all', 3600, function () {
                return self::pluck('value', 'key')->toArray();
            });
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
