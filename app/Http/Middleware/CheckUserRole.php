<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                           ->withErrors(['account' => 'Akun Anda tidak aktif.']);
        }

        if (!$user->hasAnyRole($roles)) {
            abort(403, 'Tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
