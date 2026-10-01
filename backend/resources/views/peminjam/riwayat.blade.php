@extends('layouts.app')

@section('title', 'Riwayat Peminjaman - Peminjam')
@section('header-title', 'Riwayat & Pengembalian Alat')

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

    <div>
        <h2 class="text-xl font-bold text-white">Riwayat Peminjaman</h2>
        <p class="text-sm text-slate-500">{{ $peminjamans->count() }} peminjaman tercatat</p>
    </div>

    <div class="space-y-3">
        @forelse($peminjamans as $item)
            @php
                $sudahKembali = (bool) $item->pengembalian;
                $statusKey = $sudahKembali ? 'selesai' : $item->status;

                $badge = match($statusKey) {
                    'diajukan'             => ['background:#fef9c3; color:#a16207;', 'Menunggu Persetujuan'],
                    'dipinjam'             => ['background:#dbeafe; color:#2563eb;', 'Sedang Dipinjam'],
                    'telat'                => ['background:#fee2e2; color:#dc2626;', 'Telat Dikembalikan'],
                    'menunggu_verifikasi'  => ['background:#f3e8ff; color:#7e22ce;', 'Menunggu Verifikasi'],
                    'ditolak'              => ['background:#f3f4f6; color:#6b7280;', 'Ditolak'],
                    'selesai'              => ['background:#dcfce7; color:#166534;', 'Selesai'],
                    default                => ['background:#f3f4f6; color:#4b5563;', ucfirst($item->status)],
                };

                // Estimasi denda telat (hanya untuk yang belum dikembalikan)
                $tglPlan = \Carbon\Carbon::parse($item->tgl_kembali_plan)->startOfDay();
                $tglSekarang = \Carbon\Carbon::now()->startOfDay();
                $hariTelat = $tglSekarang->greaterThan($tglPlan) ? $tglPlan->diffInDays($tglSekarang) : 0;
                $dendaEstimasi = $hariTelat * 5000;
            @endphp

            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-5">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <p class="text-xs text-gray-400">Peminjaman #{{ $item->id }}</p>
                        <p class="font-bold text-gray-900">
                            {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }} — {{ $tglPlan->format('d M Y') }}
                        </p>
                    </div>
                    <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full flex-shrink-0" style="{{ $badge[0] }}">
                        {{ $badge[1] }}
                    </span>
                </div>

                <div class="mb-4">
                    <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide mb-2">Alat Dipinjam</p>
                    <ul class="space-y-1">
                        @foreach($item->detailPinjam as $detail)
                            <li class="flex items-center gap-1.5 text-sm">
                                <span class="w-1 h-1 rounded-full bg-gray-300 flex-shrink-0"></span>
                                <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                <span class="text-xs text-gray-400">(Jumlah: {{ $detail->jumlah }})</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Denda --}}
                @if($sudahKembali)
                    <div class="rounded-xl p-3 mb-4 text-sm space-y-1" style="background:#f9fafb;">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kondisi Kembali</span>
                            <span class="font-medium text-gray-800">{{ $item->pengembalian->kondisi_kembali ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Total Denda</span>
                            <span class="font-semibold" style="color:{{ $item->pengembalian->denda > 0 ? '#dc2626' : '#6b7280' }};">
                                Rp {{ number_format($item->pengembalian->denda, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @elseif(in_array($item->status, ['dipinjam', 'telat']))
                    @if($hariTelat > 0)
                        <div class="rounded-xl p-3 mb-4 text-sm flex justify-between" style="background:#fef2f2;">
                            <span style="color:#dc2626;">Terlambat {{ $hariTelat }} hari · estimasi denda</span>
                            <span class="font-semibold" style="color:#dc2626;">Rp {{ number_format($dendaEstimasi, 0, ',', '.') }}</span>
                        </div>
                    @else
                        @php $sisa = $tglSekarang->diffInDays($tglPlan); @endphp
                        <div class="rounded-xl p-3 mb-4 text-sm flex justify-between" style="background:#f9fafb;">
                            <span class="text-gray-500">
                                Denda keterlambatan
                                <span class="text-xs text-gray-400">
                                    ({{ $sisa == 0 ? 'batas kembali hari ini' : 'sisa ' . $sisa . ' hari' }})
                                </span>
                            </span>
                            <span class="font-semibold text-gray-500">Rp 0</span>
                        </div>
                    @endif
                @elseif($item->status === 'menunggu_verifikasi')
                    <div class="rounded-xl p-3 mb-4 text-sm space-y-1" style="background:#f9fafb;">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kondisi yang Dilaporkan</span>
                            <span class="font-medium text-gray-800">{{ $item->kondisi_kembali ?? '-' }}</span>
                        </div>
                    </div>
                @endif

                {{-- Aksi --}}
                @if(!$sudahKembali && in_array($item->status, ['dipinjam', 'telat']))
                    <form action="{{ route('peminjam.riwayat.ajukanPengembalian', $item->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Kondisi Alat Saat Dikembalikan</label>
                            <select name="kondisi_kembali" required class="w-full text-sm border border-gray-200 rounded-full px-4 py-2.5 bg-white focus:outline-none focus:ring-1 focus:ring-gray-300">
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                            </select>
                        </div>
                        <button type="submit" onclick="return confirm('Ajukan pengembalian alat ini? Pastikan alat sudah kamu serahkan ke petugas.')"
                            class="w-full flex items-center justify-center gap-2 text-white text-sm font-semibold px-4 py-2.5 rounded-full transition" style="background:#0f1729;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Ajukan Pengembalian
                        </button>
                    </form>
                @elseif(!$sudahKembali && $item->status === 'menunggu_verifikasi')
                    <p class="text-center text-xs font-medium py-2 rounded-full" style="background:#f3e8ff; color:#7e22ce;">
                        Menunggu verifikasi petugas
                    </p>
                @endif
            </div>
        @empty
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm py-14 text-center">
                <p class="text-gray-500 font-medium text-sm">Kamu belum pernah meminjam alat</p>
                <a href="{{ route('peminjam.katalog') }}" class="text-sm font-semibold hover:underline mt-2 inline-block" style="color:#d98a14;">Lihat Katalog Alat →</a>
            </div>
        @endforelse
    </div>

</div>
@endsection