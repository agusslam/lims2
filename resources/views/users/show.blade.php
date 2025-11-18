@extends('layouts.app')

@section('title', 'Detail User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $user->full_name }}</h4>
        <p class="text-muted mb-0">
            Role: <span class="badge bg-primary">{{ $user->role }}</span>
            Status: <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}</span>
        </p>
    </div>
    <div>
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary me-2">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Informasi User
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="200"><strong>Nama Lengkap:</strong></td>
                        <td>{{ $user->full_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Username:</strong></td>
                        <td><code>{{ $user->username }}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Role:</strong></td>
                        <td><span class="badge bg-primary">{{ $user->role }}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'secondary' }}">
                                {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Terdaftar:</strong></td>
                        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Login:</strong></td>
                        <td>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Belum pernah' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistik Aktivitas
                </h6>
            </div>
            <div class="card-body">
                @if($user->role === 'ANALYST' || $user->role === 'SUPERVISOR_ANALYST')
                <p><strong>Sampel Ditugaskan:</strong> {{ $user->assignedSamples()->count() }}</p>
                <p><strong>Sampel Selesai:</strong> {{ $user->assignedSamples()->where('status', 'completed')->count() }}</p>
                @endif
                <p><strong>Login Count:</strong> {{ $user->login_count ?? 0 }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
