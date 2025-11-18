@extends('layouts.app')

@section('title', 'Kelola Jenis Sampel')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-flask me-2"></i>Kelola Jenis Sampel
            </h1>
            <p class="text-muted mb-0">Daftar jenis sampel yang dapat diajukan pelanggan</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addSampleTypeModal">
                <i class="fas fa-plus me-1"></i>Tambah Jenis Sampel
            </button>
            <a href="{{ route('parameters.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Jenis Sampel ({{ $sampleTypes->total() }} total)
            </h6>
        </div>
        <div class="card-body">
            @if($sampleTypes->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Jenis Sampel</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Parameter Terkait</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sampleTypes as $sampleType)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $sampleType->name }}</div>
                                @if($sampleType->description)
                                    <small class="text-muted">{{ Str::limit($sampleType->description, 80) }}</small>
                                @endif
                            </td>
                            <td><code>{{ $sampleType->code }}</code></td>
                            <td>
                                @if($sampleType->category)
                                    <span class="badge badge-secondary">{{ $sampleType->category }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $sampleType->parameters_count }} parameter</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $sampleType->is_active ? 'success' : 'secondary' }}">
                                    {{ $sampleType->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                            onclick="editSampleType({{ $sampleType->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteSampleType({{ $sampleType->id }})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{ $sampleTypes->links() }}
            @else
            <div class="text-center py-5">
                <i class="fas fa-flask fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Belum ada jenis sampel</h5>
                <p class="text-muted">Tambahkan jenis sampel untuk membantu pelanggan memilih</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Sample Type Modal -->
<div class="modal fade" id="addSampleTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Jenis Sampel</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('parameters.sample-types.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nama Jenis Sampel <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Kode <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="category">Kategori</label>
                        <input type="text" class="form-control" id="category" name="category">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">
                                Jenis Sampel Aktif
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto generate code from name
document.getElementById('name').addEventListener('input', function() {
    const code = this.value
        .toUpperCase()
        .replace(/[^A-Z0-9]/g, '_')
        .replace(/_+/g, '_')
        .replace(/^_|_$/g, '');
    document.getElementById('code').value = code;
});

function editSampleType(id) {
    // Implementation for edit functionality
    alert('Edit functionality will be implemented');
}

function deleteSampleType(id) {
    if (confirm('Apakah Anda yakin ingin menghapus jenis sampel ini?')) {
        // Implementation for delete functionality
        alert('Delete functionality will be implemented');
    }
}
</script>
@endpush
