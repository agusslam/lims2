<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Berhasil Dikirim - LIMS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .success-container {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .success-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
        }
        .success-header {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
            text-align: center;
            padding: 2rem;
            border-radius: 15px 15px 0 0;
        }
        .tracking-code {
            font-family: 'Courier New', monospace;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 2px;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            border: 2px dashed #27ae60;
        }
        .qr-code {
            width: 150px;
            height: 150px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: rgba(44, 62, 80, 0.9); backdrop-filter: blur(10px);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('landing') }}">
                <i class="fas fa-microscope me-2"></i>LIMS
            </a>
        </div>
    </nav>

    <div class="success-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="success-card mx-auto">
                        <div class="success-header">
                            <i class="fas fa-check-circle fa-5x mb-3"></i>
                            <h3 class="mb-1">Permohonan Berhasil Dikirim!</h3>
                            <p class="mb-0 opacity-75">Terima kasih, permohonan pengujian Anda telah berhasil diterima sistem</p>
                        </div>

                        <div class="p-4">
                            <!-- Tracking Information -->
                            <div class="row mb-4">
                                <div class="col-md-8">
                                    <h5 class="mb-3">
                                        <i class="fas fa-barcode text-primary me-2"></i>
                                        Kode Tracking Anda
                                    </h5>
                                    <div class="tracking-code text-center">
                                        {{ $tracking_code }}
                                    </div>
                                    <div class="alert alert-info mt-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Simpan kode ini dengan baik!</strong> Anda akan memerlukan kode tracking ini untuk melacak status pengujian sampel.
                                    </div>
                                </div>
                                <div class="col-md-4 text-center">
                                    <h6 class="mb-3">QR Code</h6>
                                    <div class="qr-code mx-auto">
                                        <!-- QR Code placeholder -->
                                        <i class="fas fa-qrcode fa-4x text-muted"></i>
                                    </div>
                                    <small class="text-muted mt-2 d-block">Scan untuk tracking cepat</small>
                                </div>
                            </div>

                            <!-- Request Summary -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-user text-primary me-2"></i>
                                                Data Pelanggan
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                <li><strong>Nama:</strong> {{ $customer->contact_person }}</li>
                                                @if($customer->company_name)
                                                <li><strong>Perusahaan:</strong> {{ $customer->company_name }}</li>
                                                @endif
                                                <li><strong>WhatsApp:</strong> {{ $customer->whatsapp_number }}</li>
                                                <li><strong>Email:</strong> {{ $customer->email }}</li>
                                                <li><strong>Alamat:</strong> {{ $customer->address }}, {{ $customer->city }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card bg-light h-100">
                                        <div class="card-body">
                                            <h6 class="card-title">
                                                <i class="fas fa-flask text-primary me-2"></i>
                                                Ringkasan Sampel
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                <li><strong>Jumlah Sampel:</strong> {{ $request->samples->count() }} sampel</li>
                                                <li><strong>Total Parameter:</strong>
                                                    {{ $request->samples->sum(function($sample) { return $sample->tests->count(); }) }} parameter
                                                </li>
                                                <li><strong>Estimasi Biaya:</strong> Rp {{ number_format($request->total_price, 0, ',', '.') }}</li>
                                                <li><strong>Status:</strong> <span class="badge bg-warning">Pending Verifikasi</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Next Steps -->
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-list-ol text-primary me-2"></i>
                                        Langkah Selanjutnya
                                    </h6>
                                    <ol class="mb-0">
                                        <li class="mb-2">
                                            <strong>Tunggu Konfirmasi:</strong> Tim kami akan melakukan verifikasi permintaan Anda dalam 1-2 hari kerja
                                        </li>
                                        <li class="mb-2">
                                            <strong>Lacak Status:</strong> Gunakan kode tracking untuk memantau progres pengujian secara real-time
                                        </li>
                                        <li class="mb-2">
                                            <strong>Serahkan Sampel:</strong> Setelah verifikasi, bawa sampel ke laboratorium sesuai jadwal yang akan diberikan
                                        </li>
                                        <li class="mb-0">
                                            <strong>Ambil Hasil:</strong> Hasil pengujian akan tersedia sesuai estimasi waktu yang diberikan
                                        </li>
                                    </ol>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card border-primary">
                                        <div class="card-body text-center">
                                            <i class="fas fa-phone fa-2x text-primary mb-3"></i>
                                            <h6>Hubungi Kami</h6>
                                            <p class="mb-0">
                                                <strong>Office:</strong> (0331) 334293<br>
                                                <strong>WhatsApp:</strong> +62 812-3456-7890
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card border-info">
                                        <div class="card-body text-center">
                                            <i class="fas fa-envelope fa-2x text-info mb-3"></i>
                                            <h6>Email Support</h6>
                                            <p class="mb-0">
                                                <strong>Customer Service:</strong><br>
                                                cs@unej.ac.id
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('tracking') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-search me-2"></i>Lacak Status
                                    </a>
                                    <button class="btn btn-success btn-lg" onclick="copyTrackingCode()">
                                        <i class="fas fa-copy me-2"></i>Salin Kode
                                    </button>
                                    <button class="btn btn-info btn-lg" onclick="window.print()">
                                        <i class="fas fa-print me-2"></i>Cetak
                                    </button>
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('submit-request') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-plus me-2"></i>Ajukan Sampel Lain
                                    </a>
                                    <a href="{{ route('landing') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function copyTrackingCode() {
            const trackingCode = '{{ $tracking_code }}';

            if (navigator.clipboard) {
                navigator.clipboard.writeText(trackingCode).then(function() {
                    alert('Kode tracking berhasil disalin: ' + trackingCode);
                });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = trackingCode;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('Kode tracking berhasil disalin: ' + trackingCode);
            }
        }

        // Auto-redirect to tracking after 5 minutes if no action
        setTimeout(function() {
            if (confirm('Ingin langsung melacak status sampel Anda?')) {
                window.location.href = '{{ route("tracking") }}';
            }
        }, 300000);
    </script>
</body>
</html>
