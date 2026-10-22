<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    // Tampilkan form register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Process registrasi user baru
    public function register(Request $request)
    {
        // VALIDASI INPUT
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // AUTO SET ROLE JADI PEMINJAM
        $validated['role'] = 'peminjam';

        // HASH PASSWORD
        $validated['password'] = Hash::make($validated['password']);

        // BUAT USER BARU
        User::create($validated);

        // REDIRECT KE LOGIN DENGAN PESAN SUKSES
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login dengan email dan password Anda.');
    }
}
