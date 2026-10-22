@extends('layouts.app')

@section('title', 'Persetujuan Peminjaman - Dashboard Petugas')
@section('header-title', 'Daftar Pengajuan Peminjaman Alat')

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

    <!-- Header + Search -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Menunggu Verifikasi Persetujuan</h2>
            <p class="text-sm text-gray-400">{{ $peminjamans->count() }} pengajuan tercatat</p>
        </div>

        <form action="{{ route('petugas.peminjaman.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-80 rounded-full px-4 py-2 bg-white border border-gray-200 shadow-sm">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                class="w-full text-sm bg-transparent focus:outline-none text-gray-700">
            @if(request('search'))
                <a href="{{ route('petugas.peminjaman.index') }}" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </a>
            @endif
        </form>
    </div>

    <!-- Tabel -->
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-gray-400" style="background:#f9fafb; border-bottom:1px solid #f0f0f0;">
                        <th class="py-3 px-5 font-semibold">Peminjam</th>
                        <th class="py-3 px-5 font-semibold">Tanggal</th>
                        <th class="py-3 px-5 font-semibold">Detail Alat</th>
                        <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($peminjamans as $item)
                        @php
                            $st = strtolower($item->status);
                            $menunggu = $st === 'dikembalikan' && !$item->pengembalian;
                        @endphp
                        <tr class="hover:bg-gray-50 transition" style="border-bottom:1px solid #f3f4f6;">

                            {{-- Peminjam --}}
                            <td class="py-4 px-5 align-top">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold" style="background:#0f1729;">
                                        {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $item->user->name ?? 'User Dihapus' }}</span>
                                </div>
                            </td>

                            {{-- Tanggal --}}
                            <td class="py-4 px-5 align-top text-xs text-gray-500 whitespace-nowrap">
                                <div><span class="text-gray-400">Pinjam:</span> {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }}</div>
                                <div><span class="text-gray-400">Rencana:</span> {{ \Carbon\Carbon::parse($item->tgl_kembali_plan)->format('d M Y') }}</div>
                            </td>

                            {{-- Detail Alat --}}
                            <td class="py-4 px-5 align-top text-gray-600">
                                <ul class="space-y-1">
                                    @foreach($item->detailPinjam as $detail)
                                        <li class="flex items-center gap-1.5">
                                            <span class="w-1 h-1 rounded-full bg-gray-300 flex-shrink-0"></span>
                                            <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                            <span class="text-xs text-gray-400">(Jumlah: {{ $detail->jumlah }})</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-4 px-5 align-top">
                                @if($st === 'diajukan')
                                    <div class="flex items-center justify-end gap-2">
                                        <form action="{{ route('petugas.peminjaman.setujui', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Setujui peminjaman alat ini?')"
                                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition" style="background:#dcfce7; color:#166534;">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                                Setujui
                                            </button>
                                        </form>

                                        <form action="{{ route('petugas.peminjaman.setujui', $item->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="ditolak">
                                            <button type="submit" onclick="return confirm('Tolak peminjaman alat ini?')"
                                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition" style="background:#fee2e2; color:#dc2626;">
                                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                                Tolak
                                            </button>
                                        </form>
                                    </div>

                                @elseif($menunggu)
                                    <div class="flex flex-col items-end gap-2">
                                        <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full" style="background:#f3e8ff; color:#7e22ce;">Menunggu Verifikasi</span>

                                        <div class="flex items-center gap-2">
                                            <form action="{{ route('petugas.pengembalian.proses', $item->id) }}" method="POST" class="flex items-center gap-2">
                                                @csrf
                                                <select name="kondisi_kembali" required class="border border-gray-200 rounded-full px-3 py-1.5 text-xs bg-white text-gray-600 focus:outline-none">
                                                    <option value="Baik">Baik</option>
                                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                                    <option value="Rusak Berat">Rusak Berat</option>
                                                </select>
                                                <button type="submit" onclick="return confirm('Setujui pengembalian alat ini?')"
                                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition" style="background:#dcfce7; color:#166534;">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                                    Setujui
                                                </button>
                                            </form>

                                            <form action="{{ route('petugas.pengembalian.tolak', $item->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" onclick="return confirm('Tolak pengajuan pengembalian ini? Alat dianggap belum diserahkan.')"
                                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition" style="background:#fee2e2; color:#dc2626;">
                                                    <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                @else
                                    @php
                                        [$bg, $fg, $label] = match(true) {
                                            $st === 'dipinjam'                         => ['#dbeafe', '#2563eb', 'Dipinjam'],
                                            $st === 'telat'                            => ['#fee2e2', '#dc2626', 'Telat'],
                                            in_array($st, ['dikembalikan', 'selesai']) => ['#dcfce7', '#166534', 'Selesai'],
                                            $st === 'ditolak'                          => ['#f3f4f6', '#4b5563', 'Ditolak'],
                                            default                                    => ['#f3f4f6', '#4b5563', ucfirst($item->status)],
                                        };
                                    @endphp
                                    <div class="flex justify-end">
                                        <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full" style="background:{{ $bg }}; color:{{ $fg }};">{{ $label }}</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-gray-400 text-sm">Tidak ada pengajuan peminjaman baru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection