@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-server me-2"></i>
                    Konfigurasi Sistem
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.update') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Laboratorium</label>
                        <input type="text" name="lab_name" class="form-control" 
                               value="{{ config('lims.lab_name', 'LIMS Laboratory') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alamat Laboratorium</label>
                        <textarea name="lab_address" class="form-control" rows="3">{{ config('lims.lab_address') }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Kode Prefix Tracking</label>
                        <input type="text" name="tracking_prefix" class="form-control" 
                               value="{{ config('lims.tracking_prefix', 'UNEJ') }}">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Simpan Konfigurasi
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-database me-2"></i>
                    Backup & Maintenance
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <form method="POST" action="{{ route('settings.backup') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-download me-2"></i>Buat Backup Database
                        </button>
                    </form>
                    
                    
                    <button type="button" class="btn btn-warning" onclick="clearCache()">
                        <i class="fas fa-broom me-2"></i>Clear Cache
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Sistem
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Laravel Version:</strong><br>
                        {{ app()->version() }}
                    </div>
                    <div class="col-md-3">
                        <strong>PHP Version:</strong><br>
                        {{ PHP_VERSION }}
                    </div>
                    <div class="col-md-3">
                        <strong>Environment:</strong><br>
                        {{ config('app.env') }}
                    </div>
                    <div class="col-md-3">
                        <strong>Debug Mode:</strong><br>
                        {{ config('app.debug') ? 'Enabled' : 'Disabled' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function clearCache() {
    if (confirm('Apakah Anda yakin ingin membersihkan cache sistem?')) {
        fetch('/api/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache berhasil dibersihkan');
            } else {
                alert('Gagal membersihkan cache');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}
</script>
@endpush
