<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueryCacheService
{
    /**
     * Cache duration in seconds
     */
    private const DEFAULT_CACHE_TTL = 300; // 5 minutes
    private const SHORT_CACHE_TTL = 60; // 1 minute
    private const LONG_CACHE_TTL = 3600; // 1 hour

    /**
     * Execute a query with caching
     *
     * @param string $cacheKey
     * @param callable $queryCallback
     * @param int|null $ttl Cache TTL in seconds (null = default)
     * @return mixed
     */
    public static function remember(string $cacheKey, callable $queryCallback, ?int $ttl = null)
    {
        $ttl = $ttl ?? self::DEFAULT_CACHE_TTL;
        
        return Cache::remember($cacheKey, $ttl, function () use ($queryCallback) {
            $startTime = microtime(true);
            $result = $queryCallback();
            $executionTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
            
            // Log slow queries (> 500ms)
            if ($executionTime > 500) {
                Log::warning("Slow query detected", [
                    'execution_time_ms' => round($executionTime, 2),
                    'cache_key' => $cacheKey
                ]);
            }
            
            return $result;
        });
    }

    /**
     * Cache count queries
     */
    public static function rememberCount(string $model, string $cacheKey, callable $queryCallback, ?int $ttl = null)
    {
        return self::remember("count:{$model}:{$cacheKey}", $queryCallback, $ttl ?? self::SHORT_CACHE_TTL);
    }

    /**
     * Cache list queries (with pagination)
     */
    public static function rememberList(string $model, string $cacheKey, callable $queryCallback, ?int $ttl = null)
    {
        return self::remember("list:{$model}:{$cacheKey}", $queryCallback, $ttl ?? self::SHORT_CACHE_TTL);
    }

    /**
     * Cache statistics/aggregates
     */
    public static function rememberStats(string $model, string $cacheKey, callable $queryCallback, ?int $ttl = null)
    {
        return self::remember("stats:{$model}:{$cacheKey}", $queryCallback, $ttl ?? self::LONG_CACHE_TTL);
    }

    /**
     * Clear cache by pattern
     */
    public static function clearByPattern(string $pattern)
    {
        // For file-based cache, we need to clear all
        // For Redis/Memcached, we could use pattern matching
        Cache::flush();
    }

    /**
     * Clear cache for a specific model
     */
    public static function clearModel(string $model)
    {
        // Clear all caches related to this model
        $patterns = [
            "count:{$model}:*",
            "list:{$model}:*",
            "stats:{$model}:*"
        ];
        
        foreach ($patterns as $pattern) {
            self::clearByPattern($pattern);
        }
    }

    /**
     * Get cache statistics
     */
    public static function getStats(): array
    {
        return [
            'cache_driver' => config('cache.default'),
            'default_ttl' => self::DEFAULT_CACHE_TTL,
            'short_ttl' => self::SHORT_CACHE_TTL,
            'long_ttl' => self::LONG_CACHE_TTL,
        ];
    }
}

