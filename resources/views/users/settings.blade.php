@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Profil Pengguna
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update-profile') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="full_name" class="form-control" 
                               value="{{ auth()->user()->full_name }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" 
                               value="{{ auth()->user()->email }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="phone" class="form-control" 
                               value="{{ auth()->user()->phone }}">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Profil
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Account Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Akun
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-{{ auth()->user()->is_active ? 'success' : 'secondary' }}">
                                {{ auth()->user()->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Terdaftar:</strong></td>
                        <td>{{ auth()->user()->created_at->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Login:</strong></td>
                        <td>{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->format('d/m/Y H:i') : 'Belum pernah' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Activity Summary -->
        @if(auth()->user()->role === 'ANALYST' || auth()->user()->role === 'SUPERVISOR_ANALYST')
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Aktivitas Saya
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Sampel Aktif:</strong> 
                   {{ auth()->user()->assignedSamples()->whereIn('status', ['assigned', 'testing'])->count() }}</p>
                <p class="mb-1"><strong>Sampel Selesai:</strong> 
                   {{ auth()->user()->assignedSamples()->where('status', 'completed')->count() }}</p>
                <p class="mb-0"><strong>Total Ditugaskan:</strong> 
                   {{ auth()->user()->assignedSamples()->count() }}</p>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-key me-2"></i>
                    Ubah Password
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.change-password') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-key me-2"></i>Ubah Password
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Preferensi
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('users.update-preferences') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Notifikasi Email</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="email_notifications" value="1" id="email_notifications"
                                   {{ (auth()->user()->preferences['email_notifications'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                Terima notifikasi via email
                            </label>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tema Dashboard</label>
                        <select name="theme" class="form-select">
                            <option value="light" {{ (auth()->user()->preferences['theme'] ?? 'light') === 'light' ? 'selected' : '' }}>Terang</option>
                            <option value="dark" {{ (auth()->user()->preferences['theme'] ?? 'light') === 'dark' ? 'selected' : '' }}>Gelap</option>
                        </select>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save me-2"></i>Simpan Preferensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Form validation for password change
document.querySelector('form[action*="change-password"]').addEventListener('submit', function(e) {
    const password = document.querySelector('input[name="password"]').value;
    const confirmation = document.querySelector('input[name="password_confirmation"]').value;
    
    if (password !== confirmation) {
        e.preventDefault();
        alert('Konfirmasi password tidak cocok');
        return;
    }
    
    if (password.length < 8) {
        e.preventDefault();
        alert('Password minimal 8 karakter');
        return;
    }
});

// Auto-save preferences
document.querySelectorAll('input[name="email_notifications"], select[name="theme"]').forEach(element => {
    element.addEventListener('change', function() {
        // Auto-save after 2 seconds of inactivity
        clearTimeout(window.preferencesTimeout);
        window.preferencesTimeout = setTimeout(() => {
            document.querySelector('form[action*="update-preferences"]').submit();
        }, 2000);
    });
});
</script>
@endpush
