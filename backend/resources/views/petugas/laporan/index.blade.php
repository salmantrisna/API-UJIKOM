@extends('layouts.app')

@section('title', 'Laporan Peminjaman - Dashboard Petugas')
@section('header-title', 'Laporan Peminjaman & Pengembalian Alat')

@section('content')
<div class="space-y-5">

    <!-- Filter -->
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
        <h3 class="text-base font-bold text-gray-900 mb-4">Filter Laporan</h3>
        <form action="{{ route('petugas.laporan.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Status Peminjaman</label>
                <select name="status" class="w-full text-sm border border-gray-200 rounded-full px-4 py-2.5 bg-white focus:outline-none focus:ring-1 focus:ring-gray-300">
                    <option value="">Semua Status</option>
                    <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                    <option value="dipinjam" {{ request('status') == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                    <option value="dikembalikan" {{ request('status') == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    <option value="telat" {{ request('status') == 'telat' ? 'selected' : '' }}>Telat</option>
                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}"
                    class="w-full text-sm border border-gray-200 rounded-full px-4 py-2.5 focus:outline-none focus:ring-1 focus:ring-gray-300">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}"
                    class="w-full text-sm border border-gray-200 rounded-full px-4 py-2.5 focus:outline-none focus:ring-1 focus:ring-gray-300">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 text-white text-sm font-semibold px-4 py-2.5 rounded-full transition" style="background:#0f1729;">
                    Filter
                </button>
                <a href="{{ route('petugas.laporan.index') }}"
                    class="px-4 py-2.5 text-sm font-semibold rounded-full transition flex items-center justify-center text-gray-600" style="background:#f3f4f6;">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Hasil -->
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4" style="border-bottom:1px solid #f3f4f6;">
            <div>
                <h3 class="text-base font-bold text-gray-900">Hasil Rekap Laporan</h3>
                <p class="text-xs text-gray-400">{{ $laporans->count() }} transaksi ditemukan</p>
            </div>
            <a href="{{ route('petugas.laporan.cetak', request()->all()) }}" target="_blank"
                class="flex items-center gap-2 text-white text-sm font-semibold px-4 py-2.5 rounded-full transition" style="background:#0f1729;">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2M6 14h12v8H6z"/></svg>
                Cetak / Print
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-gray-400" style="background:#f9fafb; border-bottom:1px solid #f0f0f0;">
                        <th class="py-3 px-5 font-semibold w-12 text-center">No</th>
                        <th class="py-3 px-5 font-semibold">Peminjam</th>
                        <th class="py-3 px-5 font-semibold">Tanggal</th>
                        <th class="py-3 px-5 font-semibold">Status</th>
                        <th class="py-3 px-5 font-semibold">Detail Alat</th>
                        <th class="py-3 px-5 font-semibold">Denda</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($laporans as $index => $item)
                        @php
                            $statusLower = strtolower($item->status);
                            if (in_array($statusLower, ['dikembalikan', 'selesai'])) {
                                $badgeBg = '#dcfce7'; $badgeColor = '#166534'; $badgeText = 'Dikembalikan';
                            } elseif ($statusLower == 'dipinjam') {
                                $badgeBg = '#dbeafe'; $badgeColor = '#2563eb'; $badgeText = 'Dipinjam';
                            } elseif ($statusLower == 'telat') {
                                $badgeBg = '#fee2e2'; $badgeColor = '#dc2626'; $badgeText = 'Telat';
                            } elseif ($statusLower == 'diajukan') {
                                $badgeBg = '#fef3c7'; $badgeColor = '#b45309'; $badgeText = 'Diajukan';
                            } elseif ($statusLower == 'ditolak') {
                                $badgeBg = '#f3f4f6'; $badgeColor = '#6b7280'; $badgeText = 'Ditolak';
                            } else {
                                $badgeBg = '#f3f4f6'; $badgeColor = '#4b5563'; $badgeText = ucfirst($item->status);
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition" style="border-bottom:1px solid #f3f4f6;">
                            <td class="py-4 px-5 align-top text-center text-gray-400">{{ $index + 1 }}</td>
                            <td class="py-4 px-5 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold" style="background:#0f1729;">
                                        {{ strtoupper(substr($item->user->name ?? '-', 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $item->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-5 align-top text-xs text-gray-500 whitespace-nowrap">
                                <div><span class="text-gray-400">Pinjam:</span> {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</div>
                                <div><span class="text-gray-400">Rencana:</span> {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d M Y') }}</div>
                            </td>
                            <td class="py-4 px-5 align-top">
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full" style="background:{{ $badgeBg }}; color:{{ $badgeColor }};">
                                    {{ $badgeText }}
                                </span>
                            </td>
                            <td class="py-4 px-5 align-top text-gray-600">
                                <ul class="space-y-1">
                                    @foreach($item->detailPinjams as $detail)
                                        <li class="flex items-center gap-1.5">
                                            <span class="w-1 h-1 rounded-full bg-gray-300 flex-shrink-0"></span>
                                            {{ $detail->alat->nama_alat ?? '-' }} <span class="text-xs text-gray-400">({{ $detail->jumlah }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-4 px-5 align-top">
                                @php $denda = $item->pengembalian->denda ?? 0; @endphp
                                <span class="text-sm font-semibold" style="color:{{ $denda > 0 ? '#dc2626' : '#9ca3af' }};">
                                    Rp {{ number_format($denda, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-400 text-sm">Tidak ada data laporan yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection