<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistem Peminjaman Alat
|--------------------------------------------------------------------------
*/

// Redirect halaman utama ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// ==========================================
// 1. ROUTE GUEST (Belum Login)
// ==========================================
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// ==========================================
// 2. ROUTE AUTHENTICATED (Sudah Login)
// ==========================================
Route::middleware('auth')->group(function () {

    // Process Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --------------------------------------
    // HAK AKSES: ADMIN ONLY
    // --------------------------------------
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // CRUD USER / PENGGUNA
        Route::get('/users', [AdminController::class, 'indexUser'])->name('user.index');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('user.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('user.store');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('user.edit');
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('user.update');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('user.destroy');

        // CRUD KATEGORI ALAT
        Route::get('/kategori', [AdminController::class, 'indexKategori'])->name('kategori.index');
        Route::get('/kategori/create', [AdminController::class, 'createKategori'])->name('kategori.create');
        Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
        Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])->name('kategori.edit');
        Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('kategori.update');
        Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('kategori.destroy');

        // CRUD ALAT (Master Data Barang)
        Route::get('/alat', [AdminController::class, 'indexAlat'])->name('alat.index');
        Route::get('/alat/create', [AdminController::class, 'createAlat'])->name('alat.create');
        Route::post('/alat', [AdminController::class, 'storeAlat'])->name('alat.store');
        Route::get('/alat/{id}/edit', [AdminController::class, 'editAlat'])->name('alat.edit');
        Route::put('/alat/{id}', [AdminController::class, 'updateAlat'])->name('alat.update');
        Route::delete('/alat/{id}', [AdminController::class, 'destroyAlat'])->name('alat.destroy');

        // TRANSAKSI PEMINJAMAN (ADMIN)
        Route::get('/peminjaman', [AdminController::class, 'indexPeminjaman'])->name('peminjaman.index');
        Route::get('/peminjaman/create', [AdminController::class, 'createPeminjaman'])->name('peminjaman.create');
        Route::post('/peminjaman', [AdminController::class, 'storePeminjaman'])->name('peminjaman.store');
        Route::get('/peminjaman/{id}', [AdminController::class, 'showPeminjaman'])->name('peminjaman.show');
        Route::patch('/peminjaman/{id}/status', [AdminController::class, 'updateStatus'])->name('peminjaman.updateStatus');
        Route::delete('/peminjaman/{id}', [AdminController::class, 'destroyPeminjaman'])->name('peminjaman.destroy');

        // TRANSAKSI PENGEMBALIAN (ADMIN)
        Route::get('/pengembalian', [AdminController::class, 'indexPengembalian'])->name('pengembalian.index');
        Route::get('/pengembalian/create/{id?}', [AdminController::class, 'createPengembalian'])->name('pengembalian.create');
        Route::post('/pengembalian/{id}', [AdminController::class, 'storePengembalian'])->name('pengembalian.store');
        Route::get('/pengembalian/{id}/edit', [AdminController::class, 'editPengembalian'])->name('pengembalian.edit');
        Route::put('/pengembalian/{id}', [AdminController::class, 'updatePengembalian'])->name('pengembalian.update');
        Route::delete('/pengembalian/{id}', [AdminController::class, 'destroyPengembalian'])->name('pengembalian.destroy');
    });

    // --------------------------------------
    // HAK AKSES: PETUGAS (Dan Admin)
    // --------------------------------------
    Route::middleware('role:petugas,admin')->prefix('petugas')->name('petugas.')->group(function () {
        // Peminjaman & Persetujuan
        Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])->name('peminjaman.index');
        Route::post('/peminjaman/{id}/setujui', [PetugasController::class, 'setujuiPeminjaman'])->name('peminjaman.setujui');

        // Pengembalian & Denda (Lengkap sesuai modul)
        Route::post('/pengembalian/{id}', [PetugasController::class, 'prosesPengembalian'])->name('pengembalian.proses');
        Route::get('/pengembalian', [PetugasController::class, 'indexPengembalian'])->name('pengembalian.index');

        // LAPORAN PETUGAS (DIMASUKKAN KE DALAM GROUP AGAR NAMANYA SESUAI)
        Route::get('/laporan', [PetugasController::class, 'laporan'])->name('laporan.index');
        Route::get('/laporan/cetak', [PetugasController::class, 'cetakLaporan'])->name('laporan.cetak');
    });

    // --------------------------------------
    // HAK AKSES: PEMINJAM (Siswa/User Biasa)
    // --------------------------------------
    Route::middleware('role:peminjam')->prefix('peminjam')->name('peminjam.')->group(function () {
        // Katalog & Pengajuan
        Route::get('/katalog', [PeminjamController::class, 'katalogAlat'])->name('katalog');
        Route::post('/peminjaman/ajukan', [PeminjamController::class, 'ajukanPeminjaman'])->name('peminjaman.ajukan');
        Route::get('/riwayat', [PeminjamController::class, 'riwayatPeminjaman'])->name('riwayat');
    });

});