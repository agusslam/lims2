@extends('layouts.app')

@section('title', 'Edit Parameter')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-edit me-2"></i>Edit Parameter: {{ $parameter->name }}
                        </h6>
                        <div>
                            <a href="{{ route('parameters.show', $parameter->id) }}" class="btn btn-outline-info btn-sm me-2">
                                <i class="fas fa-eye me-1"></i>Lihat
                            </a>
                            <a href="{{ route('parameters.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('parameters.update', $parameter->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nama Parameter <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $parameter->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">Kode Parameter <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $parameter->code) }}" required>
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
                                           id="category" name="category" value="{{ old('category', $parameter->category) }}" 
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
                                           id="unit" name="unit" value="{{ old('unit', $parameter->unit) }}">
                                    @error('unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $parameter->description) }}</textarea>
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
                                        <option value="numeric" {{ old('data_type', $parameter->data_type) === 'numeric' ? 'selected' : '' }}>Numerik</option>
                                        <option value="text" {{ old('data_type', $parameter->data_type) === 'text' ? 'selected' : '' }}>Teks</option>
                                        <option value="boolean" {{ old('data_type', $parameter->data_type) === 'boolean' ? 'selected' : '' }}>Boolean</option>
                                        <option value="date" {{ old('data_type', $parameter->data_type) === 'date' ? 'selected' : '' }}>Tanggal</option>
                                    </select>
                                    @error('data_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4" id="minValueGroup" style="{{ $parameter->data_type === 'numeric' ? '' : 'display:none' }}">
                                <div class="form-group">
                                    <label for="min_value">Nilai Minimum</label>
                                    <input type="number" step="0.0001" class="form-control @error('min_value') is-invalid @enderror" 
                                           id="min_value" name="min_value" value="{{ old('min_value', $parameter->min_value) }}">
                                    @error('min_value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4" id="maxValueGroup" style="{{ $parameter->data_type === 'numeric' ? '' : 'display:none' }}">
                                <div class="form-group">
                                    <label for="max_value">Nilai Maksimum</label>
                                    <input type="number" step="0.0001" class="form-control @error('max_value') is-invalid @enderror" 
                                           id="max_value" name="max_value" value="{{ old('max_value', $parameter->max_value) }}">
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
                                               {{ in_array($sampleType->id, old('sample_types', $parameter->sampleTypes->pluck('id')->toArray())) ? 'checked' : '' }}>
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
                                       {{ old('is_active', $parameter->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Parameter Aktif
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Update Parameter
                            </button>
                            <a href="{{ route('parameters.show', $parameter->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Informasi Parameter
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>Dibuat:</strong></td>
                            <td>{{ $parameter->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Diupdate:</strong></td>
                            <td>{{ $parameter->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{ $parameter->is_active ? 'success' : 'secondary' }}">
                                    {{ $parameter->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Statistik Penggunaan
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $usageCount = \App\Models\SampleTest::where('test_parameter_id', $parameter->id)->count();
                        $completedTests = \App\Models\SampleTest::where('test_parameter_id', $parameter->id)
                            ->where('status', 'completed')->count();
                    @endphp
                    <p class="mb-2">
                        <strong>Total Penggunaan:</strong> {{ $usageCount }} kali
                    </p>
                    <p class="mb-2">
                        <strong>Test Selesai:</strong> {{ $completedTests }} kali
                    </p>
                    <p class="mb-0">
                        <strong>Success Rate:</strong> 
                        {{ $usageCount > 0 ? round(($completedTests / $usageCount) * 100, 1) : 0 }}%
                    </p>
                </div>
            </div>

            @if($parameter->specialist_roles && count($parameter->specialist_roles) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-star me-2"></i>Spesialis Saat Ini
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($parameter->specialist_roles as $role)
                    <span class="badge bg-warning text-dark me-1 mb-1">{{ $role }}</span>
                    @endforeach
                </div>
            </div>
            @endif
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
</script>
@endpush
</script>
@endpush
