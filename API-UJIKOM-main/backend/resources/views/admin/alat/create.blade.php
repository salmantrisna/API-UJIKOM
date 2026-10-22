@extends('layouts.app')

@section('title', 'Tambah Alat Baru - Panel Admin')

@section('content')
    <!-- Container dibatasi lebarnya (max-w-2xl) dan diletakkan di tengah/kiri sesuai modul -->
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200 max-w-2xl">
        <h3 class="text-base font-bold text-gray-800 mb-6">Tambah Alat Baru</h3>

        <form action="{{ route('admin.alat.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Nama Alat -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Alat</label>
                <input type="text" name="nama_alat" value="{{ old('nama_alat') }}" placeholder="Contoh: Multimeter Digital" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('nama_alat')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <!-- Kategori -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Kategori</label>
                <select name="kategori_id" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori }}
                        </option>
                    @endforeach
                </select>
                @error('kategori_id')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <!-- Stok & Status Kondisi (Dua Kolom) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Stok</label>
                    <input type="number" name="stok" value="{{ old('stok', 1) }}" min="1" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('stok')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Status Kondisi</label>
                    <select name="status_kondisi" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                        <option value="Baik" {{ old('status_kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ old('status_kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ old('status_kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                    @error('status_kondisi')
                        <span class="text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Deskripsi (Opsional) -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi (Opsional)</label>
                <textarea name="deskripsi" rows="3" placeholder="Keterangan tambahan tentang alat..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <!-- Gambar Alat (Opsional) -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Gambar Alat (Opsional)</label>
                <input type="file" name="gambar" accept="image/*"
                    class="block text-sm text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                @error('gambar')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
            </div>

            <!-- Tombol Batal & Simpan di Sudut Kanan Kanan -->
            <div class="flex justify-end items-center gap-2 pt-4">
                <a href="{{ route('admin.alat.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium text-xs px-4 py-2 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium text-xs px-4 py-2 rounded-lg transition">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection