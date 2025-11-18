@extends('layouts.app')

@section('title', 'Detail Arsip')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Detail Arsip: {{ $sample->sample_code }}</h4>
        <p class="text-muted mb-0">
            Status: 
            <span class="badge bg-success">{{ ucfirst($sample->status) }}</span>
        </p>
    </div>
    <div>
        @if($sample->certificate && $sample->certificate->status === 'issued')
        <a href="{{ route('certificates.download', $sample->certificate->id) }}" class="btn btn-success me-2">
            <i class="fas fa-download me-2"></i>Download Sertifikat
        </a>
        @endif
        <a href="{{ route('archives.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Sample Information -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Sampel
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Kode Sampel:</strong></td>
                        <td>{{ $sample->sample_code }}</td>
                    </tr>
                    <tr>
                        <td><strong>Tracking Code:</strong></td>
                        <td>{{ $sample->sampleRequest->tracking_code }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Sampel:</strong></td>
                        <td>{{ $sample->sampleType->name ?? $sample->custom_sample_type }}</td>
                    </tr>
                    <tr>
                        <td><strong>Quantity:</strong></td>
                        <td>{{ $sample->quantity }} sampel</td>
                    </tr>
                    <tr>
                        <td><strong>Analis:</strong></td>
                        <td>{{ $sample->assignedAnalyst->full_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge bg-success">{{ ucfirst($sample->status) }}</span></td>
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
                        <td>{{ $sample->sampleRequest->customer->contact_person }}</td>
                    </tr>
                    <tr>
                        <td><strong>Perusahaan:</strong></td>
                        <td>{{ $sample->sampleRequest->customer->company_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $sample->sampleRequest->customer->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Kota:</strong></td>
                        <td>{{ $sample->sampleRequest->customer->city }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Certificate Information -->
        @if($sample->certificate)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-certificate me-2"></i>
                    Informasi Sertifikat
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Nomor:</strong></td>
                        <td>{{ $sample->certificate->certificate_number ?? 'Draft' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            <span class="badge bg-{{ $sample->certificate->status === 'issued' ? 'success' : 'warning' }}">
                                {{ ucfirst($sample->certificate->status) }}
                            </span>
                        </td>
                    </tr>
                    @if($sample->certificate->issued_at)
                    <tr>
                        <td><strong>Diterbitkan:</strong></td>
                        <td>{{ $sample->certificate->issued_at->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    @if($sample->certificate->valid_until)
                    <tr>
                        <td><strong>Berlaku hingga:</strong></td>
                        <td>{{ $sample->certificate->valid_until->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Test Results -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-microscope me-2"></i>
                    Hasil Pengujian Lengkap
                </h6>
            </div>
            <div class="card-body">
                @foreach($sample->tests->groupBy('testParameter.category') as $category => $tests)
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
                                        <td><small>{{ Str::limit($test->testParameter->method ?? '-', 30) }}</small></td>
                                        <td>
                                            <span class="badge bg-{{ $test->status === 'validated' ? 'success' : 'secondary' }}">
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

        <!-- Workflow History -->
        @if($sample->workflowHistory && $sample->workflowHistory->count() > 0)
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Riwayat Workflow
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($sample->workflowHistory->sortByDesc('created_at') as $history)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6>{{ ucfirst($history->to_status) }}</h6>
                            <small class="text-muted">{{ $history->created_at->format('d/m/Y H:i') }}</small><br>
                            <small class="text-info">{{ $history->actionBy->full_name ?? 'System' }}</small>
                            @if($history->notes)
                            <br><small class="text-muted">{{ $history->notes }}</small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
    margin-bottom: 20px;
    border-left: 2px solid #dee2e6;
}

.timeline-item:last-child {
    border-left: none;
}

.timeline-marker {
    position: absolute;
    left: -8px;
    top: 0;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding-left: 20px;
}
</style>
@endpush
