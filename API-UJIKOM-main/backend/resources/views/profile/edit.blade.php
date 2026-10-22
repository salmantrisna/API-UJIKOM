@extends('layouts.app')

@section('title', 'Edit Profil')
@section('header-title', 'Edit Profil Pengguna')

@section('content')
<div class="space-y-5">

    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Foto Profil -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-3">Foto Profil</label>
                
                <div class="flex items-end gap-6">
                    <!-- Preview -->
                    <div class="w-24 h-24 rounded-xl border-2 border-gray-200 flex items-center justify-center flex-shrink-0 text-white text-2xl font-bold overflow-hidden" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        @if($user->foto_profile && file_exists(storage_path('app/public/' . $user->foto_profile)))
                            <img src="{{ asset('storage/' . $user->foto_profile) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>

                    <!-- Upload -->
                    <div class="flex-1">
                        <input type="file" name="foto_profile" id="foto_profile" accept="image/*" class="hidden">
                        <label for="foto_profile" class="flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer hover:border-blue-400 transition">
                            <div class="text-center">
                                <svg class="w-6 h-6 text-gray-400 mx-auto mb-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                <p class="text-xs text-gray-600">Klik untuk upload foto</p>
                            </div>
                        </label>
                        <p class="text-xs text-gray-500 mt-2">Max 2MB, Format: JPEG, PNG, JPG, GIF</p>
                    </div>
                </div>
                @error('foto_profile')
                    <p class="text-red-600 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <hr class="border-gray-200">

            <!-- Nama -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" placeholder="Masukkan nama lengkap" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" placeholder="Masukkan email" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" required>
                @error('email')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nomor HP -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Nomor HP</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}" placeholder="08xxxxxxxxx" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('no_hp') border-red-500 @enderror">
                @error('no_hp')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Alamat -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Alamat</label>
                <textarea name="alamat" placeholder="Masukkan alamat lengkap" rows="4" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('alamat') border-red-500 @enderror">{{ old('alamat', $user->alamat) }}</textarea>
                @error('alamat')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Button -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-lg text-white text-sm font-semibold transition" style="background:#0f1729;">
                    Simpan Perubahan
                </button>
                <a href="{{ route('profile.show') }}" class="px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
