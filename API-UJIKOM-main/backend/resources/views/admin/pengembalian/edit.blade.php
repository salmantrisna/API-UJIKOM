@extends('layouts.app')

@section('title', 'Edit Pengembalian')
@section('header-title', 'Edit Data Pengembalian')

@section('content')

<div class="bg-white rounded-lg shadow p-6 max-w-xl">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Edit Pengembalian</h2>

    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
        <p class="text-sm text-gray-500 mb-1">Peminjam</p>
        {{-- Diubah langsung ke $pengembalian->user menggunakan safe operator --}}
        <p class="font-semibold text-gray-800 mb-3">{{ $pengembalian->user?->name ?? 'N/A' }}</p>

        <p class="text-sm text-gray-500 mb-1">Alat</p>
        <ul class="list-disc list-inside text-sm text-gray-700">
            {{-- Diubah langsung ke $pengembalian->detailPinjam --}}
            @forelse($pengembalian->detailPinjam as $detail)
                <li>{{ $detail->alat?->nama_alat ?? 'Alat' }} — {{ $detail->jumlah }} pcs</li>
            @empty
                <li>Tidak ada detail alat</li>
            @endforelse
        </ul>
    </div>

    @if($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.pengembalian.update', $pengembalian->id) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kembali</label>
            <input type="date" name="tgl_kembali" value="{{ old('tgl_kembali', $pengembalian->tgl_kembali_real ? \Carbon\Carbon::parse($pengembalian->tgl_kembali_real)->format('Y-m-d') : date('Y-m-d')) }}"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Denda (Rp)</label>
            <input type="number" name="denda" value="{{ old('denda', $pengembalian->denda) }}" min="0"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-6 py-2 rounded-lg transition">
                Update
            </button>
            <a href="{{ route('admin.pengembalian.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-6 py-2 rounded-lg transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection