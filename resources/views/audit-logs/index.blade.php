@extends('layouts.app')

@section('title', 'Audit Logs')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-history me-2"></i>
            Audit Trail
        </h6>
    </div>
    <div class="card-body">
        @if($logs->count() > 0)
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Aksi</th>
                        <th>Deskripsi</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                        <td>{{ $log->user->full_name ?? 'System' }}</td>
                        <td>
                            <span class="badge bg-primary">{{ $log->action }}</span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td>{{ $log->ip_address ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{ $logs->links() }}
        
        @else
        <div class="text-center py-4">
            <i class="fas fa-history fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada log audit</p>
        </div>
        @endif
    </div>
</div>
@endsection
