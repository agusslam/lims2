@extends('layouts.app')

@section('title', 'Detail Sertifikat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">{{ $certificate->certificate_number ?? 'Draft Sertifikat' }}</h4>
        <p class="text-muted mb-0">
            Status: 
            <span class="badge bg-{{ $certificate->status === 'draft' ? 'warning' : ($certificate->status === 'issued' ? 'success' : 'danger') }}">
                {{ ucfirst($certificate->status) }}
            </span>
        </p>
    </div>
    <div>
        @if($certificate->status === 'draft')
        <button class="btn btn-success me-2" onclick="issueCertificate({{ $certificate->id }})">
            <i class="fas fa-check me-2"></i>Terbitkan
        </button>
        @endif
        <a href="{{ route('certificates.preview', $certificate->id) }}" class="btn btn-outline-secondary me-2" target="_blank">
            <i class="fas fa-eye me-2"></i>Preview
        </a>
        @if($certificate->status === 'issued')
        <a href="{{ route('certificates.download', $certificate->id) }}" class="btn btn-primary me-2">
            <i class="fas fa-download me-2"></i>Download
        </a>
        @endif
        <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Certificate Information -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Sertifikat
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Nomor Sertifikat:</strong></td>
                        <td>{{ $certificate->certificate_number ?? 'Belum diterbitkan' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-{{ $certificate->status === 'draft' ? 'warning' : ($certificate->status === 'issued' ? 'success' : 'danger') }}">
                                {{ ucfirst($certificate->status) }}
                            </span>
                        </td>
                    </tr>
                    @if($certificate->issued_at)
                    <tr>
                        <td><strong>Tanggal Terbit:</strong></td>
                        <td>{{ $certificate->issued_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                    @if($certificate->valid_until)
                    <tr>
                        <td><strong>Berlaku Hingga:</strong></td>
                        <td>{{ $certificate->valid_until->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Dibuat:</strong></td>
                        <td>{{ $certificate->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Sample Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-vial me-2"></i>
                    Informasi Sampel
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Kode Sampel:</strong></td>
                        <td>{{ $certificate->sample->sample_code }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Sampel:</strong></td>
                        <td>{{ $certificate->sample->sampleType->name ?? $certificate->sample->custom_sample_type }}</td>
                    </tr>
                    <tr>
                        <td><strong>Quantity:</strong></td>
                        <td>{{ $certificate->sample->quantity }} sampel</td>
                    </tr>
                    <tr>
                        <td><strong>Analis:</strong></td>
                        <td>{{ $certificate->sample->assignedAnalyst->full_name }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Customer Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    Informasi Pelanggan
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Contact Person:</strong></td>
                        <td>{{ $certificate->sample->sampleRequest->customer->contact_person }}</td>
                    </tr>
                    <tr>
                        <td><strong>Perusahaan:</strong></td>
                        <td>{{ $certificate->sample->sampleRequest->customer->company_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $certificate->sample->sampleRequest->customer->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Alamat:</strong></td>
                        <td>{{ $certificate->sample->sampleRequest->customer->address }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Test Results -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-microscope me-2"></i>
                    Hasil Pengujian
                </h6>
            </div>
            <div class="card-body">
                @foreach($certificate->sample->tests->groupBy('testParameter.category') as $category => $tests)
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0 text-primary">{{ $category }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Parameter</th>
                                        <th>Hasil</th>
                                        <th>Satuan</th>
                                        <th>Metode</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tests as $test)
                                    <tr>
                                        <td><strong>{{ $test->testParameter->name }}</strong></td>
                                        <td>{{ $test->result_value ?? '-' }}</td>
                                        <td>{{ $test->testParameter->unit ?? '-' }}</td>
                                        <td><small>{{ $test->testParameter->method ?? '-' }}</small></td>
                                        <td>
                                            <span class="badge bg-{{ $test->status === 'validated' ? 'success' : 'warning' }}">
                                                {{ ucfirst($test->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @if($test->notes)
                                    <tr>
                                        <td colspan="5">
                                            <small class="text-muted">
                                                <strong>Catatan:</strong> {{ $test->notes }}
                                            </small>
                                        </td>
                                    </tr>
                                    @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Certificate Notes -->
        @if($certificate->notes)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-sticky-note me-2"></i>
                    Catatan Sertifikat
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $certificate->notes }}</p>
            </div>
        </div>
        @endif

        <!-- Digital Signature Info -->
        @if($certificate->digital_signature)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-signature me-2"></i>
                    Tanda Tangan Digital
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Hash:</strong><br>
                        <code>{{ Str::limit($certificate->digital_signature['hash'] ?? '', 50) }}</code>
                    </div>
                    <div class="col-md-6">
                        <strong>Ditandatangani oleh:</strong><br>
                        {{ $certificate->digital_signature['signed_by'] ?? 'System' }}<br>
                        <small class="text-muted">{{ $certificate->issued_at ? $certificate->issued_at->format('d/m/Y H:i') : '' }}</small>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function issueCertificate(id) {
    if (confirm('Apakah Anda yakin ingin menerbitkan sertifikat ini? Setelah diterbitkan, sertifikat tidak dapat diubah.')) {
        // Show loading state
        const btn = document.querySelector('button[onclick*="issueCertificate"]');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menerbitkan...';
        btn.disabled = true;
        
        fetch(`/certificates/${id}/issue`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Sertifikat berhasil diterbitkan!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Gagal menerbitkan sertifikat'));
                btn.innerHTML = '<i class="fas fa-check me-2"></i>Terbitkan';
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
            btn.innerHTML = '<i class="fas fa-check me-2"></i>Terbitkan';
            btn.disabled = false;
        });
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey) {
        switch(e.key) {
            case 'p': // Ctrl+P - Preview
                e.preventDefault();
                window.open('{{ route("certificates.preview", $certificate->id) }}', '_blank');
                break;
            case 'd': // Ctrl+D - Download (if issued)
                @if($certificate->status === 'issued')
                e.preventDefault();
                location.href = '{{ route("certificates.download", $certificate->id) }}';
                @endif
                break;
        }
    }
});
</script>
@endpush
