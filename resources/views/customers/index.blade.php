@extends('layouts.app')

@section('title', 'Manajemen Pelanggan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Manajemen Pelanggan</h4>
        <p class="text-muted mb-0">Kelola data pelanggan dan verifikasi</p>
    </div>
    <div>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Pelanggan Baru
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">Terverifikasi</h6>
                        <h3 class="mb-0">{{ \App\Models\Customer::where('is_verified', true)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Dapat membuat permintaan</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-warning">Pending</h6>
                        <h3 class="mb-0">{{ \App\Models\Customer::where('is_verified', false)->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-clock fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Menunggu verifikasi</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-primary">Aktif Bulan Ini</h6>
                        <h3 class="mb-0">{{ \App\Models\SampleRequest::whereMonth('created_at', now()->month)->distinct('customer_id')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Pelanggan dengan permintaan</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-info">Total Permintaan</h6>
                        <h3 class="mb-0">{{ \App\Models\SampleRequest::count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clipboard-list fa-2x text-info"></i>
                    </div>
                </div>
                <small class="text-muted">Semua permintaan sampel</small>
            </div>
        </div>
    </div>
</div>

<!-- Filter and Search -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" 
                       placeholder="Cari nama, perusahaan, email..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="city" 
                       placeholder="Kota" value="{{ request('city') }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fas fa-search"></i> Filter
                </button>
                <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-refresh"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Customers List -->
<div class="row">
    @php
        $customers = \App\Models\Customer::with('sampleRequests')->orderBy('created_at', 'desc')->paginate(12);
    @endphp
    
    @forelse($customers as $customer)
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-{{ $customer->is_verified ? 'success' : 'warning' }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-user me-2"></i>
                    {{ $customer->contact_person }}
                </h6>
                <span class="badge bg-{{ $customer->is_verified ? 'success' : 'warning' }}">
                    {{ $customer->is_verified ? 'Verified' : 'Pending' }}
                </span>
            </div>
            
            <div class="card-body">
                <div class="mb-3">
                    <strong>Perusahaan:</strong> {{ $customer->company_name ?? '-' }}<br>
                    <strong>Email:</strong> {{ $customer->email }}<br>
                    <strong>WhatsApp:</strong> {{ $customer->whatsapp_number }}<br>
                    <strong>Kota:</strong> {{ $customer->city }}
                </div>

                <div class="mb-3">
                    <small class="text-muted">
                        <strong>Alamat:</strong><br>
                        {{ Str::limit($customer->address, 100) }}
                    </small>
                </div>

                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Terdaftar: {{ $customer->created_at->diffForHumans() }}
                    </small>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <small><strong>Total Permintaan:</strong></small>
                        <span class="badge bg-primary">{{ $customer->sampleRequests->count() }}</span>
                    </div>
                </div>
            </div>
            
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <a href="{{ route('customers.show', $customer->id) }}" 
                       class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>Lihat Detail
                    </a>
                    
                    @if(!$customer->is_verified)
                    <button class="btn btn-success btn-sm" 
                            onclick="verifyCustomer({{ $customer->id }})">
                        <i class="fas fa-check me-1"></i>Verifikasi
                    </button>
                    @else
                    <div class="btn-group">
                        <a href="{{ route('customers.edit', $customer->id) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        <a href="{{ route('sample-requests.create', ['customer_id' => $customer->id]) }}" 
                           class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus me-1"></i>Buat Permintaan
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-5x text-muted mb-3"></i>
                <h5>Tidak Ada Pelanggan</h5>
                <p class="text-muted mb-3">Belum ada pelanggan yang terdaftar atau sesuai filter.</p>
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Pelanggan Pertama
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($customers->hasPages())
<div class="d-flex justify-content-center">
    {{ $customers->appends(request()->query())->links() }}
</div>
@endif
@endsection

@push('scripts')
<script>
function verifyCustomer(id) {
    if (confirm('Apakah Anda yakin ingin memverifikasi pelanggan ini?')) {
        fetch(`/customers/${id}/verify`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Gagal verifikasi pelanggan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey) {
        switch(e.key) {
            case 'n': // Ctrl+N - New customer
                e.preventDefault();
                location.href = '{{ route("customers.create") }}';
                break;
        }
    }
});
</script>
@endpush
