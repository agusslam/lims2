@extends('layouts.app')

@section('title', 'Review & Validasi')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Review & Validasi</h4>
        <p class="text-muted mb-0">Review teknis dan mutu hasil pengujian</p>
    </div>
    <div>
        @if(auth()->user()->hasRole('TECH_AUDITOR'))
        <span class="badge bg-warning fs-6 px-3 py-2">
            <i class="fas fa-microscope me-2"></i>
            Technical Auditor
        </span>
        @elseif(auth()->user()->hasRole('QUALITY_AUDITOR'))
        <span class="badge bg-info fs-6 px-3 py-2">
            <i class="fas fa-award me-2"></i>
            Quality Auditor
        </span>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-warning">Review Teknis</h6>
                        <h3 class="mb-0">{{ $stats['pending_tech_review'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-microscope fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Menunggu review teknis</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-info">Review Mutu</h6>
                        <h3 class="mb-0">{{ $stats['pending_quality_review'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-award fa-2x text-info"></i>
                    </div>
                </div>
                <small class="text-muted">Menunggu review mutu</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">Divalidasi Hari Ini</h6>
                        <h3 class="mb-0">{{ $stats['validated_today'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Validasi hari ini</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-primary">Rata-rata Review</h6>
                        <h3 class="mb-0">2.3</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Hari untuk review</small>
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
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="review_tech" {{ request('status') === 'review_tech' ? 'selected' : '' }}>Review Teknis</option>
                    <option value="review_quality" {{ request('status') === 'review_quality' ? 'selected' : '' }}>Review Mutu</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="analyst">
                    <option value="">Semua Analis</option>
                    @foreach(\App\Models\User::whereIn('role', ['ANALYST', 'SUPERVISOR_ANALYST'])->get() as $analyst)
                    <option value="{{ $analyst->id }}" {{ request('analyst') == $analyst->id ? 'selected' : '' }}>
                        {{ $analyst->full_name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('review.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-refresh"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Samples List -->
<div class="row">
    @forelse($samples as $sample)
    <div class="col-md-6 mb-4">
        <div class="card h-100 {{ $sample->status === 'review_tech' ? 'border-warning' : 'border-info' }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-vial me-2"></i>
                    {{ $sample->sample_code }}
                </h6>
                <div>
                    <span class="badge bg-{{ $sample->status === 'review_tech' ? 'warning' : 'info' }}">
                        {{ $sample->status === 'review_tech' ? 'Review Teknis' : 'Review Mutu' }}
                    </span>
                    @php
                        $urgentThreshold = now()->subDays(3);
                        $isUrgent = $sample->testing_completed_at < $urgentThreshold;
                    @endphp
                    @if($isUrgent)
                    <span class="badge bg-danger ms-1">URGENT</span>
                    @endif
                </div>
            </div>
            
            <div class="card-body">
                <div class="mb-3">
                    <strong>Pelanggan:</strong> {{ $sample->sampleRequest->customer->contact_person }}<br>
                    <strong>Jenis:</strong> {{ $sample->sampleType->name ?? $sample->custom_sample_type }}<br>
                    <strong>Analis:</strong> {{ $sample->assignedAnalyst->full_name ?? '-' }}
                </div>

                <!-- Test Summary -->
                @php
                    $totalTests = $sample->tests->count();
                    $completedTests = $sample->tests->where('status', '!=', 'pending')->count();
                @endphp
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Parameter Uji</small>
                        <small>{{ $totalTests }} parameter</small>
                    </div>
                    @foreach($sample->tests->groupBy('testParameter.category') as $category => $tests)
                        <span class="badge bg-light text-dark me-1 mb-1">
                            {{ $category }}: {{ $tests->count() }}
                        </span>
                    @endforeach
                </div>

                <!-- Testing Timeline -->
                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Pengujian selesai: {{ $sample->testing_completed_at->diffForHumans() }}
                    </small><br>
                    @if($sample->testing_completed_at)
                        @php
                            $daysSinceCompletion = $sample->testing_completed_at->diffInDays(now());
                        @endphp
                        <small class="text-{{ $daysSinceCompletion > 2 ? 'danger' : 'success' }}">
                            <i class="fas fa-calendar me-1"></i>
                            {{ $daysSinceCompletion }} hari yang lalu
                        </small>
                    @endif
                </div>

                @if($sample->tech_review_notes)
                <div class="mb-3">
                    <small class="text-info">
                        <strong>Catatan Review Teknis:</strong><br>
                        {{ $sample->tech_review_notes }}
                    </small>
                </div>
                @endif
            </div>
            
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <a href="{{ route('review.show', $sample->id) }}" 
                       class="btn btn-{{ $sample->status === 'review_tech' ? 'warning' : 'info' }}">
                        <i class="fas fa-{{ $sample->status === 'review_tech' ? 'microscope' : 'award' }} me-2"></i>
                        {{ $sample->status === 'review_tech' ? 'Review Teknis' : 'Review Mutu' }}
                    </a>
                    
                    <div class="btn-group">
                        <button class="btn btn-outline-success btn-sm" 
                                onclick="quickApprove({{ $sample->id }})">
                            <i class="fas fa-check me-1"></i>Quick Approve
                        </button>
                        <button class="btn btn-outline-danger btn-sm" 
                                onclick="quickReject({{ $sample->id }})">
                            <i class="fas fa-times me-1"></i>Quick Reject
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-check fa-5x text-muted mb-3"></i>
                <h5>Tidak Ada Sampel untuk Review</h5>
                <p class="text-muted mb-0">
                    @if(auth()->user()->hasRole('TECH_AUDITOR'))
                        Belum ada sampel yang siap untuk review teknis.
                    @elseif(auth()->user()->hasRole('QUALITY_AUDITOR'))
                        Belum ada sampel yang siap untuk review mutu.
                    @else
                        Belum ada sampel yang memerlukan review.
                    @endif
                </p>
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

<!-- Quick Action Modals -->
<div class="modal fade" id="quickApproveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Approve</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickApproveForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Catatan Review:</label>
                        <textarea name="review_notes" class="form-control" rows="3" 
                                  placeholder="Hasil review memenuhi standar..."></textarea>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="certificate_required" value="1" class="form-check-input">
                        <label class="form-check-label">Memerlukan sertifikat</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="quickRejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quick Reject</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="quickRejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan:</label>
                        <textarea name="reject_reason" class="form-control" rows="3" required
                                  placeholder="Jelaskan alasan penolakan dan tindakan yang diperlukan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function quickApprove(sampleId) {
    const form = document.getElementById('quickApproveForm');
    form.action = `/review/${sampleId}/validate`;
    
    const modal = new bootstrap.Modal(document.getElementById('quickApproveModal'));
    modal.show();
}

function quickReject(sampleId) {
    const form = document.getElementById('quickRejectForm');
    form.action = `/review/${sampleId}/reject`;
    
    const modal = new bootstrap.Modal(document.getElementById('quickRejectModal'));
    modal.show();
}

// Auto-refresh every 3 minutes
setInterval(function() {
    if (document.hidden) return;
    
    if (!document.querySelector('.modal.show')) {
        location.reload();
    }
}, 180000);

// Keyboard shortcuts for reviewers
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey) {
        switch(e.key) {
            case '1': // Ctrl+1 - First sample
                const firstSample = document.querySelector('.card .btn[href*="/review/"]');
                if (firstSample) {
                    firstSample.click();
                    e.preventDefault();
                }
                break;
            case 'a': // Ctrl+A - Quick approve first
                e.preventDefault();
                const firstCard = document.querySelector('.card');
                if (firstCard) {
                    const sampleId = firstCard.querySelector('.btn[onclick*="quickApprove"]')?.onclick.toString().match(/\d+/)?.[0];
                    if (sampleId) quickApprove(sampleId);
                }
                break;
        }
    }
});
</script>
@endpush
