<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LIMS - Laboratory Information Management System</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .feature-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .process-timeline {
            position: relative;
        }
        .process-step {
            background: white;
            border: 3px solid #3498db;
            border-radius: 50%;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-weight: bold;
            color: #3498db;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: rgba(44, 62, 80, 0.9); backdrop-filter: blur(10px);">
        <div class="container">
            <a class="navbar-brand" href="{{ route('landing') }}">
                <i class="fas fa-microscope me-2"></i>LIMS
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('landing') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#alur">Alur Proses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary px-3 ms-2" href="{{ route('submit-request') }}">
                            <i class="fas fa-flask me-1"></i>Daftar Sampel
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-light px-3 ms-2" href="{{ route('tracking') }}">
                            <i class="fas fa-search me-1"></i>Tracking
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="beranda" class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        Sistem Manajemen Laboratorium Terintegrasi
                    </h1>
                    <p class="lead mb-4">
                        LIMS (Laboratory Information Management System) menyediakan solusi lengkap untuk manajemen sampel laboratorium yang memenuhi standar ISO/IEC 17025:2017
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('submit-request') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-flask me-2"></i>Daftar Sampel Baru
                        </a>
                        <a href="{{ route('tracking') }}" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-search me-2"></i>Lacak Status Sampel
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="fas fa-vials fa-10x opacity-75"></i>
                        <div class="mt-4">
                            <span class="badge bg-light text-dark fs-6 px-3 py-2">
                                <i class="fas fa-shield-alt me-2"></i>ISO 17025 Compliant
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="layanan" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Layanan Pengujian</h2>
                <p class="lead text-muted">Kami menyediakan berbagai layanan pengujian laboratorium yang akurat dan terpercaya</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-tint fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Analisis Air</h5>
                            <p class="card-text">Pengujian kualitas air minum, air limbah, dan air tanah sesuai standar nasional dan internasional.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-seedling fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Analisis Tanah</h5>
                            <p class="card-text">Pengujian kesuburan tanah, kandungan logam berat, dan parameter fisik-kimia tanah.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-apple-alt fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Keamanan Pangan</h5>
                            <p class="card-text">Pengujian cemaran mikrobiologi, residu pestisida, dan keamanan pangan lainnya.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-leaf fa-3x text-info mb-3"></i>
                            <h5 class="card-title">Pupuk & Kompos</h5>
                            <p class="card-text">Analisis kandungan nutrisi pupuk organik dan anorganik serta kualitas kompos.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-industry fa-3x text-danger mb-3"></i>
                            <h5 class="card-title">Lingkungan</h5>
                            <p class="card-text">Monitoring kualitas lingkungan, analisis emisi, dan pencemaran lingkungan.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-cogs fa-3x text-secondary mb-3"></i>
                            <h5 class="card-title">Lainnya</h5>
                            <p class="card-text">Konsultasi dan pengujian khusus sesuai kebutuhan klien dengan metode terakreditasi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Flow Section -->
    <section id="alur" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Alur Proses Pengujian</h2>
                <p class="lead text-muted">Proses pengujian yang transparan dan terlacak dari pendaftaran hingga sertifikat</p>
            </div>

            <div class="row process-timeline">
                <div class="col-md-2 col-6 mb-4">
                    <div class="process-step">1</div>
                    <h6 class="text-center fw-bold">Pendaftaran</h6>
                    <p class="text-center small text-muted">Daftar sampel online dengan form yang mudah</p>
                </div>

                <div class="col-md-2 col-6 mb-4">
                    <div class="process-step">2</div>
                    <h6 class="text-center fw-bold">Verifikasi</h6>
                    <p class="text-center small text-muted">Verifikasi sampel dan dokumen pendukung</p>
                </div>

                <div class="col-md-2 col-6 mb-4">
                    <div class="process-step">3</div>
                    <h6 class="text-center fw-bold">Kodifikasi</h6>
                    <p class="text-center small text-muted">Pemberian kode unik untuk setiap sampel</p>
                </div>

                <div class="col-md-2 col-6 mb-4">
                    <div class="process-step">4</div>
                    <h6 class="text-center fw-bold">Pengujian</h6>
                    <p class="text-center small text-muted">Pelaksanaan pengujian oleh analis kompeten</p>
                </div>

                <div class="col-md-2 col-6 mb-4">
                    <div class="process-step">5</div>
                    <h6 class="text-center fw-bold">Review</h6>
                    <p class="text-center small text-muted">Validasi hasil oleh koordinator teknis & mutu</p>
                </div>

                <div class="col-md-2 col-6 mb-4">
                    <div class="process-step">6</div>
                    <h6 class="text-center fw-bold">Sertifikat</h6>
                    <p class="text-center small text-muted">Penerbitan sertifikat hasil uji</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Information Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h3 class="fw-bold">Standar Kualitas Internasional</h3>
                    <p class="mb-0">
                        Laboratorium kami telah terakreditasi ISO/IEC 17025:2017 dan menerapkan sistem manajemen mutu yang ketat untuk memastikan hasil pengujian yang akurat dan terpercaya.
                    </p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="fas fa-certificate fa-5x opacity-75"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">Hubungi Kami</h2>
                <p class="lead text-muted">Tim kami siap membantu kebutuhan pengujian Anda</p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-map-marker-alt fa-3x text-primary mb-3"></i>
                            <h5>Alamat</h5>
                            <p class="text-muted">Jl. Kalimantan No. 37<br>Kampus Tegalboto, Jember<br>Jawa Timur 68121</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-phone fa-3x text-success mb-3"></i>
                            <h5>Telepon</h5>
                            <p class="text-muted">
                                <strong>Office:</strong> (0331) 334293<br>
                                <strong>WhatsApp:</strong> +62 812-3456-7890
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 border-0">
                        <div class="card-body text-center p-4">
                            <i class="fas fa-envelope fa-3x text-warning mb-3"></i>
                            <h5>Email</h5>
                            <p class="text-muted">
                                <strong>Info:</strong> lab@unej.ac.id<br>
                                <strong>Customer Service:</strong> cs@unej.ac.id
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 LIMS - Laboratory Information Management System</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="{{ route('login') }}" class="text-white text-decoration-none me-3">
                        <i class="fas fa-sign-in-alt me-1"></i>Login Staff
                    </a>
                    <a href="{{ route('tracking') }}" class="text-white text-decoration-none">
                        <i class="fas fa-search me-1"></i>Tracking Sampel
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Change navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 100) {
                navbar.style.background = 'rgba(44, 62, 80, 0.95)';
            } else {
                navbar.style.background = 'rgba(44, 62, 80, 0.9)';
            }
        });
    </script>
</body>
</html>
