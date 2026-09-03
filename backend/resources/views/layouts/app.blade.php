<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Sistem')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <div class="w-64 flex flex-col justify-between flex-shrink-0 relative z-20" style="background-color: #0f172a; color: #ffffff;">
            <div>
                <!-- Judul Sidebar Dinamis Berdasarkan Role -->
                <div class="p-5 text-xl font-bold tracking-wider" style="border-bottom: 1px solid #1e293b;">
                    @if(auth()->user()->role === 'admin')
                        PANEL ADMIN
                    @elseif(auth()->user()->role === 'petugas')
                        PANEL PETUGAS
                    @else
                        PANEL USER
                    @endif
                </div>
                
                <nav class="flex-1 p-4 space-y-2">
                    <!-- ================= MENU ADMIN ================= -->
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.dashboard*') ? 'text-white font-medium shadow' : 'text-gray-400 hover:text-white' }}" style="{{ request()->routeIs('admin.dashboard*') ? 'background-color: #1e293b;' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('admin.alat.index') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.alat*') ? 'text-white font-medium shadow' : 'text-gray-400 hover:text-white' }}" style="{{ request()->routeIs('admin.alat*') ? 'background-color: #1e293b;' : '' }}">
                            Kelola Alat
                        </a>
                        <a href="{{ route('admin.kategori.index') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.kategori*') ? 'text-white font-medium shadow' : 'text-gray-400 hover:text-white' }}" style="{{ request()->routeIs('admin.kategori*') ? 'background-color: #1e293b;' : '' }}">
                            Kelola Kategori
                        </a>
                        <a href="{{ route('admin.peminjaman.index') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.peminjaman*') ? 'text-white font-medium shadow' : 'text-gray-400 hover:text-white' }}" style="{{ request()->routeIs('admin.peminjaman*') ? 'background-color: #1e293b;' : '' }}">
                            Kelola Peminjaman
                        </a>
                        <a href="{{ route('admin.pengembalian.index') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.pengembalian*') ? 'text-white font-medium shadow' : 'text-gray-400 hover:text-white' }}" style="{{ request()->routeIs('admin.pengembalian*') ? 'background-color: #1e293b;' : '' }}">
                            Kelola Pengembalian
                        </a>
                        <a href="{{ route('admin.user.index') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('admin.user*') ? 'text-white font-medium shadow' : 'text-gray-400 hover:text-white' }}" style="{{ request()->routeIs('admin.user*') ? 'background-color: #1e293b;' : '' }}">
                            Kelola User
                        </a>
                    @endif

                    <!-- ================= MENU PETUGAS ================= -->
                    @if(auth()->user()->role === 'petugas')
                        <a href="{{ route('petugas.peminjaman.index') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.peminjaman*') ? 'text-white font-medium shadow' : 'text-gray-400 hover:text-white' }}" style="{{ request()->routeIs('petugas.peminjaman*') ? 'background-color: #1e293b;' : '' }}">
                            Persetujuan Peminjaman
                        </a>
                        <a href="{{ route('petugas.pengembalian.index') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.pengembalian*') ? 'text-white font-medium shadow' : 'text-gray-400 hover:text-white' }}" style="{{ request()->routeIs('petugas.pengembalian*') ? 'background-color: #1e293b;' : '' }}">
                            Pemantauan Pengembalian
                        </a>
                        <a href="{{ route('petugas.laporan.index') }}" class="block px-4 py-2 rounded-lg transition {{ request()->routeIs('petugas.laporan*') ? 'text-white font-medium shadow' : 'text-gray-400 hover:text-white' }}" style="{{ request()->routeIs('petugas.laporan*') ? 'background-color: #1e293b;' : '' }}">
                            Cetak Laporan
                        </a>
                    @endif
                </nav>
            </div>
            
            <!-- User Info di Bawah Sidebar -->
            <div class="p-4 text-sm text-gray-400" style="border-top: 1px solid #1e293b;">
                Logged in as: <span class="text-white font-semibold">{{ auth()->user()->name ?? 'User' }}</span>
                <span class="block text-xs text-gray-400 uppercase mt-0.5">Role: {{ auth()->user()->role ?? '-' }}</span>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col overflow-y-auto">
            <!-- Header Topbar -->
            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10">
                <div class="text-lg font-semibold text-gray-800">
                    @yield('header-title', 'Manajemen Sistem')
                </div>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                        Logout
                    </button>
                </form>
            </header>

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>