<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Pengujian - LIMS</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .form-wizard {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .wizard-header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px 15px 0 0;
        }
        .nav-pills .nav-link {
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            margin: 0 0.5rem;
            transition: all 0.3s ease;
        }
        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }
        .nav-pills .nav-link:not(.active) {
            background: #ecf0f1;
            color: #7f8c8d;
        }
        .nav-pills .nav-link.completed {
            background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%);
            color: white;
        }
        .step-content {
            min-height: 400px;
            padding: 2rem;
        }
        .sample-item {
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .sample-item:hover {
            border-color: #3498db;
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.1);
        }
        .parameter-grid {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
        }
        .parameter-category {
            background: #f8f9fa;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            font-weight: bold;
        }
        .form-floating .form-control {
            border-radius: 10px;
        }
        .btn-wizard {
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        .progress-wizard {
            height: 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh;">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(44, 62, 80, 0.9);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('landing') }}">
                <i class="fas fa-microscope me-2"></i>LIMS
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('landing') }}">
                    <i class="fas fa-home me-1"></i>Beranda
                </a>
                <a class="nav-link" href="{{ route('tracking') }}">
                    <i class="fas fa-search me-1"></i>Tracking
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="form-wizard mx-auto" style="max-width: 900px;">
            <div class="wizard-header text-center">
                <h3 class="mb-1">
                    <i class="fas fa-flask me-2"></i>
                    Permohonan Pengujian Dan Kaji Ulang
                </h3>
                <p class="mb-0 opacity-75">Silakan lengkapi formulir berikut untuk mengajukan permohonan pengujian sampel</p>
            </div>

            <form id="sampleRequestForm" method="POST" action="{{ route('submit-request') ?? '#' }}">
                @csrf

                <!-- Progress Bar -->
                <div class="px-4 pt-3">
                    <div class="progress progress-wizard mb-3">
                        <div class="progress-bar" role="progressbar" style="width: 33%"></div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="px-4">
                    <ul class="nav nav-pills nav-justified" id="wizardTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="tab1-tab" data-bs-toggle="pill"
                                    data-bs-target="#tab1" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>
                                Data Pelanggan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab2-tab" data-bs-toggle="pill"
                                    data-bs-target="#tab2" type="button" role="tab" disabled>
                                <i class="fas fa-flask me-2"></i>
                                Data Sampel
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="tab3-tab" data-bs-toggle="pill"
                                    data-bs-target="#tab3" type="button" role="tab" disabled>
                                <i class="fas fa-check me-2"></i>
                                Konfirmasi
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="wizardContent">
                    <!-- Tab 1: Customer Data -->
                    <div class="tab-pane fade show active step-content" id="tab1" role="tabpanel">
                        <h5 class="mb-4">
                            <i class="fas fa-user text-primary me-2"></i>
                            Informasi Pelanggan
                        </h5>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="tel" class="form-control @error('whatsapp_number') is-invalid @enderror"
                                           id="whatsapp_number" name="whatsapp_number" placeholder="WhatsApp Number"
                                           value="{{ old('whatsapp_number') }}" required>
                                    <label for="whatsapp_number">
                                        <i class="fab fa-whatsapp me-2"></i>Nomor WhatsApp *
                                    </label>
                                    @error('whatsapp_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Format: 081234567890</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('contact_person') is-invalid @enderror"
                                           id="contact_person" name="contact_person" placeholder="Nama Lengkap"
                                           value="{{ old('contact_person') }}" required>
                                    <label for="contact_person">
                                        <i class="fas fa-user me-2"></i>Nama Lengkap *
                                    </label>
                                    @error('contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                           id="company_name" name="company_name" placeholder="Nama Perusahaan"
                                           value="{{ old('company_name') }}">
                                    <label for="company_name">
                                        <i class="fas fa-building me-2"></i>Nama Perusahaan/Sekolah
                                    </label>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email" placeholder="Email"
                                           value="{{ old('email') }}" required>
                                    <label for="email">
                                        <i class="fas fa-envelope me-2"></i>Email *
                                    </label>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control @error('city') is-invalid @enderror"
                                           id="city" name="city" placeholder="Kota/Kabupaten"
                                           value="{{ old('city') }}" required>
                                    <label for="city">
                                        <i class="fas fa-map-marker-alt me-2"></i>Kota/Kabupaten *
                                    </label>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <button type="button" class="btn btn-outline-primary w-100 h-100"
                                        onclick="getCurrentLocation()" style="min-height: 58px;">
                                    <i class="fas fa-crosshairs me-2"></i>
                                    Gunakan Lokasi Saat Ini
                                </button>
                            </div>

                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              id="address" name="address" placeholder="Alamat Lengkap"
                                              style="height: 100px" required>{{ old('address') }}</textarea>
                                    <label for="address">
                                        <i class="fas fa-home me-2"></i>Alamat Lengkap *
                                    </label>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                            <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                        </div>

                        <div class="text-end mt-4">
                            <button type="button" class="btn btn-primary btn-wizard" onclick="nextTab(2)">
                                Selanjutnya
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 2: Sample Data -->
                    <div class="tab-pane fade step-content" id="tab2" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="mb-0">
                                <i class="fas fa-flask text-primary me-2"></i>
                                Informasi Sampel
                            </h5>
                            <button type="button" class="btn btn-success" onclick="addSample()">
                                <i class="fas fa-plus me-2"></i>Tambah Sampel
                            </button>
                        </div>

                        <div id="samplesContainer">
                            <!-- Sample items will be added here -->
                        </div>

                        <div class="mt-4">
                            <div class="form-floating">
                                <textarea class="form-control" id="customer_requirements"
                                          name="customer_requirements" placeholder="Persyaratan khusus"
                                          style="height: 100px">{{ old('customer_requirements') }}</textarea>
                                <label for="customer_requirements">
                                    <i class="fas fa-comments me-2"></i>Persyaratan Permintaan Pelanggan
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-secondary btn-wizard" onclick="prevTab(1)">
                                <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                            </button>
                            <button type="button" class="btn btn-primary btn-wizard" onclick="nextTab(3)">
                                Selanjutnya
                                <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 3: Confirmation -->
                    <div class="tab-pane fade step-content" id="tab3" role="tabpanel">
                        <h5 class="mb-4">
                            <i class="fas fa-check text-primary me-2"></i>
                            Konfirmasi & Verifikasi
                        </h5>

                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Ringkasan Permintaan</h6>
                                <div id="summaryContent">
                                    <!-- Summary will be populated here -->
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <div id="captchaImage" class="mb-3">
                                            <!-- Captcha image will be loaded here -->
                                            <img src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='150' height='50'><rect width='150' height='50' fill='%23f8f9fa'/><text x='75' y='30' text-anchor='middle' font-family='monospace' font-size='20' fill='%23495057'>AB123</text></svg>"
                                                 alt="Captcha" class="border rounded">
                                        </div>
                                        <div class="form-floating">
                                            <input type="text" class="form-control @error('captcha') is-invalid @enderror"
                                                   id="captcha" name="captcha" placeholder="Kode Captcha" required>
                                            <label for="captcha">Masukkan Kode Captcha</label>
                                            @error('captcha')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary mt-2"
                                                onclick="refreshCaptcha()">
                                            <i class="fas fa-refresh me-1"></i>Refresh
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                   id="terms_accepted" name="terms_accepted" required>
                                            <label class="form-check-label" for="terms_accepted">
                                                Saya menyetujui <strong>syarat dan ketentuan</strong> pengujian laboratorium
                                            </label>
                                        </div>
                                        <hr>
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Dengan mencentang kotak ini, Anda menyatakan bahwa:
                                            <ul class="mt-2 mb-0">
                                                <li>Data yang diisi adalah benar dan akurat</li>
                                                <li>Sampel yang diserahkan sesuai dengan ketentuan</li>
                                                <li>Mengetahui prosedur dan waktu pengujian</li>
                                            </ul>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-secondary btn-wizard" onclick="prevTab(2)">
                                <i class="fas fa-arrow-left me-2"></i>Sebelumnya
                            </button>
                            <button type="submit" class="btn btn-success btn-wizard">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Permohonan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sample types and parameters data
        const sampleTypes = @json($sampleTypes);
        const testParameters = @json($testParameters);

        let sampleCounter = 0;

        // Initialize form
        document.addEventListener('DOMContentLoaded', function() {
            addSample(); // Add first sample by default
        });

        function nextTab(tabNumber) {
            if (validateCurrentTab()) {
                // Update progress bar
                const progressBar = document.querySelector('.progress-bar');
                progressBar.style.width = (tabNumber * 33.33) + '%';

                // Enable and activate next tab
                const nextTabButton = document.querySelector(`#tab${tabNumber}-tab`);
                nextTabButton.disabled = false;
                nextTabButton.classList.add('completed');

                // Show next tab
                bootstrap.Tab.getOrCreateInstance(nextTabButton).show();

                if (tabNumber === 3) {
                    generateSummary();
                }
            }
        }

        function prevTab(tabNumber) {
            const prevTabButton = document.querySelector(`#tab${tabNumber}-tab`);
            bootstrap.Tab.getOrCreateInstance(prevTabButton).show();

            // Update progress bar
            const progressBar = document.querySelector('.progress-bar');
            progressBar.style.width = (tabNumber * 33.33) + '%';
        }

        function validateCurrentTab() {
            const activeTab = document.querySelector('.tab-pane.show.active');
            const inputs = activeTab.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('is-invalid');
                    isValid = false;
                } else {
                    input.classList.remove('is-invalid');
                }
            });

            if (activeTab.id === 'tab1') {
                // Validate WhatsApp number format
                const whatsapp = document.getElementById('whatsapp_number');
                const whatsappRegex = /^[0-9]{10,15}$/;
                if (!whatsappRegex.test(whatsapp.value)) {
                    whatsapp.classList.add('is-invalid');
                    isValid = false;
                }

                // Validate email format
                const email = document.getElementById('email');
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value)) {
                    email.classList.add('is-invalid');
                    isValid = false;
                }
            }

            if (activeTab.id === 'tab2') {
                // Validate at least one sample
                const samples = document.querySelectorAll('.sample-item');
                if (samples.length === 0) {
                    alert('Minimal harus ada 1 sampel yang diuji');
                    isValid = false;
                }

                // Validate each sample
                samples.forEach(sample => {
                    const sampleType = sample.querySelector('select[name*="[sample_type]"]');
                    const quantity = sample.querySelector('input[name*="[quantity]"]');
                    const parameters = sample.querySelectorAll('input[name*="[parameters]"]:checked');

                    if (!sampleType.value || !quantity.value || parameters.length === 0) {
                        isValid = false;
                    }
                });
            }

            return isValid;
        }

        function addSample() {
            const container = document.getElementById('samplesContainer');
            const sampleHtml = `
                <div class="sample-item" data-sample="${sampleCounter}">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Sampel ${sampleCounter + 1}</h6>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSample(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select" name="samples[${sampleCounter}][sample_type]"
                                        onchange="updateParameters(${sampleCounter})" required>
                                    <option value="">Pilih Jenis Sampel</option>
                                    ${sampleTypes.map(type => `<option value="${type.name}">${type.name}</option>`).join('')}
                                    <option value="lainnya">Lainnya (tulis manual)</option>
                                </select>
                                <label>Jenis Sampel *</label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="number" class="form-control"
                                       name="samples[${sampleCounter}][quantity]"
                                       min="1" max="100" value="1" required>
                                <label>Jumlah Sampel *</label>
                            </div>
                        </div>

                        <div class="col-12" id="customSampleType${sampleCounter}" style="display: none;">
                            <div class="form-floating">
                                <input type="text" class="form-control"
                                       name="samples[${sampleCounter}][custom_sample_type]"
                                       placeholder="Jenis sampel custom">
                                <label>Jenis Sampel (Custom)</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-bold">Parameter Uji *</label>
                            <div class="parameter-grid" id="parameters${sampleCounter}">
                                <!-- Parameters will be loaded here -->
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control"
                                          name="samples[${sampleCounter}][description]"
                                          placeholder="Deskripsi sampel"
                                          style="height: 80px"></textarea>
                                <label>Deskripsi Sampel (Opsional)</label>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', sampleHtml);
            sampleCounter++;
        }

        function removeSample(button) {
            if (document.querySelectorAll('.sample-item').length > 1) {
                button.closest('.sample-item').remove();
            } else {
                alert('Minimal harus ada 1 sampel');
            }
        }

        function updateParameters(sampleIndex) {
            const sampleTypeSelect = document.querySelector(`select[name="samples[${sampleIndex}][sample_type]"]`);
            const customTypeDiv = document.getElementById(`customSampleType${sampleIndex}`);
            const parametersDiv = document.getElementById(`parameters${sampleIndex}`);

            // Show/hide custom sample type input
            if (sampleTypeSelect.value === 'lainnya') {
                customTypeDiv.style.display = 'block';
            } else {
                customTypeDiv.style.display = 'none';
            }

            // Load parameters based on sample type
            let parametersHtml = '';
            Object.keys(testParameters).forEach(category => {
                parametersHtml += `
                    <div class="parameter-category">${category}</div>
                    <div class="row g-2 mb-3">
                `;

                testParameters[category].forEach(param => {
                    parametersHtml += `
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="samples[${sampleIndex}][parameters][]"
                                       value="${param.id}" id="param_${sampleIndex}_${param.id}">
                                <label class="form-check-label" for="param_${sampleIndex}_${param.id}">
                                    ${param.name}
                                    <small class="text-muted">(Rp ${param.price.toLocaleString('id-ID')})</small>
                                </label>
                            </div>
                        </div>
                    `;
                });

                parametersHtml += '</div>';
            });

            parametersDiv.innerHTML = parametersHtml;
        }

        function generateSummary() {
            const form = document.getElementById('sampleRequestForm');
            const formData = new FormData(form);

            let summaryHtml = '<div class="row">';

            // Customer info
            summaryHtml += `
                <div class="col-md-6">
                    <h6>Data Pelanggan:</h6>
                    <ul class="list-unstyled">
                        <li><strong>Nama:</strong> ${formData.get('contact_person') || '-'}</li>
                        <li><strong>Perusahaan:</strong> ${formData.get('company_name') || '-'}</li>
                        <li><strong>WhatsApp:</strong> ${formData.get('whatsapp_number') || '-'}</li>
                        <li><strong>Email:</strong> ${formData.get('email') || '-'}</li>
                        <li><strong>Alamat:</strong> ${formData.get('address') || '-'}, ${formData.get('city') || '-'}</li>
                    </ul>
                </div>
            `;

            // Sample info
            const samples = document.querySelectorAll('.sample-item');
            summaryHtml += `
                <div class="col-md-6">
                    <h6>Data Sampel (${samples.length} sampel):</h6>
                    <ul class="list-unstyled">
            `;

            samples.forEach((sample, index) => {
                const sampleType = sample.querySelector('select[name*="[sample_type]"]').value;
                const quantity = sample.querySelector('input[name*="[quantity]"]').value;
                const checkedParams = sample.querySelectorAll('input[name*="[parameters]"]:checked');

                summaryHtml += `
                    <li>
                        <strong>Sampel ${index + 1}:</strong> ${sampleType} (${quantity} buah)<br>
                        <small>Parameter: ${checkedParams.length} parameter dipilih</small>
                    </li>
                `;
            });

            summaryHtml += '</ul></div></div>';

            if (formData.get('customer_requirements')) {
                summaryHtml += `
                    <div class="mt-3">
                        <h6>Persyaratan Khusus:</h6>
                        <p class="text-muted">${formData.get('customer_requirements')}</p>
                    </div>
                `;
            }

            document.getElementById('summaryContent').innerHTML = summaryHtml;
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('latitude').value = position.coords.latitude;
                        document.getElementById('longitude').value = position.coords.longitude;
                        alert('Lokasi berhasil dideteksi!');
                    },
                    function(error) {
                        alert('Gagal mendapatkan lokasi: ' + error.message);
                    }
                );
            } else {
                alert('Browser tidak mendukung geolokasi');
            }
        }

        function refreshCaptcha() {
            // Simulate captcha refresh
            const captchaImage = document.querySelector('#captchaImage img');
            const randomCode = Math.random().toString(36).substr(2, 5).toUpperCase();
            captchaImage.src = `data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='150' height='50'><rect width='150' height='50' fill='%23f8f9fa'/><text x='75' y='30' text-anchor='middle' font-family='monospace' font-size='20' fill='%23495057'>${randomCode}</text></svg>`;
        }

        // Form validation
        document.getElementById('sampleRequestForm').addEventListener('submit', function(e) {
            if (!validateCurrentTab()) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang diperlukan');
            }
        });
    </script>
</body>
</html>
