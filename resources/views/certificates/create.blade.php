@extends('layouts.app')

@section('title', 'Buat Sertifikat Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Buat Sertifikat Baru</h4>
        <p class="text-muted mb-0">Pilih sampel yang sudah divalidasi untuk diterbitkan sertifikatnya</p>
    </div>
    <div>
        <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

@if(\App\Models\Sample::where('status', 'validated')->count() === 0)
<div class="alert alert-warning">
    <i class="fas fa-exclamation-triangle me-2"></i>
    Belum ada sampel yang siap untuk diterbitkan sertifikat. 
    Sampel harus melalui proses validasi terlebih dahulu.
</div>
@else

<form action="{{ route('certificates.store') }}" method="POST" id="certificateForm">
    @csrf
    
    <div class="row">
        <!-- Sample Selection -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-vials me-2"></i>
                        Pilih Sampel yang Sudah Divalidasi
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach(\App\Models\Sample::with(['sampleRequest.customer', 'sampleType', 'tests.testParameter'])->where('status', 'validated')->get() as $sample)
                        <div class="col-md-6 mb-3">
                            <div class="card border-success sample-card" onclick="selectSample(this, {{ $sample->id }})">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ $sample->sample_code }}</h6>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sample_id" value="{{ $sample->id }}" required>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-2">
                                        <strong>Pelanggan:</strong> {{ $sample->sampleRequest->customer->contact_person }}<br>
                                        <strong>Perusahaan:</strong> {{ $sample->sampleRequest->customer->company_name }}<br>
                                        <strong>Jenis:</strong> {{ $sample->sampleType->name ?? $sample->custom_sample_type }}
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>
                                            {{ $sample->tests->count() }} parameter divalidasi
                                        </small>
                                    </div>
                                    
                                    <div>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            Divalidasi: {{ $sample->validated_at ? $sample->validated_at->diffForHumans() : 'Baru saja' }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Certificate Configuration -->
            <div class="card mt-3" id="certificateConfig" style="display: none;">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Konfigurasi Sertifikat
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Template Sertifikat</label>
                                <select name="template" class="form-select">
                                    <option value="standard">Template Standar</option>
                                    <option value="detailed">Template Detail</option>
                                    <option value="summary">Template Ringkas</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Masa Berlaku</label>
                                <select name="validity_period" class="form-select">
                                    <option value="365">1 Tahun</option>
                                    <option value="730">2 Tahun</option>
                                    <option value="1095">3 Tahun</option>
                                    <option value="">Tidak Terbatas</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Sertifikat</label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Catatan khusus untuk sertifikat ini (opsional)"></textarea>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="auto_issue" id="auto_issue">
                        <label class="form-check-label" for="auto_issue">
                            Terbitkan langsung setelah dibuat
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Details -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Detail Sampel Terpilih
                    </h6>
                </div>
                <div class="card-body" id="sampleDetails">
                    <p class="text-muted text-center">Pilih sampel untuk melihat detail</p>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-certificate me-2"></i>
                        Informasi Sertifikat
                    </h6>
                </div>
                <div class="card-body">
                    <p><strong>Format:</strong> PDF dengan tanda tangan digital</p>
                    <p><strong>Standar:</strong> ISO/IEC 17025:2017</p>
                    <p><strong>Bahasa:</strong> Indonesia & Inggris</p>
                    <p class="mb-0"><strong>Keamanan:</strong> QR Code untuk verifikasi</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Actions -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary me-md-2">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                    <i class="fas fa-save me-2"></i>Buat Sertifikat
                </button>
            </div>
        </div>
    </div>
</form>

@endif
@endsection

@push('scripts')
<script>
let samples = @json(\App\Models\Sample::with(['sampleRequest.customer', 'sampleType', 'tests.testParameter'])->where('status', 'validated')->get());

function selectSample(card, sampleId) {
    // Clear previous selection
    document.querySelectorAll('.sample-card').forEach(c => {
        c.classList.remove('border-primary');
        c.classList.add('border-success');
    });
    
    // Select current card
    card.classList.remove('border-success');
    card.classList.add('border-primary');
    
    // Check the radio button
    card.querySelector('input[type="radio"]').checked = true;
    
    // Show certificate config
    document.getElementById('certificateConfig').style.display = 'block';
    
    // Update sample details
    const sample = samples.find(s => s.id === sampleId);
    if (sample) {
        updateSampleDetails(sample);
    }
    
    // Enable submit button
    document.getElementById('submitBtn').disabled = false;
}

function updateSampleDetails(sample) {
    const detailsHtml = `
        <table class="table table-sm table-borderless">
            <tr>
                <td><strong>Kode:</strong></td>
                <td>${sample.sample_code}</td>
            </tr>
            <tr>
                <td><strong>Pelanggan:</strong></td>
                <td>${sample.sample_request.customer.contact_person}</td>
            </tr>
            <tr>
                <td><strong>Perusahaan:</strong></td>
                <td>${sample.sample_request.customer.company_name}</td>
            </tr>
            <tr>
                <td><strong>Jenis:</strong></td>
                <td>${sample.sample_type ? sample.sample_type.name : sample.custom_sample_type}</td>
            </tr>
            <tr>
                <td><strong>Parameter:</strong></td>
                <td>${sample.tests.length} parameter</td>
            </tr>
        </table>
        
        <h6 class="text-primary mt-3">Parameter Uji:</h6>
        <div class="mb-2">
            ${sample.tests.map(test => `
                <span class="badge bg-light text-dark me-1 mb-1">
                    ${test.test_parameter.name}
                </span>
            `).join('')}
        </div>
    `;
    
    document.getElementById('sampleDetails').innerHTML = detailsHtml;
}

// Form validation
document.getElementById('certificateForm').addEventListener('submit', function(e) {
    const selectedSample = document.querySelector('input[name="sample_id"]:checked');
    
    if (!selectedSample) {
        e.preventDefault();
        alert('Mohon pilih sampel yang akan diterbitkan sertifikatnya');
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Membuat Sertifikat...';
    submitBtn.disabled = true;
});

// Auto-issue checkbox change
document.getElementById('auto_issue').addEventListener('change', function() {
    const submitBtn = document.getElementById('submitBtn');
    if (this.checked) {
        submitBtn.innerHTML = '<i class="fas fa-certificate me-2"></i>Buat & Terbitkan';
        submitBtn.classList.remove('btn-primary');
        submitBtn.classList.add('btn-success');
    } else {
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Buat Sertifikat';
        submitBtn.classList.remove('btn-success');
        submitBtn.classList.add('btn-primary');
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        const selectedSample = document.querySelector('input[name="sample_id"]:checked');
        if (selectedSample) {
            document.getElementById('certificateForm').submit();
        }
    }
});
</script>
@endpush
