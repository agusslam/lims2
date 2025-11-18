@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Laporan & Analitik</h4>
        <p class="text-muted mb-0">Dashboard laporan dan analisis sistem</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-primary">Sampel Bulan Ini</h6>
                        <h3 class="mb-0">{{ $stats['samples_this_month'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-flask fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Total sampel masuk</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">Selesai Bulan Ini</h6>
                        <h3 class="mb-0">{{ $stats['completed_this_month'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Sampel selesai</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-warning">Sertifikat Terbit</h6>
                        <h3 class="mb-0">{{ $stats['certificates_issued'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-certificate fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Bulan ini</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-info">Analis Aktif</h6>
                        <h3 class="mb-0">{{ $stats['active_analysts'] ?? 0 }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                </div>
                <small class="text-muted">Analis tersedia</small>
            </div>
        </div>
    </div>
</div>

<!-- Report Categories -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar me-2"></i>
                    Laporan Sampel
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">Laporan detail tentang sampel, status, dan progres pengujian.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i>Status sampel per periode</li>
                    <li><i class="fas fa-check text-success me-2"></i>Tren penerimaan sampel</li>
                    <li><i class="fas fa-check text-success me-2"></i>Distribusi jenis sampel</li>
                    <li><i class="fas fa-check text-success me-2"></i>Waktu penyelesaian</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('reports.samples') }}" class="btn btn-primary w-100">
                    <i class="fas fa-eye me-2"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line me-2"></i>
                    Laporan Performa
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">Analisis kinerja analis dan efisiensi laboratorium.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i>Produktivitas analis</li>
                    <li><i class="fas fa-check text-success me-2"></i>Waktu rata-rata pengujian</li>
                    <li><i class="fas fa-check text-success me-2"></i>Beban kerja departemen</li>
                    <li><i class="fas fa-check text-success me-2"></i>Tingkat penyelesaian</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('reports.performance') }}" class="btn btn-success w-100">
                    <i class="fas fa-eye me-2"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-shield-alt me-2"></i>
                    Laporan Kepatuhan
                </h5>
            </div>
            <div class="card-body">
                <p class="card-text">Laporan kepatuhan terhadap standar ISO 17025 dan audit trail.</p>
                <ul class="list-unstyled">
                    <li><i class="fas fa-check text-success me-2"></i>Kelengkapan audit trail</li>
                    <li><i class="fas fa-check text-success me-2"></i>Kepatuhan review</li>
                    <li><i class="fas fa-check text-success me-2"></i>Kualitas sertifikat</li>
                    <li><i class="fas fa-check text-success me-2"></i>Aktivitas user</li>
                </ul>
            </div>
            <div class="card-footer">
                <a href="{{ route('reports.compliance') }}" class="btn btn-warning w-100">
                    <i class="fas fa-eye me-2"></i>Lihat Laporan
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Export -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-download me-2"></i>
                    Export Laporan Cepat
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('reports.export') }}" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label">Jenis Laporan</label>
                        <select class="form-select" name="report_type" required>
                            <option value="">Pilih jenis laporan</option>
                            <option value="samples">Laporan Sampel</option>
                            <option value="performance">Laporan Performa</option>
                            <option value="compliance">Laporan Kepatuhan</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Format</label>
                        <select class="form-select" name="format" required>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" class="form-control" name="date_from" 
                               value="{{ date('Y-m-01') }}" required>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" class="form-control" name="date_to" 
                               value="{{ date('Y-m-d') }}" required>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-download me-2"></i>Export Laporan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection