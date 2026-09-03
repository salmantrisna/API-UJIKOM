<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Peminjaman Alat</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        /* Custom Warna Biru Persib khas Maung Bandung */
        .bg-persib-dark {
            background-color: #061838;
        }
        .bg-persib-card {
            background-color: #0c234a;
        }
        .bg-persib-blue {
            background-color: #003399;
        }
        .hover\:bg-persib-blue-hover:hover {
            background-color: #002673;
        }
        .border-persib {
            border-color: #16366f;
        }
        .border-persib-focus:focus {
            border-color: #1d4ed8;
        }
    </style>
</head>
<body class="bg-persib-dark text-slate-100 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-sm">
        
        <!-- Header Logo & Title -->
        <div class="mb-8 text-center">
            <!-- Icon/Badge dengan aksen biru & gold khas Persib -->
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-persib-card border border-persib text-amber-400 mb-3 shadow-lg">
                <i class="fa-solid fa-boxes-stacked text-xl"></i>
            </div>
            <h1 class="text-xl font-bold text-white tracking-tight">Sistem Peminjaman Alat</h1>
            <p class="text-xs text-blue-200/60 mt-1">Masukkan akun Anda untuk melanjutkan</p>
        </div>

        <!-- Alert Error (Jika Ada) -->
        @if(session('error') || $errors->any())
            <div class="mb-5 p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-300 text-xs flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>{{ session('error') ?? 'Email atau password yang Anda masukkan salah.' }}</span>
            </div>
        @endif

        <!-- Card Form -->
        <div class="bg-persib-card border border-persib rounded-2xl p-6 shadow-2xl">
            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-xs font-semibold text-blue-100/80 mb-1.5">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        placeholder="admin@gmail.com" 
                        class="w-full px-3.5 py-2.5 bg-[#061838] border border-persib rounded-xl text-white placeholder-blue-300/30 text-sm focus:outline-none border-persib-focus focus:ring-1 focus:ring-blue-500 transition-all">
                </div>

                <!-- Password Field -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold text-blue-100/80">Password</label>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            placeholder="••••••••" 
                            class="w-full pl-3.5 pr-10 py-2.5 bg-[#061838] border border-persib rounded-xl text-white placeholder-blue-300/30 text-sm focus:outline-none border-persib-focus focus:ring-1 focus:ring-blue-500 transition-all">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-blue-300/50 hover:text-white">
                            <i id="eyeIcon" class="fa-regular fa-eye text-xs"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember Me -->
                <div class="flex items-center pt-1">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-persib text-blue-600 focus:ring-blue-500 focus:ring-offset-[#061838] bg-[#061838]">
                    <label for="remember" class="ml-2 text-xs text-blue-200/70 cursor-pointer select-none">Ingat saya</label>
                </div>

                <!-- Submit Button Biru Persib -->
                <button type="submit" 
                    class="w-full py-3 px-4 bg-persib-blue hover:bg-persib-blue-hover text-white font-semibold text-sm rounded-xl transition-all duration-150 flex items-center justify-center gap-2 shadow-md mt-2">
                    <span>Masuk</span>
                    <i class="fa-solid fa-arrow-right text-xs"></i>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-[11px] text-blue-300/40 mt-6">
            &copy; {{ date('Y') }} Sistem Peminjaman Alat
        </p>

    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>