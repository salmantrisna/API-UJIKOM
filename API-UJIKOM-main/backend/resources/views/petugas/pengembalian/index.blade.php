@extends('layouts.app')

@section('title', 'Pemantauan Pengembalian - Dashboard Petugas')
@section('header-title', 'Pemantauan & Proses Pengembalian Alat')

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
            <h2 class="text-xl font-bold text-gray-900">Daftar Peminjaman Aktif</h2>
            <p class="text-sm text-gray-400">{{ $peminjamans->count() }} peminjaman belum dikembalikan</p>
        </div>

        <form action="{{ route('petugas.pengembalian.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-80 rounded-full px-4 py-2 bg-white border border-gray-200 shadow-sm">
            <svg class="w-4 h-4 text-gray-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama peminjam..."
                class="w-full text-sm bg-transparent focus:outline-none text-gray-700">
            @if(request('search'))
                <a href="{{ route('petugas.pengembalian.index') }}" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </a>
            @endif
        </form>
    </div>

    <!-- List Card -->
    <div class="space-y-3">
        @forelse($peminjamans as $item)
            @php
                $tglPlan = \Carbon\Carbon::parse($item->tgl_kembali_plan ?? $item->tanggal_kembali)->startOfDay();
                $tglSekarang = \Carbon\Carbon::now()->startOfDay();
                $hariTelat = $tglSekarang->greaterThan($tglPlan) ? $tglPlan->diffInDays($tglSekarang) : 0;
                $dendaTelat = $hariTelat * 5000;

                // Kalau peminjam sudah lapor kondisi, pakai itu sebagai default dropdown & hitung preview kerusakan
                $kondisiDefault = $item->kondisi_kembali ?? 'Baik';
                $dendaKerusakanAwal = match($kondisiDefault) {
                    'Rusak Ringan' => 25000,
                    'Rusak Berat' => 100000,
                    default => 0,
                };
                $dendaPreview = $dendaTelat + $dendaKerusakanAwal;
            @endphp
            <div class="rounded-2xl bg-white border p-5" style="{{ $item->status == 'menunggu_verifikasi' ? 'border-color:#f59e0b; box-shadow:0 0 0 1px #f59e0b;' : 'border-color:#f3f4f6;' }}">
                <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-5">

                    <!-- Info kiri -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-3 mb-3 flex-wrap">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold" style="background:#0f1729;">
                                {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900">{{ $item->user->name ?? 'User Dihapus' }}</p>
                                <p class="text-xs text-gray-400">
                                    Pinjam {{ \Carbon\Carbon::parse($item->tgl_pinjam)->format('d M Y') }} · Rencana {{ \Carbon\Carbon::parse($item->tgl_kembali_plan ?? $item->tanggal_kembali)->format('d M Y') }}
                                </p>
                            </div>

                            <!-- Badge Status -->
                            @if($item->status == 'menunggu_verifikasi')
                                <span class="ml-1 px-2.5 py-1 text-[11px] font-semibold rounded-full flex items-center gap-1" style="background:#fef3c7; color:#b45309;">
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v4l3 3"/><circle cx="12" cy="12" r="10"/></svg>
                                    Menunggu Verifikasi
                                </span>
                            @elseif($item->status == 'telat')
                                <span class="ml-1 px-2.5 py-1 text-[11px] font-semibold rounded-full" style="background:#fee2e2; color:#dc2626;">
                                    Telat
                                </span>
                            @else
                                <span class="ml-1 px-2.5 py-1 text-[11px] font-semibold rounded-full" style="background:#dbeafe; color:#2563eb;">
                                    Dipinjam
                                </span>
                            @endif
                        </div>

                        <ul class="space-y-1 pl-1">
                            @foreach($item->detailPinjam as $detail)
                                <li class="flex items-center gap-1.5 text-sm">
                                    <span class="w-1 h-1 rounded-full bg-gray-300 flex-shrink-0"></span>
                                    <span class="font-medium text-gray-800">{{ $detail->alat->nama_alat ?? 'Alat Dihapus' }}</span>
                                    <span class="text-xs text-gray-400">(Jumlah: {{ $detail->jumlah }})</span>
                                </li>
                            @endforeach
                        </ul>

                        @if($item->status == 'menunggu_verifikasi' && $item->kondisi_kembali)
                            <p class="text-xs mt-2 flex items-center gap-1.5" style="color:#b45309;">
                                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"/><path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"/></svg>
                                Dilaporkan peminjam: <strong>{{ $item->kondisi_kembali }}</strong>
                            </p>
                        @endif
                    </div>

                    <!-- Form Aksi Kanan -->
                    <form action="{{ route('petugas.pengembalian.proses', $item->id) }}" method="POST"
                        class="flex flex-col sm:flex-row lg:flex-col gap-3 rounded-2xl p-4 w-full lg:w-72 flex-shrink-0" style="background:#f9fafb;">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Kondisi Kembali</label>
                            <select name="kondisi_kembali" required onchange="updateDenda{{ $item->id }}()" id="kondisi-{{ $item->id }}" class="w-full text-sm border border-gray-200 rounded-full px-3 py-2 bg-white focus:outline-none focus:ring-1 focus:ring-gray-300">
                                <option value="Baik" {{ $kondisiDefault == 'Baik' ? 'selected' : '' }}>Baik</option>
                                <option value="Rusak Ringan" {{ $kondisiDefault == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                <option value="Rusak Berat" {{ $kondisiDefault == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                            </select>
                        </div>

                        <div class="flex-1">
                            <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">
                                Denda @if($hariTelat > 0)<span style="color:#dc2626;">(telat {{ $hariTelat }} hari)</span>@endif
                            </label>
                            <div id="denda-{{ $item->id }}" class="w-full text-sm rounded-full px-3 py-2 font-semibold" style="background:#f3f4f6; color:{{ $dendaPreview > 0 ? '#dc2626' : '#6b7280' }};">
                                Rp {{ number_format($dendaPreview, 0, ',', '.') }}
                            </div>
                        </div>

                        <button type="submit" onclick="return confirm('Proses pengembalian alat ini?')"
                            class="flex items-center justify-center gap-2 text-white text-sm font-semibold px-4 py-2.5 rounded-full transition" style="background:#0f1729;">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                            Terima Pengembalian
                        </button>
                    </form>

                </div>
            </div>

            <script>
                function updateDenda{{ $item->id }}() {
                    const kondisi = document.getElementById('kondisi-{{ $item->id }}').value;
                    const dendaTelat = {{ $dendaTelat }};
                    let dendaKerusakan = 0;
                    if (kondisi === 'Rusak Ringan') dendaKerusakan = 25000;
                    if (kondisi === 'Rusak Berat') dendaKerusakan = 100000;
                    const total = dendaTelat + dendaKerusakan;
                    const el = document.getElementById('denda-{{ $item->id }}');
                    el.textContent = 'Rp ' + total.toLocaleString('id-ID');
                    el.style.color = total > 0 ? '#dc2626' : '#6b7280';
                }
            </script>
        @empty
            <div class="rounded-2xl bg-white border border-gray-100 shadow-sm py-10 text-center text-gray-400 text-sm">
                Tidak ada peminjaman yang sedang aktif saat ini.
            </div>
        @endforelse
    </div>

</div>
@endsection