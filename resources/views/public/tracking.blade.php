<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Sampel - LIMS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .tracking-container {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .tracking-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
        }
        .tracking-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            text-align: center;
            padding: 2rem;
            border-radius: 15px 15px 0 0;
        }
        .form-floating .form-control {
            border-radius: 10px;
        }
        .btn-track {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 2rem;
            font-weight: 600;
        }
        .status-timeline {
            position: relative;
            padding: 2rem 0;
        }
        .timeline-item {
            display: flex;
            margin-bottom: 2rem;
            position: relative;
        }
        .timeline-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }
        .timeline-content {
            flex: 1;
            padding-top: 8px;
        }
        .timeline-item.completed .timeline-icon {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
        }
        .timeline-item.active .timeline-icon {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }
        .timeline-item.pending .timeline-icon {
            background: #ecf0f1;
            color: #95a5a6;
        }
        .timeline-connector {
            position: absolute;
            left: 25px;
            top: 60px;
            bottom: -10px;
            width: 2px;
            background: #ecf0f1;
        }
        .timeline-item.completed .timeline-connector {
            background: #27ae60;
        }
        .sample-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: rgba(44, 62, 80, 0.9); backdrop-filter: blur(10px);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('landing') }}">
                <i class="fas fa-microscope me-2"></i>LIMS
            </a>

            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('landing') }}">
                    <i class="fas fa-home me-1"></i>Beranda
                </a>
                <a class="nav-link" href="{{ route('submit-request') }}">
                    <i class="fas fa-flask me-1"></i>Daftar Sampel
                </a>
            </div>
        </div>
    </nav>

    <div class="tracking-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="tracking-card mx-auto">
                        <div class="tracking-header">
                            <h3 class="mb-1">
                                <i class="fas fa-search me-2"></i>
                                Tracking Status Sampel
                            </h3>
                            <p class="mb-0 opacity-75">Masukkan kode tracking untuk melihat status pengujian sampel Anda</p>
                        </div>

                        <div class="p-4">
                            @if(!isset($sampleRequest))
                            <!-- Tracking Form -->
                            <form method="POST" action="{{ route('tracking-result') }}">
                                @csrf
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-8">
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('tracking_code') is-invalid @enderror"
                                                   id="tracking_code" name="tracking_code"
                                                   placeholder="UNEJ202412000001"
                                                   value="{{ old('tracking_code') }}" required
                                                   pattern="UNEJ[0-9]{12}"
                                                   title="Format: UNEJ202412000001">
                                            <label for="tracking_code">
                                                <i class="fas fa-barcode me-2"></i>Kode Tracking
                                            </label>
                                            @error('tracking_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Format: UNEJ + Tahun + Bulan + Nomor Urut (contoh: UNEJ202412000001)
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary btn-track w-100">
                                            <i class="fas fa-search me-2"></i>
                                            Lacak Status
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <hr class="my-4">

                            <!-- Information -->
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-info-circle fa-3x text-primary mb-3"></i>
                                            <h6 class="card-title">Cara Menggunakan</h6>
                                            <p class="card-text small text-muted">
                                                1. Masukkan kode tracking yang Anda terima setelah mendaftarkan sampel<br>
                                                2. Klik tombol "Lacak Status" untuk melihat progres pengujian<br>
                                                3. Status akan diperbarui secara real-time sesuai progres di laboratorium
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-0 bg-light h-100">
                                        <div class="card-body text-center">
                                            <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                            <h6 class="card-title">Waktu Pengujian</h6>
                                            <p class="card-text small text-muted">
                                                Waktu pengujian bervariasi tergantung jenis sampel dan parameter yang diuji:<br>
                                                • Air: 3-7 hari kerja<br>
                                                • Tanah: 5-10 hari kerja<br>
                                                • Pangan: 3-14 hari kerja
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @else
                            <!-- Tracking Results -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">{{ $sampleRequest->tracking_code }}</h5>
                                        <p class="text-muted mb-0">
                                            Pelanggan: {{ $sampleRequest->customer->contact_person }}
                                            @if($sampleRequest->customer->company_name)
                                                ({{ $sampleRequest->customer->company_name }})
                                            @endif
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-{{
                                            $sampleRequest->status === 'completed' ? 'success' :
                                            ($sampleRequest->status === 'testing' ? 'warning' : 'primary')
                                        }} fs-6 px-3 py-2">
                                            {{ ucfirst($sampleRequest->status) }}
                                        </span>
                                        <div class="small text-muted">
                                            Terdaftar: {{ $sampleRequest->submitted_at->format('d/m/Y H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Timeline -->
                            <div class="status-timeline">
                                @php
                                    $statuses = [
                                        'pending' => ['icon' => 'fa-clock', 'title' => 'Permohonan Diterima', 'desc' => 'Permohonan pengujian telah diterima sistem'],
                                        'registered' => ['icon' => 'fa-check-circle', 'title' => 'Terdaftar', 'desc' => 'Sampel telah diverifikasi dan terdaftar'],
                                        'assigned' => ['icon' => 'fa-user-tie', 'title' => 'Ditugaskan', 'desc' => 'Sampel telah ditugaskan kepada analis'],
                                        'testing' => ['icon' => 'fa-vial', 'title' => 'Dalam Pengujian', 'desc' => 'Proses pengujian sedang berlangsung'],
                                        'review' => ['icon' => 'fa-search', 'title' => 'Review & Validasi', 'desc' => 'Hasil pengujian sedang di-review'],
                                        'validated' => ['icon' => 'fa-certificate', 'title' => 'Tervalidasi', 'desc' => 'Hasil pengujian telah divalidasi'],
                                        'completed' => ['icon' => 'fa-check', 'title' => 'Selesai', 'desc' => 'Pengujian selesai, sertifikat siap']
                                    ];

                                    $currentStatusIndex = array_search($sampleRequest->status, array_keys($statuses));
                                @endphp

                                @foreach($statuses as $status => $info)
                                    @php
                                        $statusIndex = array_search($status, array_keys($statuses));
                                        $itemClass = $statusIndex < $currentStatusIndex ? 'completed' :
                                                    ($statusIndex == $currentStatusIndex ? 'active' : 'pending');
                                    @endphp

                                    <div class="timeline-item {{ $itemClass }}">
                                        @if(!$loop->last)
                                        <div class="timeline-connector"></div>
                                        @endif
                                        <div class="timeline-icon">
                                            <i class="fas {{ $info['icon'] }}"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">{{ $info['title'] }}</h6>
                                            <p class="text-muted small mb-0">{{ $info['desc'] }}</p>
                                            @if($statusIndex <= $currentStatusIndex)
                                                <small class="text-success">
                                                    <i class="fas fa-check me-1"></i>Selesai
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Sample Details -->
                            @if($sampleRequest->samples->count() > 0)
                            <div class="mt-4">
                                <h6 class="mb-3">Detail Sampel ({{ $sampleRequest->samples->count() }} sampel)</h6>

                                @foreach($sampleRequest->samples as $sample)
                                <div class="sample-card card">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-md-8">
                                                <h6 class="card-title mb-1">
                                                    {{ $sample->sample_code }}
                                                    @if($sample->sampleType)
                                                        <small class="text-muted">({{ $sample->sampleType->name }})</small>
                                                    @endif
                                                </h6>
                                                <p class="card-text small text-muted mb-2">
                                                    Quantity: {{ $sample->quantity }} |
                                                    Parameter: {{ $sample->tests->count() }} parameter
                                                </p>
                                                @if($sample->assignedAnalyst)
                                                <small class="text-info">
                                                    <i class="fas fa-user me-1"></i>
                                                    Analis: {{ $sample->assignedAnalyst->full_name }}
                                                </small>
                                                @endif
                                            </div>
                                            <div class="col-md-4 text-end">
                                                <span class="badge bg-{{
                                                    $sample->status === 'completed' ? 'success' :
                                                    ($sample->status === 'testing' ? 'warning' : 'primary')
                                                }}">
                                                    {{ ucfirst($sample->status) }}
                                                </span>
                                                @if($sample->tests->count() > 0)
                                                    @php
                                                        $completedTests = $sample->tests->where('status', 'completed')->count();
                                                        $totalTests = $sample->tests->count();
                                                        $progress = $totalTests > 0 ? ($completedTests / $totalTests) * 100 : 0;
                                                    @endphp
                                                    <div class="progress mt-2" style="height: 6px;">
                                                        <div class="progress-bar bg-success"
                                                             style="width: {{ $progress }}%"></div>
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $completedTests }}/{{ $totalTests }} parameter selesai
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="text-center mt-4">
                                <a href="{{ route('tracking') }}" class="btn btn-outline-primary me-2">
                                    <i class="fas fa-search me-2"></i>Lacak Sampel Lain
                                </a>

                                @if($sampleRequest->status === 'completed' && !$sampleRequest->feedback_completed)
                                <a href="{{ route('feedback', $sampleRequest->tracking_code) }}"
                                   class="btn btn-warning me-2">
                                    <i class="fas fa-star me-2"></i>Berikan Feedback
                                </a>
                                @endif

                                @if($sampleRequest->status === 'completed')
                                <a href="{{ route('download', $sampleRequest->tracking_code) }}"
                                   class="btn btn-success">
                                    <i class="fas fa-download me-2"></i>Download Hasil
                                </a>
                                @endif
                            </div>
                            @endif

                            @if(session('error'))
                            <div class="alert alert-danger mt-4">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                {{ session('error') }}
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Auto-refresh tracking status every 30 seconds if showing results
        @if(isset($sampleRequest))
        setInterval(function() {
            if (document.hidden) return; // Don't refresh if tab is not active

            fetch(`/api/samples/status/{{ $sampleRequest->tracking_code }}`)
                .then(response => response.json())
                .then(data => {
                    // Update status if changed
                    if (data.status !== '{{ $sampleRequest->status }}') {
                        location.reload();
                    }
                })
                .catch(console.error);
        }, 30000);
        @endif

        // Format tracking code input
        document.getElementById('tracking_code')?.addEventListener('input', function(e) {
            let value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

            if (value.length > 4 && !value.startsWith('UNEJ')) {
                value = 'UNEJ' + value.substring(4);
            }

            if (value.length > 16) {
                value = value.substring(0, 16);
            }

            e.target.value = value;
        });
    </script>
</body>
</html>
