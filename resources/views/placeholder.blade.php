@extends('layouts.app')

@section('title', $module)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-construction me-2"></i>{{ $module }}
                    </h5>
                </div>
                <div class="card-body text-center py-5">
                    <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                    <h4>Module {{ $module }} (ID: {{ $moduleId }})</h4>
                    <p class="text-muted">This module is under development and will be available soon.</p>
                    <p class="small">You have permission to access this module.</p>
                    
                    <div class="mt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-home me-1"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
