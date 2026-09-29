<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Sistem')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', ui-sans-serif, sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 3px; }

        .nav-card {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 16px 10px 8px;
            margin-right: 0;
            border-radius: 999px 0 0 999px;
            transition: all .15s ease;
        }
        .nav-card:not(.active) { color: #94a3b8; }
        .nav-card:not(.active):hover { background: rgba(255,255,255,0.05); color: #e2e8f0; }
        .nav-card.active {
            background: #f8fafc;
            color: #0f1729;
            font-weight: 600;
        }
        .nav-icon {
            width: 36px; height: 36px; border-radius: 999px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .nav-card:not(.active) .nav-icon { color: #64748b; }
        .nav-card.active .nav-icon {
            background: #f2a93b; color: #0f1729;
            box-shadow: 0 4px 12px rgba(242,169,59,0.5);
        }
    </style>
</head>
<body class="antialiased">
    <div class="flex h-screen overflow-hidden" style="background:#f8fafc;">

        <!-- ================= SIDEBAR ================= -->
        <aside class="w-64 flex flex-col flex-shrink-0 relative z-20" style="background:#0f1729;">

            <!-- Brand -->
            <div class="flex items-center gap-3 px-6 py-6">
                <div class="w-9 h-9 rounded-2xl flex items-center justify-center flex-shrink-0 text-slate-900 font-bold" style="background:#f2a93b;">
                    🔧
                </div>
                <div>
                    <div class="text-white font-bold text-sm leading-tight">Sistem Peminjaman</div>
                    <div class="text-[11px] text-slate-500">
                        @if(auth()->user()->role === 'admin') Panel Admin
                        @elseif(auth()->user()->role === 'petugas') Panel Petugas
                        @else Panel Peminjam
                        @endif
                    </div>
                </div>
            </div>

            <!-- Nav -->
            <nav class="flex-1 pl-4 py-4 space-y-2 overflow-y-auto">

                @if(auth()->user()->role === 'admin')
                    <div class="pl-4 pb-1 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Menu Utama</div>

                    <a href="{{ route('admin.dashboard') }}" class="nav-card {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5 12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1V9.5Z"/></svg></div>
                        <span class="text-sm">Dashboard</span>
                    </a>
                    <a href="{{ route('admin.alat.index') }}" class="nav-card {{ request()->routeIs('admin.alat*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/></svg></div>
                        <span class="text-sm">Kelola Alat</span>
                    </a>
                    <a href="{{ route('admin.kategori.index') }}" class="nav-card {{ request()->routeIs('admin.kategori*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41 13.42 20.6a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82Z"/><circle cx="7" cy="7" r="1"/></svg></div>
                        <span class="text-sm">Kelola Kategori</span>
                    </a>

                    <div class="pl-4 pb-1 pt-3 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Transaksi</div>

                    <a href="{{ route('admin.peminjaman.index') }}" class="nav-card {{ request()->routeIs('admin.peminjaman*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1"/><path d="M9 13h6M9 17h6"/></svg></div>
                        <span class="text-sm">Kelola Peminjaman</span>
                    </a>
                    <a href="{{ route('admin.pengembalian.index') }}" class="nav-card {{ request()->routeIs('admin.pengembalian*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></div>
                        <span class="text-sm">Kelola Pengembalian</span>
                    </a>

                    <div class="pl-4 pb-1 pt-3 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Lainnya</div>

                    <a href="{{ route('admin.user.index') }}" class="nav-card {{ request()->routeIs('admin.user*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                        <span class="text-sm">Kelola User</span>
                    </a>
                @endif

                @if(auth()->user()->role === 'petugas')
                    <div class="pl-4 pb-1 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Transaksi</div>

                    <a href="{{ route('petugas.peminjaman.index') }}" class="nav-card {{ request()->routeIs('petugas.peminjaman*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
                        <span class="text-sm">Persetujuan Peminjaman</span>
                    </a>
                    <a href="{{ route('petugas.pengembalian.index') }}" class="nav-card {{ request()->routeIs('petugas.pengembalian*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></div>
                        <span class="text-sm">Pemantauan Pengembalian</span>
                    </a>

                    <div class="pl-4 pb-1 pt-3 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Laporan</div>

                    <a href="{{ route('petugas.laporan.index') }}" class="nav-card {{ request()->routeIs('petugas.laporan*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M9 13h6M9 17h6"/></svg></div>
                        <span class="text-sm">Cetak Laporan</span>
                    </a>
                @endif

                @if(auth()->user()->role !== 'admin' && auth()->user()->role !== 'petugas')
                    <div class="pl-4 pb-1 text-[10px] font-bold text-slate-600 uppercase tracking-widest">Menu Utama</div>

                    <a href="{{ route('peminjam.katalog') }}" class="nav-card {{ request()->routeIs('peminjam.katalog*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5M12 22V12"/></svg></div>
                        <span class="text-sm">Katalog Alat</span>
                    </a>
                    <a href="{{ route('peminjam.riwayat') }}" class="nav-card {{ request()->routeIs('peminjam.riwayat*') ? 'active' : '' }}">
                        <div class="nav-icon"><svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg></div>
                        <span class="text-sm">Riwayat & Pengembalian</span>
                    </a>
                @endif
            </nav>

            <!-- User card -->
            <div class="pl-4 py-4">
                <div class="nav-card" style="background:rgba(255,255,255,0.04); color:#e2e8f0; border-radius:999px 0 0 999px;">
                    <div class="nav-icon" style="background:#f2a93b; color:#0f1729;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-[10px] text-slate-500 uppercase">{{ auth()->user()->role ?? '-' }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Logout" class="w-6 h-6 flex items-center justify-center rounded-full text-slate-400 hover:text-red-400 transition flex-shrink-0 mr-2">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- ================= MAIN AREA ================= -->
        <div class="flex-1 flex flex-col overflow-hidden">

            <!-- Topbar -->
            <header class="h-16 flex items-center justify-between px-8 flex-shrink-0 bg-white" style="border-bottom: 1px solid #e5e7eb;">
                <div>
                    <h1 class="text-lg font-bold text-gray-900">@yield('header-title', 'Manajemen Sistem')</h1>
                    <p class="text-xs text-gray-400">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold" style="background:#fef3c7; color:#b45309;">
                        {{ ucfirst(auth()->user()->role ?? '') }}
                    </div>
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-slate-900 text-sm font-bold" style="background:#f2a93b;">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-8" style="background:#f8fafc;">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>