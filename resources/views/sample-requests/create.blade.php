@extends('layouts.app')

@section('title', 'Tambah Permohonan Sampel')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-plus me-2"></i>Tambah Permohonan Sampel
            </h1>
            <p class="text-muted mb-0">Buat permohonan pengujian sampel baru</p>
        </div>
        <div>
            <a href="{{ route('sample-requests.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Permohonan</h6>
                </div>
                <div class="card-body">
                    <form id="sampleRequestForm" method="POST" action="{{ route('sample-requests.store') }}">
                        @csrf
                        
                        <!-- Customer Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_person">Nama Kontak <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('contact_person') is-invalid @enderror" 
                                           id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
                                    @error('contact_person')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name">Nama Perusahaan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                           id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Nomor Telepon <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="address">Alamat <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('address') is-invalid @enderror" 
                                              id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">Kota <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city') }}" required>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Sample Information -->
                        <hr>
                        <h5>Informasi Sampel</h5>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sample_type_id">Jenis Sampel <span class="text-danger">*</span></label>
                                    <select class="form-control @error('sample_type_id') is-invalid @enderror" 
                                            id="sample_type_id" name="sample_type_id" required>
                                        <option value="">Pilih Jenis Sampel</option>
                                        @foreach($sampleTypes as $sampleType)
                                            <option value="{{ $sampleType->id }}" 
                                                    {{ old('sample_type_id') == $sampleType->id ? 'selected' : '' }}>
                                                {{ $sampleType->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('sample_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quantity">Jumlah Sampel <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror" 
                                           id="quantity" name="quantity" value="{{ old('quantity', 1) }}" min="1" required>
                                    @error('quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Parameters Selection -->
                        <div class="form-group">
                            <label>Parameter Uji <span class="text-danger">*</span></label>
                            <div id="parametersContainer" class="border p-3" style="max-height: 300px; overflow-y: auto;">
                                <p class="text-muted">Pilih jenis sampel terlebih dahulu untuk melihat parameter yang tersedia</p>
                            </div>
                            @error('parameters')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="customer_requirements">Kebutuhan Khusus</label>
                            <textarea class="form-control @error('customer_requirements') is-invalid @enderror" 
                                      id="customer_requirements" name="customer_requirements" rows="3" 
                                      placeholder="Tuliskan kebutuhan khusus atau catatan untuk pengujian ini">{{ old('customer_requirements') }}</textarea>
                            @error('customer_requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="urgent" name="urgent" value="1" 
                                       {{ old('urgent') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="urgent">
                                    <span class="text-danger">Urgent</span> - Pengujian prioritas
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button id="submitBtn" type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Simpan Permohonan
                            </button>
                            <a href="{{ route('sample-requests.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Panduan</h6>
                </div>
                <div class="card-body">
                    <h6>Tips Mengisi Permohonan:</h6>
                    <ul class="mb-3">
                        <li>Pastikan data kontak valid dan dapat dihubungi</li>
                        <li>Pilih jenis sampel yang sesuai</li>
                        <li>Tentukan parameter uji yang dibutuhkan</li>
                        <li>Jelaskan kebutuhan khusus jika ada</li>
                    </ul>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Kode Tracking</strong> akan digenerate otomatis setelah permohonan disimpan.
                    </div>

                    <!-- Summary placeholder -->
                    <div id="requestSummary" class="mt-3">
                        <p class="text-muted mb-0">Tambahkan parameter untuk melihat ringkasan estimasi.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- Helper safe selectors ---
    const qs = (s, root = document) => root.querySelector(s);
    const qsa = (s, root = document) => Array.from(root.querySelectorAll(s));

    // Parameters loading (sample type select)
    const sampleTypeSelect = qs('#sample_type_id');
    const parametersContainer = qs('#parametersContainer');

    if (sampleTypeSelect && parametersContainer) {
        sampleTypeSelect.addEventListener('change', function() {
            const sampleTypeId = this.value;
            const container = parametersContainer;
            if (sampleTypeId) {
                container.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat parameter...</div>';
                fetch(`/api/parameters/sample-type/${sampleTypeId}`)
                    .then(response => response.json())
                    .then(data => {
                        let html = '';
                        if (data && data.length > 0) {
                            const categories = [...new Set(data.map(p => p.category))];
                            categories.forEach(category => {
                                html += `<h6 class="mt-3 mb-2">${category}</h6>`;
                                const categoryParams = data.filter(p => p.category === category);
                                categoryParams.forEach(param => {
                                    html += `
                                        <div class="custom-control custom-checkbox mb-2">
                                            <input type="checkbox" class="custom-control-input parameter-checkbox" 
                                                   id="param_${param.id}" name="parameters[]" value="${param.id}" data-price="${param.price ?? 0}">
                                            <label class="custom-control-label" for="param_${param.id}">
                                                <strong>${param.name}</strong>
                                                ${param.unit ? ` (${param.unit})` : ''}
                                                ${param.description ? `<br><small class="text-muted">${param.description}</small>` : ''}
                                            </label>
                                        </div>
                                    `;
                                });
                            });
                        } else {
                            html = '<p class="text-muted">Tidak ada parameter tersedia untuk jenis sampel ini</p>';
                        }
                        container.innerHTML = html;

                        // Jika ada sample-card/price logic, inisialisasi listeners aman
                        initSampleParameterListeners();
                        updateRequestSummary();
                    })
                    .catch(error => {
                        console.error(error);
                        container.innerHTML = '<p class="text-danger">Error memuat parameter. Silakan coba lagi.</p>';
                    });
            } else {
                container.innerHTML = '<p class="text-muted">Pilih jenis sampel terlebih dahulu untuk melihat parameter yang tersedia</p>';
                updateRequestSummary();
            }
        });
    }

    // --- Sample card & price logic (safe) ---
    function initSampleParameterListeners() {
        const sampleCards = qsa('.sample-card');
        if (sampleCards.length === 0) {
            // Attach change listeners to parametersContainer checkboxes for simple flow
            qsa('#parametersContainer .parameter-checkbox').forEach(cb => {
                cb.addEventListener('change', () => updateRequestSummary());
            });
            return;
        }

        sampleCards.forEach((card, idx) => {
            qsa('.parameter-checkbox', card).forEach(cb => {
                cb.addEventListener('change', () => calculateSamplePrice(card));
            });
            const qty = qs('.quantity-input', card);
            if (qty) qty.addEventListener('input', () => calculateSamplePrice(card));
        });
    }

    function calculateSamplePrice(sampleCard) {
        const checkedParams = qsa('.parameter-checkbox:checked', sampleCard);
        const qtyEl = qs('.quantity-input', sampleCard);
        const quantity = qtyEl ? (parseInt(qtyEl.value) || 1) : 1;
        let total = 0;
        checkedParams.forEach(checkbox => {
            const price = parseFloat(checkbox.dataset.price || 0) || 0;
            total += price * quantity;
        });
        const totalEl = qs('.sample-total', sampleCard);
        if (totalEl) totalEl.textContent = `Rp ${total.toLocaleString('id-ID')}`;
        updateRequestSummary();
    }

    function updateRequestSummary() {
        const sampleCards = qsa('.sample-card');
        if (sampleCards.length === 0) {
            const allChecked = qsa('#parametersContainer .parameter-checkbox:checked');
            const totalSamples = 1;
            const totalParameters = allChecked.length;
            let totalPrice = 0;
            allChecked.forEach(checkbox => {
                totalPrice += parseFloat(checkbox.dataset.price || 0) || 0;
            });
            const summaryHtml = `
                <p class="mb-1"><strong>Total Sampel:</strong> ${totalSamples}</p>
                <p class="mb-1"><strong>Total Parameter:</strong> ${totalParameters}</p>
                <p class="mb-0"><strong>Estimasi Total:</strong> <span class="text-success">Rp ${totalPrice.toLocaleString('id-ID')}</span></p>
            `;
            const requestSummary = qs('#requestSummary');
            if (requestSummary) requestSummary.innerHTML = summaryHtml;
            return;
        }

        let totalSamples = sampleCards.length;
        let totalParameters = 0;
        let totalPrice = 0;
        sampleCards.forEach(card => {
            const checked = qsa('.parameter-checkbox:checked', card);
            const qty = qs('.quantity-input', card);
            const quantity = qty ? (parseInt(qty.value) || 1) : 1;
            totalParameters += checked.length;
            checked.forEach(box => {
                totalPrice += (parseFloat(box.dataset.price || 0) || 0) * quantity;
            });
        });
        const summaryHtml = totalSamples > 0 ? `
            <p class="mb-1"><strong>Total Sampel:</strong> ${totalSamples}</p>
            <p class="mb-1"><strong>Total Parameter:</strong> ${totalParameters}</p>
            <p class="mb-0"><strong>Estimasi Total:</strong> <span class="text-success">Rp ${totalPrice.toLocaleString('id-ID')}</span></p>
        ` : `<p class="text-muted mb-0">Tambahkan sampel untuk melihat ringkasan</p>`;
        const requestSummary = qs('#requestSummary');
        if (requestSummary) requestSummary.innerHTML = summaryHtml;
    }

    // --- Customer select listener (safe) ---
    const customerSelect = qs('select[name="customer_id"]');
    if (customerSelect) {
        customerSelect.addEventListener('change', function() {
            const customerId = this.value;
            const customerDetails = qs('#customerDetails');
            const customerInfo = qs('#customerInfo');
            if (!customerDetails || !customerInfo) return;
            if (customerId && typeof customers !== 'undefined') {
                const customer = customers.find(c => c.id == customerId);
                if (customer) {
                    customerInfo.innerHTML = `
                        <small>
                            <strong>Perusahaan:</strong> ${customer.company_name}<br>
                            <strong>Email:</strong> ${customer.email}<br>
                            <strong>WhatsApp:</strong> ${customer.whatsapp_number}<br>
                            <strong>Kota:</strong> ${customer.city}
                        </small>
                    `;
                    customerDetails.style.display = 'block';
                }
            } else {
                customerDetails.style.display = 'none';
            }
        });
    }

    // --- Form validation & submit handling (safe) ---
    const sampleForm = qs('#sampleRequestForm');
    const submitBtn = qs('#submitBtn');

    if (sampleForm) {
        sampleForm.addEventListener('submit', function(e) {
            const sampleCards = qsa('.sample-card');
            if (sampleCards.length === 0) {
                const checked = qsa('#parametersContainer .parameter-checkbox:checked');
                if (checked.length === 0) {
                    e.preventDefault();
                    alert('Mohon pilih minimal 1 parameter uji');
                    return;
                }
            } else {
                let hasValid = false;
                sampleCards.forEach(card => {
                    const checked = qsa('.parameter-checkbox:checked', card);
                    if (checked.length > 0) hasValid = true;
                });
                if (!hasValid) {
                    e.preventDefault();
                    alert('Mohon pilih minimal 1 parameter uji untuk setiap sampel');
                    return;
                }
            }

            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Membuat Permintaan...';
                submitBtn.disabled = true;
            }
        });
    }

    // Initialize summary if page already has parameters
    updateRequestSummary();
});
</script>
@endpush
