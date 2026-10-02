@extends('layouts.app')
@section('title', 'Pilih Peminjaman')
@section('header-title', 'Pilih Peminjaman untuk Dikembalikan')

@section('content')
<div class="space-y-5">

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm" style="background:#dcfce7; color:#166534;">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm" style="background:#fee2e2; color:#dc2626;">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Peminjaman Aktif</h2>
            <p class="text-sm text-gray-400">{{ $peminjamans->count() }} peminjaman belum dikembalikan</p>
        </div>

        <a href="{{ route('admin.pengembalian.index') }}"
            class="flex items-center gap-2 text-gray-600 text-sm font-semibold px-4 py-2.5 rounded-full transition whitespace-nowrap"
            style="background:#f3f4f6;">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            Kembali
        </a>
    </div>

    <!-- Tabel -->
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-gray-400" style="background:#f9fafb; border-bottom:1px solid #f0f0f0;">
                        <th class="py-3 px-5 font-semibold">Peminjam</th>
                        <th class="py-3 px-5 font-semibold">Alat Dipinjam</th>
                        <th class="py-3 px-5 font-semibold">Tanggal</th>
                        <th class="py-3 px-5 font-semibold">Status</th>
                        <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($peminjamans as $peminjaman)
                    <tr class="hover:bg-gray-50 transition" style="border-bottom:1px solid #f3f4f6;">
                        <td class="py-4 px-5 align-top">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold" style="background:#0f1729;">
                                    {{ strtoupper(substr($peminjaman->user->name ?? 'N', 0, 1)) }}
                                </div>
                                <span class="font-semibold text-gray-900 whitespace-nowrap">{{ $peminjaman->user->name ?? 'N/A' }}</span>
                            </div>
                        </td>

                        <td class="py-4 px-5 align-top text-gray-600">
                            <ul class="space-y-1">
                                @foreach($peminjaman->detailPinjam as $detail)
                                    <li class="flex items-center gap-1.5">
                                        <span class="w-1 h-1 rounded-full bg-gray-300 flex-shrink-0"></span>
                                        <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat' }}</span>
                                        <span class="text-xs text-gray-400 whitespace-nowrap">({{ $detail->jumlah }} pcs)</span>
                                    </li>
                                @endforeach
                            </ul>
                        </td>

                        <td class="py-4 px-5 align-top text-xs text-gray-500 whitespace-nowrap">
                            <div><span class="text-gray-400">Pinjam:</span> {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}</div>
                            <div><span class="text-gray-400">Rencana:</span> {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}</div>
                        </td>

                        <td class="py-4 px-5 align-top">
                            @if(strtolower($peminjaman->status) == 'telat')
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full whitespace-nowrap" style="background:#fee2e2; color:#dc2626;">Telat</span>
                            @else
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full whitespace-nowrap" style="background:#dbeafe; color:#2563eb;">Dipinjam</span>
                            @endif
                        </td>

                        <td class="py-4 px-5 align-top">
                            <div class="flex justify-end">
                                <a href="{{ route('admin.pengembalian.create', $peminjaman->id) }}"
                                    class="inline-flex items-center gap-2 text-white text-xs font-semibold px-4 py-2 rounded-full transition whitespace-nowrap"
                                    style="background:#0f1729;">
                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
                                    Proses Kembali
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-gray-400 text-sm">Tidak ada peminjaman aktif yang perlu dikembalikan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection