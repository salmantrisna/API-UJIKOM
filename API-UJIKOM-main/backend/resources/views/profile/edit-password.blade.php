@extends('layouts.app')

@section('title', 'Ubah Password')
@section('header-title', 'Ubah Kata Sandi')

@section('content')
<div class="space-y-5">

    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm" style="background:#dcfce7; color:#166534;">
            <svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl bg-white border border-gray-100 shadow-sm p-6 max-w-md">
        <form action="{{ route('profile.updatePassword') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Password Lama -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Password Saat Ini</label>
                <div class="relative">
                    <input type="password" name="password_lama" id="password_lama" placeholder="Masukkan password saat ini" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password_lama') border-red-500 @enderror" required>
                    <button type="button" onclick="togglePassword('password_lama')" class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password_lama')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password Baru -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Password Baru</label>
                <div class="relative">
                    <input type="password" name="password_baru" id="password_baru" placeholder="Masukkan password baru (min 8 karakter)" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password_baru') border-red-500 @enderror" required>
                    <button type="button" onclick="togglePassword('password_baru')" class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password_baru')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konfirmasi Password -->
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Konfirmasi Password Baru</label>
                <div class="relative">
                    <input type="password" name="password_baru_confirmation" id="password_baru_confirmation" placeholder="Ketik ulang password baru" class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password_baru_confirmation') border-red-500 @enderror" required>
                    <button type="button" onclick="togglePassword('password_baru_confirmation')" class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password_baru_confirmation')
                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info -->
            <div class="rounded-lg p-4" style="background:#fef3c7; color:#92400e;">
                <p class="text-xs font-semibold mb-2">💡 Tips Password Kuat:</p>
                <ul class="text-xs space-y-1">
                    <li>✓ Minimal 8 karakter</li>
                    <li>✓ Campuran huruf besar dan kecil</li>
                    <li>✓ Gunakan angka dan simbol</li>
                </ul>
            </div>

            <!-- Button -->
            <div class="flex gap-3 pt-4">
                <button type="submit" class="flex-1 px-4 py-2.5 rounded-lg text-white text-sm font-semibold transition" style="background:#0f1729;">
                    Ubah Password
                </button>
                <a href="{{ route('profile.show') }}" class="px-4 py-2.5 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>

</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    field.type = field.type === 'password' ? 'text' : 'password';
}
</script>
@endsection
