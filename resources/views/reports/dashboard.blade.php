<!-- filepath: c:\Users\piter\Documents\Laravel\LIMS\resources\views\reports\dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard Analytics')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Period Filter -->
        <div class="mb-6">
            <form method="GET" class="flex gap-4 items-center">
                <select name="period" onchange="this.form.submit()" 
                        class="px-3 py-2 border border-gray-300 rounded-md">
                    <option value="7_days" {{ $period === '7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="30_days" {{ $period === '30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="90_days" {{ $period === '90_days' ? 'selected' : '' }}>90 Hari Terakhir</option>
                    <option value="1_year" {{ $period === '1_year' ? 'selected' : '' }}>1 Tahun Terakhir</option>
                </select>
            </form>
        </div>

        <!-- Sample Status Distribution -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Distribusi Status Sampel</h3>
                <div class="space-y-3">
                    @foreach($stats['samples_by_status'] as $status => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm">{{ config('lims.workflow.statuses.' . $status) }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" 
                                     style="width: {{ ($count / array_sum($stats['samples_by_status'])) * 100 }}%"></div>
                            </div>
                            <span class="text-sm font-medium">{{ $count }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Jenis Sampel Populer</h3>
                <div class="space-y-3">
                    @foreach($stats['samples_by_type'] as $type => $count)
                    <div class="flex justify-between items-center">
                        <span class="text-sm">{{ $type }}</span>
                        <span class="text-sm font-medium bg-blue-100 px-2 py-1 rounded">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Waktu Penyelesaian</h3>
                <div class="text-3xl font-bold text-blue-600 mb-2">
                    {{ round($stats['testing_turnaround']['avg_turnaround_days'] ?? 0, 1) }}
                </div>
                <div class="text-sm text-gray-600">Rata-rata hari</div>
                <div class="mt-4 text-xs">
                    <span class="text-green-600">Min: {{ $stats['testing_turnaround']['min_turnaround'] ?? 0 }} hari</span> |
                    <span class="text-red-600">Max: {{ $stats['testing_turnaround']['max_turnaround'] ?? 0 }} hari</span>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold mb-4">Sampel Diterima</h3>
                <div class="text-3xl font-bold text-blue-600 mb-2">
                    {{ $stats['samples_received'] ?? 0 }}
                </div>
                <div class="text-sm text-gray-600">Total sampel diterima</div>
            </div>