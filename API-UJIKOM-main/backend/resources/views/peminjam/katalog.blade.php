@extends('layouts.app')

@section('title', 'Katalog Alat - Peminjam')
@section('header-title', 'Katalog & Pengajuan Peminjaman')

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
    @if($errors->any())
        <div class="rounded-2xl px-4 py-3 text-sm" style="background:#fee2e2; color:#dc2626;">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST" id="form-pinjam">
        @csrf

        <!-- Header + Filter -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-5">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Katalog Alat</h2>
                <p class="text-sm text-gray-400">{{ $alats->count() }} alat ditemukan · centang alat yang ingin dipinjam</p>
            </div>

            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full md:w-auto">
                <div class="flex items-center gap-2 w-full md:w-72 rounded-full px-4 py-2 bg-white border border-gray-200 shadow-sm">
                    <svg class="w-4 h-4 text-gray-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" id="search-input" value="{{ $search }}" placeholder="Cari nama alat, lalu Enter..."
                        class="w-full text-sm bg-transparent focus:outline-none text-gray-700">
                </div>
                <select id="kategori-select" class="text-sm border border-gray-200 rounded-full px-4 py-2.5 bg-white text-gray-700 shadow-sm focus:outline-none focus:ring-1 focus:ring-gray-300">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $k)
                        <option value="{{ $k->id }}" {{ $kategori_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Grid katalog -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pb-40">
            @forelse($alats as $alat)
                @php $habis = $alat->stok < 1; @endphp
                <div id="card-{{ $alat->id }}" class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden transition" style="{{ $habis ? 'opacity:.85;' : '' }}">

                    <!-- Foto -->
                    <div class="h-40 flex items-center justify-center relative" style="background:#f3f4f6;">
                        @if($alat->gambar)
                            <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}"
                                class="h-full w-full object-cover {{ $habis ? 'grayscale opacity-50' : '' }}">
                        @else
                            <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @endif

                        @if($habis)
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="px-3 py-1 text-xs font-bold uppercase tracking-widest text-white rounded-full" style="background:#dc2626;">Stok Habis</span>
                            </div>
                        @endif
                    </div>

                    <!-- Info + pilihan -->
                    <div class="p-4" style="border-top:1px solid #f3f4f6;">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h4 class="font-bold text-gray-900 text-sm">{{ $alat->nama_alat }}</h4>
                            @if($habis)
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full flex-shrink-0 whitespace-nowrap" style="background:#fee2e2; color:#dc2626;">Habis</span>
                            @else
                                <span class="text-[10px] font-bold uppercase px-2 py-0.5 rounded-full flex-shrink-0 whitespace-nowrap" style="background:#fef3c7; color:#b45309;">Stok {{ $alat->stok }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mb-3">{{ $alat->kategori->nama_kategori ?? '-' }}</p>

                        <label class="flex items-center gap-2 mb-2 {{ $habis ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
                            <input type="checkbox" class="chk-alat w-4 h-4 rounded"
                                style="accent-color:#0f1729;"
                                data-id="{{ $alat->id }}" data-nama="{{ $alat->nama_alat }}" data-stok="{{ $alat->stok }}"
                                {{ $habis ? 'disabled' : '' }}>
                            <span class="text-xs font-medium text-gray-600">{{ $habis ? 'Stok tidak tersedia' : 'Pilih alat ini' }}</span>
                        </label>

                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wide">Jumlah</span>
                            <input type="number" class="input-jumlah w-24 text-sm rounded-full px-3 py-1.5 text-gray-800 border border-gray-200 bg-white focus:outline-none focus:ring-1 focus:ring-gray-300 disabled:bg-gray-100 disabled:text-gray-400"
                                data-id="{{ $alat->id }}" min="1" max="{{ max($alat->stok, 1) }}" value="1" disabled placeholder="1">
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-2xl bg-white border border-gray-100 shadow-sm py-16 text-center">
                    <p class="text-gray-500 font-medium text-sm">Alat tidak ditemukan</p>
                    <p class="text-gray-400 text-xs mt-1">Coba kata kunci atau kategori lain.</p>
                </div>
            @endforelse
        </div>

        <!-- Panel pengajuan (fixed bottom) -->
        <div class="fixed bottom-0 left-0 md:left-64 right-0 p-4 z-30 bg-white" style="border-top:1px solid #e5e7eb; box-shadow:0 -4px 12px rgba(0,0,0,0.05);">
            <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-end gap-3">
                <div class="flex-1 w-full">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Tanggal Pinjam</label>
                    <input type="date" name="tgl_pinjam" id="tgl_pinjam" required min="{{ date('Y-m-d') }}"
                        class="w-full text-sm border border-gray-200 rounded-full px-4 py-2.5 bg-white focus:outline-none focus:ring-1 focus:ring-gray-300">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-[11px] font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Rencana Kembali</label>
                    <input type="date" name="tgl_kembali_plan" id="tgl_kembali_plan" required min="{{ date('Y-m-d') }}"
                        class="w-full text-sm border border-gray-200 rounded-full px-4 py-2.5 bg-white focus:outline-none focus:ring-1 focus:ring-gray-300">
                </div>
                <div id="selected-count" class="text-xs font-semibold text-gray-500 whitespace-nowrap pb-2 md:pb-2.5">0 alat dipilih</div>
                <button type="submit" id="btn-ajukan" disabled
                    class="w-full md:w-auto px-6 py-2.5 rounded-full text-sm font-semibold transition whitespace-nowrap text-white disabled:cursor-not-allowed"
                    style="background:#0f1729;">
                    Ajukan Peminjaman
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    /* Tombol ajukan: redup saat belum ada alat dipilih */
    #btn-ajukan:disabled { background:#d1d5db !important; color:#6b7280; }
    /* Kartu alat yang dipilih diberi garis tepi gelap */
    .card-terpilih { border-color:#0f1729 !important; box-shadow:0 0 0 1px #0f1729; }
</style>

<script>
    // Filter search & kategori (reload dengan query string)
    function applyFilter() {
        const search = document.getElementById('search-input').value;
        const kategori_id = document.getElementById('kategori-select').value;
        const params = new URLSearchParams();
        if (search) params.set('search', search);
        if (kategori_id) params.set('kategori_id', kategori_id);
        window.location.href = "{{ route('peminjam.katalog') }}?" + params.toString();
    }
    document.getElementById('kategori-select').addEventListener('change', applyFilter);
    document.getElementById('search-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); applyFilter(); }
    });

    // Rencana kembali tidak boleh lebih awal dari tanggal pinjam
    const tglPinjam = document.getElementById('tgl_pinjam');
    const tglKembali = document.getElementById('tgl_kembali_plan');
    tglPinjam.addEventListener('change', function () {
        if (!this.value) return;
        tglKembali.min = this.value;
        if (tglKembali.value && tglKembali.value < this.value) tglKembali.value = this.value;
    });

    // Checkbox -> aktifkan input jumlah & buat hidden input untuk submit
    const form = document.getElementById('form-pinjam');
    const checkboxes = document.querySelectorAll('.chk-alat');
    const btnAjukan = document.getElementById('btn-ajukan');
    const selectedCount = document.getElementById('selected-count');

    function updateState() {
        let count = 0;
        form.querySelectorAll('input[type=hidden].dynamic-field').forEach(el => el.remove());

        checkboxes.forEach(chk => {
            const id = chk.dataset.id;
            const stok = parseInt(chk.dataset.stok) || 0;
            const jumlahInput = document.querySelector(`.input-jumlah[data-id="${id}"]`);
            jumlahInput.disabled = !chk.checked;

            const card = document.getElementById('card-' + id);
            if (card) card.classList.toggle('card-terpilih', chk.checked && stok > 0);

            if (chk.checked && stok > 0) {
                // Batasi jumlah antara 1 dan stok
                let val = parseInt(jumlahInput.value) || 1;
                if (val < 1) val = 1;
                if (val > stok) val = stok;
                jumlahInput.value = val;

                count++;
                const hiddenId = document.createElement('input');
                hiddenId.type = 'hidden';
                hiddenId.name = 'alat_id[]';
                hiddenId.value = id;
                hiddenId.classList.add('dynamic-field');
                form.appendChild(hiddenId);

                const hiddenJumlah = document.createElement('input');
                hiddenJumlah.type = 'hidden';
                hiddenJumlah.name = 'jumlah[]';
                hiddenJumlah.value = val;
                hiddenJumlah.classList.add('dynamic-field');
                form.appendChild(hiddenJumlah);
            }
        });

        selectedCount.textContent = count + ' alat dipilih';
        btnAjukan.disabled = count === 0;
    }

    checkboxes.forEach(chk => chk.addEventListener('change', updateState));
    document.querySelectorAll('.input-jumlah').forEach(inp => inp.addEventListener('input', updateState));
</script>
@endsection