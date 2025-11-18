<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Kepuasan - {{ config('app.name', 'LIMS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="py-12">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6">
                    <h2 class="text-2xl font-bold mb-4">Kuisioner Kepuasan Pelanggan</h2>
                    <p class="text-gray-600 mb-6">
                        Sampel: <strong>{{ $sample->tracking_code }}</strong> - {{ $sample->customer->company_name }}
                    </p>

                    @if($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('submit-feedback', $sample->tracking_code) }}">
                        @csrf

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Bagaimana tingkat kepuasan Anda terhadap layanan kami? *
                            </label>
                            <div class="flex justify-center space-x-4">
                                @for($i = 1; $i <= 5; $i++)
                                <label class="flex flex-col items-center cursor-pointer">
                                    <input type="radio" name="rating" value="{{ $i }}" required
                                           class="sr-only peer" {{ old('rating') == $i ? 'checked' : '' }}>
                                    <div class="text-3xl peer-checked:text-yellow-400 text-gray-300 hover:text-yellow-300">
                                        ⭐
                                    </div>
                                    <span class="text-xs mt-1">{{ $i }}</span>
                                </label>
                                @endfor
                            </div>
                            <div class="text-center mt-2">
                                <span class="text-xs text-gray-500">1 = Sangat Tidak Puas, 5 = Sangat Puas</span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Komentar dan Saran (Opsional)
                            </label>
                            <textarea name="comments" rows="4" placeholder="Berikan komentar atau saran Anda..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('comments') }}</textarea>
                        </div>

                        <div class="flex justify-between">
                            <a href="{{ route('tracking') }}"
                               class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                                Kembali
                            </a>
                            <button type="submit"
                                    class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                                Kirim Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
