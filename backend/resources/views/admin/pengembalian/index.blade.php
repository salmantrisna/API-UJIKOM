@extends('layouts.app')

@section('content')
<div class="space-y-4">
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Header & Search Bar -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-800">Data Transaksi Pengembalian</h2>
            
            <!-- Tombol Tambah & Search Bar -->
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <a href="{{ route('admin.pengembalian.create') }}" class="w-full sm:w-auto text-center bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm">
                    + Proses Pengembalian
                </a>
                <form action="{{ route('admin.pengembalian.index') }}" method="GET" class="flex gap-2 w-full sm:w-auto">
                    <input type="text" name="search" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800 w-64" placeholder="Cari nama peminjam..." value="{{ request('search') }}">
                    <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        Cari
                    </button>
                </form>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200 text-xs font-bold text-gray-700 uppercase tracking-wider">
                        <th class="py-3 px-4">NO</th>
                        <th class="py-3 px-4">NAMA PEMINJAM</th>
                        <th class="py-3 px-4">DAFTAR ALAT</th>
                        <th class="py-3 px-4">TGL PINJAM</th>
                        <th class="py-3 px-4">RENCANA KEMBALI</th>
                        <th class="py-3 px-4">DENDA</th>
                        <th class="py-3 px-4">PETUGAS</th>
                        <th class="py-3 px-4 text-center">STATUS</th>
                        <th class="py-3 px-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($peminjamans as $key => $peminjaman)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-3 px-4 font-medium text-gray-600">{{ $peminjamans->firstItem() + $key }}</td>
                            <td class="py-3 px-4 font-bold text-gray-800">{{ $peminjaman->user->name ?? 'User Tidak Ditemukan' }}</td>
                            <td class="py-3 px-4">
                                <ul class="space-y-1">
                                    @foreach($peminjaman->detailPinjam as $detail)
                                        <span class="text-gray-700 block">
                                            • {{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} <span class="font-bold">({{ $detail->jumlah }})</span>
                                        </span>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('Y-m-d') }}</td>
                            <td class="py-3 px-4 text-gray-600 whitespace-nowrap">{{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('Y-m-d') }}</td>
                            <td class="py-3 px-4 font-semibold whitespace-nowrap">
                                @php
                                    $denda = $peminjaman->denda ?? 0;
                                    $statusLower = strtolower($peminjaman->status);
                                    if($denda == 0 && in_array($statusLower, ['dipinjam', 'telat'])) {
                                        $plan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);
                                        $now = \Carbon\Carbon::now();
                                        if($now->greaterThan($plan)) {
                                            $daysLate = $plan->diffInDays($now);
                                            $denda = $daysLate * 5000;
                                        }
                                    }
                                @endphp
                                <span class="{{ $denda > 0 ? 'text-red-600' : 'text-gray-600' }}">
                                    {{ $denda > 0 ? 'Rp ' . number_format($denda, 0, ',', '.') : 'Rp 0' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-600">
                                {{ $peminjaman->petugas->name ?? 'Admin' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($statusLower == 'dipinjam')
                                    <span class="inline-block bg-green-100 text-green-700 font-semibold text-xs px-3 py-1 rounded-full">Dipinjam</span>
                                @elseif(in_array($statusLower, ['selesai', 'dikembalikan']))
                                    <span class="inline-block bg-blue-100 text-blue-700 font-semibold text-xs px-3 py-1 rounded-full">Dikembalikan</span>
                                @elseif($statusLower == 'telat')
                                    <span class="inline-block bg-red-100 text-red-700 font-semibold text-xs px-3 py-1 rounded-full">Telat</span>
                                @else
                                    <span class="inline-block bg-gray-100 text-gray-700 font-semibold text-xs px-3 py-1 rounded-full">{{ ucfirst($peminjaman->status) }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex flex-col items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.pengembalian.edit', $peminjaman->id) }}" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs px-4 py-1 rounded-md transition shadow-sm w-20 text-center">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.pengembalian.destroy', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="w-20">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold text-xs px-4 py-1 rounded-md transition shadow-sm w-full">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-gray-500 italic">Tidak ada data transaksi pengembalian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $peminjamans->links() }}
        </div>
    </div>
</div>
@endsection