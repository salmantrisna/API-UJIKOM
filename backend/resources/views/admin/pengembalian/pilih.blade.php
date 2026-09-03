@extends('layouts.app')
@section('title', 'Pilih Peminjaman')
@section('header-title', 'Pilih Peminjaman untuk Dikembalikan')

@section('content')

<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Peminjaman Aktif (Belum Dikembalikan)</h2>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                    <th class="py-3 px-4 font-semibold">PEMINJAM</th>
                    <th class="py-3 px-4 font-semibold">ALAT DIPINJAM</th>
                    <th class="py-3 px-4 font-semibold">TGL PINJAM / RENCANA KEMBALI</th>
                    <th class="py-3 px-4 font-semibold">STATUS</th>
                    <th class="py-3 px-4 font-semibold text-center">AKSI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm">
                @forelse($peminjamans as $peminjaman)
                <tr class="hover:bg-gray-50">
                    <td class="py-4 px-4 font-medium text-gray-800">
                        {{ $peminjaman->user->name ?? 'N/A' }}
                    </td>

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

                    <td class="py-4 px-4 text-gray-600">
                        <div><span class="text-xs text-gray-400">Pinjam:</span> {{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->format('Y-m-d') }}</div>
                        <div><span class="text-xs text-gray-400">Rencana:</span> {{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->format('Y-m-d') }}</div>
                    </td>

                    <td class="py-4 px-4">
                        @if($peminjaman->status == 'telat')
                            <span class="bg-red-100 text-red-800 text-xs px-3 py-1 rounded-full font-medium">Telat</span>
                        @else
                            <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-medium">Dipinjam</span>
                        @endif
                    </td>

                    <td class="py-4 px-4 text-center">
                        <a href="{{ route('admin.pengembalian.create', $peminjaman->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2 rounded-lg transition">
                            Proses Kembali
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-6 text-center text-gray-400">Tidak ada peminjaman aktif yang perlu dikembalikan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection