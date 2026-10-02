<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    private const DENDA_PER_HARI = 5000;
    private const DENDA_KERUSAKAN = ['Rusak Ringan' => 25000, 'Rusak Berat' => 100000];

    // ---------- HELPER ----------

    private function catat(string $aktivitas): void
    {
        LogAktivitas::create(['user_id' => Auth::id(), 'aktivitas' => $aktivitas]);
    }

    private function hitungDendaTelat($tglPlan, $tglKembali): int
    {
        $plan = Carbon::parse($tglPlan)->startOfDay();
        $kembali = Carbon::parse($tglKembali)->startOfDay();

        return $kembali->greaterThan($plan) ? (int) $plan->diffInDays($kembali) * self::DENDA_PER_HARI : 0;
    }

    // Peminjaman yang belum selesai: diajukan, dipinjam, telat,
    // atau sudah diajukan pengembalian tapi belum diproses petugas
    private function pinjamanBelumSelesai()
    {
        return Peminjaman::where(function ($q) {
            $q->whereIn('status', ['diajukan', 'dipinjam', 'telat'])
              ->orWhere(function ($q2) {
                  $q2->where('status', 'dikembalikan')->doesntHave('pengembalian');
              });
        });
    }

    // Barangnya masih di tangan peminjam (stok belum kembali)
    private function stokMasihKeluar(Peminjaman $p): bool
    {
        $status = strtolower($p->status);

        return in_array($status, ['dipinjam', 'telat'])
            || ($status === 'dikembalikan' && !$p->pengembalian && !$p->tgl_kembali_real);
    }

    private function hapusPeminjaman($id, string $label, string $route)
    {
        $peminjaman = Peminjaman::with('pengembalian')->findOrFail($id);

        if ($this->stokMasihKeluar($peminjaman)) {
            return redirect()->route($route)->with('error', 'Data tidak bisa dihapus: alat masih dipinjam. Proses pengembaliannya dulu.');
        }

        DB::transaction(function () use ($peminjaman) {
            DetailPinjam::where('peminjaman_id', $peminjaman->id)->delete();
            Pengembalian::where('peminjaman_id', $peminjaman->id)->delete();
            $peminjaman->delete();
        });

        $this->catat("Menghapus data {$label} ID: {$id}");

        return redirect()->route($route)->with('success', "Data {$label} berhasil dihapus.");
    }

    // ---------- DASHBOARD ----------

    public function index()
    {
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('logs'));
    }

    // ---------- CRUD USER ----------

    public function indexUser(Request $request)
    {
        $search = $request->input('search');

        $users = User::when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('role', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.user.index', compact('users', 'search'));
    }

    public function createUser()
    {
        return view('admin.user.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,petugas,peminjam',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'no_hp'    => $request->no_hp,
        ]);

        $this->catat('Menambahkan user baru: ' . $user->name);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);

        return view('admin.user.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'role'  => 'required|in:admin,petugas,peminjam',
        ]);

        $data = $request->only(['name', 'email', 'role', 'no_hp']);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $this->catat('Memperbarui data user: ' . $user->name);

        return redirect()->route('admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Kamu tidak bisa menghapus akun sendiri.');
        }

        $aktif = $this->pinjamanBelumSelesai()->where('user_id', $user->id)->count();

        if ($aktif > 0) {
            return back()->with('error', "User tidak bisa dihapus: masih punya {$aktif} peminjaman yang belum selesai.");
        }

        $nama = $user->name;
        $user->delete();

        $this->catat('Menghapus user: ' . $nama);

        return back()->with('success', 'User berhasil dihapus.');
    }

    // ---------- CRUD KATEGORI ----------

    public function indexKategori(Request $request)
    {
        $search = $request->input('search');

        $kategoris = Kategori::when($search, fn ($q, $s) => $q->where('nama_kategori', 'like', "%{$s}%"))
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.kategori.index', compact('kategoris', 'search'));
    }

    public function createKategori()
    {
        return view('admin.kategori.create');
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori',
        ]);

        $kategori = Kategori::create(['nama_kategori' => $request->nama_kategori]);

        $this->catat('Menambahkan kategori: ' . $kategori->nama_kategori);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        return view('admin.kategori.edit', compact('kategori'));
    }

    public function updateKategori(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,' . $id,
        ]);

        $kategori->update(['nama_kategori' => $request->nama_kategori]);

        $this->catat('Memperbarui kategori menjadi: ' . $kategori->nama_kategori);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        // Nama relasi harus sama dengan di model Kategori (alat() atau alats())
        if ($kategori->alat()->count() > 0) {
            return redirect()->route('admin.kategori.index')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $nama = $kategori->nama_kategori;
        $kategori->delete();

        $this->catat('Menghapus kategori: ' . $nama);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    // ---------- CRUD ALAT ----------

    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('nama_alat', 'like', "%{$search}%")
                      ->orWhere('status_kondisi', 'like', "%{$search}%")
                      ->orWhereHas('kategori', fn ($k) => $k->where('nama_kategori', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    public function createAlat()
    {
        $kategoris = Kategori::all();

        return view('admin.alat.create', compact('kategoris'));
    }

    public function storeAlat(Request $request)
    {
        $request->validate([
            'nama_alat'      => 'required|string|max:255',
            'kategori_id'    => 'required|exists:kategori,id',
            'stok'           => 'required|integer|min:0',
            'status_kondisi' => 'required|string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['nama_alat', 'kategori_id', 'stok', 'status_kondisi']);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('alat', 'public');
        }

        $alat = Alat::create($data);

        $this->catat('Menambahkan alat baru: ' . $alat->nama_alat);

        return redirect()->route('admin.alat.index')->with('success', 'Alat berhasil ditambahkan.');
    }

    public function editAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $kategoris = Kategori::all();

        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    public function updateAlat(Request $request, $id)
    {
        $alat = Alat::findOrFail($id);

        $request->validate([
            'nama_alat'      => 'required|string|max:255',
            'kategori_id'    => 'required|exists:kategori,id',
            'stok'           => 'required|integer|min:0',
            'status_kondisi' => 'required|string',
            'gambar'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->only(['nama_alat', 'kategori_id', 'stok', 'status_kondisi']);

        if ($request->hasFile('gambar')) {
            if ($alat->gambar && Storage::disk('public')->exists($alat->gambar)) {
                Storage::disk('public')->delete($alat->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('alat', 'public');
        }

        $alat->update($data);

        $this->catat('Memperbarui data alat: ' . $alat->nama_alat);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);

        $dipakai = $this->pinjamanBelumSelesai()
            ->whereHas('detailPinjam', fn ($q) => $q->where('alat_id', $alat->id))
            ->count();

        if ($dipakai > 0) {
            return redirect()->route('admin.alat.index')
                ->with('error', "Alat tidak bisa dihapus: masih ada {$dipakai} peminjaman yang belum selesai.");
        }

        $nama = $alat->nama_alat;

        if ($alat->gambar && Storage::disk('public')->exists($alat->gambar)) {
            Storage::disk('public')->delete($alat->gambar);
        }

        $alat->delete();

        $this->catat('Menghapus alat: ' . $nama);

        return redirect()->route('admin.alat.index')->with('success', 'Alat berhasil dihapus.');
    }

    // ---------- TRANSAKSI PEMINJAMAN ----------

    public function indexPeminjaman(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.peminjaman.index', compact('peminjamans', 'search'));
    }

    public function createPeminjaman()
    {
        $users = User::where('role', 'peminjam')->get();
        $alats = Alat::where('stok', '>', 0)->get();

        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    public function storePeminjaman(Request $request)
    {
        $request->validate([
            'user_id'          => 'required|exists:users,id',
            'tgl_pinjam'       => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id'          => 'required|array|min:1',
            'alat_id.*'        => 'required|distinct|exists:alat,id',
            'jumlah'           => 'required|array|min:1',
            'jumlah.*'         => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $peminjaman = Peminjaman::create([
                    'user_id'          => $request->user_id,
                    'tgl_pinjam'       => $request->tgl_pinjam,
                    'tgl_kembali_plan' => $request->tgl_kembali_plan,
                    'status'           => 'diajukan',
                ]);

                foreach ($request->alat_id as $index => $alatId) {
                    $qty = (int) $request->jumlah[$index];
                    $alat = Alat::lockForUpdate()->findOrFail($alatId);

                    if ($alat->stok < $qty) {
                        throw new \RuntimeException("Stok alat {$alat->nama_alat} tidak mencukupi!");
                    }

                    DetailPinjam::create([
                        'peminjaman_id' => $peminjaman->id,
                        'alat_id'       => $alatId,
                        'jumlah'        => $qty,
                    ]);

                    $alat->decrement('stok', $qty);
                }

                $this->catat('Membuat transaksi peminjaman baru (ID: ' . $peminjaman->id . ')');
            });
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.peminjaman.index')->with('success', 'Transaksi peminjaman berhasil ditambahkan.');
    }

    public function showPeminjaman($id)
    {
        $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->findOrFail($id);

        return view('admin.peminjaman.show', compact('peminjaman'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'           => 'required|string',
            'tgl_kembali_real' => 'nullable|date',
        ]);

        $statusTarget = strtolower($request->status);
        if ($statusTarget === 'selesai') {
            $statusTarget = 'dikembalikan';
        }

        $tglKembaliReal = $request->tgl_kembali_real ?? now()->format('Y-m-d');
        $denda = 0;

        DB::transaction(function () use ($id, $statusTarget, $tglKembaliReal, &$denda) {
            $peminjaman = Peminjaman::with(['detailPinjam', 'pengembalian'])->lockForUpdate()->findOrFail($id);
            $denda = $peminjaman->denda ?? 0;
            $updateData = ['status' => $statusTarget];

            if ($statusTarget === 'dikembalikan') {
                $updateData['tgl_kembali_real'] = $tglKembaliReal;

                // Stok hanya dikembalikan kalau barangnya memang masih keluar
                // dan belum diproses lewat storePengembalian
                if (in_array(strtolower($peminjaman->status), ['dipinjam', 'telat']) && !$peminjaman->pengembalian) {
                    $denda = $this->hitungDendaTelat($peminjaman->tgl_kembali_plan, $tglKembaliReal);
                    $updateData['denda'] = $denda;

                    foreach ($peminjaman->detailPinjam as $detail) {
                        Alat::where('id', $detail->alat_id)->increment('stok', $detail->jumlah);
                    }
                }
            }

            $peminjaman->update($updateData);

            $this->catat('Memperbarui status peminjaman ID ' . $peminjaman->id . ' menjadi ' . $statusTarget);
        });

        $pesan = 'Status data peminjaman/pengembalian berhasil diperbarui.';
        if ($statusTarget === 'dikembalikan') {
            $pesan .= $denda > 0
                ? ' Keterlambatan dikenakan denda sebesar Rp ' . number_format($denda, 0, ',', '.')
                : ' Tidak ada denda.';
        }

        return back()->with('success', $pesan);
    }

    public function destroyPeminjaman($id)
    {
        return $this->hapusPeminjaman($id, 'peminjaman', 'admin.peminjaman.index');
    }

    // ---------- TRANSAKSI PENGEMBALIAN ----------

    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->whereIn(DB::raw('LOWER(status)'), ['dipinjam', 'dikembalikan', 'selesai', 'telat'])
            ->when($search, function ($query, $search) {
                return $query->where(function ($q) use ($search) {
                    $q->where('status', 'like', "%{$search}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.pengembalian.index', compact('peminjamans'));
    }

    public function createPengembalian($id = null)
    {
        if ($id) {
            $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->findOrFail($id);

            $denda = in_array(strtolower($peminjaman->status), ['dipinjam', 'telat'])
                ? $this->hitungDendaTelat($peminjaman->tgl_kembali_plan, now())
                : 0;

            return view('admin.pengembalian.create', compact('peminjaman', 'denda'));
        }

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn(DB::raw('LOWER(status)'), ['dipinjam', 'telat'])
            ->latest()
            ->paginate(10);

        return view('admin.pengembalian.pilih', compact('peminjamans'));
    }

    public function storePengembalian(Request $request, $id)
    {
        $request->validate([
            'tgl_kembali'     => 'required|date',
            'kondisi_kembali' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $peminjaman = Peminjaman::with(['detailPinjam', 'pengembalian'])->lockForUpdate()->findOrFail($id);

                if ($peminjaman->pengembalian) {
                    throw new \RuntimeException('Pengembalian ini sudah pernah diproses.');
                }
                if (!in_array(strtolower($peminjaman->status), ['dipinjam', 'telat', 'dikembalikan'])) {
                    throw new \RuntimeException('Peminjaman ini belum disetujui atau tidak sedang dipinjam.');
                }

                $dendaTelat = $this->hitungDendaTelat($peminjaman->tgl_kembali_plan, $request->tgl_kembali);
                $dendaKerusakan = self::DENDA_KERUSAKAN[$request->kondisi_kembali] ?? 0;
                $denda = $dendaTelat + $dendaKerusakan;

                Pengembalian::create([
                    'peminjaman_id'   => $peminjaman->id,
                    'tgl_kembali'     => $request->tgl_kembali,
                    'kondisi_kembali' => $request->kondisi_kembali,
                    'denda'           => $denda,
                    'petugas_id'      => Auth::id(),
                ]);

                $peminjaman->update([
                    'status'           => 'dikembalikan',
                    'denda'            => $denda,
                    'tgl_kembali_real' => $request->tgl_kembali,
                ]);

                foreach ($peminjaman->detailPinjam as $detail) {
                    Alat::where('id', $detail->alat_id)->increment('stok', $detail->jumlah);
                }

                $this->catat('Memproses pengembalian alat untuk peminjaman ID: ' . $peminjaman->id);
            });
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.pengembalian.index')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.pengembalian.index')->with('success', 'Pengembalian berhasil diproses dan stok telah diperbarui!');
    }

    public function editPengembalian($id)
    {
        $pengembalian = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])->findOrFail($id);

        return view('admin.pengembalian.edit', compact('pengembalian'));
    }

    public function updatePengembalian(Request $request, $id)
    {
        $request->validate(['denda' => 'nullable|numeric|min:0']);

        $denda = $request->denda ?? 0;

        DB::transaction(function () use ($id, $denda) {
            $peminjaman = Peminjaman::findOrFail($id);
            $peminjaman->update(['denda' => $denda]);

            // Halaman daftar membaca denda dari tabel pengembalian, jadi ikut diperbarui
            Pengembalian::where('peminjaman_id', $peminjaman->id)->update(['denda' => $denda]);
        });

        $this->catat('Memperbarui data pengembalian/denda ID: ' . $id);

        return redirect()->route('admin.pengembalian.index')->with('success', 'Data pengembalian berhasil diperbarui!');
    }

   public function destroyPengembalian($id)
{
    $peminjaman = Peminjaman::findOrFail($id);

    // Transaksi yang masih berjalan tidak boleh dihapus
    if (in_array(strtolower($peminjaman->status), ['diajukan', 'dipinjam', 'telat', 'menunggu_verifikasi'])) {
        return redirect()->route('admin.pengembalian.index')
            ->with('error', 'Data tidak bisa dihapus karena alat masih dipinjam. Proses pengembaliannya dulu.');
    }

    return $this->hapusPeminjaman($id, 'pengembalian', 'admin.pengembalian.index');
}
}