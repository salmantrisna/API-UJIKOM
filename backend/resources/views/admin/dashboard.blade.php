@extends('layouts.app')

@section('content')
<div class="space-y-5">

    <!-- Header Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Ringkasan</h2>
            <p class="text-sm text-gray-500">Selamat datang kembali, {{ Auth::user()->name ?? 'Admin' }}</p>
        </div>
        <span class="inline-block px-3 py-1.5 rounded-full text-xs font-semibold" style="background:#fef3c7; color:#b45309;">
            {{ now()->translatedFormat('d F Y') }}
        </span>
    </div>

    <!-- Kotak Statistik -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Kartu Hero Gelap -->
        <div class="rounded-2xl p-5 relative overflow-hidden" style="background:#0f1729;">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Alat</p>
            <p class="text-3xl font-bold text-white mb-1">{{ \App\Models\Alat::count() }}</p>
            <p class="text-[11px]" style="color:#f2a93b;">Unit terdaftar di sistem</p>
        </div>

        <!-- Sedang Dipinjam -->
        <div class="rounded-2xl p-5 bg-white border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#fef3c7;">
                    <svg class="w-5 h-5" style="color:#b45309;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/>
                        <rect x="8" y="2" width="8" height="4" rx="1"/>
                        <path d="M9 13h6M9 17h6"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Peminjaman::whereIn('status', ['Dipinjam', 'dipinjam', 'Telat', 'telat'])->count() }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Sedang Dipinjam</p>
        </div>

        <!-- Pending Request -->
        <div class="rounded-2xl p-5 bg-white border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#dbeafe;">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 11l3 3L22 4"/>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Peminjaman::whereIn('status', ['Diajukan', 'diajukan', 'Pending', 'pending'])->count() }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Pending Request</p>
        </div>

        <!-- Total User -->
        <div class="rounded-2xl p-5 bg-white border border-gray-100 shadow-sm">
            <div class="flex items-start justify-between mb-3">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background:#dcfce7;">
                    <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ \App\Models\User::count() }}</p>
            <p class="text-[11px] text-gray-400 mt-1">Total User</p>
        </div>

    </div>

    <!-- Log Aktivitas -->
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <h3 class="text-base font-bold text-gray-900">Log Aktivitas Terbaru</h3>
            <div class="flex flex-wrap items-center gap-3 text-[10px] uppercase tracking-wider text-gray-400">
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full" style="background:#3b82f6;"></span>Import/Update</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full" style="background:#16a34a;"></span>Approve/Add</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full" style="background:#f2a93b;"></span>Request</span>
                <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full" style="background:#dc2626;"></span>Return/Delete</span>
            </div>
        </div>

        <div class="space-y-2">
            @forelse($logs ?? [] as $log)
                @php
                    // Ambil 2-3 kata pertama untuk mendeteksi jenis aktivitas
                    $awalKalimat = strtolower(implode(' ', array_slice(explode(' ', $log->aktivitas), 0, 3)));

                    if (str_contains($awalKalimat, 'hapus') || str_contains($awalKalimat, 'memverifikasi pengembalian') || str_contains($awalKalimat, 'menolak')) {
                        $badgeBg = '#fee2e2'; 
                        $badgeColor = '#dc2626'; 
                        $badgeText = 'Return/Delete';
                    } elseif (str_contains($awalKalimat, 'menyetujui') || str_contains($awalKalimat, 'menambahkan')) {
                        $badgeBg = '#dcfce7'; 
                        $badgeColor = '#16a34a'; 
                        $badgeText = 'Approve/Add';
                    } elseif (str_contains($awalKalimat, 'membuat') || str_contains($awalKalimat, 'mengajukan')) {
                        $badgeBg = '#fef3c7'; 
                        $badgeColor = '#d97706'; 
                        $badgeText = 'Request';
                    } else {
                        $badgeBg = '#dbeafe'; 
                        $badgeColor = '#3b82f6'; 
                        $badgeText = 'Action';
                    }

                    $initial = strtoupper(substr($log->user->name ?? 'S', 0, 1));
                @endphp

                <div class="flex items-center gap-4 rounded-full px-4 py-2.5 hover:bg-gray-50 transition" style="background:#f9fafb;">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold" style="background:#0f1729;">
                        {{ $initial }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $log->user->name ?? 'System' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $log->aktivitas }}</p>
                    </div>
                    <span class="hidden sm:inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase flex-shrink-0" style="background: {{ $badgeBg }}; color: {{ $badgeColor }};">
                        {{ $badgeText }}
                    </span>
                    <span class="text-[11px] text-gray-400 font-mono flex-shrink-0 hidden md:inline">{{ $log->created_at->format('H:i') }}</span>
                    <svg class="w-4 h-4 text-gray-300 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                </div>
            @empty
                <p class="text-center text-gray-400 text-sm py-8">Belum ada log aktivitas yang tercatat.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection