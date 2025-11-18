@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-6">Profil Saya</h2>
                
                <form method="POST" action="{{ route('dashboard.update-profile') }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                            <input type="text" value="{{ Auth::user()->username }}" disabled
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <input type="text" value="{{ Auth::user()->role_name }}" disabled
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500">
                        </div>
                    </div>
                    
                    <div class="flex justify-end mt-6">
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                            Perbarui Profil
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
