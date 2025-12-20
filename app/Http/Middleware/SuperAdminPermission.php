<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Admin;

class SuperAdminPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     * @param  string|null  $guard
     */
    public function handle(Request $request, Closure $next, string $permission, ?string $guard = null): Response
    {
        try {
            $guard = $guard ?? 'admin';
            
            // Check if user is authenticated with the guard
            if (!auth()->guard($guard)->check()) {
                abort(403, 'Unauthorized');
            }

            $user = auth()->guard($guard)->user();
            
            // If user is null due to database error, deny access
            if (!$user) {
                abort(403, 'Unable to verify user. Please try again later.');
            }

        // If user is super admin, allow access (no permission check needed)
        if ($user instanceof Admin && $user->isSuperAdmin()) {
            return $next($request);
        }

        // Handle multiple permissions (comma-separated)
        $permissions = is_array($permission) ? $permission : explode('|', $permission);

        // Check if user has any of the required permissions
        $hasPermission = false;
        foreach ($permissions as $perm) {
            if ($user->hasPermissionTo(trim($perm), $guard)) {
                $hasPermission = true;
                break;
            }
        }

        if (!$hasPermission) {
            abort(403, 'You do not have permission to access this resource.');
        }

        return $next($request);
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::error('Database connection failed in SuperAdminPermission middleware: ' . $e->getMessage());
            abort(503, 'Service temporarily unavailable. Please try again later.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in SuperAdminPermission middleware: ' . $e->getMessage());
            abort(500, 'An error occurred. Please try again later.');
        }
    }
}

