@extends('layouts.app')

@section('title', 'Tambah Peminjaman')
@section('header_title', 'Form Tambah Transaksi Peminjaman')

@section('content')
<div class="max-w-2xl bg-white rounded-lg shadow p-6">

    <!-- Notifikasi Error Validasi -->
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.peminjaman.store') }}" method="POST">
        @csrf

        <!-- Pilih Peminjam -->
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Peminjam (User)</label>
            <select name="user_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- Pilih Peminjam --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
        </div>

        <!-- Tanggal Pinjam & Kembali (Default terisi otomatis untuk menghindari error) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Tanggal Pinjam</label>
                <input type="date" name="tgl_pinjam" value="{{ date('Y-m-d') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Rencana Tanggal Kembali</label>
                <!-- Diberi default +3 hari agar tidak kosong -->
                <input type="date" name="tgl_kembali_plan" value="{{ date('Y-m-d', strtotime('+3 days')) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        <!-- Section Pilih Alat -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Daftar Alat yang Dipinjam</label>
            
            <div id="alat-container" class="space-y-3">
                <div class="flex items-center gap-3 alat-item">
                    <select name="alat_id[]" required class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Alat --</option>
                        @foreach($alats as $alat)
                            <option value="{{ $alat->id }}">{{ $alat->nama_alat }} (Stok: {{ $alat->stok }})</option>
                        @endforeach
                    </select>
                    
                    <input type="number" name="jumlah[]" value="1" min="1" required class="w-20 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    
                    <button type="button" class="btn-remove bg-red-500 hover:bg-red-600 text-white text-xs px-3 py-2.5 rounded-lg transition">
                        ✕
                    </button>
                </div>
            </div>

            <button type="button" id="btn-add-alat" class="mt-3 bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold px-3 py-2 rounded-lg transition">
                + Tambah Alat Lain
            </button>
        </div>

        <!-- Tombol Aksi -->
        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.peminjaman.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                Batal
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Simpan Peminjaman
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('alat-container');
        const btnAdd = document.getElementById('btn-add-alat');

        btnAdd.addEventListener('click', function () {
            const firstRow = container.querySelector('.alat-item');
            const newRow = firstRow.cloneNode(true);
            
            newRow.querySelector('select').value = '';
            newRow.querySelector('input[type="number"]').value = '1';
            
            container.appendChild(newRow);
        });

        container.addEventListener('click', function (e) {
            if (e.target.classList.contains('btn-remove')) {
                const rows = container.querySelectorAll('.alat-item');
                if (rows.length > 1) {
                    e.target.closest('.alat-item').remove();
                } else {
                    alert('Minimal harus memilih 1 alat.');
                }
            }
        });
    });
</script>
@endsection