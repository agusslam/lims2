@extends('layouts.app')

@section('title', 'Detail Permohonan Sampel')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-eye me-2"></i>Detail Permohonan Sampel
            </h1>
            <p class="text-muted mb-0">{{ $request->tracking_code }}</p>
        </div>
        <div>
            <a href="{{ route('sample-requests.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Informasi Pelanggan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Nama Kontak:</div>
                        <div class="col-sm-9">{{ $request->contact_person }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Perusahaan:</div>
                        <div class="col-sm-9">{{ $request->company_name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Telepon:</div>
                        <div class="col-sm-9">{{ $request->phone }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Email:</div>
                        <div class="col-sm-9">{{ $request->email }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Alamat:</div>
                        <div class="col-sm-9">{{ $request->address }}, {{ $request->city }}</div>
                    </div>
                </div>
            </div>

            <!-- Informasi Sampel -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Sampel</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Jenis Sampel:</div>
                        <div class="col-sm-9">{{ $request->sampleType->name ?? 'Tidak diketahui' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Jumlah:</div>
                        <div class="col-sm-9">{{ $request->quantity }}</div>
                    </div>

                    @if($request->customer_requirements)
                    <div class="row mb-3">
                        <div class="col-sm-3 font-weight-bold">Kebutuhan Khusus:</div>
                        <div class="col-sm-9">{{ $request->customer_requirements }}</div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Parameter Uji -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Parameter Uji</h6>
                </div>
                <div class="card-body">
                    @if($request->parameters->count() > 0)
                        @php $parametersByCategory = $request->parameters->groupBy('category'); @endphp
                        @foreach($parametersByCategory as $category => $parameters)
                            <h6 class="mt-3 mb-2">{{ $category }}</h6>
                            <div class="row">
                                @foreach($parameters as $parameter)
                                <div class="col-md-6 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check text-success me-2"></i>
                                        <span>{{ $parameter->name }}{{ $parameter->unit ? ' (' . $parameter->unit . ')' : '' }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Tidak ada parameter yang dipilih</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Status & Aksi -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status & Informasi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="font-weight-bold">Status:</label><br>
                        @php
                            $statusConfig = config("lims.sample_status.{$request->status}", [
                                'name' => ucfirst($request->status),
                                'color' => 'secondary'
                            ]);
                        @endphp
                        <span class="badge badge-{{ $statusConfig['color'] }} badge-lg">
                            {{ $statusConfig['name'] }}
                        </span>

                        @if($request->urgent)
                            <span class="badge badge-danger badge-lg ml-2">Urgent</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold">Kode Tracking:</label><br>
                        <code>{{ $request->tracking_code }}</code>
                    </div>

                    <div class="mb-3">
                        <label class="font-weight-bold">Tanggal Pengajuan:</label><br>
                        {{ $request->submitted_at->format('d/m/Y H:i') }}
                    </div>

                    @if(Auth::user()->hasAnyRole(['SUPERVISOR', 'ADMIN', 'DEVEL']) && $request->status === 'pending')
                    <hr>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-success" onclick="updateStatus('registered')">
                            <i class="fas fa-check me-1"></i>Setujui & Registrasi
                        </button>
                        <button type="button" class="btn btn-warning" onclick="showUpdateModal()">
                            <i class="fas fa-edit me-1"></i>Update Status
                        </button>
                        <a href="{{ route('sample-requests.edit', $request->id) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit me-1"></i>Edit Data
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Status History -->
            @if($request->rejection_reason || $request->archived_at)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-secondary">Riwayat Status</h6>
                    </div>
                    <div class="card-body">
                        @if($request->rejection_reason)
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-times-circle me-2"></i>Alasan Penolakan:</h6>
                                <p class="mb-0">{{ $request->rejection_reason }}</p>
                                @if($request->rejected_at)
                                    <small class="text-muted">Ditolak pada: {{ $request->rejected_at->format('d/m/Y H:i') }}</small>
                                @endif
                            </div>
                        @endif

                        @if($request->archived_at)
                            <div class="alert alert-secondary">
                                <h6><i class="fas fa-archive me-2"></i>Diarsipkan:</h6>
                                <small class="text-muted">Diarsipkan pada: {{ $request->archived_at->format('d/m/Y H:i') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Permohonan</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="updateStatusForm" method="POST" action="{{ route('sample-requests.update-status', $request->id) }}">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status">Status Baru</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="pending" {{ $request->status === 'pending' ? 'selected' : '' }}>Menunggu</option>
                            <option value="registered" {{ $request->status === 'registered' ? 'selected' : '' }}>Terdaftar</option>
                            <option value="assigned" {{ $request->status === 'assigned' ? 'selected' : '' }}>Ditugaskan</option>
                            <option value="testing" {{ $request->status === 'testing' ? 'selected' : '' }}>Dalam Pengujian</option>
                            <option value="review" {{ $request->status === 'review' ? 'selected' : '' }}>Review</option>
                            <option value="completed" {{ $request->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Catatan</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"
                                  placeholder="Tambahkan catatan untuk perubahan status"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateStatus(newStatus) {
    document.getElementById('status').value = newStatus;
    document.getElementById('updateStatusForm').submit();
}

function showUpdateModal() {
    $('#updateStatusModal').modal('show');
}

/* Optional: functions untuk preview/approve/archive (pakai $request->id) */
function previewSample() {
    window.open('{{ route("samples.preview", $request->id) }}', '_blank', 'width=800,height=600');
}

function approveSample() {
    if (confirm('Apakah Anda yakin ingin menyetujui permohonan sampel ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("sample-requests.approve", $request->id) }}';
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function archiveSample() {
    if (confirm('Apakah Anda yakin ingin mengarsipkan permohonan sampel ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("sample-requests.archive", $request->id) }}';
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
