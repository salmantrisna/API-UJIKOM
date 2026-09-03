@extends('layouts.app')

@section('content')
<div class="space-y-4 max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Tambah / Proses Pengembalian Alat</h2>

        <form action="{{ route('admin.pengembalian.store') }}" method="POST" class="space-y-5">
            @csrf

            <!-- Pilih / Info Peminjaman -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Transaksi Peminjaman</label>
                <select name="peminjaman_id" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800 w-full" required>
                    <option value="">-- Pilih Peminjam / Alat --</option>
                    @foreach($peminjamans ?? [] as $p)
                        <option value="{{ $p->id }}">
                            {{ $p->user->name ?? 'User' }} - ({{ \Carbon\Carbon::parse($p->tgl_pinjam)->format('d-m-Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nama yang Mengembalikan / Petugas -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Pengembali / Penerima</label>
                <input type="text" name="nama_pengembali" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800 w-full" placeholder="Masukkan nama yang mengembalikan..." required>
            </div>

            <!-- Tanggal Pengembalian -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Pengembalian</label>
                <input type="date" name="tgl_kembali" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800 w-full" value="{{ date('Y-m-d') }}" required>
            </div>

            <!-- Denda -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Denda (Jika ada)</label>
                <input type="number" name="denda" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800 w-full" placeholder="0" value="0">
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.pengembalian.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection