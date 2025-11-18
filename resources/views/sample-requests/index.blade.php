@extends('layouts.app')

@section('title', 'Daftar Sampel Baru')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-list-alt me-2"></i>Daftar Permohonan Sampel
            </h1>
            <p class="text-muted mb-0">Kelola dan pantau semua permohonan pengujian sampel</p>
        </div>
        <div>
            <a href="{{ route('sample-requests.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Tambah Permohonan
            </a>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Menunggu</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['pending'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Terdaftar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['registered'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Dalam Pengujian</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['testing'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flask fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Selesai</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $statusCounts['completed'] ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sample Requests Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Daftar Permohonan ({{ isset($requests) ? $requests->total() : (isset($samples) ? $samples->total() : 0) }} total)
            </h6>
        </div>
        <div class="card-body">
            @php
                $items = $requests ?? $samples ?? collect();
            @endphp
            
            @if($items->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode Tracking</th>
                            <th>Pelanggan</th>
                            <th>Sampel</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>
                                <span class="font-weight-bold">{{ $item->tracking_code }}</span>
                                @if(isset($item->urgent) && $item->urgent)
                                    <span class="badge badge-danger ml-1">Urgent</span>
                                @endif
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $item->contact_person ?? $item->customer_name }}</div>
                                @if($item->company_name)
                                    <small class="text-muted">{{ $item->company_name }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $item->parameters->count() ?? 0 }} parameter</span>
                                @if($item->sampleType)
                                    <br><small class="text-muted">{{ $item->sampleType->name }}</small>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusConfig = config("lims.sample_status.{$item->status}", [
                                        'name' => ucfirst($item->status),
                                        'color' => 'secondary'
                                    ]);
                                @endphp
                                <span class="badge badge-{{ $statusConfig['color'] }}">
                                    {{ $statusConfig['name'] }}
                                </span>
                            </td>
                            <td>
                                <div>{{ ($item->submitted_at ?? $item->created_at)->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ ($item->submitted_at ?? $item->created_at)->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('sample-requests.show', $item->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(in_array(Auth::user()->role, ['SUPERVISOR', 'ADMIN', 'DEVEL']))
                                        @if($item->status === 'pending')
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="updateStatus({{ $item->id }}, 'registered')" 
                                                title="Registrasi">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        @endif
                                        
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="{{ route('sample-requests.edit', $item->id) }}">
                                                    <i class="fas fa-edit me-2"></i>Edit
                                                </a>
                                            </div>
                                        </div>
                                    @endif
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
                    Menampilkan {{ $items->firstItem() ?? 1 }} - {{ $items->lastItem() ?? $items->count() }} dari {{ $items->total() ?? $items->count() }} data
                </div>
                @if(method_exists($items, 'links'))
                    {{ $items->links() }}
                @endif
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Belum ada permohonan sampel</h5>
                <p class="text-muted">Permohonan yang masuk akan ditampilkan di sini</p>
                <a href="{{ route('sample-requests.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Tambah Permohonan Pertama
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="statusForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="status">Status Baru</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="">Pilih Status</option>
                            <option value="pending">Menunggu</option>
                            <option value="registered">Terdaftar</option>
                            <option value="assigned">Ditugaskan</option>
                            <option value="testing">Dalam Pengujian</option>
                            <option value="review">Review</option>
                            <option value="completed">Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Catatan (Opsional)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Tambahkan catatan untuk perubahan status ini"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="submitStatusForm()">Update Status</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function updateStatus(requestId, status) {
    const form = document.getElementById('statusForm');
    form.action = `/sample-requests/${requestId}/status`;
    document.getElementById('status').value = status;
    $('#statusModal').modal('show');
}

function submitStatusForm() {
    document.getElementById('statusForm').submit();
}

// Auto refresh every 30 seconds
setInterval(function() {
    if (!document.hidden) {
        location.reload();
    }
}, 30000);

// Auto-refresh every 2 minutes for pending requests
setInterval(function() {
    if (document.hidden) return;
    
    const hasPendingRequests = document.querySelector('.border-warning');
    if (hasPendingRequests) {
        location.reload();
    }
}, 120000);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey) {
        switch(e.key) {
            case 'n': // Ctrl+N - New request
                e.preventDefault();
                location.href = '{{ route("sample-requests.create") }}';
                break;
            case 'r': // Ctrl+R - Refresh
                e.preventDefault();
                location.reload();
                break;
        }
    }
});
</script>
@endpush
