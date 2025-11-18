<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LIMS - Laboratory Information Management System</title>
    
    <!-- Bootstrap 5.3.2 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome 6.5.0 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --lims-primary: #2563eb;
            --lims-secondary: #64748b;
            --lims-success: #059669;
            --lims-warning: #d97706;
            --lims-danger: #dc2626;
            --lims-info: #0891b2;
            --lims-light: #f8fafc;
            --lims-dark: #1e293b;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
        }
        
        .hero-section {
            background: linear-gradient(135deg, var(--lims-primary) 0%, var(--lims-info) 100%);
            min-height: 80vh;
            display: flex;
            align-items: center;
            color: white;
        }
        
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 1rem;
            overflow: hidden;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .feature-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .process-step {
            text-align: center;
            position: relative;
        }
        
        .process-step::after {
            content: '';
            position: absolute;
            top: 50px;
            right: -50%;
            width: 100%;
            height: 2px;
            background: var(--lims-primary);
            z-index: 1;
        }
        
        .process-step:last-child::after {
            display: none;
        }
        
        .process-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--lims-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin: 0 auto 1rem;
            position: relative;
            z-index: 2;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .btn-cta {
            padding: 12px 30px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        @media (max-width: 768px) {
            .process-step::after {
                display: none;
            }
            
            .process-step {
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand text-primary" href="#">
                <i class="fas fa-flask me-2"></i>LIMS
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#beranda">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#layanan">Layanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#proses">Proses</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kontak">Kontak</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('public.tracking') }}">
                            <i class="fas fa-search me-1"></i>Tracking
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a href="{{ route('public.request') }}" class="btn btn-primary btn-cta">
                            <i class="fas fa-plus me-2"></i>Ajukan Pengujian
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
                        Sistem Informasi Manajemen Laboratorium
                    </h1>
                    <p class="lead mb-4">
                        Solusi terintegrasi untuk pengelolaan sampel laboratorium yang memenuhi standar ISO/IEC 17025:2017. 
                        Proses yang efisien, akurat, dan terpercaya.
                    </p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('public.request') }}" class="btn btn-light btn-cta btn-lg">
                            <i class="fas fa-vial me-2"></i>Ajukan Pengujian
                        </a>
                        <a href="{{ route('public.tracking') }}" class="btn btn-outline-light btn-cta btn-lg">
                            <i class="fas fa-search me-2"></i>Lacak Sampel
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <i class="fas fa-microscope" style="font-size: 15rem; opacity: 0.3;"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="layanan" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Layanan Kami</h2>
                    <p class="text-muted">Layanan pengujian laboratorium yang komprehensif dan berkualitas</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-primary">
                                <i class="fas fa-flask"></i>
                            </div>
                            <h5 class="card-title">Pengujian Sampel</h5>
                            <p class="card-text">
                                Pengujian berbagai jenis sampel dengan metode standar dan teknologi modern 
                                sesuai SNI dan standar internasional.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-success">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <h5 class="card-title">Sertifikat Resmi</h5>
                            <p class="card-text">
                                Penerbitan sertifikat hasil pengujian yang diakui secara nasional 
                                dan internasional dengan standar ISO/IEC 17025:2017.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-info">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5 class="card-title">Monitoring Real-time</h5>
                            <p class="card-text">
                                Sistem tracking sampel secara real-time untuk memantau progress 
                                pengujian dari pendaftaran hingga hasil akhir.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-warning">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5 class="card-title">Kualitas Terjamin</h5>
                            <p class="card-text">
                                Sistem manajemen mutu yang ketat dengan audit trail lengkap 
                                dan kontrol kualitas berlapis.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-danger">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h5 class="card-title">Proses Cepat</h5>
                            <p class="card-text">
                                Workflow yang efisien dan otomatis untuk mempercepat proses 
                                pengujian tanpa mengorbankan akurasi hasil.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body p-4">
                            <div class="feature-icon bg-secondary">
                                <i class="fas fa-users"></i>
                            </div>
                            <h5 class="card-title">Tim Ahli</h5>
                            <p class="card-text">
                                Didukung oleh tim analis berpengalaman dan tersertifikasi 
                                dengan keahlian di berbagai bidang pengujian.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Process Section -->
    <section id="proses" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Alur Proses Pengujian</h2>
                    <p class="text-muted">Proses yang terstruktur dan transparan untuk hasil yang optimal</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-2">
                    <div class="process-step">
                        <div class="process-number">1</div>
                        <h6>Permohonan</h6>
                        <p class="small text-muted">Ajukan permohonan pengujian secara online</p>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="process-step">
                        <div class="process-number">2</div>
                        <h6>Verifikasi</h6>
                        <p class="small text-muted">Tim kami melakukan verifikasi dan kodifikasi sampel</p>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="process-step">
                        <div class="process-number">3</div>
                        <h6>Pengujian</h6>
                        <p class="small text-muted">Proses pengujian oleh analis bersertifikat</p>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="process-step">
                        <div class="process-number">4</div>
                        <h6>Review</h6>
                        <p class="small text-muted">Review teknis dan mutu hasil pengujian</p>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="process-step">
                        <div class="process-number">5</div>
                        <h6>Sertifikat</h6>
                        <p class="small text-muted">Penerbitan sertifikat hasil pengujian</p>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <div class="process-step">
                        <div class="process-number">6</div>
                        <h6>Selesai</h6>
                        <p class="small text-muted">Pengambilan hasil dan feedback kepuasan</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-5 bg-primary text-white">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3 mb-4">
                    <h3 class="fw-bold" id="statSamples">{{ \App\Models\Sample::count() }}+</h3>
                    <p>Sampel Teruji</p>
                </div>
                
                <div class="col-md-3 mb-4">
                    <h3 class="fw-bold" id="statParameters">{{ \App\Models\TestParameter::count() }}+</h3>
                    <p>Parameter Uji</p>
                </div>
                <div class="col-md-3 mb-4">
                    <h3 class="fw-bold">ISO 17025</h3>
                    <p>Standar Kualitas</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="kontak" class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <h2 class="fw-bold">Hubungi Kami</h2>
                    <p class="text-muted">Tim kami siap membantu kebutuhan pengujian laboratorium Anda</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon bg-primary mx-auto">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h5>Alamat</h5>
                    <p class="text-muted">
                        Jl. Kalimantan No. 37<br>
                        Jember, Jawa Timur 68121
                    </p>
                </div>
                
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon bg-success mx-auto">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h5>Telepon</h5>
                    <p class="text-muted">
                        (0331) 330224<br>
                        WhatsApp: 081234567890
                    </p>
                </div>
                
                <div class="col-md-4 text-center mb-4">
                    <div class="feature-icon bg-info mx-auto">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h5>Email</h5>
                    <p class="text-muted">
                        info@lims.unej.ac.id<br>
                        laboratorium@unej.ac.id
                    </p>
                </div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <div class="card">
                        <div class="card-body">
                            <h5>Mulai Pengujian Sekarang</h5>
                            <p class="text-muted mb-3">
                                Dapatkan hasil pengujian yang akurat dan terpercaya untuk kebutuhan Anda
                            </p>
                            <a href="{{ route('public.request') }}" class="btn btn-primary btn-cta me-3">
                                <i class="fas fa-vial me-2"></i>Ajukan Pengujian
                            </a>
                            <a href="{{ route('public.tracking') }}" class="btn btn-outline-primary btn-cta">
                                <i class="fas fa-search me-2"></i>Lacak Sampel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4 bg-dark text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-flask me-2 fs-4"></i>
                        <div>
                            <h6 class="mb-0">LIMS - Laboratory Information Management System</h6>
                            <small class="text-muted">ISO/IEC 17025:2017 Compliant</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex justify-content-end gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">
                                <i class="fas fa-sign-in-alt me-1"></i>Login Staff
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-md-6">
                    <small>&copy; {{ date('Y') }} LIMS. All rights reserved.</small>
                </div>
                <div class="col-md-6 text-end">
                    <small>Powered by Laravel {{ app()->version() }}</small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
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

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.backgroundColor = 'rgba(255, 255, 255, 0.95)';
                navbar.style.backdropFilter = 'blur(10px)';
            } else {
                navbar.style.backgroundColor = 'white';
                navbar.style.backdropFilter = 'none';
            }
        });

        // Counter animation
        function animateCounter(element, target) {
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if (current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current) + '+';
            }, 30);
        }

        // Trigger counter animation when in viewport
        const observerOptions = {
            threshold: 0.5,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !entry.target.hasAttribute('data-animated')) {
                    const target = parseInt(entry.target.textContent);
                    animateCounter(entry.target, target);
                    entry.target.setAttribute('data-animated', 'true');
                }
            });
        }, observerOptions);

        document.querySelectorAll('[id^="stat"]').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
