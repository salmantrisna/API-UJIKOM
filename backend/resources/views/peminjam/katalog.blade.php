@extends('layouts.app')

@section('title', 'Katalog Alat - Peminjam')
@section('header-title', 'Katalog & Pengajuan Peminjaman')

@section('content')
    @if(session('success'))
        <div class="mb-4 rounded p-3 text-sm" style="background:rgba(74,222,128,0.12); border:1px solid rgba(74,222,128,0.35); color:#4ade80;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 rounded p-3 text-sm" style="background:rgba(248,113,113,0.12); border:1px solid rgba(248,113,113,0.35); color:#f87171;">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div class="mb-4 rounded p-3 text-sm" style="background:rgba(248,113,113,0.12); border:1px solid rgba(248,113,113,0.35); color:#f87171;">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('peminjam.peminjaman.ajukan') }}" method="POST" id="form-pinjam">
        @csrf

        <!-- Filter -->
        <div class="flex flex-col md:flex-row gap-3 mb-5">
            <div class="relative flex-1">
                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="search-input" value="{{ $search }}" placeholder="Cari nama alat..."
                    class="w-full pl-9 pr-3 py-2 text-sm rounded text-white placeholder-slate-500 focus:outline-none"
                    style="background:#0f1729; border:1px solid #1e293b;"
                    onfocus="this.style.borderColor='#f2a93b'" onblur="this.style.borderColor='#1e293b'">
            </div>
            <select id="kategori-select" class="px-3 py-2 text-sm rounded text-white focus:outline-none" style="background:#0f1729; border:1px solid #1e293b;">
                <option value="">Semua Kategori</option>
                @foreach($kategoris as $k)
                    <option value="{{ $k->id }}" {{ $kategori_id == $k->id ? 'selected' : '' }}>{{ $k->nama_kategori }}</option>
                @endforeach
            </select>
        </div>

        <!-- Grid katalog -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-32">
            @forelse($alats as $alat)
                @php $habis = $alat->stok < 1; @endphp
                <div class="rounded overflow-hidden transition" style="background:#0f1729; border:1px solid #1e293b;">

                    <!-- Foto -->
                    <div class="h-36 flex items-center justify-center relative" style="background:#131d32;">
                        @if($alat->gambar)
                            <img src="{{ asset('storage/' . $alat->gambar) }}" alt="{{ $alat->nama_alat }}"
                                class="h-full w-full object-cover {{ $habis ? 'grayscale opacity-50' : '' }}">
                        @else
                            <svg class="w-12 h-12 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        @endif

                        @if($habis)
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="px-3 py-1 text-xs font-bold uppercase tracking-widest text-white" style="background:#dc2626;">Stok Habis</span>
                            </div>
                        @endif
                    </div>

                    <!-- Info + pilihan -->
                    <div class="p-4" style="border-top:1px solid #1e293b;">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <h4 class="font-bold text-white text-sm">{{ $alat->nama_alat }}</h4>
                            @if($habis)
                                <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 flex-shrink-0 text-white whitespace-nowrap" style="background:#dc2626;">Stok Habis</span>
                            @else
                                <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 flex-shrink-0 text-slate-900 whitespace-nowrap" style="background:#f2a93b;">Stok {{ $alat->stok }}</span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mb-3">{{ $alat->kategori->nama_kategori ?? '-' }}</p>

                        <label class="flex items-center gap-2 mb-2 {{ $habis ? 'cursor-not-allowed opacity-50' : 'cursor-pointer' }}">
                            <input type="checkbox" class="chk-alat w-4 h-4 rounded"
                                style="accent-color:#f2a93b;"
                                data-id="{{ $alat->id }}" data-nama="{{ $alat->nama_alat }}" data-stok="{{ $alat->stok }}"
                                {{ $habis ? 'disabled' : '' }}>
                            <span class="text-xs font-medium text-slate-300">{{ $habis ? 'Stok tidak tersedia' : 'Pilih alat ini' }}</span>
                        </label>
                        <input type="number" class="input-jumlah w-full text-xs rounded px-2 py-1.5 text-white disabled:text-slate-600"
                            style="background:#0b1120; border:1px solid #1e293b;"
                            data-id="{{ $alat->id }}" min="1" max="{{ max($alat->stok, 1) }}" value="1" disabled placeholder="Jumlah">
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded py-16 text-center" style="background:#0f1729; border:1px dashed #1e293b;">
                    <p class="text-slate-500 font-medium text-sm">Alat tidak ditemukan</p>
                </div>
            @endforelse
        </div>

        <!-- Panel pengajuan (fixed bottom) -->
        <div class="fixed bottom-0 left-0 md:left-64 right-0 p-4 z-30" style="background:#0f1729; border-top:1px solid #1e293b;">
            <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-end gap-3">
                <div class="flex-1 w-full">
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Tanggal Pinjam</label>
                    <input type="date" name="tgl_pinjam" id="tgl_pinjam" required min="{{ date('Y-m-d') }}"
                        class="w-full text-sm rounded px-3 py-2 text-white focus:outline-none"
                        style="background:#0b1120; border:1px solid #1e293b; color-scheme:dark;">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-1">Rencana Kembali</label>
                    <input type="date" name="tgl_kembali_plan" id="tgl_kembali_plan" required min="{{ date('Y-m-d') }}"
                        class="w-full text-sm rounded px-3 py-2 text-white focus:outline-none"
                        style="background:#0b1120; border:1px solid #1e293b; color-scheme:dark;">
                </div>
                <div id="selected-count" class="text-xs text-slate-400 whitespace-nowrap pb-2 md:pb-2.5">0 alat dipilih</div>
                <button type="submit" id="btn-ajukan" disabled
                    class="w-full md:w-auto px-6 py-2.5 rounded text-sm font-semibold transition whitespace-nowrap text-slate-900 disabled:opacity-40 disabled:cursor-not-allowed"
                    style="background:#f2a93b;">
                    Ajukan Peminjaman
                </button>
            </div>
        </div>
    </form>

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