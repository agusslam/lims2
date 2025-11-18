<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Permohonan Pengujian - {{ config('app.name', 'LIMS') }}</title>

    <!-- Bootstrap 5.3.0 from CDN (Laravel 12 compatible) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        .step-indicator {
            background: #e9ecef;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .step-indicator.active { background: #0d6efd; color: white; }
        .step-indicator.completed { background: #198754; color: white; }
        .step-line { height: 2px; background: #dee2e6; flex: 1; margin: 0 1rem; }
        .step-line.active { background: #0d6efd; }
        .parameter-group {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            margin-bottom: 1rem;
            background: #f8f9fa;
        }
        .parameter-item {
            padding: 0.75rem;
            border-bottom: 1px solid #e9ecef;
            background: white;
            border-radius: 0.25rem;
            margin-bottom: 0.5rem;
        }
        .parameter-item:last-child { margin-bottom: 0; }
        .price-display { color: #198754; font-weight: 600; }
        .total-price {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 0.5rem;
            padding: 1.5rem;
            border: 2px solid #0d6efd;
        }
        .tab-content { display: none; }
        .tab-content.show { display: block; }
        .form-check-input:checked { background-color: #0d6efd; border-color: #0d6efd; }
        .btn { transition: all 0.3s ease; }
        .btn:hover { transform: translateY(-1px); }
        .loading { opacity: 0.6; pointer-events: none; }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('public.landing') ?? '/' }}">
                <i class="fas fa-flask me-2"></i>{{ config('app.name', 'LIMS') }}
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('public.tracking') ?? '#' }}">
                    <i class="fas fa-search me-1"></i>Tracking
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Progress Steps -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <div class="step-indicator" id="step-indicator-1">1</div>
                            <div class="step-line" id="step-line-1"></div>
                            <div class="step-indicator" id="step-indicator-2">2</div>
                            <div class="step-line" id="step-line-2"></div>
                            <div class="step-indicator" id="step-indicator-3">3</div>
                        </div>
                        <div class="d-flex justify-content-between text-center">
                            <div class="flex-fill"><small class="text-muted">Data Pemohon</small></div>
                            <div class="flex-fill"><small class="text-muted">Data Sampel</small></div>
                            <div class="flex-fill"><small class="text-muted">Konfirmasi</small></div>
                        </div>
                    </div>
                </div>

                <div class="card shadow" x-data="sampleRequestForm()">
                    <div class="card-body">
                        <h2 class="card-title text-center mb-4">
                            <i class="fas fa-vial text-primary me-2"></i>
                            Permohonan Pengujian Dan Kaji Ulang
                        </h2>

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Terdapat kesalahan:</h6>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('public.submit-request') ?? '#' }}" @submit.prevent="submitForm">
                            @csrf

                            <!-- Tab 1: Data Pemohon -->
                            <div class="tab-content show" id="tab-1">
                                <h4 class="mb-4"><i class="fas fa-user me-2 text-primary"></i>Data Pemohon</h4>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Nomor WhatsApp <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fab fa-whatsapp text-success"></i></span>
                                            <input type="tel" name="phone" x-model="form.phone"
                                                   class="form-control" placeholder="08xxxxxxxxxx" required
                                                   pattern="^(\+62|62|0)8[1-9][0-9]{6,9}$"
                                                   value="{{ old('phone') }}">
                                        </div>
                                        <small class="text-muted">Format: 08xxxxxxxxxx atau +628xxxxxxxxxx</small>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Contact Person <span class="text-danger">*</span></label>
                                        <input type="text" name="contact_person" x-model="form.contact_person"
                                               class="form-control" required value="{{ old('contact_person') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Nama Perusahaan/Sekolah <span class="text-danger">*</span></label>
                                        <input type="text" name="company_name" x-model="form.company_name"
                                               class="form-control" required value="{{ old('company_name') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Kota/Kabupaten <span class="text-danger">*</span></label>
                                        <input type="text" name="city" x-model="form.city"
                                               class="form-control" required value="{{ old('city') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" x-model="form.email"
                                               class="form-control" required value="{{ old('email') }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold">Alamat Lengkap <span class="text-danger">*</span></label>
                                        <textarea name="address" x-model="form.address" rows="3"
                                                  class="form-control" required
                                                  placeholder="Alamat lengkap dengan kode pos">{{ old('address') }}</textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end mt-4">
                                    <button type="button" @click="nextTab()" class="btn btn-primary btn-lg">
                                        Selanjutnya <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Tab 2: Data Sampel -->
                            <div class="tab-content" id="tab-2">
                                <h4 class="mb-4"><i class="fas fa-flask me-2 text-primary"></i>Data Sampel</h4>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Jenis Sampel <span class="text-danger">*</span></label>
                                        <select name="sample_type_id" x-model="form.sample_type_id"
                                                class="form-select" required @change="updateParameterOptions()">
                                            <option value="">Pilih jenis sampel</option>
                                            @if(isset($sampleTypes))
                                                @foreach($sampleTypes as $type)
                                                    <option value="{{ $type->id }}" {{ old('sample_type_id') == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
                                                @endforeach
                                            @endif
                                            <option value="other">Lainnya (tulis manual)</option>
                                        </select>
                                        <input type="text" name="custom_sample_type" x-show="form.sample_type_id === 'other'"
                                               class="form-control mt-2" placeholder="Tuliskan jenis sampel"
                                               value="{{ old('custom_sample_type') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">Jumlah Sampel <span class="text-danger">*</span></label>
                                        <input type="number" name="quantity" x-model="form.quantity"
                                               min="1" max="50" class="form-control" required
                                               @input="calculateTotal()" value="{{ old('quantity', 1) }}">
                                    </div>
                                </div>

                                <!-- Parameter Selection -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Parameter Uji <span class="text-danger">*</span></label>
                                    <div class="parameter-groups">
                                        @if(isset($parameters))
                                            @foreach($parameters as $category => $categoryParameters)
                                                <div class="parameter-group">
                                                    <h6 class="fw-bold text-primary mb-3">
                                                        <i class="fas fa-cog me-2"></i>{{ $category }}
                                                    </h6>
                                                    <div class="row">
                                                        @foreach($categoryParameters as $parameter)
                                                            <div class="col-md-6">
                                                                <div class="parameter-item">
                                                                    <div class="form-check">
                                                                        <input type="checkbox" name="parameters[]"
                                                                               value="{{ $parameter->id }}"
                                                                               class="form-check-input"
                                                                               @change="calculateTotal()"
                                                                               {{ in_array($parameter->id, old('parameters', [])) ? 'checked' : '' }}>
                                                                        <label class="form-check-label d-flex justify-content-between w-100">
                                                                            <div>
                                                                                <strong>{{ $parameter->name }}</strong>
                                                                                <small class="text-muted d-block">({{ $parameter->unit }})</small>
                                                                            </div>
                                                                            <span class="price-display">Rp {{ number_format($parameter->price, 0, ',', '.') }}</span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Parameter uji tidak tersedia. Silahkan hubungi administrator.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Additional Requirements -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Persyaratan Permintaan Pelanggan</label>
                                    <textarea name="customer_requirements" rows="3" class="form-control"
                                              placeholder="Masukkan persyaratan khusus jika ada...">{{ old('customer_requirements') }}</textarea>
                                </div>

                                <!-- Total Price Display -->
                                <div class="total-price">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <h5 class="mb-1"><i class="fas fa-calculator me-2"></i>Estimasi Total Biaya</h5>
                                            <small class="text-muted">
                                                <span x-text="getSelectedParametersCount()"></span> parameter ×
                                                <span x-text="form.quantity || 1"></span> sampel
                                            </small>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <h3 class="text-success mb-0">
                                                Rp <span x-text="formatPrice(totalPrice)"></span>
                                            </h3>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" @click="prevTab()" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-1"></i> Sebelumnya
                                    </button>
                                    <button type="button" @click="nextTab()" class="btn btn-primary btn-lg">
                                        Selanjutnya <i class="fas fa-arrow-right ms-1"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Tab 3: Konfirmasi -->
                            <div class="tab-content" id="tab-3">
                                <h4 class="mb-4"><i class="fas fa-check-circle me-2 text-primary"></i>Konfirmasi Permohonan</h4>

                                <!-- Summary -->
                                <div class="card bg-light mb-4">
                                    <div class="card-body">
                                        <h6><i class="fas fa-clipboard-list me-2"></i>Ringkasan Permohonan:</h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>Contact Person:</strong> <span x-text="form.contact_person || '-'"></span></p>
                                                <p class="mb-2"><strong>Perusahaan:</strong> <span x-text="form.company_name || '-'"></span></p>
                                                <p class="mb-2"><strong>WhatsApp:</strong> <span x-text="form.phone || '-'"></span></p>
                                                <p class="mb-0"><strong>Kota:</strong> <span x-text="form.city || '-'"></span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-2"><strong>Jumlah Sampel:</strong> <span x-text="form.quantity || 1"></span></p>
                                                <p class="mb-2"><strong>Parameter:</strong> <span x-text="getSelectedParametersCount()"></span> item</p>
                                                <p class="mb-0"><strong>Total Biaya:</strong>
                                                    <span class="text-success fw-bold">Rp <span x-text="formatPrice(totalPrice)"></span></span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Captcha Placeholder -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Verifikasi Keamanan</label>
                                    <div class="border rounded p-3 bg-light text-center">
                                        <div class="form-check d-inline-flex align-items-center">
                                            <input type="checkbox" name="captcha_verified" class="form-check-input me-2" required>
                                            <label class="form-check-label">
                                                <i class="fas fa-robot me-1"></i>Saya bukan robot
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms and Conditions -->
                                <div class="mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" name="terms_accepted" class="form-check-input" required>
                                        <label class="form-check-label">
                                            Saya menyetujui <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#termsModal">syarat dan ketentuan</a>
                                            yang berlaku di laboratorium
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" @click="prevTab()" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-1"></i> Sebelumnya
                                    </button>
                                    <button type="submit" class="btn btn-success btn-lg" :disabled="isSubmitting">
                                        <span x-show="!isSubmitting">
                                            <i class="fas fa-paper-plane me-2"></i>Kirim Permohonan
                                        </span>
                                        <span x-show="isSubmitting">
                                            <i class="fas fa-spinner fa-spin me-2"></i>Mengirim...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Syarat dan Ketentuan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Ketentuan Pengujian Laboratorium:</h6>
                    <ol>
                        <li>Sampel yang diterima akan diuji sesuai dengan metode standar yang berlaku</li>
                        <li>Hasil pengujian hanya berlaku untuk sampel yang diuji</li>
                        <li>Sampel akan disimpan maksimal 30 hari setelah pengujian selesai</li>
                        <li>Pembayaran dilakukan sesuai dengan invoice yang diterbitkan</li>
                        <li>Sertifikat dapat diambil setelah pembayaran lunas</li>
                    </ol>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Saya Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        function sampleRequestForm() {
            return {
                currentTab: 1,
                totalPrice: 0,
                isSubmitting: false,
                form: {
                    phone: '{{ old("phone") }}',
                    contact_person: '{{ old("contact_person") }}',
                    company_name: '{{ old("company_name") }}',
                    city: '{{ old("city") }}',
                    email: '{{ old("email") }}',
                    address: '{{ old("address") }}',
                    sample_type_id: '{{ old("sample_type_id") }}',
                    quantity: {{ old('quantity', 1) }},
                    customer_requirements: '{{ old("customer_requirements") }}'
                },

                init() {
                    this.updateStepIndicators();
                    this.calculateTotal();
                },

                nextTab() {
                    if (this.validateCurrentTab()) {
                        if (this.currentTab < 3) {
                            this.currentTab++;
                            this.updateStepIndicators();
                            this.scrollToTop();
                        }
                    } else {
                        this.showValidationErrors();
                    }
                },

                prevTab() {
                    if (this.currentTab > 1) {
                        this.currentTab--;
                        this.updateStepIndicators();
                        this.scrollToTop();
                    }
                },

                updateStepIndicators() {
                    // Hide all tabs
                    document.querySelectorAll('.tab-content').forEach(tab => {
                        tab.classList.remove('show');
                    });

                    // Show current tab
                    const currentTabEl = document.getElementById(`tab-${this.currentTab}`);
                    if (currentTabEl) {
                        currentTabEl.classList.add('show');
                    }

                    // Update step indicators
                    for (let i = 1; i <= 3; i++) {
                        const stepIndicator = document.getElementById(`step-indicator-${i}`);
                        const stepLine = document.getElementById(`step-line-${i}`);

                        if (stepIndicator) {
                            stepIndicator.classList.remove('active', 'completed');
                            if (i < this.currentTab) {
                                stepIndicator.classList.add('completed');
                            } else if (i === this.currentTab) {
                                stepIndicator.classList.add('active');
                            }
                        }

                        if (stepLine && i < 3) {
                            stepLine.classList.toggle('active', i < this.currentTab);
                        }
                    }
                },

                validateCurrentTab() {
                    if (this.currentTab === 1) {
                        return this.form.phone && this.form.contact_person &&
                               this.form.company_name && this.form.city &&
                               this.form.email && this.form.address;
                    } else if (this.currentTab === 2) {
                        return this.form.sample_type_id && this.form.quantity > 0 &&
                               this.getSelectedParametersCount() > 0;
                    }
                    return true;
                },

                showValidationErrors() {
                    let message = 'Mohon lengkapi semua field yang wajib diisi:';

                    if (this.currentTab === 1) {
                        if (!this.form.phone) message += '\n- Nomor WhatsApp';
                        if (!this.form.contact_person) message += '\n- Contact Person';
                        if (!this.form.company_name) message += '\n- Nama Perusahaan/Sekolah';
                        if (!this.form.city) message += '\n- Kota/Kabupaten';
                        if (!this.form.email) message += '\n- Email';
                        if (!this.form.address) message += '\n- Alamat Lengkap';
                    } else if (this.currentTab === 2) {
                        if (!this.form.sample_type_id) message += '\n- Jenis Sampel';
                        if (this.form.quantity <= 0) message += '\n- Jumlah Sampel';
                        if (this.getSelectedParametersCount() === 0) message += '\n- Parameter Uji (minimal 1)';
                    }

                    alert(message);
                },

                calculateTotal() {
                    this.totalPrice = 0;
                    const checkboxes = document.querySelectorAll('input[name="parameters[]"]:checked');
                    const quantity = parseInt(this.form.quantity) || 1;

                    checkboxes.forEach(checkbox => {
                        const parameterItem = checkbox.closest('.parameter-item');
                        const priceText = parameterItem?.querySelector('.price-display')?.textContent;
                        if (priceText) {
                            const price = parseInt(priceText.replace(/[^\d]/g, ''));
                            this.totalPrice += price * quantity;
                        }
                    });
                },

                getSelectedParametersCount() {
                    return document.querySelectorAll('input[name="parameters[]"]:checked').length;
                },

                formatPrice(price) {
                    return new Intl.NumberFormat('id-ID').format(price || 0);
                },

                updateParameterOptions() {
                    // Reset parameters when sample type changes
                    const checkboxes = document.querySelectorAll('input[name="parameters[]"]');
                    checkboxes.forEach(cb => cb.checked = false);
                    this.calculateTotal();
                },

                scrollToTop() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                },

                submitForm(event) {
                    if (this.isSubmitting) return;

                    // Final validation
                    if (!this.validateCurrentTab()) {
                        this.showValidationErrors();
                        return;
                    }

                    // Check required checkboxes
                    const captchaChecked = document.querySelector('input[name="captcha_verified"]')?.checked;
                    const termsChecked = document.querySelector('input[name="terms_accepted"]')?.checked;

                    if (!captchaChecked || !termsChecked) {
                        alert('Mohon centang verifikasi keamanan dan persetujuan syarat & ketentuan');
                        return;
                    }

                    this.isSubmitting = true;

                    // Submit the form
                    setTimeout(() => {
                        event.target.submit();
                    }, 500);
                }
            }
        }

        // Initialize form after page load
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-calculate total when page loads
            setTimeout(() => {
                const event = new Event('change');
                document.querySelectorAll('input[name="parameters[]"]:checked').forEach(cb => {
                    cb.dispatchEvent(event);
                });
            }, 500);
        });
    </script>
</body>
</html>
