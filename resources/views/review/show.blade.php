@extends('layouts.app')

@section('title', 'Review Sampel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Review Sampel: {{ $sample->sample_code }}</h4>
        <p class="text-muted mb-0">
            Status: 
            <span class="badge bg-{{ $sample->status === 'review_tech' ? 'warning' : 'info' }}">
                {{ $sample->status === 'review_tech' ? 'Review Teknis' : 'Review Mutu' }}
            </span>
        </p>
    </div>
    <div>
        <a href="{{ route('review.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

<div class="row">
    <!-- Sample Information -->
    <div class="col-md-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Sampel
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>Kode Sampel:</strong></td>
                        <td>{{ $sample->sample_code }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pelanggan:</strong></td>
                        <td>{{ $sample->sampleRequest->customer->contact_person }}</td>
                    </tr>
                    <tr>
                        <td><strong>Perusahaan:</strong></td>
                        <td>{{ $sample->sampleRequest->customer->company_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Sampel:</strong></td>
                        <td>{{ $sample->sampleType->name ?? $sample->custom_sample_type }}</td>
                    </tr>
                    <tr>
                        <td><strong>Analis:</strong></td>
                        <td>{{ $sample->assignedAnalyst->full_name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Selesai Diuji:</strong></td>
                        <td>{{ $sample->testing_completed_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Workflow History -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Riwayat Workflow
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($sample->workflowHistory->take(5) as $history)
                    <div class="timeline-item">
                        <small class="text-muted">{{ $history->created_at->format('d/m H:i') }}</small><br>
                        <strong>{{ $history->actionBy->full_name ?? 'System' }}</strong><br>
                        <span class="badge bg-primary">{{ ucfirst($history->to_status) }}</span>
                        @if($history->notes)
                        <br><small class="text-muted">{{ $history->notes }}</small>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Test Results Review -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-microscope me-2"></i>
                    Review Hasil Pengujian
                </h6>
            </div>
            <div class="card-body">
                @if($sample->status === 'review_tech')
                <form action="{{ route('review.technical', $sample->id) }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <h6>Review Teknis</h6>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="review_decision" 
                                   id="approve" value="approve" required>
                            <label class="form-check-label text-success" for="approve">
                                <i class="fas fa-check me-1"></i>Setuju
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="review_decision" 
                                   id="reject" value="reject" required>
                            <label class="form-check-label text-danger" for="reject">
                                <i class="fas fa-times me-1"></i>Tolak
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Catatan Review Teknis:</label>
                        <textarea name="review_notes" class="form-control" rows="3"
                                  placeholder="Berikan catatan review teknis..."></textarea>
                    </div>

                    <!-- Individual Test Review -->
                    <h6>Review per Parameter:</h6>
                    @foreach($sample->tests->groupBy('testParameter.category') as $category => $tests)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">{{ $category }}</h6>
                        </div>
                        <div class="card-body">
                            @foreach($tests as $test)
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <strong>{{ $test->testParameter->name }}</strong><br>
                                    <small class="text-muted">{{ $test->testParameter->method }}</small>
                                </div>
                                <div class="col-md-3">
                                    <strong>Hasil:</strong> {{ $test->result_value }} {{ $test->testParameter->unit }}<br>
                                    @if($test->notes)
                                    <small class="text-muted">{{ $test->notes }}</small>
                                    @endif
                                </div>
                                <div class="col-md-3">
                                    @if($test->instrument_files)
                                    <small class="text-info">
                                        <i class="fas fa-paperclip me-1"></i>
                                        {{ count($test->instrument_files) }} file(s)
                                    </small>
                                    @endif
                                </div>
                                <div class="col-md-2">
                                    <input type="hidden" name="test_reviews[{{ $loop->index }}][test_id]" value="{{ $test->id }}">
                                    <select name="test_reviews[{{ $loop->index }}][status]" class="form-select form-select-sm" required>
                                        <option value="approve">✓ Setuju</option>
                                        <option value="reject">✗ Tolak</option>
                                    </select>
                                    <textarea name="test_reviews[{{ $loop->index }}][notes]" 
                                              class="form-control form-control-sm mt-1" rows="1" 
                                              placeholder="Catatan..."></textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Review Teknis
                        </button>
                    </div>
                </form>

                @elseif($sample->status === 'review_quality')
                <form action="{{ route('review.quality', $sample->id) }}" method="POST">
                    @csrf
                    
                    @if($sample->tech_review_notes)
                    <div class="alert alert-info">
                        <strong>Catatan Review Teknis:</strong><br>
                        {{ $sample->tech_review_notes }}
                    </div>
                    @endif

                    <div class="mb-4">
                        <h6>Review Mutu</h6>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="review_decision" 
                                   id="approve_quality" value="approve" required>
                            <label class="form-check-label text-success" for="approve_quality">
                                <i class="fas fa-check me-1"></i>Setuju
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="review_decision" 
                                   id="reject_quality" value="reject" required>
                            <label class="form-check-label text-danger" for="reject_quality">
                                <i class="fas fa-times me-1"></i>Tolak
                            </label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Catatan Review Mutu:</label>
                        <textarea name="review_notes" class="form-control" rows="3"
                                  placeholder="Berikan catatan review mutu..."></textarea>
                    </div>

                    <div class="mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="certificate_required" 
                                   value="1" id="certificate_required">
                            <label class="form-check-label" for="certificate_required">
                                Memerlukan penerbitan sertifikat
                            </label>
                        </div>
                    </div>

                    <!-- Quality Review per Parameter -->
                    <h6>Review Mutu per Parameter:</h6>
                    @foreach($sample->tests->groupBy('testParameter.category') as $category => $tests)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0">{{ $category }}</h6>
                        </div>
                        <div class="card-body">
                            @foreach($tests as $test)
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <strong>{{ $test->testParameter->name }}</strong><br>
                                    <small class="text-muted">{{ $test->testParameter->method }}</small>
                                </div>
                                <div class="col-md-4">
                                    <strong>Hasil:</strong> {{ $test->result_value }} {{ $test->testParameter->unit }}<br>
                                    @if($test->tech_review_notes)
                                    <small class="text-info">Review Teknis: {{ $test->tech_review_notes }}</small>
                                    @endif
                                </div>
                                <div class="col-md-4">
                                    <input type="hidden" name="test_reviews[{{ $loop->index }}][test_id]" value="{{ $test->id }}">
                                    <select name="test_reviews[{{ $loop->index }}][status]" class="form-select form-select-sm" required>
                                        <option value="approve">✓ Setuju</option>
                                        <option value="reject">✗ Tolak</option>
                                    </select>
                                    <textarea name="test_reviews[{{ $loop->index }}][notes]" 
                                              class="form-control form-control-sm mt-1" rows="1" 
                                              placeholder="Catatan mutu..."></textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-check me-2"></i>Simpan Review Mutu
                        </button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 20px;
}

.timeline-item {
    position: relative;
    padding-bottom: 15px;
    margin-bottom: 15px;
    border-left: 2px solid #dee2e6;
    padding-left: 15px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -6px;
    top: 0;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #007bff;
}
</style>
@endpush

@push('scripts')
<script>
// Auto-select all tests when main decision changes
document.addEventListener('DOMContentLoaded', function() {
    const mainDecisionRadios = document.querySelectorAll('input[name="review_decision"]');
    const testSelects = document.querySelectorAll('select[name*="[status]"]');
    
    mainDecisionRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const decision = this.value;
            testSelects.forEach(select => {
                select.value = decision;
            });
        });
    });
    
    // Warn if rejecting - require notes
    document.querySelector('form').addEventListener('submit', function(e) {
        const rejectRadio = document.querySelector('input[value="reject"]:checked');
        const reviewNotes = document.querySelector('textarea[name="review_notes"]');
        
        if (rejectRadio && (!reviewNotes.value || reviewNotes.value.trim().length < 10)) {
            e.preventDefault();
            alert('Catatan review harus diisi minimal 10 karakter untuk penolakan');
            reviewNotes.focus();
        }
    });
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey) {
        switch(e.key) {
            case 's': // Ctrl+S - Save
                e.preventDefault();
                document.querySelector('form').submit();
                break;
            case '1': // Ctrl+1 - Approve
                e.preventDefault();
                const approveRadio = document.querySelector('input[value="approve"]');
                if (approveRadio) approveRadio.click();
                break;
            case '2': // Ctrl+2 - Reject
                e.preventDefault();
                const rejectRadio = document.querySelector('input[value="reject"]');
                if (rejectRadio) rejectRadio.click();
                break;
        }
    }
});
</script>
@endpush
