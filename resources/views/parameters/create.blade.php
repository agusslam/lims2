@extends('layouts.app')

@section('title', 'Tambah Parameter')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-plus me-2"></i>Tambah Parameter Baru
                        </h6>
                        <a href="{{ route('parameters.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('parameters.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nama Parameter <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Kode Parameter <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category">Kategori <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                           id="category" name="category" value="{{ old('category') }}" 
                                           list="categoryList" required>
                                    <datalist id="categoryList">
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}">
                                        @endforeach
                                    </datalist>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="unit">Satuan</label>
                                    <input type="text" class="form-control @error('unit') is-invalid @enderror" 
                                           id="unit" name="unit" value="{{ old('unit') }}">
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="data_type">Tipe Data <span class="text-danger">*</span></label>
                                    <select class="form-control @error('data_type') is-invalid @enderror" 
                                            id="data_type" name="data_type" required>
                                        <option value="">Pilih Tipe Data</option>
                                        <option value="numeric" {{ old('data_type') === 'numeric' ? 'selected' : '' }}>Numerik</option>
                                        <option value="text" {{ old('data_type') === 'text' ? 'selected' : '' }}>Teks</option>
                                        <option value="boolean" {{ old('data_type') === 'boolean' ? 'selected' : '' }}>Boolean</option>
                                        <option value="date" {{ old('data_type') === 'date' ? 'selected' : '' }}>Tanggal</option>
                                    </select>
                                    @error('data_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4" id="minValueGroup">
                                <div class="form-group">
                                    <label for="min_value">Nilai Minimum</label>
                                    <input type="number" step="0.0001" class="form-control @error('min_value') is-invalid @enderror" 
                                           id="min_value" name="min_value" value="{{ old('min_value') }}">
                                    @error('min_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4" id="maxValueGroup">
                                <div class="form-group">
                                    <label for="max_value">Nilai Maksimum</label>
                                    <input type="number" step="0.0001" class="form-control @error('max_value') is-invalid @enderror" 
                                           id="max_value" name="max_value" value="{{ old('max_value') }}">
                                    @error('max_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Jenis Sampel Terkait</label>
                            <div class="row">
                                @foreach($sampleTypes as $sampleType)
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" 
                                               id="sampleType{{ $sampleType->id }}" 
                                               name="sample_types[]" value="{{ $sampleType->id }}"
                                               {{ in_array($sampleType->id, old('sample_types', [])) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="sampleType{{ $sampleType->id }}">
                                            {{ $sampleType->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Parameter Aktif
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Simpan Parameter
                            </button>
                            <a href="{{ route('parameters.index') }}" class="btn btn-secondary">
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
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Panduan
                    </h6>
                </div>
                <div class="card-body">
                    <h6>Tips Menambah Parameter:</h6>
                    <ul class="mb-3">
                        <li>Gunakan nama yang jelas dan mudah dipahami</li>
                        <li>Kode parameter harus unik</li>
                        <li>Kelompokkan parameter berdasarkan kategori</li>
                        <li>Tentukan satuan yang sesuai</li>
                    </ul>
                    
                    <h6>Tipe Data:</h6>
                    <ul>
                        <li><strong>Numerik:</strong> Untuk nilai angka</li>
                        <li><strong>Teks:</strong> Untuk nilai teks/deskripsi</li>
                        <li><strong>Boolean:</strong> Untuk nilai Ya/Tidak</li>
                        <li><strong>Tanggal:</strong> Untuk nilai tanggal</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('data_type').addEventListener('change', function() {
    const minGroup = document.getElementById('minValueGroup');
    const maxGroup = document.getElementById('maxValueGroup');
    
    if (this.value === 'numeric') {
        minGroup.style.display = 'block';
        maxGroup.style.display = 'block';
    } else {
        minGroup.style.display = 'none';
        maxGroup.style.display = 'none';
    }
});

// Auto generate code from name
document.getElementById('name').addEventListener('input', function() {
    const code = this.value
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, '_')
        .replace(/_+/g, '_')
        .replace(/^_|_$/g, '');
    document.getElementById('code').value = code;
});
</script>
@endpush
            const categoryCode = category.substring(0, 3).toUpperCase();
            const nameCode = name.split(' ').map(word => word.charAt(0)).join('').toUpperCase();
            const randomNum = Math.floor(Math.random() * 999) + 1;
            
            codeInput.value = `${categoryCode}-${nameCode}-${randomNum.toString().padStart(3, '0')}`;
        }
    }
    
    nameInput.addEventListener('blur', generateCode);
    categoryInput.addEventListener('blur', generateCode);
});

// Format price input
document.querySelector('input[name="price"]').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    this.value = value;
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.querySelector('input[name="name"]').value.trim();
    const code = document.querySelector('input[name="code"]').value.trim();
    const category = document.querySelector('input[name="category"]').value.trim();
    const price = document.querySelector('input[name="price"]').value;
    
    if (!name || !code || !category || !price) {
        e.preventDefault();
        alert('Mohon lengkapi semua field yang wajib diisi (*)');
        return;
    }
    
    if (price < 0) {
        e.preventDefault();
        alert('Harga tidak boleh negatif');
        return;
    }
});
</script>
@endpush
