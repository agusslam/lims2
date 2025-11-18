<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$requiredRoles)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        $user = Auth::user();
        $userRole = $user->role;

        // Developer always has access
        if ($userRole === 'DEVEL') {
            return $next($request);
        }

        // Check if user role is in required roles
        if (!empty($requiredRoles) && in_array($userRole, $requiredRoles)) {
            return $next($request);
        }

        // If no specific roles required, allow authenticated users
        if (empty($requiredRoles)) {
            return $next($request);
        }

        Log::warning("Role access denied for user {$user->id} with role {$userRole}. Required roles: " . implode(',', $requiredRoles));
        
        abort(403, 'Unauthorized access. Your role (' . $userRole . ') does not have permission to access this resource.');
    }
}