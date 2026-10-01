@extends('layouts.app')

@section('content')
<div class="space-y-4 max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-800 mb-6">Proses Pengembalian</h2>

        @php
            $plan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $now = \Carbon\Carbon::now()->startOfDay();
            $hariTelat = $now->greaterThan($plan) ? $plan->diffInDays($now) : 0;
            $dendaTelat = $hariTelat * 5000;
        @endphp

        <!-- Info Peminjaman -->
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-5 space-y-3">
            <div>
                <p class="text-xs text-gray-500">Peminjam</p>
                <p class="font-semibold text-gray-800">{{ $peminjaman->user->name ?? 'User Tidak Ditemukan' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Batas Waktu Kembali</p>
                <p class="font-semibold text-gray-800">{{ $plan->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Alat yang Dipinjam</p>
                <ul class="list-disc list-inside text-sm text-gray-800 mt-1">
                    @foreach($peminjaman->detailPinjam as $detail)
                        <li>{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }} — {{ $detail->jumlah }} pcs</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <form action="{{ route('admin.pengembalian.store', $peminjaman->id) }}" method="POST" class="space-y-5">
            @csrf

            <!-- Tanggal Pengembalian -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800 w-full" value="{{ date('Y-m-d') }}" required>
            </div>

            <!-- Kondisi Alat -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Kondisi Alat</label>
                <select name="kondisi_kembali" id="kondisi_kembali" onchange="updateDenda()" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-slate-800 w-full" required>
                    <option value="Baik">Baik</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
            </div>

            <!-- Denda (otomatis, read-only) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Denda (Rp)</label>
                <input type="text" id="denda_display" value="Rp {{ number_format($dendaTelat, 0, ',', '.') }}" readonly
                    class="border border-gray-300 rounded-lg px-4 py-2 text-sm w-full bg-gray-100 text-gray-700 cursor-not-allowed">
                <p class="text-xs text-gray-500 mt-1">
                    @if($hariTelat > 0)
                        Terlambat {{ $hariTelat }} hari dari batas waktu (Rp {{ number_format($dendaTelat, 0, ',', '.') }}).
                    @else
                        Tidak ada keterlambatan.
                    @endif
                    <span id="ket_kerusakan"></span>
                </p>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.pengembalian.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                    Simpan Pengembalian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const dendaTelat = {{ $dendaTelat }};

    function updateDenda() {
        const kondisi = document.getElementById('kondisi_kembali').value;
        let dendaKerusakan = 0;
        let ketKerusakan = '';

        if (kondisi === 'Rusak Ringan') {
            dendaKerusakan = 25000;
            ketKerusakan = ' + Rp 25.000 (Rusak Ringan)';
        } else if (kondisi === 'Rusak Berat') {
            dendaKerusakan = 100000;
            ketKerusakan = ' + Rp 100.000 (Rusak Berat)';
        }

        const total = dendaTelat + dendaKerusakan;
        document.getElementById('denda_display').value = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('ket_kerusakan').textContent = ketKerusakan;
    }
</script>
@endsection