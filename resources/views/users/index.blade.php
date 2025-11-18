@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Manajemen Pengguna</h4>
        <p class="text-muted mb-0">Kelola akses pengguna dan hak akses sistem</p>
    </div>
    <div>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>User Baru
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">User Aktif</h6>
                        <h3 class="mb-0">{{ \App\Models\User::where('is_active', true)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Dapat mengakses sistem</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-primary">Analis</h6>
                        <h3 class="mb-0">{{ \App\Models\User::whereIn('role', ['ANALYST', 'SUPERVISOR_ANALYST'])->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-microscope fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Analis dan supervisor</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-warning">Login Hari Ini</h6>
                        <h3 class="mb-0">{{ \App\Models\User::whereDate('last_login_at', today())->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-sign-in-alt fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">User aktif hari ini</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-info">Admin & Supervisor</h6>
                        <h3 class="mb-0">{{ \App\Models\User::whereIn('role', ['ADMIN', 'SUPERVISOR', 'DEVEL'])->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-shield fa-2x text-info"></i>
                    </div>
                </div>
                <small class="text-muted">Pengguna admin</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter and Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" 
                       placeholder="Cari nama, username, email..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="role">
                    <option value="">Semua Role</option>
                    <option value="DEVEL" {{ request('role') === 'DEVEL' ? 'selected' : '' }}>DEVEL</option>
                    <option value="SUPERVISOR" {{ request('role') === 'SUPERVISOR' ? 'selected' : '' }}>SUPERVISOR</option>
                    <option value="ADMIN" {{ request('role') === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                    <option value="ANALYST" {{ request('role') === 'ANALYST' ? 'selected' : '' }}>ANALYST</option>
                    <option value="SUPERVISOR_ANALYST" {{ request('role') === 'SUPERVISOR_ANALYST' ? 'selected' : '' }}>SUPERVISOR_ANALYST</option>
                    <option value="TECH_AUDITOR" {{ request('role') === 'TECH_AUDITOR' ? 'selected' : '' }}>TECH_AUDITOR</option>
                    <option value="QUALITY_AUDITOR" {{ request('role') === 'QUALITY_AUDITOR' ? 'selected' : '' }}>QUALITY_AUDITOR</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-refresh"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-users me-2"></i>
            Daftar Pengguna
        </h6>
    </div>
    <div class="card-body">
        @php
            $users = \App\Models\User::orderBy('created_at', 'desc')->paginate(15);
        @endphp
        
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Login</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-3">
                                    <div class="avatar-placeholder bg-{{ $user->is_active ? 'primary' : 'secondary' }}">
                                        {{ strtoupper(substr($user->full_name, 0, 2)) }}
                                    </div>
                                </div>
                                <div>
                                    <strong>{{ $user->full_name }}</strong>
                                    @if(!$user->is_active)
                                    <br><small class="text-muted">Tidak Aktif</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td><code>{{ $user->username }}</code></td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role === 'DEVEL' ? 'danger' : ($user->role === 'SUPERVISOR' ? 'warning' : 'primary') }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Tidak Aktif</span>
                            @endif
                        </td>
                        <td>
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                            @else
                                <span class="text-muted">Belum pernah</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('users.show', $user->id) }}" 
                                   class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('users.edit', $user->id) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }} btn-sm"
                                        onclick="toggleUser({{ $user->id }})">
                                    <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                </button>
                                @if($user->id !== auth()->id())
                                <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="resetPassword({{ $user->id }})">
                                    <i class="fas fa-key"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($users->hasPages())
        <div class="d-flex justify-content-center mt-3">
            {{ $users->appends(request()->query())->links() }}
        </div>
        @endif
        
        @else
        <div class="text-center py-5">
            <i class="fas fa-users fa-5x text-muted mb-3"></i>
            <h5>Tidak Ada User Ditemukan</h5>
            <p class="text-muted mb-3">Belum ada pengguna yang terdaftar atau sesuai filter.</p>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah User Pertama
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}
</style>
@endpush

@push('scripts')
<script>
function toggleUser(id) {
    if (confirm('Apakah Anda yakin ingin mengubah status user ini?')) {
        fetch(`/users/${id}/toggle`, {
            method: 'POST',
            headers
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Gagal mengubah status'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}

function resetPassword(userId, username) {
    const newPassword = prompt(`Reset password untuk user "${username}".\nMasukkan password baru (kosong untuk default "123456"):`);
    
    if (newPassword !== null) {
        const password = newPassword.trim() || '123456';
        
        if (confirm(`Reset password user "${username}" ke "${password}"?`)) {
            fetch(`/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    new_password: password
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Password berhasil direset ke: ${password}`);
                } else {
                    alert('Error: ' + (data.message || 'Gagal reset password'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan sistem');
            });
        }
    }
}

// Auto-refresh every 5 minutes for online status
setInterval(function() {
    if (document.hidden) return;
    location.reload();
}, 300000);
</script>
@endpush
