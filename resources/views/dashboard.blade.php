@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </h1>
        <small class="text-muted">Selamat datang, {{ Auth::user()->name }}!</small>
    </div>

    <!-- Role Badge -->
    <div class="mb-4">
        @php
            $roleConfig = Auth::user()->getRoleConfigAttribute();
        @endphp
        <span class="badge badge-{{ $roleConfig['color'] }} badge-lg">
            {{ $roleConfig['name'] }}
        </span>
        <span class="text-muted">{{ Auth::user()->department }}</span>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Sample Request Stats -->
        @if(Auth::user()->hasPermission(1))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Permohonan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_sample_requests'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Menunggu Proses
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['pending_samples'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Testing Stats for Analysts -->
        @if(Auth::user()->hasPermission(4))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tugas Saya
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['my_assignments'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Management Stats -->
        @if(Auth::user()->hasAnyRole(['SUPERVISOR', 'ADMIN', 'DEVEL']))
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Selesai
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['completed_samples'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Activities -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Aktivitas Terbaru</h6>
                </div>
                <div class="card-body">
                    @if($stats['recent_activities']->count() > 0)
                    <div class="timeline">
                        @foreach($stats['recent_activities'] as $activity)
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    @if($activity['type'] === 'sample_request')
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-plus"></i>
                                        </div>
                                    @elseif($activity['type'] === 'assignment')
                                        <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-tasks"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-grow-1 ml-3">
                                    <div class="font-weight-bold">{{ $activity['title'] }}</div>
                                    <div class="text-muted small">{{ $activity['description'] }}</div>
                                    <div class="text-muted small">
                                        <i class="fas fa-clock"></i> {{ $activity['timestamp']->diffForHumans() }}
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ $activity['url'] }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>Belum ada aktivitas terbaru</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik Sistem</h6>
                </div>
                <div class="card-body">
                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Parameter Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $quickStats['parameters'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cogs text-gray-300"></i>
                        </div>
                    </div>

                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Jenis Sampel</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $quickStats['sample_types'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-flask text-gray-300"></i>
                        </div>
                    </div>

                    <div class="row no-gutters align-items-center mb-3">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Pengguna Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $quickStats['active_users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users text-gray-300"></i>
                        </div>
                    </div>

                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Permohonan Hari Ini</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $quickStats['today_requests'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    @if(Auth::user()->hasPermission(1))
                    <a href="{{ route('sample-requests.create') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-plus"></i> Tambah Permohonan
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission(4))
                    <a href="{{ route('testing.index') }}" class="btn btn-info btn-block mb-2">
                        <i class="fas fa-microscope"></i> Mulai Testing
                    </a>
                    @endif

                    @if(Auth::user()->hasPermission(8))
                    <a href="{{ route('parameters.index') }}" class="btn btn-secondary btn-block mb-2">
                        <i class="fas fa-cogs"></i> Kelola Parameter
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
