@extends('layouts.app')

@section('title', 'Proses Pengembalian')
@section('header-title', 'Proses Pengembalian Alat')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">

    @if($errors->any())
        <div class="flex items-start gap-3 rounded-2xl px-4 py-3 text-sm" style="background:#fee2e2; color:#dc2626;">
            <svg class="w-5 h-5 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg>
            <div>
                @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    @php
        $plan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
        $now = \Carbon\Carbon::now()->startOfDay();
        $hariTelat = $now->greaterThan($plan) ? $plan->diffInDays($now) : 0;
        $dendaTelat = $hariTelat * 5000;
    @endphp

    <!-- Info Peminjaman -->
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
        <h3 class="text-base font-bold text-gray-900 mb-4">Informasi Peminjaman</h3>

        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-white text-sm font-bold" style="background:#0f1729;">
                {{ strtoupper(substr($peminjaman->user->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <p class="font-semibold text-gray-900">{{ $peminjaman->user->name ?? 'User Tidak Ditemukan' }}</p>
                <p class="text-xs text-gray-400">
                    Batas kembali: <span class="font-medium text-gray-600">{{ $plan->format('d M Y') }}</span>
                    @if($hariTelat > 0)
                        <span class="ml-1 px-2 py-0.5 text-[10px] font-semibold rounded-full" style="background:#fee2e2; color:#dc2626;">Telat {{ $hariTelat }} hari</span>
                    @endif
                </p>
            </div>
        </div>

        <div class="rounded-2xl p-4" style="background:#f9fafb;">
            <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-2">Alat yang Dipinjam</p>
            <ul class="space-y-1.5">
                @foreach($peminjaman->detailPinjam as $detail)
                    <li class="flex items-center gap-1.5 text-sm">
                        <span class="w-1 h-1 rounded-full bg-gray-300 flex-shrink-0"></span>
                        <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                        <span class="text-xs text-gray-400 whitespace-nowrap">({{ $detail->jumlah }} pcs)</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Form -->
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6">
        <h3 class="text-base font-bold text-gray-900 mb-4">Data Pengembalian</h3>

        <form action="{{ route('admin.pengembalian.store', $peminjaman->id) }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tanggal Pengembalian -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Tanggal Kembali</label>
                    <input type="date" name="tgl_kembali" value="{{ date('Y-m-d') }}" required
                        class="w-full text-sm border border-gray-200 rounded-full px-4 py-2.5 bg-white focus:outline-none focus:ring-1 focus:ring-gray-300">
                </div>

                <!-- Kondisi Alat -->
                <div>
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Kondisi Alat</label>
                    <select name="kondisi_kembali" id="kondisi_kembali" onchange="updateDenda()" required
                        class="w-full text-sm border border-gray-200 rounded-full px-4 py-2.5 bg-white focus:outline-none focus:ring-1 focus:ring-gray-300">
                        <option value="Baik">Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
            </div>

            <!-- Denda (otomatis, read-only) -->
            <div>
                <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Denda (Rp)</label>
                <input type="text" id="denda_display" value="Rp {{ number_format($dendaTelat, 0, ',', '.') }}" readonly
                    class="w-full text-sm font-semibold rounded-full px-4 py-2.5 cursor-not-allowed focus:outline-none"
                    style="background:#f3f4f6; color:#dc2626; border:1px solid #f3f4f6;">
                <p class="text-xs text-gray-400 mt-1.5">
                    @if($hariTelat > 0)
                        Terlambat {{ $hariTelat }} hari dari batas waktu (Rp {{ number_format($dendaTelat, 0, ',', '.') }}).
                    @else
                        Tidak ada keterlambatan.
                    @endif
                    <span id="ket_kerusakan"></span>
                </p>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-3 pt-4" style="border-top:1px solid #f3f4f6;">
                <a href="{{ route('admin.pengembalian.index') }}"
                    class="text-gray-600 text-sm font-semibold px-5 py-2.5 rounded-full transition whitespace-nowrap" style="background:#f3f4f6;">
                    Batal
                </a>
                <button type="submit"
                    class="flex items-center gap-2 text-white text-sm font-semibold px-5 py-2.5 rounded-full transition whitespace-nowrap" style="background:#0f1729;">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
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