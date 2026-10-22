@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header-title', 'Profil Pengguna')

@section('content')
<div class="space-y-5">

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm" style="background:#dcfce7; color:#166534;">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Header Profil -->
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-32"></div>
        
        <div class="px-6 pb-6">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 -mt-14 mb-6">
                <div class="flex items-end gap-4">
                    <!-- Foto Profil -->
                    <div class="w-32 h-32 rounded-2xl border-4 border-white shadow-lg flex items-center justify-center text-white text-4xl font-bold" style="background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        @if($user->foto_profile && file_exists(storage_path('app/public/' . $user->foto_profile)))
                            <img src="{{ asset('storage/' . $user->foto_profile) }}" alt="{{ $user->name }}" class="w-full h-full object-cover rounded-xl">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-500">{{ ucfirst($user->role) }} • {{ $user->email }}</p>
                        <p class="text-xs text-gray-400 mt-1">Bergabung sejak {{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 rounded-lg text-white text-sm font-semibold transition" style="background:#0f1729;">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                        Edit Profil
                    </a>
                </div>
            </div>

            <!-- Info Profil -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Kiri -->
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Nama Lengkap</label>
                        <p class="text-gray-900 font-semibold mt-1">{{ $user->name }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Email</label>
                        <p class="text-gray-900 font-semibold mt-1">{{ $user->email }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Role</label>
                        <div class="mt-1">
                            @php
                                $roleColor = match($user->role) {
                                    'admin' => ['#ede9fe', '#7c3aed'],
                                    'petugas' => ['#dbeafe', '#2563eb'],
                                    'peminjam' => ['#dcfce7', '#166534'],
                                };
                            @endphp
                            <span class="px-3 py-1 text-xs font-semibold rounded-full" style="background:{{ $roleColor[0] }}; color:{{ $roleColor[1] }};">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div class="space-y-4">
                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Nomor HP</label>
                        <p class="text-gray-900 font-semibold mt-1">{{ $user->no_hp ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Alamat</label>
                        <p class="text-gray-900 font-semibold mt-1">{{ $user->alamat ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="text-xs font-semibold text-gray-500 uppercase">Terakhir Diperbarui</label>
                        <p class="text-gray-900 font-semibold mt-1">{{ $user->updated_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Opsi Tambahan -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <a href="{{ route('profile.editPassword') }}" class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 hover:shadow-md transition">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white" style="background:#fef3c7; color:#b45309;">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Ubah Password</h3>
                    <p class="text-sm text-gray-500 mt-1">Perbarui kata sandi akun Anda</p>
                </div>
            </div>
        </a>

        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-lg flex items-center justify-center text-white" style="background:#dbeafe; color:#2563eb;">
                    <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Informasi Akun</h3>
                    <p class="text-sm text-gray-500 mt-1">Status: <span class="font-semibold text-green-600">Aktif ✓</span></p>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
