@extends('layouts.app')

@section('header-title', 'Data Transaksi Pengembalian')

@section('content')
<div class="space-y-5">

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm" style="background:#dcfce7; color:#166534;">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <!-- Header + Search + Tambah -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Data Transaksi Pengembalian</h2>
            <p class="text-sm text-gray-400">{{ $peminjamans->total() ?? $peminjamans->count() }} transaksi tercatat</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-80 rounded-full px-4 py-2 bg-white border border-gray-200 shadow-sm">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                    class="w-full text-sm bg-transparent focus:outline-none text-gray-700">
                @if(request('search'))
                    <a href="{{ route('admin.pengembalian.index') }}" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.pengembalian.create') }}"
                class="flex items-center gap-2 text-white text-sm font-semibold px-4 py-2.5 rounded-full transition whitespace-nowrap flex-shrink-0"
                style="background:#0f1729;">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                Proses Pengembalian
            </a>
        </div>
    </div>

    <!-- Tabel -->
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-gray-400" style="background:#f9fafb; border-bottom:1px solid #f0f0f0;">
                        <th class="py-3 px-5 font-semibold">Peminjam</th>
                        <th class="py-3 px-5 font-semibold">Daftar Alat</th>
                        <th class="py-3 px-5 font-semibold">Tanggal</th>
                        <th class="py-3 px-5 font-semibold">Denda</th>
                        <th class="py-3 px-5 font-semibold">Petugas</th>
                        <th class="py-3 px-5 font-semibold">Status</th>
                        <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($peminjamans as $peminjaman)
                        @php
                            $statusLower = strtolower($peminjaman->status);

                            if (in_array($statusLower, ['dikembalikan', 'selesai']) && $peminjaman->pengembalian) {
                                $denda = $peminjaman->pengembalian->denda ?? 0;
                            } else {
                                $denda = 0;
                                if (in_array($statusLower, ['dipinjam', 'telat'])) {
                                    $plan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
                                    $now = \Carbon\Carbon::now()->startOfDay();
                                    if ($now->greaterThan($plan)) {
                                        $denda = $plan->diffInDays($now) * 5000;
                                    }
                                }
                            }

                            if ($statusLower == 'dipinjam') {
                                $badgeBg = '#dcfce7'; $badgeColor = '#166534'; $badgeText = 'Dipinjam';
                            } elseif (in_array($statusLower, ['selesai', 'dikembalikan'])) {
                                $badgeBg = '#dbeafe'; $badgeColor = '#2563eb'; $badgeText = 'Dikembalikan';
                            } elseif ($statusLower == 'telat') {
                                $badgeBg = '#fee2e2'; $badgeColor = '#dc2626'; $badgeText = 'Telat';
                            } else {
                                $badgeBg = '#f3f4f6'; $badgeColor = '#4b5563'; $badgeText = ucfirst($peminjaman->status);
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition" style="border-bottom:1px solid #f3f4f6;">
                            <td class="py-4 px-5 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold" style="background:#0f1729;">
                                        {{ strtoupper(substr($peminjaman->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $peminjaman->user->name ?? 'User Tidak Ditemukan' }}</span>
                                </div>
                            </td>

                            <td class="py-4 px-5 align-top text-gray-600">
                                <ul class="space-y-1">
                                    @foreach($peminjaman->detailPinjam as $detail)
                                        <li class="flex items-center gap-1.5">
                                            <span class="w-1 h-1 rounded-full bg-gray-300 flex-shrink-0"></span>
                                            <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="text-xs text-gray-400">({{ $detail->jumlah }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <td class="py-4 px-5 align-top text-xs text-gray-500 whitespace-nowrap">
                                <div><span class="text-gray-400">Pinjam:</span> {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}</div>
                                <div><span class="text-gray-400">Rencana:</span> {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}</div>
                            </td>

                            <td class="py-4 px-5 align-top">
                                <span class="text-sm font-semibold" style="color:{{ $denda > 0 ? '#dc2626' : '#9ca3af' }};">
                                    Rp {{ number_format($denda, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="py-4 px-5 align-top text-gray-600">
                                {{ $peminjaman->petugas->name ?? 'Admin' }}
                            </td>

                            <td class="py-4 px-5 align-top">
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full" style="background:{{ $badgeBg }}; color:{{ $badgeColor }};">
                                    {{ $badgeText }}
                                </span>
                            </td>

                            <td class="py-4 px-5 align-top">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.pengembalian.edit', $peminjaman->id) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-full transition"
                                        style="background:#fef3c7; color:#b45309;" title="Edit">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                    </a>
                                    <form action="{{ route('admin.pengembalian.destroy', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full transition" style="background:#fee2e2; color:#dc2626;" title="Hapus">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-gray-400 text-sm">Tidak ada data transaksi pengembalian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($peminjamans->hasPages())
            <div class="px-5 py-4" style="border-top:1px solid #f3f4f6;">
                {{ $peminjamans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection