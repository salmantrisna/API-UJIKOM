<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Sistem Peminjaman Alat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', ui-sans-serif, sans-serif; }
    </style>
</head>
<body class="antialiased bg-gray-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md">
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center mx-auto mb-4 text-white text-xl font-bold" style="background:#0f1729;">
                        🔧
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Registrasi</h1>
                    <p class="text-sm text-gray-500 mt-2">Buat akun baru untuk sistem peminjaman alat</p>
                </div>

                <!-- Form -->
                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" 
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" 
                            required>
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email" 
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                            required>
                        @error('email')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>



                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" name="password" id="password" placeholder="Minimal 8 karakter" 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror" 
                                required>
                            <button type="button" onclick="togglePassword('password')" class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-900 mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Ketik ulang password" 
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password_confirmation') border-red-500 @enderror" 
                                required>
                            <button type="button" onclick="togglePassword('password_confirmation')" class="absolute right-3 top-2.5 text-gray-500 hover:text-gray-700">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Info Tips -->
                    <div class="rounded-lg p-4 mt-6" style="background:#fef3c7; color:#92400e;">
                        <p class="text-xs font-semibold mb-2">💡 Tips Password Kuat:</p>
                        <ul class="text-xs space-y-1">
                            <li>✓ Minimal 8 karakter</li>
                            <li>✓ Gunakan huruf, angka, dan simbol</li>
                            <li>✓ Mudah diingat tapi sulit ditebak</li>
                        </ul>
                    </div>

                    <!-- Button -->
                    <button type="submit" class="w-full px-4 py-2.5 rounded-lg text-white text-sm font-semibold transition mt-6" style="background:#0f1729;">
                        Registrasi
                    </button>

                    <!-- Link ke Login -->
                    <p class="text-center text-sm text-gray-600 mt-4">
                        Sudah punya akun? 
                        <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Login di sini</a>
                    </p>
                </form>
            </div>

            <!-- Footer -->
            <p class="text-center text-xs text-gray-500 mt-6">© 2026 Sistem Peminjaman Alat. All rights reserved.</p>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
