<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $moduleId = null)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();

        // Developer always has access
        if ($user->role === 'DEVEL') {
            return $next($request);
        }

        // If no module ID specified, just check auth
        if ($moduleId === null) {
            return $next($request);
        }

        // Check if user has permission for this module
        try {
            if (!$user->hasPermission((int)$moduleId)) {
                Log::warning("Permission denied for user {$user->id} to module {$moduleId}");
                abort(403, "You do not have permission to access this module. Required permission: Module {$moduleId}");
            }
        } catch (\Exception $e) {
            Log::error("Error checking permission: " . $e->getMessage());
            abort(500, 'Error checking permissions');
        }

        return $next($request);
    }
}
