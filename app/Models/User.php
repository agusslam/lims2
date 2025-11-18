<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'full_name',
        'username',
        'email',
        'password',
        'role',
        'department',
        'phone',
        'is_active',
        'last_login_at',
        'locked_at',
        'failed_login_attempts',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_at' => 'datetime',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * Check if user account is locked
     */
    public function isLocked()
    {
        return !is_null($this->locked_at) && $this->locked_at->isFuture();
    }

    /**
     * Lock user account for specified minutes
     */
    public function lockAccount($minutes = 30)
    {
        $this->update([
            'locked_at' => now()->addMinutes($minutes)
        ]);
    }

    /**
     * Unlock user account
     */
    public function unlockAccount()
    {
        $this->update([
            'locked_at' => null,
            'failed_login_attempts' => 0
        ]);
    }

    /**
     * Increment failed login attempts
     */
    public function incrementLoginAttempts()
    {
        $attempts = $this->failed_login_attempts + 1;
        $this->update(['failed_login_attempts' => $attempts]);
        
        // Lock account after 5 failed attempts
        if ($attempts >= 5) {
            $this->lockAccount(30); // Lock for 30 minutes
        }
    }

    /**
     * Reset failed login attempts
     */
    public function resetLoginAttempts()
    {
        $this->update([
            'failed_login_attempts' => 0,
            'last_login_at' => now()
        ]);
    }

    public function getRoleConfigAttribute()
    {
        $defaultConfig = [
            'name' => ucfirst($this->role ?? 'Unknown'),
            'level' => 99,
            'color' => 'secondary',
            'modules' => []
        ];

        try {
            return config("lims.roles.{$this->role}", $defaultConfig);
        } catch (\Exception $e) {
            \Log::error("Error getting role config for {$this->role}: " . $e->getMessage());
            return $defaultConfig;
        }
    }

    /**
     * Check if user has permission to access module
     */
    public function hasPermission($moduleId)
    {
        // Developer always has access
        if ($this->role === 'DEVEL') {
            return true;
        }

        try {
            $roleConfig = $this->getRoleConfigAttribute();
            
            if ($roleConfig['modules'] === 'all') {
                return true;
            }

            return is_array($roleConfig['modules']) && in_array((int)$moduleId, $roleConfig['modules']);
        } catch (\Exception $e) {
            \Log::error("Error checking permission for user {$this->id}: " . $e->getMessage());
            return false;
        }
    }

    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            return $this->role === $roles;
        }
        
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        
        return false;
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }

    public function isAdmin()
    {
        return in_array($this->role, ['ADMIN', 'SUPERVISOR', 'DEVEL']);
    }

    public function isSupervisor()
    {
        return in_array($this->role, ['SUPERVISOR', 'DEVEL']);
    }

    public function isDeveloper()
    {
        return $this->role === 'DEVEL';
    }

    public function registeredSamples()
    {
        return $this->hasMany(Sample::class, 'registered_by');
    }

    public function assignedSamples()
    {
        return $this->hasMany(Sample::class, 'assigned_to');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function auditLogs()
    {
        // Ganti 'App\Models\AuditLog' dengan nama model log yang sebenarnya.
        // Ganti 'user_id' jika kolom foreign key di tabel audit logs berbeda.
        return $this->hasMany(AuditLog::class); 
    }
}