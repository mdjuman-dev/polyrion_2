<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class HandleDatabaseAuthErrors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Wrap authentication checks in try-catch to handle database connection failures
        try {
            // Pre-check authentication to catch database errors early
            // This will fail gracefully if database is unavailable
            if (auth()->check()) {
                try {
                    auth()->user(); // This will trigger database query
                } catch (\Illuminate\Database\QueryException $e) {
                    Log::warning('Database connection failed when loading authenticated user', [
                        'error' => $e->getMessage(),
                        'user_id' => auth()->id()
                    ]);
                    // Clear the session to prevent further errors
                    auth()->logout();
                    session()->invalidate();
                }
            }
        } catch (\Illuminate\Database\QueryException $e) {
            Log::warning('Database connection failed during authentication check', [
                'error' => $e->getMessage()
            ]);
            // Clear any stale session data
            try {
                auth()->logout();
                session()->invalidate();
            } catch (\Exception $logoutException) {
                // Ignore logout errors
            }
        } catch (\Exception $e) {
            // Catch any other authentication-related errors
            Log::error('Error during authentication check', [
                'error' => $e->getMessage()
            ]);
        }

        return $next($request);
    }
}

