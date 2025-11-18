@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="row">
    <div class="col-md-12">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2"></i>
                    Pengaturan Sistem
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('settings.update') }}"> 
                    @csrf
                    @method('PATCH') 
                    
                    <h6 class="text-primary mb-3 mt-0">Informasi Laboratorium</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Laboratorium</label>
                            <input type="text" name="settings[lab_name]" 
                                   value="{{ $settings->get('lab_name')->setting_value ?? '' }}" 
                                   class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Akreditasi</label>
                            <input type="text" name="settings[accreditation_number]" 
                                   value="{{ $settings->get('accreditation_number')->setting_value ?? '' }}" 
                                   class="form-control">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="settings[lab_address]" rows="3"
                                      class="form-control">{{ $settings->get('lab_address')->setting_value ?? '' }}</textarea> 
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telepon</label>
                            <input type="text" name="settings[lab_phone]" 
                                   value="{{ $settings->get('lab_phone')->setting_value ?? '' }}" 
                                   class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="settings[lab_email]" 
                                   value="{{ $settings->get('lab_email')->setting_value ?? '' }}" 
                                   class="form-control">
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="text-primary mb-3">Konfigurasi Sistem</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Masa Berlaku Sertifikat (hari)</label>
                            <input type="number" name="settings[certificate_validity_days]" 
                                   value="{{ $settings->get('certificate_validity_days')->setting_value ?? '365' }}" 
                                   class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Session Timeout (menit)</label>
                            <input type="number" name="settings[session_lifetime]" 
                                   value="{{ $settings->get('session_lifetime')->setting_value ?? '120' }}" 
                                   class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Auto Assign Sampel</label>
                            <select name="settings[auto_assign_samples]"
                                    class="form-select">
                                <option value="false" {{ ($settings->get('auto_assign_samples')->setting_value ?? 'false') == 'false' ? 'selected' : '' }}>Tidak</option> 
                                <option value="true" {{ ($settings->get('auto_assign_samples')->setting_value ?? 'false') == 'true' ? 'selected' : '' }}>Ya</option> 
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Wajib Upload Foto Sampel</label>
                            <select name="settings[require_sample_photos]"
                                    class="form-select">
                                <option value="false" {{ ($settings->get('require_sample_photos')->setting_value ?? 'false') == 'false' ? 'selected' : '' }}>Tidak</option> 
                                <option value="true" {{ ($settings->get('require_sample_photos')->setting_value ?? 'false') == 'true' ? 'selected' : '' }}>Ya</option> 
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end pt-3">
                        <button type="submit" 
                                class="btn btn-primary">
                            <i class="fas fa-save me-2"></i> Simpan Pengaturan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-broom me-2"></i>
                    Aksi Sistem
                </h5>
            </div>
            <div class="card-body">
                <p>Klik tombol di bawah ini untuk membersihkan cache aplikasi. Tindakan ini mungkin diperlukan setelah melakukan pembaruan sistem.</p>
                <button type="button" class="btn btn-warning" onclick="clearCache()">
                    <i class="fas fa-sync-alt me-2"></i> Bersihkan Cache
                </button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
// NOTE: Sesuai pedoman, fungsi confirm/alert harus diganti dengan modal kustom di Blade.
// Untuk menjaga fungsionalitas sementara, fungsi ini dipertahankan dengan perubahan pada
// pemanggilan AJAX untuk menampilkan pesan sukses/gagal melalui notifikasi Blade (alert-success).
function clearCache() {
    if (confirm('Apakah Anda yakin ingin membersihkan cache sistem?')) {
        fetch('/api/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tampilkan pesan sukses (gunakan Bootstrap alert)
                const alertHtml = `
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Cache berhasil dibersihkan
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                document.querySelector('.row').insertAdjacentHTML('afterbegin', alertHtml);
            } else {
                // Tampilkan pesan gagal
                const alertHtml = `
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Gagal membersihkan cache
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                document.querySelector('.row').insertAdjacentHTML('afterbegin', alertHtml);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const alertHtml = `
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    Terjadi kesalahan sistem
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            document.querySelector('.row').insertAdjacentHTML('afterbegin', alertHtml);
        });
    }
}
</script>
@endpush
