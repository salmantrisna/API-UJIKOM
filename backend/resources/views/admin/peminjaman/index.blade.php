@extends('layouts.app')

@section('title', 'Kelola Peminjaman')
@section('header_title', 'Manajemen Transaksi Peminjaman')

@section('content')

<!-- Alert Pesan Sukses -->
@if(session('success'))
    <div class="mb-4 p-4 bg-emerald-100 border border-emerald-300 text-emerald-700 rounded-lg text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-lg shadow p-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h2 class="text-xl font-bold text-gray-800">Daftar Transaksi Peminjaman</h2>
        
        <div class="flex items-center gap-3">
            <form action="{{ route('admin.peminjaman.index') }}" method="GET" class="flex gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam / status..." class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
                <button type="submit" class="bg-gray-900 hover:bg-gray-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    Cari
                </button>
            </form>

            <a href="{{ route('admin.peminjaman.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition flex items-center gap-1">
                + Tambah Peminjaman
            </a>
        </div>
    </div>

    <!-- Tabel Data Peminjaman -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                    <th class="py-3 px-4 font-semibold">PEMINJAM</th>
                    <th class="py-3 px-4 font-semibold">ALAT YANG DIPINJAM</th>
                    <th class="py-3 px-4 font-semibold">TGL PINJAM / RENCANA KEMBALI</th>
                    <th class="py-3 px-4 font-semibold">STATUS</th>
                    <th class="py-3 px-4 font-semibold text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($peminjamans as $peminjaman)
                <tr class="hover:bg-gray-50">
                    <!-- Peminjam -->
                    <td class="py-4 px-4 font-medium text-gray-800">
                        {{ $peminjaman->user->name ?? 'N/A' }}
                    </td>

                    <!-- Alat yang dipinjam -->
                    <td class="py-4 px-4 text-gray-600">
                        <ul class="list-disc list-inside space-y-1">
                           @foreach($peminjaman->detailPinjam as $detail)
                                <li>
                                    <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat' }}</span> 
                                    <span class="text-xs text-gray-500">({{ $detail->jumlah }} pcs)</span>
                                </li>
                            @endforeach
                        </ul>
                    </td>

                    <!-- Tanggal -->
                    <td class="py-4 px-4 text-gray-600">
                        <div><span class="text-xs text-gray-400">Pinjam:</span> {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('Y-m-d') }}</div>
                        <div><span class="text-xs text-gray-400">Rencana:</span> {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('Y-m-d') }}</div>
                    </td>

                    <!-- Status Badge -->
                    <td class="py-4 px-4">
                        @if($peminjaman->status == 'Diajukan')
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-3 py-1 rounded-full font-medium">Diajukan</span>
                        @elseif($peminjaman->status == 'Dipinjam')
                            <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-medium">Dipinjam</span>
                        @elseif($peminjaman->status == 'Selesai' || $peminjaman->status == 'Dikembalikan')
                            <span class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-medium">Selesai</span>
                        @elseif($peminjaman->status == 'Telat')
                            <span class="bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full font-medium">Telat</span>
                        @endif
                    </td>

                    <!-- Aksi (Dropdown Status + Tombol Hapus) -->
                    <td class="py-4 px-4 text-center">
                        <div class="flex flex-col items-center gap-2">
                            <!-- Dropdown Ubah Status -->
                            <form action="{{ route('admin.peminjaman.updateStatus', $peminjaman->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="border border-gray-300 rounded px-2 py-1 text-xs bg-white text-gray-700 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 cursor-pointer">
                                    <option value="Diajukan" {{ $peminjaman->status == 'Diajukan' ? 'selected' : '' }}>Diajukan</option>
                                    <option value="Dipinjam" {{ $peminjaman->status == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                    <option value="Selesai" {{ $peminjaman->status == 'Selesai' || $peminjaman->status == 'Dikembalikan' ? 'selected' : '' }}>Selesai</option>
                                    <option value="Telat" {{ $peminjaman->status == 'Telat' ? 'selected' : '' }}>Telat</option>
                                </select>
                            </form>

                            <!-- Tombol Hapus -->
                            <form action="{{ route('admin.peminjaman.destroy', $peminjaman->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-xs font-semibold px-4 py-1 rounded transition w-full">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-gray-400">Belum ada data peminjaman.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $peminjamans->links() }}
    </div>
</div>
@endsection