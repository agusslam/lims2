@extends('layouts.app')

@section('title', 'Kodifikasi Barang Uji')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tags me-2"></i>Kodifikasi Barang Uji
            </h1>
            <p class="text-muted mb-0">Proses kodifikasi sampel yang telah terdaftar</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Menunggu Kodifikasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $samples->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                Daftar Sampel Terdaftar ({{ $samples->total() }} total)
            </h6>
        </div>
        <div class="card-body">
            @if($samples->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Kode Tracking</th>
                            <th>Kode Sampel</th>
                            <th>Pelanggan</th>
                            <th>Jenis Sampel</th>
                            <th>Parameter</th>
                            <th>Tanggal Registrasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($samples as $sample)
                        <tr>
                            <td>
                                <span class="font-weight-bold">{{ $sample->tracking_code }}</span>
                            </td>
                            <td>
                                <span class="badge badge-primary">{{ $sample->sample_code }}</span>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $sample->customer_name }}</div>
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
                                @if($sample->parameters->first())
                                    <br><small class="text-muted">{{ $sample->parameters->first()->name }}</small>
                                @endif
                            </td>
                            <td>
                                <div>{{ $sample->registered_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $sample->registered_at->format('H:i') }}</small>
                                @if($sample->registeredBy)
                                    <br><small class="text-muted">oleh {{ $sample->registeredBy->name }}</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    {{-- Mengubah rute menjadi samples.codification.show --}}
                                    <a href="{{ route('samples.codification.show', $sample->id) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('samples.print-form', $sample->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Print Form" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
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
                    Menampilkan {{ $samples->firstItem() }} - {{ $samples->lastItem() }} dari {{ $samples->total() }} data
                </div>
                {{ $samples->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-tags fa-3x text-gray-300 mb-3"></i>
                <h5 class="text-gray-600">Tidak ada sampel untuk kodifikasi</h5>
                <p class="text-muted">Sampel yang telah terdaftar akan muncul di sini untuk proses kodifikasi</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto refresh every 60 seconds
setInterval(function() {
    if (!document.hidden) {
        location.reload();
    }
}, 60000);
</script>
@endpush
