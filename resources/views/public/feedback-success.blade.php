<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Feedback Terkirim - {{ config('app.name', 'LIMS') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8 text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Terima Kasih!</h1>
            <p class="text-gray-600 mb-6">Feedback Anda telah berhasil dikirim dan sangat berharga bagi kami untuk meningkatkan kualitas layanan.</p>

            <div class="space-y-3">
                <a href="{{ route('landing') }}"
                   class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 inline-block">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</body>
</html>
