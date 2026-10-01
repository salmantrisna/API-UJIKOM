@extends('layouts.app')

@section('title', 'Kelola User - Panel Admin')
@section('header-title', 'Manajemen Pengguna Sistem')

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

    <!-- Header + Search + Tambah -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Daftar Pengguna Sistem</h2>
            <p class="text-sm text-gray-400">{{ $users->total() }} pengguna terdaftar</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <form action="{{ route('admin.user.index') }}" method="GET" class="flex items-center gap-2 w-full md:w-80 rounded-full px-4 py-2 bg-white border border-gray-200 shadow-sm">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, role..."
                    class="w-full text-sm bg-transparent focus:outline-none text-gray-700">
                @if(request('search'))
                    <a href="{{ route('admin.user.index') }}" class="text-gray-400 hover:text-gray-600 flex-shrink-0">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.user.create') }}"
                class="flex items-center gap-2 text-white text-sm font-semibold px-4 py-2.5 rounded-full transition whitespace-nowrap flex-shrink-0"
                style="background:#0f1729;">
                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                Tambah User
            </a>
        </div>
    </div>

    <!-- Tabel -->
    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] uppercase tracking-wider text-gray-400" style="background:#f9fafb; border-bottom:1px solid #f0f0f0;">
                        <th class="py-3 px-5 font-semibold">Nama</th>
                        <th class="py-3 px-5 font-semibold">Email</th>
                        <th class="py-3 px-5 font-semibold">Role</th>
                        <th class="py-3 px-5 font-semibold">No. HP</th>
                        <th class="py-3 px-5 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($users as $user)
                        @php
                            [$badgeBg, $badgeColor] = match($user->role) {
                                'admin'   => ['#ede9fe', '#7c3aed'],
                                'petugas' => ['#dbeafe', '#2563eb'],
                                default   => ['#dcfce7', '#166534'],
                            };
                            $diriSendiri = $user->id === auth()->id();
                        @endphp
                        <tr class="hover:bg-gray-50 transition" style="border-bottom:1px solid #f3f4f6;">
                            <td class="py-3 px-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold" style="background:#0f1729;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <span class="font-semibold text-gray-900">{{ $user->name }}</span>
                                    @if($diriSendiri)
                                        <span class="px-2 py-0.5 text-[10px] font-semibold rounded-full" style="background:#f3f4f6; color:#6b7280;">Kamu</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3 px-5 text-gray-500">{{ $user->email }}</td>
                            <td class="py-3 px-5">
                                <span class="px-2.5 py-1 text-[11px] font-semibold rounded-full" style="background:{{ $badgeBg }}; color:{{ $badgeColor }};">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="py-3 px-5 text-gray-500">{{ $user->no_hp ?? '-' }}</td>
                            <td class="py-3 px-5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.user.edit', $user->id) }}"
                                        class="w-8 h-8 flex items-center justify-center rounded-full transition"
                                        style="background:#fef3c7; color:#b45309;" title="Edit">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>
                                    </a>

                                    <form action="{{ route('admin.user.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus user {{ e($user->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-8 h-8 flex items-center justify-center rounded-full transition {{ $diriSendiri ? 'opacity-40 cursor-not-allowed' : '' }}"
                                            style="background:#fee2e2; color:#dc2626;"
                                            title="{{ $diriSendiri ? 'Tidak bisa menghapus akun sendiri' : 'Hapus' }}"
                                            {{ $diriSendiri ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-gray-400 text-sm">Belum ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-4" style="border-top:1px solid #f3f4f6;">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection