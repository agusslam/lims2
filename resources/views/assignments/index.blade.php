@extends('layouts.app')

@section('title', 'Penugasan Analis')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Penugasan Analis</h4>
        <p class="text-muted mb-0">Tugaskan sampel yang sudah dikodifikasi kepada analis</p>
    </div>
    <div>
        <span class="badge bg-info fs-6 px-3 py-2">
            <i class="fas fa-clipboard-list me-2"></i>
            Siap Ditugaskan: {{ $samples->count() }} sampel
        </span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-warning">Menunggu Penugasan</h6>
                        <h3 class="mb-0">{{ $samples->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Sampel yang sudah dikodifikasi</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-primary">Analis Tersedia</h6>
                        <h3 class="mb-0">{{ $analysts->where('is_active', true)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Analis aktif</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">Ditugaskan Hari Ini</h6>
                        <h3 class="mb-0">{{ \App\Models\Sample::where('status', 'assigned')->whereDate('assigned_at', today())->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Penugasan hari ini</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-info">Rata-rata Beban Kerja</h6>
                        <h3 class="mb-0">{{ round(\App\Models\Sample::whereIn('status', ['assigned', 'testing'])->count() / max(1, $analysts->count())) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-balance-scale fa-2x text-info"></i>
                    </div>
                </div>
                <small class="text-muted">Sampel per analis</small>
            </div>
        </div>
    </div>
</div>

<!-- Analyst Workload Overview -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-chart-bar me-2"></i>
            Beban Kerja Analis
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($analysts as $analyst)
            @php
                $workload = $analyst->assignedSamples()->whereIn('status', ['assigned', 'testing'])->count();
                $maxWorkload = 10; // Configurable max workload
                $percentage = min(100, ($workload / $maxWorkload) * 100);
                $statusClass = $percentage > 80 ? 'danger' : ($percentage > 60 ? 'warning' : 'success');
            @endphp
            <div class="col-md-6 col-lg-3 mb-3">
                <div class="card border-{{ $statusClass }}">
                    <div class="card-body text-center">
                        <h6 class="card-title">{{ $analyst->full_name }}</h6>
                        <div class="progress mb-2" style="height: 8px;">
                            <div class="progress-bar bg-{{ $statusClass }}" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                        <small class="text-muted">{{ $workload }}/{{ $maxWorkload }} sampel</small>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Samples List -->
<div class="row">
    @forelse($samples as $sample)
    <div class="col-md-6 mb-4">
        <div class="card border-warning">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-vial me-2"></i>
                    {{ $sample->sample_code }}
                </h6>
                <span class="badge bg-warning">Siap Ditugaskan</span>
            </div>
            
            <div class="card-body">
                <div class="mb-3">
                    <strong>Pelanggan:</strong> {{ $sample->sampleRequest->customer->contact_person }}<br>
                    <strong>Perusahaan:</strong> {{ $sample->sampleRequest->customer->company_name }}<br>
                    <strong>Jenis Sampel:</strong> {{ $sample->sampleType->name ?? $sample->custom_sample_type }}<br>
                    <strong>Quantity:</strong> {{ $sample->quantity }} sampel
                </div>

                <!-- Test Parameters -->
                <div class="mb-3">
                    <small class="text-muted">Parameter Uji:</small><br>
                    @foreach($sample->tests->groupBy('testParameter.category') as $category => $tests)
                        <span class="badge bg-light text-dark me-1 mb-1">
                            {{ $category }}: {{ $tests->count() }}
                        </span>
                    @endforeach
                </div>

                <!-- Required Specialist Check -->
                @php
                    $requiredSpecialists = $sample->tests->pluck('testParameter.specialist_roles')
                        ->filter()->flatten()->unique();
                    $availableAnalysts = $analysts->filter(function($analyst) use ($requiredSpecialists) {
                        return $requiredSpecialists->isEmpty() || 
                               $requiredSpecialists->contains($analyst->role);
                    });
                @endphp

                @if($requiredSpecialists->isNotEmpty())
                <div class="mb-3">
                    <small class="text-info">
                        <i class="fas fa-star me-1"></i>
                        Memerlukan spesialis: {{ $requiredSpecialists->implode(', ') }}
                    </small>
                </div>
                @endif

                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Dikodifikasi: {{ $sample->updated_at->diffForHumans() }}
                    </small>
                </div>
            </div>
            
            <div class="card-footer">
                <form action="{{ route('samples.assign', $sample->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pilih Analis:</label>
                        <select name="analyst_id" class="form-select" required>
                            <option value="">-- Pilih Analis --</option>
                            @foreach($availableAnalysts as $analyst)
                                @php
                                    $currentWorkload = $analyst->assignedSamples()
                                        ->whereIn('status', ['assigned', 'testing'])->count();
                                @endphp
                                <option value="{{ $analyst->id }}">
                                    {{ $analyst->full_name }} 
                                    ({{ $currentWorkload }} sampel aktif)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Penugasan:</label>
                        <textarea name="assignment_notes" class="form-control" rows="2" 
                                  placeholder="Catatan khusus untuk analis..."></textarea>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-user-plus me-2"></i>
                            Tugaskan Analis
                        </button>
                        <a href="{{ route('samples.verification-form', $sample->id) }}" 
                           class="btn btn-outline-secondary" target="_blank">
                            <i class="fas fa-print me-1"></i>
                            Form Verifikasi
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-user-tie fa-5x text-muted mb-3"></i>
                <h5>Tidak Ada Sampel untuk Ditugaskan</h5>
                <p class="text-muted mb-0">Semua sampel sudah ditugaskan atau belum dikodifikasi</p>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($samples->hasPages())
<div class="d-flex justify-content-center">
    {{ $samples->links() }}
</div>
@endif
@endsection

@push('scripts')
<script>
// Auto-assign based on workload
function autoAssign(sampleId) {
    if (confirm('Otomatis tugaskan ke analis dengan beban kerja terendah?')) {
        // Implementation for auto-assignment logic
        alert('Fitur otomatis penugasan sedang dikembangkan');
    }
}

// Show analyst details
function showAnalystDetails(analystId) {
    // Implementation to show analyst performance and current workload
    alert('Detail analis: ' + analystId);
}

// Bulk assignment
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const sampleCheckboxes = document.querySelectorAll('.sample-checkbox');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            sampleCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
});
</script>
@endpush
