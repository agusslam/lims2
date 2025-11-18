@extends('layouts.app')

@section('title', 'Edit Permohonan Sampel')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><i class="fas fa-edit me-2"></i>Edit Permohonan Sampel</h1>
        <a href="{{ route('sample-requests.show', $request->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('sample-requests.update', $request->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card shadow mb-4">
            <div class="card-header">
                <strong>Informasi Pelanggan</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nama Kontak</label>
                    <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $request->contact_person) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Perusahaan</label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $request->company_name) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $request->phone) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $request->email) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" class="form-control" rows="2">{{ old('address', $request->address) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kota</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city', $request->city) }}">
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header">
                <strong>Informasi Sampel</strong>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Jenis Sampel</label>
                    <select name="sample_type_id" class="form-control" required>
                        <option value="">-- Pilih Jenis Sampel --</option>
                        @foreach($sampleTypes as $type)
                            <option value="{{ $type->id }}" {{ (old('sample_type_id', $request->sample_type_id) == $type->id) ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input type="number" min="1" name="quantity" class="form-control" value="{{ old('quantity', $request->quantity) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Kebutuhan Khusus</label>
                    <textarea name="customer_requirements" class="form-control" rows="3">{{ old('customer_requirements', $request->customer_requirements) }}</textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Parameter Uji (pilih minimal 1)</label>
                    <select name="parameters[]" class="form-control" multiple size="6">
                        @php
                            $selected = old('parameters', $request->parameters->pluck('id')->toArray() ?? []);
                        @endphp
                        @foreach($parameters as $param)
                            <option value="{{ $param->id }}" {{ in_array($param->id, $selected) ? 'selected' : '' }}>
                                {{ $param->category }} - {{ $param->name }}{{ $param->unit ? ' (' . $param->unit . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Tekan Ctrl/Cmd + klik untuk memilih lebih dari satu.</small>
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" value="1" id="urgent" name="urgent" {{ old('urgent', $request->urgent) ? 'checked' : '' }}>
                    <label class="form-check-label" for="urgent">Urgent</label>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Simpan Perubahan</button>
            <a href="{{ route('sample-requests.show', $request->id) }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
