<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Tracking - {{ config('app.name', 'LIMS') }}</title>

    <!-- Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('public.landing') }}" class="text-xl font-bold text-blue-600">{{ config('app.name', 'LIMS') }}</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('public.tracking') }}" class="text-gray-700 hover:text-blue-600">Lacak Lagi</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Sample Info -->
            <div class="bg-white shadow-sm rounded-lg mb-6">
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-4">Informasi Sampel</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Kode Tracking</p>
                            <p class="font-semibold text-lg">{{ $sample->tracking_code }}</p>
                        </div>
                        @if($sample->sample_code)
                        <div>
                            <p class="text-sm text-gray-600">Kode Sampel</p>
                            <p class="font-semibold">{{ $sample->sample_code }}</p>
                        </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Perusahaan</p>
                            <p class="font-semibold">{{ $sample->customer->company_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Jenis Sampel</p>
                            <p class="font-semibold">{{ $sample->sampleType->name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status Saat Ini</p>
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
                        <div>
                            <p class="text-sm text-gray-600">Tanggal Submit</p>
                            <p class="font-semibold">{{ $sample->submitted_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Timeline -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Riwayat Proses</h3>
                    <div class="space-y-4">
                        @foreach($sample->workflowHistory as $history)
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-3 h-3 bg-blue-600 rounded-full"></div>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium">{{ config('lims.workflow.statuses.' . $history->to_status) }}</p>
                                <p class="text-sm text-gray-600">{{ $history->action_at->format('d/m/Y H:i') }}</p>
                                @if($history->notes)
                                <p class="text-sm text-gray-500 mt-1">{{ $history->notes }}</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            @if($sample->status === 'completed')
            <div class="mt-6 text-center">
                <a href="{{ route('feedback', $sample->tracking_code) }}"
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Berikan Feedback
                </a>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
