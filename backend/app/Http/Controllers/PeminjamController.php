<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // Katalog: semua alat tampil, yang stoknya habis ditaruh paling bawah
    public function katalogAlat(Request $request)
    {
        $search = $request->input('search');
        $kategori_id = $request->input('kategori_id');

        $alats = Alat::with('kategori')
            ->when($search, fn ($q) => $q->where('nama_alat', 'like', "%{$search}%"))
            ->when($kategori_id, fn ($q) => $q->where('kategori_id', $kategori_id))
            ->orderByRaw('stok < 1')
            ->orderBy('nama_alat')
            ->get();

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('peminjam.katalog', compact('alats', 'kategoris', 'search', 'kategori_id'));
    }

    // Pengajuan peminjaman (banyak alat sekaligus)
    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_pinjam'       => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id'          => 'required|array|min:1',
            'alat_id.*'        => 'distinct|exists:alat,id',
            'jumlah'           => 'required|array|size:' . count((array) $request->alat_id),
            'jumlah.*'         => 'integer|min:1',
        ], [
            'alat_id.required' => 'Pilih minimal satu alat untuk diajukan.',
            'alat_id.*.distinct' => 'Ada alat yang dipilih dua kali.',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $peminjaman = Peminjaman::create([
                    'user_id'          => Auth::id(),
                    'tgl_pinjam'       => $request->tgl_pinjam,
                    'tgl_kembali_plan' => $request->tgl_kembali_plan,
                    'status'           => 'diajukan',
                ]);

                foreach ($request->alat_id as $index => $alatId) {
                    $jumlah = (int) ($request->jumlah[$index] ?? 1);

                    // Cek stok di sisi server (max di HTML bisa diakali lewat browser)
                    $alat = Alat::lockForUpdate()->findOrFail($alatId);

                    if ($alat->stok < 1) {
                        throw new \RuntimeException("Stok {$alat->nama_alat} sedang habis.");
                    }
                    if ($alat->stok < $jumlah) {
                        throw new \RuntimeException("Stok {$alat->nama_alat} tidak mencukupi (tersisa {$alat->stok}).");
                    }

                    DetailPinjam::create([
                        'peminjaman_id' => $peminjaman->id,
                        'alat_id'       => $alatId,
                        'jumlah'        => $jumlah,
                    ]);
                }

                LogAktivitas::create([
                    'user_id'   => Auth::id(),
                    'aktivitas' => 'Mengajukan peminjaman baru (ID: ' . $peminjaman->id . ')',
                ]);
            });
        } catch (\RuntimeException $e) {
            return redirect()->back()->withInput()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        } catch (\Throwable $e) {
            report($e);
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat mengajukan peminjaman. Coba lagi.');
        }

        return redirect()->route('peminjam.riwayat')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim, menunggu persetujuan petugas.');
    }

    // Riwayat peminjaman milik user yang login
    public function riwayatPeminjaman()
    {
        $peminjamans = Peminjaman::with(['detailPinjam.alat', 'pengembalian'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('peminjam.riwayat', compact('peminjamans'));
    }

    // Peminjam menandai alat sudah dikembalikan secara fisik (menunggu verifikasi petugas)
    public function ajukanPengembalian(Request $request, $id)
{
    $request->validate([
        'kondisi_kembali' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
    ]);

    $peminjaman = Peminjaman::where('user_id', Auth::id())->findOrFail($id);

    if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
        return redirect()->back()->with('error', 'Peminjaman ini tidak bisa diajukan pengembalian.');
    }

    $peminjaman->update([
        'status' => 'menunggu_verifikasi',
        'kondisi_kembali' => $request->kondisi_kembali,
    ]);

    LogAktivitas::create([
        'user_id'   => Auth::id(),
        'aktivitas' => 'Mengajukan pengembalian untuk peminjaman ID: ' . $peminjaman->id,
    ]);

    return redirect()->back()->with('success', 'Pengajuan pengembalian berhasil dikirim.');
}
}