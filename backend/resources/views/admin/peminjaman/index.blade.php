@extends('layouts.app')

@section('title', 'Kelola Peminjaman')
@section('header-title', 'Manajemen Transaksi Peminjaman')

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

    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Daftar Transaksi Peminjaman</h2>
            <p class="text-sm text-gray-400">{{ $peminjamans->total() ?? $peminjamans->count() }} transaksi tercatat</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.peminjaman.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-80 rounded-full px-4 py-2 bg-white border border-gray-200 shadow-sm">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam / status..."
                    class="w-full text-sm bg-transparent focus:outline-none text-gray-700">
                @if(request('search'))
                    <a href="{{ route('admin.peminjaman.index') }}" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.peminjaman.create') }}"
                class="flex items-center gap-2 text-white text-sm font-semibold px-4 py-2.5 rounded-full transition whitespace-nowrap flex-shrink-0"
                style="background:#0f1729;">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah Peminjaman
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-gray-400" style="background:#f9fafb; border-bottom:1px solid #f0f0f0;">
                        <th class="py-3 px-5 font-semibold">Peminjam</th>
                        <th class="py-3 px-5 font-semibold">Alat yang Dipinjam</th>
                        <th class="py-3 px-5 font-semibold">Tanggal</th>
                        <th class="py-3 px-5 font-semibold">Status</th>
                        <th class="py-3 px-5 font-semibold">Denda</th>
                        <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($peminjamans as $peminjaman)
                        @php
                            $statusLower = strtolower($peminjaman->status);

                            // Transaksi yang alatnya masih keluar tidak boleh dihapus
                            // (samakan dengan aturan di method stokMasihKeluar pada AdminController)
                            $masihDipinjam = in_array($statusLower, ['dipinjam', 'telat', 'menunggu_verifikasi']);

                            if (in_array($statusLower, ['diajukan'])) {
                                $badgeBg = '#fef3c7'; $badgeColor = '#b45309';
                            } elseif (in_array($statusLower, ['dipinjam'])) {
                                $badgeBg = '#dbeafe'; $badgeColor = '#2563eb';
                            } elseif (in_array($statusLower, ['selesai', 'dikembalikan'])) {
                                $badgeBg = '#dcfce7'; $badgeColor = '#166534';
                            } elseif (in_array($statusLower, ['telat'])) {
                                $badgeBg = '#fee2e2'; $badgeColor = '#dc2626';
                            } else {
                                $badgeBg = '#f3f4f6'; $badgeColor = '#4b5563';
                            }

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
                        @endphp
                        <tr class="hover:bg-gray-50 transition" style="border-bottom:1px solid #f3f4f6;">
                            <td class="py-4 px-5 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold" style="background:#0f1729;">
                                        {{ strtoupper(substr($peminjaman->user->name ?? 'N', 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $peminjaman->user->name ?? 'N/A' }}</span>
                                </div>
                            </td>

                            <td class="py-4 px-5 align-top text-gray-600">
                                <ul class="space-y-1">
                                    @foreach($peminjaman->detailPinjam as $detail)
                                        <li class="flex items-center gap-1.5">
                                            <span class="w-1 h-1 rounded-full bg-gray-300 flex-shrink-0"></span>
                                            <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat' }}</span>
                                            <span class="text-xs text-gray-400">({{ $detail->jumlah }} pcs)</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <td class="py-4 px-5 align-top text-xs text-gray-500 whitespace-nowrap">
                                <div><span class="text-gray-400">Pinjam:</span> {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('d M Y') }}</div>
                                <div><span class="text-gray-400">Rencana:</span> {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('d M Y') }}</div>
                            </td>

                            <td class="py-4 px-5 align-top">
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full" style="background:{{ $badgeBg }}; color:{{ $badgeColor }};">
                                    {{ $peminjaman->status == 'Dikembalikan' ? 'Selesai' : $peminjaman->status }}
                                </span>
                            </td>

                            <td class="py-4 px-5 align-top">
                                <span class="text-sm font-semibold" style="color:{{ $denda > 0 ? '#dc2626' : '#9ca3af' }};">
                                    Rp {{ number_format($denda, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="py-4 px-5 align-top">
                                <div class="flex flex-col items-end gap-2">
                                    <form action="{{ route('admin.peminjaman.updateStatus', $peminjaman->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="border border-gray-200 rounded-full px-3 py-1.5 text-xs bg-white text-gray-600 shadow-sm focus:outline-none focus:ring-1 focus:ring-gray-300 cursor-pointer">
                                            <option value="Diajukan" {{ $statusLower == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                            <option value="Dipinjam" {{ $statusLower == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                            <option value="Selesai" {{ in_array($statusLower, ['selesai', 'dikembalikan']) ? 'selected' : '' }}>Selesai</option>
                                            <option value="Telat" {{ $statusLower == 'telat' ? 'selected' : '' }}>Telat</option>
                                        </select>
                                    </form>

                                    @if($masihDipinjam)
                                        <span class="w-8 h-8 flex items-center justify-center rounded-full cursor-not-allowed"
                                            style="background:#f3f4f6; color:#9ca3af;" title="Tidak bisa dihapus, alat masih dipinjam">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg>
                                        </span>
                                    @else
                                        <form action="{{ route('admin.peminjaman.destroy', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-full transition" style="background:#fee2e2; color:#dc2626;" title="Hapus">
                                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-gray-400 text-sm">Belum ada data peminjaman.</td>
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