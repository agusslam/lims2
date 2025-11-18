@extends('layouts.app')

@section('title', 'Detail Parameter')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-info-circle me-2"></i>Detail Parameter
            </h1>
            <p class="text-muted mb-0">{{ $parameter->name }}</p>
        </div>
        <div>
            <a href="{{ route('parameters.edit', $parameter->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
            <a href="{{ route('parameters.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Parameter</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Nama Parameter:</div>
                        <div class="col-sm-9">{{ $parameter->name }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Kode:</div>
                        <div class="col-sm-9"><code>{{ $parameter->code }}</code></div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Kategori:</div>
                        <div class="col-sm-9">
                            <span class="badge badge-secondary">{{ $parameter->category }}</span>
                        </div>
                    </div>
                    
                    @if($parameter->unit)
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Satuan:</div>
                        <div class="col-sm-9">{{ $parameter->unit }}</div>
                    </div>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Tipe Data:</div>
                        <div class="col-sm-9">
                            <span class="badge badge-info">{{ ucfirst($parameter->data_type) }}</span>
                        </div>
                    </div>
                    
                    @if($parameter->data_type === 'numeric')
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Range Nilai:</div>
                        <div class="col-sm-9">
                            @if($parameter->min_value || $parameter->max_value)
                                {{ $parameter->min_value ?? 'Min tidak terbatas' }} - {{ $parameter->max_value ?? 'Max tidak terbatas' }}
                            @else
                                <span class="text-muted">Tidak dibatasi</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    @if($parameter->description)
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Deskripsi:</div>
                        <div class="col-sm-9">{{ $parameter->description }}</div>
                    </div>
                    @endif
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Status:</div>
                        <div class="col-sm-9">
                            <span class="badge badge-{{ $parameter->is_active ? 'success' : 'secondary' }}">
                                {{ $parameter->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Dibuat:</div>
                        <div class="col-sm-9">{{ $parameter->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Diperbarui:</div>
                        <div class="col-sm-9">{{ $parameter->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jenis Sampel Terkait</h6>
                </div>
                <div class="card-body">
                    @if($parameter->sampleTypes->count() > 0)
                        @foreach($parameter->sampleTypes as $sampleType)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ $sampleType->name }}</span>
                            <span class="badge badge-{{ $sampleType->is_active ? 'success' : 'secondary' }}">
                                {{ $sampleType->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">Belum dikaitkan dengan jenis sampel</p>
                    @endif
                </div>
            </div>
            
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('parameters.edit', $parameter->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit Parameter
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteParameter()">
                            <i class="fas fa-trash me-1"></i>Hapus Parameter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal -->
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
                <p>Apakah Anda yakin ingin menghapus parameter <strong>{{ $parameter->name }}</strong>?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan dan akan menghapus semua relasi parameter ini.</small></p>
            </div>
            <div class="modal-footer">
                <form method="POST" action="{{ route('parameters.destroy', $parameter->id) }}">
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
function deleteParameter() {
    $('#deleteModal').modal('show');
}
</script>
@endpush