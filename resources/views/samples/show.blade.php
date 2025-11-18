@extends('layouts.app')

@section('title', 'Detail Sampel')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Sample Header -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $sample->tracking_code }}</h1>
                        @if($sample->sample_code)
                        <p class="text-lg text-gray-600">{{ $sample->sample_code }}</p>
                        @endif
                    </div>
                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full
                        @switch($sample->status)
                            @case('intake') bg-gray-100 text-gray-800 @break
                            @case('registered') bg-blue-100 text-blue-800 @break
                            @case('testing') bg-yellow-100 text-yellow-800 @break
                            @case('completed') bg-green-100 text-green-800 @break
                            @default bg-gray-100 text-gray-800
                        @endswitch">
                        {{ config('lims.workflow.statuses.' . $sample->status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Sample Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Informasi Sampel</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Perusahaan</p>
                                <p class="font-medium">{{ $sample->customer->company_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Contact Person</p>
                                <p class="font-medium">{{ $sample->customer->contact_person }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Jenis Sampel</p>
                                <p class="font-medium">{{ $sample->sampleType->name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Jumlah</p>
                                <p class="font-medium">{{ $sample->quantity }}</p>
                            </div>
                            @if($sample->assignedAnalyst)
                            <div>
                                <p class="text-sm text-gray-600">Analis</p>
                                <p class="font-medium">{{ $sample->assignedAnalyst->name }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-sm text-gray-600">Tanggal Submit</p>
                                <p class="font-medium">{{ $sample->submitted_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        
                        @if($sample->customer_requirements)
                        <div class="mt-4">
                            <p class="text-sm text-gray-600">Persyaratan Khusus</p>
                            <p class="font-medium">{{ $sample->customer_requirements }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Test Parameters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Parameter Pengujian</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Parameter</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Hasil</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($sample->testParameters as $testParam)
                                    <tr>
                                        <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                            {{ $testParam->parameter->name }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            {{ $testParam->result_value ?? '-' }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-500">
                                            {{ $testParam->result_unit ?? $testParam->parameter->unit }}
                                        </td>
                                        <td class="px-4 py-2">
                                            @if($testParam->result_value)
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Selesai
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Actions -->
                @if(auth()->user()->canAccessModule(1) && $sample->status === 'intake')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Tindakan</h3>
                        <form method="POST" action="{{ route('samples.update-status', $sample) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="registered">
                            <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 mb-2">
                                Verifikasi Sampel
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                <!-- Workflow History -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Riwayat Proses</h3>
                        <div class="space-y-3">
                            @foreach($sample->workflowHistory as $history)
                            <div class="border-l-4 border-blue-200 pl-4">
                                <p class="font-medium">{{ config('lims.workflow.statuses.' . $history->to_status) }}</p>
                                <p class="text-sm text-gray-600">{{ $history->actionBy->name }}</p>
                                <p class="text-xs text-gray-500">{{ $history->action_at->format('d/m/Y H:i') }}</p>
                                @if($history->notes)
                                <p class="text-xs text-gray-700 mt-1">{{ $history->notes }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
