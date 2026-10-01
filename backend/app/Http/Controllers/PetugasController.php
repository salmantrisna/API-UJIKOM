<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Exception;

class PetugasController extends Controller
{
    // Menampilkan daftar pengajuan peminjaman (status: diajukan) yang perlu disetujui/ditolak
    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->when($search, function ($query, $search) {
                return $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return view('petugas.peminjaman.index', compact('peminjamans', 'search'));
    }

    // Menampilkan daftar alat yang sedang dipinjam/telat/menunggu verifikasi untuk dimantau
   public function indexPengembalian(Request $request)
{
    $search = $request->input('search');

    $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
        ->whereIn('status', ['dipinjam', 'telat', 'menunggu_verifikasi'])
        ->when($search, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
        ->orderByRaw("FIELD(status, 'menunggu_verifikasi', 'telat', 'dipinjam')")
        ->latest()
        ->get();

    return view('petugas.pengembalian.index', compact('peminjamans', 'search'));
}
    // Menyetujui atau menolak peminjaman
    public function setujuiPeminjaman(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);

            if (strtolower($peminjaman->status) !== 'diajukan') {
                return redirect()->back()->with('error', 'Peminjaman ini sudah diproses sebelumnya.');
            }

            // Cek apakah ini aksi tolak atau setuju
            if ($request->input('status') === 'ditolak') {
                $peminjaman->update(['status' => 'ditolak']);

                LogAktivitas::create([
                    'user_id'   => auth()->id(),
                    'aktivitas' => 'Menolak pengajuan peminjaman ID: ' . $peminjaman->id,
                ]);

                DB::commit();
                return redirect()->back()->with('success', 'Pengajuan peminjaman telah ditolak.');
            }

            // Kalau bukan tolak, berarti setujui seperti biasa
            $peminjaman->update(['status' => 'dipinjam']);

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            LogAktivitas::create([
                'user_id'   => auth()->id(),
                'aktivitas' => 'Menyetujui peminjaman ID: ' . $peminjaman->id,
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Memproses pengembalian alat, menghitung denda otomatis, dan mengembalikan stok
    public function prosesPengembalian(Request $request, $pinjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($pinjamanId);

            // Hitung denda keterlambatan
            $dendaTelat = 0;
            $tglPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $tglSekarang = Carbon::now()->startOfDay();

            if ($tglSekarang->greaterThan($tglPlan)) {
                $hariTelat = $tglPlan->diffInDays($tglSekarang);
                $dendaTelat = $hariTelat * 5000;
            }

            // Hitung denda kerusakan berdasarkan kondisi kembali
            $dendaKerusakan = match ($request->kondisi_kembali) {
                'Rusak Ringan' => 25000,
                'Rusak Berat' => 100000,
                default => 0,
            };

            $denda = $dendaTelat + $dendaKerusakan;

            // Simpan data pengembalian
            Pengembalian::create([
                'peminjaman_id' => $peminjaman->id,
                'tgl_kembali' => now(),
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda' => $denda,
                'petugas_id' => auth()->id(),
            ]);

            // Update status peminjaman jadi dikembalikan, sekaligus simpan dendanya
            $peminjaman->update([
                'status' => 'dikembalikan',
                'denda' => $denda,
            ]);

            // Kembalikan stok alat ke inventaris
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            LogAktivitas::create([
                'user_id'   => auth()->id(),
                'aktivitas' => 'Memverifikasi pengembalian untuk peminjaman ID: ' . $peminjaman->id,
            ]);

            DB::commit();

            $pesanDenda = $denda > 0
                ? " Total denda: Rp " . number_format($denda, 0, ',', '.')
                : " Tidak ada denda.";

            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.' . $pesanDenda);
        } catch (Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function laporan(Request $request)
    {
        $status = $request->input('status');
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');

        $laporans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
                return $query->whereBetween('tgl_pinjam', [$dari_tanggal, $sampai_tanggal]);
            })
            ->latest()
            ->get();

        return view('petugas.laporan.index', compact('laporans', 'status', 'dari_tanggal', 'sampai_tanggal'));
    }

    // Menampilkan halaman khusus cetak (print preview)
    public function cetakLaporan(Request $request)
    {
        $status = $request->input('status');
        $dari_tanggal = $request->input('dari_tanggal');
        $sampai_tanggal = $request->input('sampai_tanggal');

        $laporans = Peminjaman::with(['user', 'detailPinjams.alat', 'pengembalian'])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($dari_tanggal && $sampai_tanggal, function ($query) use ($dari_tanggal, $sampai_tanggal) {
                return $query->whereBetween('tgl_pinjam', [$dari_tanggal, $sampai_tanggal]);
            })
            ->latest()
            ->get();

        return view('petugas.laporan.cetak', compact('laporans', 'status', 'dari_tanggal', 'sampai_tanggal'));
    }
}