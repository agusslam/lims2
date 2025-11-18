@extends('layouts.app')

@section('title', 'Pencatatan Hasil')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Pencatatan Hasil Pengujian</h4>
        <p class="text-muted mb-0">Input hasil pengujian dan upload dokumen instrumen</p>
    </div>
    <div>
        @if(auth()->user()->hasRole('ANALYST'))
        <span class="badge bg-info fs-6 px-3 py-2">
            <i class="fas fa-user me-2"></i>
            Tugas Anda: {{ auth()->user()->getCurrentWorkload() }} sampel
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
                        <h6 class="card-title text-warning">Ditugaskan</h6>
                        <h3 class="mb-0">{{ $samples->where('status', 'assigned')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-tie fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Siap untuk pengujian</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-primary">Sedang Diuji</h6>
                        <h3 class="mb-0">{{ $samples->where('status', 'testing')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-vial fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Dalam proses pengujian</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">Selesai Hari Ini</h6>
                        <h3 class="mb-0">{{ \App\Models\Sample::where('status', 'review_tech')->whereDate('testing_completed_at', today())->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Pengujian selesai</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-info">Total Parameter</h6>
                        <h3 class="mb-0">{{ $samples->sum(function($sample) { return $sample->tests->count(); }) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-list fa-2x text-info"></i>
                    </div>
                </div>
                <small class="text-muted">Parameter yang harus diuji</small>
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
                    <option value="assigned" {{ request('status') === 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                    <option value="testing" {{ request('status') === 'testing' ? 'selected' : '' }}>Sedang Diuji</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-select" name="priority">
                    <option value="">Semua Prioritas</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('testing.index') }}" class="btn btn-outline-secondary">
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
        <div class="card h-100 {{ $sample->status === 'assigned' ? 'border-warning' : 'border-primary' }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-vial me-2"></i>
                    {{ $sample->sample_code }}
                </h6>
                <span class="badge bg-{{ $sample->status === 'assigned' ? 'warning' : 'primary' }}">
                    {{ ucfirst($sample->status) }}
                </span>
            </div>
            
            <div class="card-body">
                <div class="mb-3">
                    <strong>Pelanggan:</strong> {{ $sample->sampleRequest->customer->contact_person }}<br>
                    <strong>Jenis:</strong> {{ $sample->sampleType->name ?? $sample->custom_sample_type }}<br>
                    <strong>Quantity:</strong> {{ $sample->quantity }} sampel
                </div>

                <!-- Test Progress -->
                @php
                    $totalTests = $sample->tests->count();
                    $completedTests = $sample->tests->where('status', 'completed')->count();
                    $progress = $totalTests > 0 ? ($completedTests / $totalTests) * 100 : 0;
                @endphp
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small>Progress Pengujian</small>
                        <small>{{ $completedTests }}/{{ $totalTests }} parameter</small>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-{{ $progress === 100 ? 'success' : 'primary' }}" 
                             style="width: {{ $progress }}%"></div>
                    </div>
                </div>

                <!-- Test Parameters Summary -->
                <div class="mb-3">
                    <small class="text-muted">Parameter Uji:</small>
                    @foreach($sample->tests->groupBy('testParameter.category') as $category => $tests)
                        <div class="badge bg-light text-dark me-1 mb-1">
                            {{ $category }}: {{ $tests->count() }}
                        </div>
                    @endforeach
                </div>

                @if($sample->assigned_at)
                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Ditugaskan: {{ $sample->assigned_at->diffForHumans() }}
                    </small>
                </div>
                @endif
            </div>
            
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <a href="{{ route('testing.show', $sample->id) }}" 
                       class="btn btn-{{ $sample->status === 'assigned' ? 'warning' : 'primary' }}">
                        <i class="fas fa-{{ $sample->status === 'assigned' ? 'play' : 'edit' }} me-2"></i>
                        {{ $sample->status === 'assigned' ? 'Mulai Pengujian' : 'Lanjutkan Pengujian' }}
                    </a>
                    
                    @if($sample->status === 'testing')
                    <div class="btn-group">
                        <a href="{{ route('testing.data-form', $sample->id) }}" 
                           class="btn btn-outline-secondary btn-sm" target="_blank">
                            <i class="fas fa-print me-1"></i>Form Data
                        </a>
                        @if($progress === 100)
                        <button class="btn btn-success btn-sm" onclick="completeTesting({{ $sample->id }})">
                            <i class="fas fa-check me-1"></i>Selesai
                        </button>
                        @endif
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
                <i class="fas fa-vials fa-5x text-muted mb-3"></i>
                <h5>Tidak Ada Sampel untuk Pengujian</h5>
                <p class="text-muted mb-0">
                    @if(auth()->user()->hasRole('ANALYST'))
                        Belum ada sampel yang ditugaskan kepada Anda.
                    @else
                        Belum ada sampel yang siap untuk pengujian.
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
@endsection

@push('scripts')
<script>
function completeTesting(sampleId) {
    if (confirm('Apakah Anda yakin semua pengujian untuk sampel ini sudah selesai?')) {
        fetch(`/testing/${sampleId}/complete`, {
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
                alert('Error: ' + (data.message || 'Gagal menyelesaikan pengujian'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}

// Auto-refresh every 2 minutes
setInterval(function() {
    if (document.hidden) return;
    
    // Only refresh if no modal is open
    if (!document.querySelector('.modal.show')) {
        location.reload();
    }
}, 120000);

// Keyboard shortcuts for analysts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey) {
        switch(e.key) {
            case '1': // Ctrl+1 - First sample
                const firstSample = document.querySelector('.card .btn[href*="/testing/"]');
                if (firstSample) {
                    firstSample.click();
                    e.preventDefault();
                }
                break;
            case 'r': // Ctrl+R - Refresh (override default)
                e.preventDefault();
                location.reload();
                break;
        }
    }
});
</script>
@endpush
