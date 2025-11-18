@extends('layouts.app')

@section('title', 'Manajemen Parameter')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2"></i>Manajemen Parameter
            </h1>
            <p class="text-muted mb-0">Kelola parameter uji dan jenis sampel untuk membantu pelanggan</p>
        </div>
        <div>
            <a href="{{ route('parameters.create') }}" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i>Tambah Parameter
            </a>
            <a href="{{ route('parameters.sample-types') }}" class="btn btn-outline-primary">
                <i class="fas fa-flask me-1"></i>Kelola Jenis Sampel
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Parameter</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $parameters->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jenis Sampel</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sampleTypes->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flask fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Kategori</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $parametersByCategory->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Parameters Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Parameter ({{ $parameters->total() }} total)
            </h6>
            <div>
                <input type="text" class="form-control form-control-sm" placeholder="Cari parameter..." id="searchParameter">
            </div>
        </div>
        <div class="card-body">
            @if($parameters->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nama Parameter</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Satuan</th>
                            <th>Tipe Data</th>
                            <th>Jenis Sampel</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parameters as $parameter)
                        <tr>
                            <td>
                                <div class="font-weight-bold">{{ $parameter->name }}</div>
                                @if($parameter->description)
                                    <small class="text-muted">{{ Str::limit($parameter->description, 50) }}</small>
                                @endif
                            </td>
                            <td><code>{{ $parameter->code }}</code></td>
                            <td>
                                <span class="badge badge-secondary">{{ $parameter->category }}</span>
                            </td>
                            <td>{{ $parameter->unit ?? '-' }}</td>
                            <td>
                                <span class="badge badge-info">{{ ucfirst($parameter->data_type) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $parameter->sampleTypes->count() }} jenis</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $parameter->is_active ? 'success' : 'secondary' }}">
                                    {{ $parameter->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('parameters.show', $parameter->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('parameters.edit', $parameter->id) }}" 
                                       class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteParameter({{ $parameter->id }})" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Menampilkan {{ $parameters->firstItem() }} - {{ $parameters->lastItem() }} dari {{ $parameters->total() }} data
                </div>
                {{ $parameters->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-cogs fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Belum ada parameter</h5>
                <p class="text-muted">Tambahkan parameter untuk membantu pelanggan memilih jenis pengujian</p>
                <a href="{{ route('parameters.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Tambah Parameter Pertama
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus parameter ini?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteParameter(id) {
    const form = document.getElementById('deleteForm');
    form.action = `/parameters/${id}`;
    $('#deleteModal').modal('show');
}

// Search functionality
document.getElementById('searchParameter').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endpush