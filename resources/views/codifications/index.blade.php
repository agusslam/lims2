@extends('layouts.app')

@section('title', 'Kodifikasi Barang Uji')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Kodifikasi Barang Uji</h4>
        <p class="text-muted mb-0">Verifikasi dan kodifikasi sampel yang telah terdaftar</p>
    </div>
    <div>
        <span class="badge bg-info fs-6 px-3 py-2">
            <i class="fas fa-qrcode me-2"></i>
            {{ $samples->total() }} Sampel Pending
        </span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-warning">Menunggu Kodifikasi</h6>
                        <h3 class="mb-0">{{ $samples->total() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x text-warning"></i>
                    </div>
                </div>
                <small class="text-muted">Sampel siap dikodifikasi</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-success">Selesai Hari Ini</h6>
                        <h3 class="mb-0">{{ \App\Models\Sample::where('status', 'codified')->whereDate('updated_at', today())->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                </div>
                <small class="text-muted">Kodifikasi hari ini</small>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-danger">Ditolak</h6>
                        <h3 class="mb-0">{{ \App\Models\Sample::where('status', 'pending')->whereDate('updated_at', today())->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                </div>
                <small class="text-muted">Perlu perbaikan</small>
            </div>
        </div>
    </div>
</div>

<!-- Samples for Codification -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-list me-2"></i>
            Daftar Sampel untuk Kodifikasi
        </h5>
    </div>
    <div class="card-body">
        @if($samples->count() > 0)
            @foreach($samples as $sample)
            <div class="card mb-3 border-left-warning">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="card-title mb-2">
                                <i class="fas fa-vial text-primary me-2"></i>
                                {{ $sample->sample_code ?? 'Belum dikodifikasi' }}
                                <span class="badge bg-warning ms-2">{{ $sample->status }}</span>
                            </h6>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Pelanggan:</strong>
{{ optional(optional($sample->sampleRequest)->customer)->contact_person
   ?? $sample->customer_name
   ?? '— Tidak ada data pelanggan —' }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Tracking:</strong> {{ $sample->sampleRequest->tracking_code }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Jenis Sampel:</strong> {{ $sample->sampleType->name ?? $sample->custom_sample_type }}
                                    </p>
                                    <p class="mb-1">
                                        <strong>Quantity:</strong> {{ $sample->quantity }} sampel
                                    </p>
                                    <p class="mb-1">
                                        <strong>Parameter:</strong> {{ optional($sample->tests)->count() ?? 0 }} parameter
                                    </p>
                                </div>
                            </div>

                            @if($sample->description)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Deskripsi:</strong> {{ $sample->description }}
                            </div>
                            @endif

                            <!-- Test Parameters -->
                            <!-- <div class="mb-3">
                                <h6>Parameter Uji:</h6>
                                <div class="row">
                                    @foreach($sample->tests->groupBy('testParameter.category') as $category => $tests)
                                    <div class="col-md-6 mb-2">
                                        <strong>{{ $category }}:</strong>
                                        <ul class="mb-0 ms-3">
                                            @foreach($tests as $test)
                                            <li>{{ $test->testParameter->name }} ({{ $test->testParameter->unit }})</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    @endforeach
                                </div>
                            </div> -->
                        </div>
                        
                        <div class="col-md-4">
                            <form action="{{ route('samples.codify', $sample->id) }}" method="POST">
    @csrf

    <div class="mb-3">
        <label class="form-label">Catatan Verifikasi</label>
        <textarea class="form-control" name="codification_notes"
                  rows="3" placeholder="Catatan verifikasi (opsional)"></textarea>
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input class="form-check-input" type="radio" name="action"
                   value="approve" id="approve_{{ $sample->id }}" required>
            <label class="form-check-label text-success" for="approve_{{ $sample->id }}">
                <i class="fas fa-check me-1"></i>
                Memenuhi Persyaratan
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="action"
                   value="reject" id="reject_{{ $sample->id }}" required>
            <label class="form-check-label text-danger" for="reject_{{ $sample->id }}">
                <i class="fas fa-times me-1"></i>
                Tidak Memenuhi Persyaratan
            </label>
        </div>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-qrcode me-2"></i>
            Proses Kodifikasi
        </button>
        <a href="{{ route('samples.codification.report', $sample->id) }}"
           class="btn btn-outline-secondary" target="_blank">
            <i class="fas fa-print me-2"></i>
            Cetak Laporan
        </a>
    </div>
</form>

                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $samples->links() }}
            </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-check-circle fa-5x text-success mb-3"></i>
            <h5>Semua Sampel Sudah Dikodifikasi</h5>
            <p class="text-muted">Tidak ada sampel yang menunggu kodifikasi saat ini.</p>
            <a href="{{ route('sample-requests.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali ke Daftar Sampel
            </a>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Auto-refresh every 3 minutes for new samples
setInterval(function() {
    if (document.hidden) return;
    location.reload();
}, 180000);

// Form validation — updated to match current form field names:
// - radio name="action" with values "approve" or "reject"
// - textarea name="codification_notes"
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        try {
            // Only validate forms that post to codify route to avoid interfering other forms
            const actionUrl = form.getAttribute('action') || '';
            if (!actionUrl.includes('/codify') && !actionUrl.includes('/process')) {
                return; // skip validation for unrelated forms
            }

            const actionRadio = form.querySelector('input[name="action"]:checked');
            const notes = form.querySelector('textarea[name="codification_notes"]');

            if (!actionRadio) {
                e.preventDefault();
                alert('Silakan pilih apakah sampel memenuhi persyaratan atau tidak');
                return false;
            }

            // if reject and no notes, warn user
            if (actionRadio.value === 'reject' && (!notes || !notes.value.trim())) {
                if (!confirm('Anda menolak sampel tanpa catatan. Lanjutkan?')) {
                    e.preventDefault();
                    return false;
                }
            }

            // final confirmation
            if (!confirm('Apakah Anda yakin dengan keputusan kodifikasi ini?')) {
                e.preventDefault();
                return false;
            }

            // allow submit
            return true;
        } catch (err) {
            // if any runtime JS error happens, do not block the form — allow submission
            console.error('Validation script error:', err);
            return true;
        }
    });
});
</script>
@endpush
