@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Edit User: {{ $user->full_name }}</h4>
        <p class="text-muted mb-0">Username: <code>{{ $user->username }}</code></p>
    </div>
    <div>
        <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-info me-2">
            <i class="fas fa-eye me-2"></i>Lihat Detail
        </a>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<form action="{{ route('users.update', $user->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Form Edit User
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                                       value="{{ old('full_name', $user->full_name) }}" required>
                                @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                       value="{{ old('username', $user->username) }}" required>
                                @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="DEVEL" {{ old('role', $user->role) === 'DEVEL' ? 'selected' : '' }}>DEVEL</option>
                                    <option value="SUPERVISOR" {{ old('role', $user->role) === 'SUPERVISOR' ? 'selected' : '' }}>SUPERVISOR</option>
                                    <option value="ADMIN" {{ old('role', $user->role) === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                                    <option value="ANALYST" {{ old('role', $user->role) === 'ANALYST' ? 'selected' : '' }}>ANALYST</option>
                                    <option value="SUPERVISOR_ANALYST" {{ old('role', $user->role) === 'SUPERVISOR_ANALYST' ? 'selected' : '' }}>SUPERVISOR_ANALYST</option>
                                    <option value="TECH_AUDITOR" {{ old('role', $user->role) === 'TECH_AUDITOR' ? 'selected' : '' }}>TECH_AUDITOR</option>
                                    <option value="QUALITY_AUDITOR" {{ old('role', $user->role) === 'QUALITY_AUDITOR' ? 'selected' : '' }}>QUALITY_AUDITOR</option>
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" 
                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                User aktif (dapat login ke sistem)
                            </label>
                        </div>
                    </div>

                    <hr>

                    <h6>Reset Password (Opsional)</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informasi User
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Dibuat:</strong></td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Diupdate:</strong></td>
                            <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Last Login:</strong></td>
                            <td>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Belum pernah' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
