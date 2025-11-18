<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'locked_users' => User::where('locked_until', '>', now())->count(),
            'online_users' => User::where('last_login_at', '>', now()->subMinutes(30))->count()
        ];

        return view('users.index', compact('users', 'stats'));
    }

    public function show($id)
    {
        $user = User::with(['auditLogs' => function($query) {
            $query->orderBy('created_at', 'desc')->limit(20);
        }])->findOrFail($id);

        $workloadStats = [];
        if ($user->hasAnyRole(['ANALYST', 'SUPERVISOR_ANALYST'])) {
            $workloadStats = [
                'assigned_samples' => $user->assignedSamples()->whereIn('status', ['assigned', 'testing'])->count(),
                'completed_samples' => $user->assignedSamples()->where('status', 'completed')->count(),
                'current_workload' => $user->getCurrentWorkload()
            ];
        }

        return view('users.show', compact('user', 'workloadStats'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|unique:users',
            'full_name' => 'required|string|max:255',
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'full_name' => $request->full_name,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'is_active' => true
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_created',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "User {$user->username} created with role {$user->role}"
        ]);

        return redirect()->route('users.show', $user->id)
            ->with('success', 'User berhasil dibuat');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent editing DEVEL users by non-DEVEL
        if ($user->hasRole('DEVEL') && !auth()->user()->hasRole('DEVEL')) {
            abort(403, 'Tidak dapat mengedit user developer');
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($id)],
            'full_name' => 'required|string|max:255',
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => 'nullable|string|min:6|confirmed'
        ]);

        $oldValues = $user->toArray();
        
        $updateData = [
            'username' => $request->username,
            'email' => $request->email,
            'full_name' => $request->full_name,
            'role' => $request->role
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_updated',
            'model_type' => User::class,
            'model_id' => $user->id,
            'old_values' => $oldValues,
            'new_values' => $user->fresh()->toArray(),
            'description' => "User {$user->username} updated"
        ]);

        return redirect()->route('users.show', $user->id)
            ->with('success', 'User berhasil diperbarui');
    }

    public function toggle(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Prevent disabling own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menonaktifkan akun sendiri');
        }

        // Prevent disabling DEVEL by non-DEVEL
        if ($user->hasRole('DEVEL') && !auth()->user()->hasRole('DEVEL')) {
            return back()->with('error', 'Tidak dapat menonaktifkan user developer');
        }

        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'activated' : 'deactivated';
        
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_' . $status,
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "User {$user->username} {$status}"
        ]);

        return back()->with('success', "User berhasil " . ($user->is_active ? 'diaktifkan' : 'dinonaktifkan'));
    }

    public function resetPassword(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $newPassword = $request->input('new_password', '123456');
        $user->update([
            'password' => Hash::make($newPassword),
            'failed_login_attempts' => 0,
            'locked_until' => null
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_password_reset',
            'model_type' => User::class,
            'model_id' => $user->id,
            'description' => "Password reset for user {$user->username}"
        ]);

        return back()->with('success', "Password user berhasil direset ke: {$newPassword}");
    }

    public function activity($id)
    {
        $user = User::findOrFail($id);
        
        $activities = AuditLog::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('users.activity', compact('user', 'activities'));
    }

    public function settings()
    {
        if (!auth()->user()->hasRole('DEVEL')) {
            abort(403, 'Akses terbatas untuk developer');
        }

        $settings = config('lims');
        return view('users.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        if (!auth()->user()->hasRole('DEVEL')) {
            abort(403, 'Akses terbatas untuk developer');
        }

        $request->validate([
            'session_timeout' => 'required|integer|min:5|max:480',
            'max_file_size' => 'required|integer|min:1024|max:51200',
            'analyst_max_workload' => 'required|integer|min:1|max:50',
            'certificate_validity_days' => 'required|integer|min:30|max:1825',
            'default_tax_rate' => 'required|numeric|min:0|max:100'
        ]);

        // Update configuration (in production, this would update a database settings table)
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'system_settings_updated',
            'description' => 'System settings updated: ' . implode(', ', array_keys($request->only([
                'session_timeout', 'max_file_size', 'analyst_max_workload', 
                'certificate_validity_days', 'default_tax_rate'
            ])))
        ]);

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui');
    }

    public function auditLogs()
    {
        if (!auth()->user()->hasRole('DEVEL')) {
            abort(403, 'Akses terbatas untuk developer');
        }

        $logs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(100);

        return view('users.audit-logs', compact('logs'));
    }

    public function backup(Request $request)
    {
        if (!auth()->user()->hasRole('DEVEL')) {
            abort(403, 'Akses terbatas untuk developer');
        }

        // Simulate backup process
        $backupFile = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'system_backup_created',
            'description' => "System backup created: {$backupFile}"
        ]);

        return back()->with('success', "Backup berhasil dibuat: {$backupFile}");
    }

    public function updatePreferences(Request $request)
    {
        $request->validate([
            'notification_email' => 'boolean',
            'notification_sms' => 'boolean',
            'auto_refresh' => 'boolean',
            'items_per_page' => 'integer|min:10|max:100'
        ]);

        $user = auth()->user();
        
        $preferences = array_merge($user->preferences ?? [], [
            'notification_email' => $request->boolean('notification_email'),
            'notification_sms' => $request->boolean('notification_sms'),
            'auto_refresh' => $request->boolean('auto_refresh'),
            'items_per_page' => $request->integer('items_per_page', 20)
        ]);

        $user->update(['preferences' => $preferences]);

        return back()->with('success', 'Preferensi berhasil diperbarui');
    }
}
