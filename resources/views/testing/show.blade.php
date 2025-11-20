@extends('layouts.app')

@section('title', 'Pengujian Sampel')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Pengujian Sampel: {{ $sample->sample_code }}</h4>
        <p class="text-muted mb-0">
            Status: 
            <span class="badge bg-{{ $sample->status === 'assigned' ? 'warning' : 'primary' }}">
                {{ $sample->status === 'assigned' ? 'Ditugaskan' : 'Sedang Diuji' }}
            </span>
        </p>
    </div>
    <div>
        @if($sample->status === 'assigned')
        <button class="btn btn-warning me-2" onclick="startTesting()">
            <i class="fas fa-play me-2"></i>Mulai Pengujian
        </button>
        @endif
        <a href="{{ route('testing.data-form', $sample->id) }}" class="btn btn-outline-secondary me-2" target="_blank">
            <i class="fas fa-print me-2"></i>Form Data
        </a>
        <a href="{{ route('testing.index') }}" class="btn btn-outline-secondary">
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
                <table class="table table-sm table-borderless">
                    <tr>
                        <td><strong>Kode Sampel:</strong></td>
                        <td>{{ $sample->sample_code }}</td>
                    </tr>
                    <tr>
                        <td><strong>Pelanggan:</strong></td>
                        <td>{{ optional(optional($sample->sampleRequest)->customer)->contact_person ?? '— Tidak ada data pelanggan —' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Perusahaan:</strong></td>
                        <td>{{ optional(optional($sample->sampleRequest)->customer)->company_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td><strong>Jenis Sampel:</strong></td>
                        <td>{{ $sample->sampleType->name ?? $sample->custom_sample_type }}</td>
                    </tr>
                    <tr>
                        <td><strong>Quantity:</strong></td>
                        <td>{{ $sample->quantity }} sampel</td>
                    </tr>
                    <tr>
                        <td><strong>Analis:</strong></td>
                        <td>{{ $sample->assignedAnalyst->full_name }}</td>
                    </tr>
                    @if($sample->assigned_at)
                    <tr>
                        <td><strong>Ditugaskan:</strong></td>
                        <td>{{ $sample->assigned_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                    @if($sample->testing_started_at)
                    <tr>
                        <td><strong>Mulai Pengujian:</strong></td>
                        <td>{{ $sample->testing_started_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>

        <!-- Testing Progress -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>
                    Progress Pengujian
                </h6>
            </div>
            <div class="card-body">
                @php
                    $totalTests = $sample->tests->count();
                    $completedTests = $sample->tests->where('status', 'completed')->count();
                    $progress = $totalTests > 0 ? ($completedTests / $totalTests) * 100 : 0;
                @endphp
                
                <div class="text-center mb-3">
                    <div class="progress-circle" data-progress="{{ $progress }}">
                        <span>{{ round($progress) }}%</span>
                    </div>
                </div>
                
                <p class="text-center mb-2">
                    <strong>{{ $completedTests }}/{{ $totalTests }}</strong> parameter selesai
                </p>
                
                @if($progress === 100)
                <div class="d-grid">
                    <button class="btn btn-success" onclick="completeTesting()">
                        <i class="fas fa-check me-2"></i>Selesaikan Pengujian
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Test Parameters -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-microscope me-2"></i>
                    Parameter Pengujian
                </h6>
            </div>
            <div class="card-body">
                @foreach($sample->parameters->groupBy('category') as $category => $parameters)
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0 text-primary">{{ $category }}</h6>
                    </div>
                    <div class="card-body">
                        @foreach($parameters  as $test)
                        <div class="card mb-3 border-{{ $test->status === 'completed' ? 'success' : ($test->status === 'testing' ? 'warning' : 'secondary') }}">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $test->name }}</strong>
                                    @if($test->unit)
                                    <small class="text-muted">({{ $test->unit }})</small>
                                    @endif
                                </div>
                                <span class="badge bg-{{ $test->status === 'completed' ? 'success' : ($test->status === 'testing' ? 'warning' : 'secondary') }}">
                                    {{ ucfirst($test->status) }}
                                </span>
                            </div>
                            <div class="card-body">
                                @if($test->method)
                                <p class="mb-2"><small class="text-muted"><strong>Metode:</strong> {{ $test->method }}</small></p>
                                @endif
                                
                                @if($test->status === 'completed')
                                <!-- Display completed test results -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Hasil:</strong> {{ $test->result_value }} {{ $test->unit }}<br>
                                        @if($test->notes)
                                        <strong>Catatan:</strong> {{ $test->notes }}<br>
                                        @endif
                                        <small class="text-muted">
                                            Diuji: {{ $test->tested_at ? $test->tested_at->format('d/m/Y H:i') : '-' }}
                                        </small>
                                    </div>
                                    <div class="col-md-6">
                                        @if($test->instrument_files && count($test->instrument_files) > 0)
                                        <strong>File Instrumen:</strong><br>
                                        @foreach($test->instrument_files as $index => $file)
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small>
                                                <i class="fas fa-file me-1"></i>
                                                {{ $file['filename'] }}
                                                <span class="text-muted">({{ number_format($file['size']/1024, 1) }} KB)</span>
                                            </small>
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="deleteFile({{ $test->id }}, {{ $index }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                                @else
                                <!-- Test input form -->
                                <form action="{{ route('testing.complete', $test->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Hasil Pengujian <span class="text-danger">*</span></label>
                                                <input type="text" name="result_value" class="form-control" 
                                                       placeholder="Masukkan hasil pengujian" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Catatan</label>
                                                <textarea name="notes" class="form-control" rows="2" 
                                                          placeholder="Catatan pengujian (opsional)"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">Upload File Instrumen</label>
                                        <div class="file-upload-area" ondrop="dropHandler(event, this);" ondragover="dragOverHandler(event);">
                                            <input type="file" name="instrument_files[]" class="form-control" multiple 
                                                   accept=".pdf,.jpg,.jpeg,.png,.xlsx,.xls,.doc,.docx">
                                            <div class="upload-placeholder">
                                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                                <p class="text-muted">Drag & drop file atau klik untuk browse</p>
                                                <small class="text-muted">PDF, Excel, Word, Image (Max: 10MB per file)</small>
                                            </div>
                                        </div>
                                        <div class="uploaded-files mt-2"></div>
                                    </div>
                                    
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Simpan Hasil
                                        </button>
                                    </div>
                                </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.progress-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: conic-gradient(#28a745 0deg, #28a745 var(--progress, 0deg), #e9ecef var(--progress, 0deg) 360deg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    position: relative;
}

.progress-circle::before {
    content: '';
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: white;
    position: absolute;
}

.progress-circle span {
    font-weight: bold;
    font-size: 18px;
    color: #28a745;
    z-index: 1;
}

.file-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    transition: border-color 0.3s;
    position: relative;
}

.file-upload-area:hover,
.file-upload-area.dragover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.upload-placeholder {
    pointer-events: none;
}

.file-upload-area input[type="file"] {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
function startTesting() {
    if (confirm('Mulai pengujian untuk sampel ini?')) {
        fetch(`/testing/{{ $sample->id }}/start`, {
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
                alert('Error: ' + (data.message || 'Gagal memulai pengujian'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}

function completeTesting() {
    if (confirm('Apakah Anda yakin semua pengujian sudah selesai dan hasilnya benar?')) {
        fetch(`/testing/{{ $sample->id }}/complete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pengujian berhasil diselesaikan dan dikirim untuk review');
                location.href = '{{ route("testing.index") }}';
            } else {
                alert('Error: ' + (data.message || 'Gagal menyelesaikan pengujian'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}

function deleteFile(testId, fileIndex) {
    if (confirm('Hapus file ini?')) {
        fetch(`/testing/file/${testId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({ file_index: fileIndex })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: Gagal menghapus file');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan sistem');
        });
    }
}

// File upload handlers
function dragOverHandler(ev) {
    ev.preventDefault();
    ev.currentTarget.classList.add('dragover');
}

function dropHandler(ev, element) {
    ev.preventDefault();
    element.classList.remove('dragover');
    
    const files = ev.dataTransfer.files;
    const input = element.querySelector('input[type="file"]');
    input.files = files;
    
    displayUploadedFiles(files, element);
}

function displayUploadedFiles(files, container) {
    const filesContainer = container.parentElement.querySelector('.uploaded-files');
    filesContainer.innerHTML = '';
    
    Array.from(files).forEach((file, index) => {
        const fileElement = document.createElement('div');
        fileElement.className = 'alert alert-info alert-dismissible fade show';
        fileElement.innerHTML = `
            <i class="fas fa-file me-2"></i>
            ${file.name} (${(file.size/1024/1024).toFixed(2)} MB)
            <button type="button" class="btn-close" onclick="removeUploadedFile(${index}, this)"></button>
        `;
        filesContainer.appendChild(fileElement);
    });
}

function removeUploadedFile(index, button) {
    button.closest('.alert').remove();
    // Additional logic to remove file from input if needed
}

// Initialize progress circle
document.addEventListener('DOMContentLoaded', function() {
    const progressCircle = document.querySelector('.progress-circle');
    if (progressCircle) {
        const progress = progressCircle.dataset.progress;
        progressCircle.style.setProperty('--progress', (progress * 3.6) + 'deg');
    }
});

// Auto-save functionality (optional)
let autoSaveTimeout;
document.querySelectorAll('input, textarea').forEach(element => {
    element.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            // Auto-save logic here
            console.log('Auto-saving...');
        }, 5000);
    });
});
</script>
@endpush
