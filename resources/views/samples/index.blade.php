@extends('layouts.app')

@section('title', 'Daftar Sampel Baru')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-list-alt me-2"></i>Daftar Sampel Baru
            </h1>
            <p class="text-muted mb-0">Validasi dan persetujuan permohonan sampel</p>
        </div>
        @if(Auth::user()->hasPermission(1))
        <div>
            <a href="{{ route('sample-requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Tambah Permohonan
            </a>
        </div>
        @endif
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu Validasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPending }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sudah Terdaftar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalRegistered }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Samples Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Sampel Pending ({{ $samples->total() }} total)
            </h6>
        </div>
        <div class="card-body">
            @if($samples->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode Tracking</th>
                            <th>Pelanggan</th>
                            <th>Jenis Sampel</th>
                            <th>Parameter</th>
                            <th>Tanggal Masuk</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($samples as $sample)
                        <tr>
                            <td>
                                <span class="font-weight-bold">{{ $sample->tracking_code }}</span>
                                @if($sample->urgent)
                                    <span class="badge badge-danger ml-1">Urgent</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $sample->contact_person }}</div>
                                @if($sample->company_name)
                                    <small class="text-muted">{{ $sample->company_name }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $sample->sampleType->name ?? 'Tidak diketahui' }}
                                <br><small class="text-muted">Qty: {{ $sample->quantity }}</small>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $sample->parameters->count() }} parameter</span>
                            </td>
                            <td>
                                <div>{{ $sample->submitted_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $sample->submitted_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('samples.show', $sample->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(Auth::user()->hasAnyRole(['SUPERVISOR', 'ADMIN', 'DEVEL']))
                                        <a href="{{ route('samples.edit', $sample->id) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="approveModal({{ $sample->id }})" title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="rejectModal({{ $sample->id }})" title="Tolak">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $samples->links() }}
            @else
            <div class="text-center py-5">
                <i class="fas fa-check-circle fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Tidak ada sampel pending</h5>
                <p class="text-muted">Semua permohonan sampel sudah diproses</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function approveModal(id) {
    if (confirm('Apakah Anda yakin ingin menyetujui sampel ini?')) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/samples/${id}/approve`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        form.appendChild(csrf);
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectModal(id) {
    const reason = prompt('Masukkan alasan penolakan:');
    if (reason && reason.trim() !== '') {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/samples/${id}/reject`;
        
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        
        const reasonInput = document.createElement('input');
        reasonInput.type = 'hidden';
        reasonInput.name = 'rejection_reason';
        reasonInput.value = reason;
        
        form.appendChild(csrf);
        form.appendChild(reasonInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
