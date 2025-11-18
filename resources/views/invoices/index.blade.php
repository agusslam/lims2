@extends('layouts.app')

@section('title', 'Invoice & Tagihan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Invoice & Tagihan</h4>
        <p class="text-muted mb-0">Kelola invoice dan pembayaran pelanggan</p>
    </div>
    <div>
        <a href="{{ route('invoices.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Invoice Baru
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-warning">Pending Payment</h6>
                        <h3 class="mb-0">{{ $stats['pending_payment'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Menunggu pembayaran</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">Paid This Month</h6>
                        <h3 class="mb-0">{{ $stats['paid_this_month'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Terbayar bulan ini</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-danger">Overdue</h6>
                        <h3 class="mb-0">{{ $stats['overdue_invoices'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                    </div>
                </div>
                <small class="text-muted">Invoice terlambat</small>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-primary">Total Invoices</h6>
                        <h3 class="mb-0">{{ $stats['total_invoices'] }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-invoice fa-2x text-primary"></i>
                    </div>
                </div>
                <small class="text-muted">Semua invoice</small>
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
                       placeholder="Cari nomor invoice, pelanggan..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>Terkirim</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Terbayar</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Terlambat</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fas fa-search"></i> Filter
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Invoices List -->
<div class="row">
    @forelse($invoices as $invoice)
    <div class="col-md-6 mb-4">
        <div class="card h-100 border-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }}">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fas fa-file-invoice me-2"></i>
                    {{ $invoice->invoice_number ?? 'Draft' }}
                </h6>
                <span class="badge bg-{{ $invoice->status === 'paid' ? 'success' : ($invoice->status === 'overdue' ? 'danger' : 'warning') }}">
                    {{ ucfirst($invoice->status) }}
                </span>
            </div>
            
            <div class="card-body">
                <div class="mb-3">
                    <strong>Pelanggan:</strong> {{ $invoice->sampleRequest->customer->contact_person }}<br>
                    <strong>Perusahaan:</strong> {{ $invoice->sampleRequest->customer->company_name }}<br>
                    <strong>Total:</strong> <span class="text-success fw-bold">Rp {{ number_format($invoice->total_amount) }}</span>
                </div>

                @if($invoice->due_date)
                <div class="mb-3">
                    <small class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        Jatuh tempo: {{ $invoice->due_date->format('d/m/Y') }}
                    </small>
                </div>
                @endif

                @if($invoice->paid_at)
                <div class="mb-3">
                    <small class="text-success">
                        <i class="fas fa-check-circle me-1"></i>
                        Dibayar: {{ $invoice->paid_at->format('d/m/Y H:i') }}
                    </small>
                </div>
                @endif
            </div>
            
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <a href="{{ route('invoices.show', $invoice->id) }}" 
                       class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>Lihat Detail
                    </a>
                    
                    @if($invoice->status === 'draft')
                    <button class="btn btn-success btn-sm" 
                            onclick="sendInvoice({{ $invoice->id }})">
                        <i class="fas fa-paper-plane me-1"></i>Kirim
                    </button>
                    @elseif($invoice->status === 'sent')
                    <button class="btn btn-primary btn-sm" 
                            onclick="markAsPaid({{ $invoice->id }})">
                        <i class="fas fa-check me-1"></i>Tandai Lunas
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-invoice fa-5x text-muted mb-3"></i>
                <h5>Tidak Ada Invoice</h5>
                <p class="text-muted mb-3">Belum ada invoice yang dibuat.</p>
                <a href="{{ route('invoices.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Buat Invoice Pertama
                </a>
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($invoices->hasPages())
<div class="d-flex justify-content-center">
    {{ $invoices->appends(request()->query())->links() }}
</div>
@endif
@endsection

@push('scripts')
<script>
function sendInvoice(id) {
    if (confirm('Kirim invoice ini ke pelanggan?')) {
        // Implementation for sending invoice
        alert('Invoice berhasil dikirim');
    }
}

function markAsPaid(id) {
    if (confirm('Tandai invoice ini sebagai lunas?')) {
        // Implementation for marking as paid
        alert('Invoice ditandai sebagai lunas');
    }
}
</script>
@endpush