<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;
use App\Models\AuditLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $key = 'login.' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik."
            ]);
        }

        $user = User::where('username', $request->username)->first();
        
        if (!$user) {
            RateLimiter::hit($key, 300);
            return back()->withErrors(['username' => 'Username tidak ditemukan']);
        }

        if ($user->isLocked()) {
            return back()->withErrors([
                'username' => 'Akun terkunci sampai ' . $user->locked_until->format('H:i d/m/Y')
            ]);
        }

        if (!$user->is_active) {
            return back()->withErrors(['username' => 'Akun tidak aktif']);
        }

        if (!Hash::check($request->password, $user->password)) {
            $user->increment('failed_login_attempts');
            
            if ($user->failed_login_attempts >= 3) {
                $user->locked_until = now()->addMinutes(30);
                $user->save();
                
                return back()->withErrors([
                    'username' => 'Akun terkunci karena terlalu banyak percobaan login yang gagal'
                ]);
            }
            
            RateLimiter::hit($key, 300);
            return back()->withErrors(['password' => 'Password salah']);
        }

        RateLimiter::clear($key);
        
        $user->update([
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'last_login_at' => now(),
            'last_login_ip' => $request->ip()
        ]);

        Auth::login($user);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => 'User login successful'
        ]);

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'logout',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'description' => 'User logout'
        ]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
