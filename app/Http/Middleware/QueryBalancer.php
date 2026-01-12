<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class QueryBalancer
{
    /**
     * Maximum queries per request
     */
    private const MAX_QUERIES_PER_REQUEST = 50;

    /**
     * Maximum query execution time in milliseconds
     */
    private const MAX_QUERY_TIME_MS = 1000;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Enable query logging for this request
        DB::enableQueryLog();
        
        $startTime = microtime(true);
        
        try {
            $response = $next($request);
            
            // Get query log
            $queries = DB::getQueryLog();
            $queryCount = count($queries);
            $totalTime = (microtime(true) - $startTime) * 1000; // Convert to milliseconds
            
            // Check for too many queries
            if ($queryCount > self::MAX_QUERIES_PER_REQUEST) {
                Log::warning("Too many queries in request", [
                    'count' => $queryCount,
                    'max_allowed' => self::MAX_QUERIES_PER_REQUEST,
                    'route' => $request->route()?->getName() ?? $request->path(),
                    'method' => $request->method(),
                ]);
            }
            
            // Check for slow queries
            $slowQueries = array_filter($queries, function ($query) {
                return ($query['time'] ?? 0) > self::MAX_QUERY_TIME_MS;
            });
            
            if (!empty($slowQueries)) {
                Log::warning("Slow queries detected", [
                    'count' => count($slowQueries),
                    'route' => $request->route()?->getName() ?? $request->path(),
                    'queries' => array_map(function ($q) {
                        return [
                            'query' => $q['query'] ?? 'N/A',
                            'time_ms' => round($q['time'] ?? 0, 2),
                        ];
                    }, $slowQueries),
                ]);
            }
            
            // Log performance metrics for monitoring
            if ($totalTime > 1000) { // Log if request takes more than 1 second
                Log::info("Request performance", [
                    'route' => $request->route()?->getName() ?? $request->path(),
                    'total_time_ms' => round($totalTime, 2),
                    'query_count' => $queryCount,
                    'avg_query_time_ms' => $queryCount > 0 ? round($totalTime / $queryCount, 2) : 0,
                ]);
            }
            
            return $response;
        } catch (\Exception $e) {
            // Log query information even on errors
            $queries = DB::getQueryLog();
            Log::error("Request failed with queries", [
                'error' => $e->getMessage(),
                'query_count' => count($queries),
                'route' => $request->route()?->getName() ?? $request->path(),
            ]);
            
            throw $e;
        } finally {
            // Disable query logging to save memory
            DB::disableQueryLog();
        }
    }
}

