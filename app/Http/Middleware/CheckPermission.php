<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();
        
        if (!$user->is_active) {
            auth()->logout();
            return redirect('/login')->with('error', 'Akun Anda tidak aktif');
        }

        if ($user->isLocked()) {
            auth()->logout();
            return redirect('/login')->with('error', 'Akun terkunci sampai ' . $user->locked_until->format('H:i d/m/Y'));
        }

        return $next($request);
    }
}
