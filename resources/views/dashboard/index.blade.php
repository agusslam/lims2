@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Statistics Cards -->
    <div class="col-md-3 mb-4">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-primary">Total Permintaan</h6>
                        <h3 class="mb-0">{{ $stats['total_requests'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-flask fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Semua permintaan sampel</small>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasAnyRole(['ANALYST', 'SUPERVISOR_ANALYST']))
    <div class="col-md-3 mb-4">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-warning">Tugas Saya</h6>
                        <h3 class="mb-0">{{ $stats['assigned_samples'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tasks fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Sampel yang ditugaskan</small>
            </div>
        </div>
    </div>
    @endif

    @if(auth()->user()->hasAnyRole(['TECH_AUDITOR', 'QUALITY_AUDITOR']))
    <div class="col-md-3 mb-4">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-info">Pending Review</h6>
                        <h3 class="mb-0">{{ $stats['pending_reviews'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x text-info"></i>
                    </div>
                </div>
                <small class="text-muted">Menunggu review</small>
            </div>
        </div>
    </div>
    @endif

    <div class="col-md-3 mb-4">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">Selesai</h6>
                        <h3 class="mb-0">{{ $stats['completed_requests'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-certificate fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Permintaan selesai</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sample Activity Chart -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Aktivitas Sampel (7 Hari Terakhir)
                </h5>
            </div>
            <div class="card-body">
                <canvas id="sampleChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Aksi Cepat
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if(auth()->user()->canAccessModule(1))
                    <a href="{{ route('sample-requests.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>
                        Daftar Sampel Baru
                    </a>
                    @endif

                    @if(auth()->user()->canAccessModule(4))
                    <a href="{{ route('testing.index') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-vial me-2"></i>
                        Pencatatan Hasil
                    </a>
                    @endif

                    @if(auth()->user()->canAccessModule(5))
                    <a href="{{ route('review.index') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-check-circle me-2"></i>
                        Review & Validasi
                    </a>
                    @endif

                    <a href="{{ route('public.tracking') }}" class="btn btn-secondary btn-sm" target="_blank">
                        <i class="fas fa-search me-2"></i>
                        Tracking Publik
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Samples -->
    <div class="col-md-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-flask me-2"></i>
                    Sampel Terbaru
                </h5>
                <a href="{{ route('sample-requests.index') }}" class="btn btn-outline-primary btn-sm">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body">
                @if($recent_samples->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Kode Sampel</th>
                                <th>Pelanggan</th>
                                <th>Status</th>
                                <th>Analis</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_samples as $sample)
                            <tr>
                                <td>
                                    <a href="{{ route('sample-requests.show', $sample->sampleRequest->id) }}" 
                                       class="text-decoration-none">
                                        <strong>{{ $sample->sample_code }}</strong>
                                    </a>
                                </td>
                                <td>{{ $sample->sampleRequest->customer->contact_person }}</td>
                                <td>
                                    <span class="badge status-badge bg-{{ 
                                        $sample->status === 'completed' ? 'success' : 
                                        ($sample->status === 'testing' ? 'warning' : 'primary') 
                                    }}">
                                        {{ $sample->status_label }}
                                    </span>
                                </td>
                                <td>
                                    {{ $sample->assignedAnalyst->full_name ?? '-' }}
                                </td>
                                <td>{{ $sample->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-flask fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada sampel terbaru</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pending Tasks -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tasks me-2"></i>
                    Tugas Pending
                </h5>
            </div>
            <div class="card-body">
                @if(!empty($pending_tasks))
                <ul class="list-group list-group-flush">
                    @foreach($pending_tasks as $task => $count)
                    @if($count > 0)
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <small>
                            @switch($task)
                                @case('new_requests')
                                    Permintaan Baru
                                    @break
                                @case('needs_verification')
                                    Verifikasi Pelanggan
                                    @break
                                @case('needs_assignment')
                                    Penugasan Analis
                                    @break
                                @case('assigned_tests')
                                    Pengujian Tertugaskan
                                    @break
                                @case('pending_reviews')
                                    Review Pending
                                    @break
                                @case('pending_certificates')
                                    Sertifikat Pending
                                    @break
                            @endswitch
                        </small>
                        <span class="badge bg-danger rounded-pill">{{ $count }}</span>
                    </li>
                    @endif
                    @endforeach
                </ul>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">Semua tugas selesai!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- System Notifications -->
@if(auth()->user()->hasAnyRole(['SUPERVISOR', 'DEVEL']))
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bell me-2"></i>
                    Notifikasi Sistem
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="fas fa-info-circle me-3"></i>
                    <div>
                        <strong>LIMS v2.0.0</strong> - Sistem Laboratory Information Management telah aktif dan siap digunakan.
                        <a href="{{ route('reports.index') }}" class="alert-link">Lihat laporan sistem</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Sample Activity Chart
const ctx = document.getElementById('sampleChart').getContext('2d');
const sampleChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chart_data['labels']) !!},
        datasets: [{
            label: 'Sampel Baru',
            data: {!! json_encode($chart_data['samples']) !!},
            borderColor: '#3498db',
            backgroundColor: 'rgba(52, 152, 219, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Auto-refresh dashboard every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endpush
