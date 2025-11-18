@extends('layouts.app')

@section('title', 'Penerbitan Sertifikat')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Penerbitan Sertifikat</h4>
        <p class="text-muted mb-0">Kelola penerbitan sertifikat hasil pengujian</p>
    </div>
    <div>
        <a href="{{ route('certificates.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Buat Sertifikat
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-warning">Siap Diterbitkan</h6>
                        <h3 class="mb-0">{{ \App\Models\Sample::where('status', 'validated')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Sampel yang sudah divalidasi</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-primary">Draft</h6>
                        <h3 class="mb-0">{{ \App\Models\Certificate::where('status', 'draft')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-edit fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Sertifikat dalam proses</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">Diterbitkan</h6>
                        <h3 class="mb-0">{{ \App\Models\Certificate::where('status', 'issued')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-certificate fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Sertifikat aktif</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-info">Bulan Ini</h6>
                        <h3 class="mb-0">{{ \App\Models\Certificate::where('status', 'issued')->whereMonth('issued_at', now()->month)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x text-info"></i>
                    </div>
                </div>
                <small class="text-muted">Diterbitkan bulan ini</small>
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
                       placeholder="Cari nomor sertifikat, pelanggan..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Diterbitkan</option>
                    <option value="revoked" {{ request('status') === 'revoked' ? 'selected' : '' }}>Dicabut</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Certificates List -->
<div class="row">
    @php
        $certificates = \App\Models\Certificate::with(['sample.sampleRequest.customer'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    @endphp
    
    @forelse($certificates as $certificate)
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-{{ $certificate->status === 'draft' ? 'warning' : ($certificate->status === 'issued' ? 'success' : 'danger') }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-certificate me-2"></i>
                    {{ $certificate->certificate_number ?? 'Draft' }}
                </h6>
                <span class="badge bg-{{ $certificate->status === 'draft' ? 'warning' : ($certificate->status === 'issued' ? 'success' : 'danger') }}">
                    {{ ucfirst($certificate->status) }}
                </span>
            </div>
            
            <div class="card-body">
                <div class="mb-3">
                    <strong>Sampel:</strong> {{ $certificate->sample->sample_code }}<br>
                    <strong>Pelanggan:</strong> {{ $certificate->sample->sampleRequest->customer->contact_person }}<br>
                    <strong>Perusahaan:</strong> {{ $certificate->sample->sampleRequest->customer->company_name }}<br>
                    <strong>Jenis Sampel:</strong> {{ $certificate->sample->sampleType->name ?? $certificate->sample->custom_sample_type }}
                </div>

                @if($certificate->issued_at)
                <div class="mb-3">
                    <small class="text-success">
                        <i class="fas fa-calendar-check me-1"></i>
                        Diterbitkan: {{ $certificate->issued_at->format('d/m/Y H:i') }}
                    </small><br>
                    @if($certificate->valid_until)
                    <small class="text-muted">
                        <i class="fas fa-calendar-times me-1"></i>
                        Berlaku hingga: {{ $certificate->valid_until->format('d/m/Y') }}
                    </small>
                    @endif
                </div>
                @endif

                @if($certificate->notes)
                <div class="mb-3">
                    <small class="text-muted">
                        <strong>Catatan:</strong><br>
                        {{ Str::limit($certificate->notes, 100) }}
                    </small>
                </div>
                @endif
            </div>
            
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <a href="{{ route('certificates.show', $certificate->id) }}" 
                       class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>Lihat Detail
                    </a>
                    
                    @if($certificate->status === 'draft')
                    <div class="btn-group">
                        <a href="{{ route('certificates.preview', $certificate->id) }}" 
                           class="btn btn-outline-secondary btn-sm" target="_blank">
                            <i class="fas fa-eye me-1"></i>Preview
                        </a>
                        <button class="btn btn-success btn-sm" 
                                onclick="issueCertificate({{ $certificate->id }})">
                            <i class="fas fa-check me-1"></i>Terbitkan
                        </button>
                    </div>
                    @elseif($certificate->status === 'issued')
                    <div class="btn-group">
                        <a href="{{ route('certificates.download', $certificate->id) }}" 
                           class="btn btn-success btn-sm">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                        <a href="{{ route('certificates.preview', $certificate->id) }}" 
                           class="btn btn-outline-secondary btn-sm" target="_blank">
                            <i class="fas fa-eye me-1"></i>Preview
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-certificate fa-5x text-muted mb-3"></i>
                <h5>Tidak Ada Sertifikat</h5>
                <p class="text-muted mb-3">Belum ada sertifikat yang dibuat atau sesuai filter.</p>
                <a href="{{ route('certificates.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Buat Sertifikat Pertama
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($certificates->hasPages())
<div class="d-flex justify-content-center">
    {{ $certificates->appends(request()->query())->links() }}
</div>
@endif

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-tasks me-2"></i>
                    Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="list-group list-group-flush">
                    <a href="{{ route('certificates.create') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Buat Sertifikat Baru
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="exportCertificates()">
                        <i class="fas fa-download text-success me-2"></i>
                        Export Daftar Sertifikat
                    </a>
                    <a href="#" class="list-group-item list-group-item-action" onclick="bulkIssue()">
                        <i class="fas fa-certificate text-warning me-2"></i>
                        Terbitkan Massal
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Status Sertifikat
                </h6>
            </div>
            <div class="card-body">
                <canvas id="certificateStatusChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function issueCertificate(id) {
    if (confirm('Apakah Anda yakin ingin menerbitkan sertifikat ini?')) {
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
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Gagal menerbitkan sertifikat'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}

function exportCertificates() {
    window.open('/certificates/export?format=excel', '_blank');
}

function bulkIssue() {
    alert('Fitur terbitkan massal akan segera tersedia');
}

// Certificate Status Chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('certificateStatusChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Draft', 'Diterbitkan', 'Dicabut'],
            datasets: [{
                data: [
                    {{ \App\Models\Certificate::where('status', 'draft')->count() }},
                    {{ \App\Models\Certificate::where('status', 'issued')->count() }},
                    {{ \App\Models\Certificate::where('status', 'revoked')->count() }}
                ],
                backgroundColor: ['#ffc107', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey) {
        switch(e.key) {
            case 'n': // Ctrl+N - New certificate
                e.preventDefault();
                location.href = '{{ route("certificates.create") }}';
                break;
            case 'e': // Ctrl+E - Export
                e.preventDefault();
                exportCertificates();
                break;
        }
    }
});
</script>
@endpush
