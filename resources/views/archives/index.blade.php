@extends('layouts.app')

@section('title', 'Arsip & Data')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Arsip & Data Laboratorium</h4>
        <p class="text-muted mb-0">Akses data historis dan arsip dokumen</p>
    </div>
    <div>
        <button class="btn btn-success" onclick="exportArchive()">
            <i class="fas fa-download me-2"></i>Export Data
        </button>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">Total Arsip</h6>
                        <h3 class="mb-0">{{ $stats['total_archived'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-archive fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Sampel yang diarsipkan</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-primary">Bulan Ini</h6>
                        <h3 class="mb-0">{{ $stats['archived_this_month'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-calendar fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Diarsipkan bulan ini</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-warning">Sertifikat</h6>
                        <h3 class="mb-0">{{ $stats['certificates_issued'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-certificate fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Sertifikat diterbitkan</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-info">Pelanggan</h6>
                        <h3 class="mb-0">{{ $stats['total_customers'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                </div>
                <small class="text-muted">Total pelanggan unik</small>
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
                       placeholder="Cari kode sampel, pelanggan..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="sample_type">
                    <option value="">Semua Jenis</option>
                    @foreach($sampleTypes as $name => $label)
                    <option value="{{ $name }}" {{ request('sample_type') === $name ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}" placeholder="Dari">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Archive List -->
<div class="row">
    @forelse($samples as $sample)
    <div class="col-md-6 mb-4">
        <div class="card border-success">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-vial me-2"></i>
                    {{ $sample->sample_code }}
                </h6>
                <span class="badge bg-success">{{ ucfirst($sample->status) }}</span>
            </div>
            
            <div class="card-body">
                <div class="mb-3">
                    <strong>Pelanggan:</strong> {{ $sample->sampleRequest->customer->contact_person }}<br>
                    <strong>Perusahaan:</strong> {{ $sample->sampleRequest->customer->company_name }}<br>
                    <strong>Jenis:</strong> {{ $sample->sampleType->name ?? $sample->custom_sample_type }}<br>
                    <strong>Analis:</strong> {{ $sample->assignedAnalyst->full_name ?? '-' }}
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <small><strong>Total Parameter:</strong></small>
                        <span class="badge bg-primary">{{ $sample->tests->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <small><strong>Parameter Selesai:</strong></small>
                        <span class="badge bg-success">{{ $sample->tests->where('status', 'validated')->count() }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-success">
                        <i class="fas fa-calendar-check me-1"></i>
                        Diupdate: {{ $sample->updated_at->format('d/m/Y H:i') }}
                    </small><br>
                    @if($sample->testing_completed_at)
                    <small class="text-info">
                        <i class="fas fa-microscope me-1"></i>
                        Pengujian selesai: {{ $sample->testing_completed_at->format('d/m/Y H:i') }}
                    </small>
                    @endif
                </div>

                @if($sample->certificate)
                <div class="mb-3">
                    <small class="text-warning">
                        <i class="fas fa-certificate me-1"></i>
                        Sertifikat: {{ $sample->certificate->certificate_number ?? 'Draft' }}
                    </small>
                </div>
                @endif
            </div>
            
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <a href="{{ route('archives.show', $sample->id) }}" 
                       class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>Lihat Detail Arsip
                    </a>
                    @if($sample->certificate && $sample->certificate->status === 'issued')
                    <a href="{{ route('certificates.download', $sample->certificate->id) }}" 
                       class="btn btn-outline-success btn-sm">
                        <i class="fas fa-download me-1"></i>Download Sertifikat
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-archive fa-5x text-muted mb-3"></i>
                <h5>Tidak Ada Data Arsip</h5>
                <p class="text-muted mb-0">Belum ada data yang diarsipkan atau sesuai filter.</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($samples->hasPages())
<div class="d-flex justify-content-center">
    {{ $samples->appends(request()->query())->links() }}
</div>
@endif

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Data Arsip</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('archives.export') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Format Export</label>
                        <select name="format" class="form-select" required>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="date" name="date_from" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="date" name="date_to" class="form-control" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Export</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportArchive() {
    const modal = new bootstrap.Modal(document.getElementById('exportModal'));
    modal.show();
}

// Set default dates
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    
    const dateFromInput = document.querySelector('input[name="date_from"]');
    const dateToInput = document.querySelector('input[name="date_to"]');
    
    if (dateFromInput && !dateFromInput.value) {
        dateFromInput.value = firstDay.toISOString().split('T')[0];
    }
    if (dateToInput && !dateToInput.value) {
        dateToInput.value = today.toISOString().split('T')[0];
    }
});
</script>
@endpush