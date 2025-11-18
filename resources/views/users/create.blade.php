@extends('layouts.app')

@section('title', 'Tambah User Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Tambah User Baru</h4>
        <p class="text-muted mb-0">Buat akun pengguna baru untuk sistem</p>
    </div>
    <div>
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
                    <i class="fas fa-user-plus me-2"></i>
                    Form User Baru
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('users.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" 
                                       value="{{ old('full_name') }}" required>
                                @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" 
                                       value="{{ old('username') }}" required>
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
                                       value="{{ old('email') }}" required>
                                @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Role <span class="text-danger">*</span></label>
                                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="DEVEL" {{ old('role') === 'DEVEL' ? 'selected' : '' }}>DEVEL</option>
                                    <option value="SUPERVISOR" {{ old('role') === 'SUPERVISOR' ? 'selected' : '' }}>SUPERVISOR</option>
                                    <option value="ADMIN" {{ old('role') === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                                    <option value="ANALYST" {{ old('role') === 'ANALYST' ? 'selected' : '' }}>ANALYST</option>
                                    <option value="SUPERVISOR_ANALYST" {{ old('role') === 'SUPERVISOR_ANALYST' ? 'selected' : '' }}>SUPERVISOR_ANALYST</option>
                                    <option value="TECH_AUDITOR" {{ old('role') === 'TECH_AUDITOR' ? 'selected' : '' }}>TECH_AUDITOR</option>
                                    <option value="QUALITY_AUDITOR" {{ old('role') === 'QUALITY_AUDITOR' ? 'selected' : '' }}>QUALITY_AUDITOR</option>
                                </select>
                                @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" 
                                   {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                User aktif (dapat login ke sistem)
                            </label>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary me-md-2">
                            <i class="fas fa-times me-2"></i>Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Buat User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Panduan Role
                </h6>
            </div>
            <div class="card-body">
                <small>
                    <strong>DEVEL:</strong> Akses penuh sistem<br>
                    <strong>SUPERVISOR:</strong> Manajemen laboratorium<br>
                    <strong>ADMIN:</strong> Customer service<br>
                    <strong>ANALYST:</strong> Pengujian sampel<br>
                    <strong>SUPERVISOR_ANALYST:</strong> Supervisi analis<br>
                    <strong>TECH_AUDITOR:</strong> Review teknis<br>
                    <strong>QUALITY_AUDITOR:</strong> Review mutu
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
