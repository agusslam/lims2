<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('email', 'remember'));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Find user
        $user = User::where('email', $request->email)->first();
        
        // Check if user exists
        if (!$user) {
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'User with this email does not exist.']);
        }

        // Check if account is locked
        if ($user->isLocked()) {
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Account is temporarily locked. Please try again later.']);
        }

        // Check if user is active
        if (!$user->is_active) {
            return redirect()->back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Your account is inactive. Please contact administrator.']);
        }

        // Attempt login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            
            // Reset failed login attempts and update last login
            $user->resetLoginAttempts();

            // Redirect based on user role and permissions
            return $this->authenticated($request, $user);
        }

        // Login failed - increment attempts
        $user->incrementLoginAttempts();

        return redirect()->back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    /**
     * The user has been authenticated.
     */
    protected function authenticated(Request $request, $user)
    {
        // Redirect based on user role and permissions
        if ($user->hasPermission(1)) {
            return redirect()->route('sample-requests.index');
        } elseif ($user->hasPermission(4)) {
            return redirect()->route('testing.index');
        } elseif ($user->hasPermission(8)) {
            return redirect()->route('parameters.index');
        } else {
            return redirect()->route('dashboard');
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }
}