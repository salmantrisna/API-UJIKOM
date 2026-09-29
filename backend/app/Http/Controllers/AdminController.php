<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Alat;
use App\Models\Peminjaman; 
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminController extends Controller
{
    // --- DASHBOARD ---
    public function index()
    {
        $logs = LogAktivitas::with('user')->latest()->take(10)->get();
        return view('admin.dashboard', compact('logs'));
    }

    // --- CRUD USER ---
    public function indexUser(Request $request)
    {
        $search = $request->input('search');
        $users = User::when($search, function ($query, $search) {
            return $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('role', 'like', "%{$search}%");
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

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Menambahkan user baru: ' . $user->name,
        ]);

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
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'role'  => 'required|in:admin,petugas,peminjam',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
            'no_hp' => $request->no_hp,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Memperbarui data user: ' . $user->name,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $namaUser = $user->name;
        $user->delete();

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Menghapus user: ' . $namaUser,
        ]);

        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    // --- CRUD KATEGORI ---
    public function indexKategori(Request $request)
    {
        $search = $request->input('search');
        $kategoris = Kategori::when($search, function ($query, $search) {
            return $query->where('nama_kategori', 'like', "%{$search}%");
        })
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

        $kategori = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Menambahkan kategori: ' . $kategori->nama_kategori,
        ]);

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
            'nama_kategori' => 'required|string|max:255|unique:kategori,nama_kategori,'.$id,
        ]);

        $kategori->update([
            'nama_kategori' => $request->nama_kategori,
        ]);

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Memperbarui kategori menjadi: ' . $kategori->nama_kategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori($id)
    {
        $kategori = Kategori::findOrFail($id);

        if ($kategori->alat()->count() > 0) {
            return redirect()->route('admin.kategori.index')->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data alat.');
        }

        $namaKategori = $kategori->nama_kategori;
        $kategori->delete();

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Menghapus kategori: ' . $namaKategori,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus.');
    }

    // --- CRUD ALAT ---
    public function indexAlat(Request $request)
    {
        $search = $request->input('search');

        $alats = Alat::with('kategori')
            ->when($search, function ($query, $search) {
                return $query->where('nama_alat', 'like', "%{$search}%")
                    ->orWhere('status_kondisi', 'like', "%{$search}%")
                    ->orWhereHas('kategori', function ($q) use ($search) {
                        $q->where('nama_kategori', 'like', "%{$search}%");
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

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Menambahkan alat baru: ' . $alat->nama_alat,
        ]);

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

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Memperbarui data alat: ' . $alat->nama_alat,
        ]);

        return redirect()->route('admin.alat.index')->with('success', 'Data alat berhasil diperbarui.');
    }

    public function destroyAlat($id)
    {
        $alat = Alat::findOrFail($id);
        $namaAlat = $alat->nama_alat;

        if ($alat->gambar && Storage::disk('public')->exists($alat->gambar)) {
            Storage::disk('public')->delete($alat->gambar);
        }

        $alat->delete();

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Menghapus alat: ' . $namaAlat,
        ]);

        return redirect()->route('admin.alat.index')->with('success', 'Alat berhasil dihapus.');
    }

    // --- TRANSAKSI PEMINJAMAN ---
   public function indexPeminjaman(Request $request)
{
    $search = $request->input('search');

    $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
        ->when($search, function ($query, $search) {
            return $query->where('status', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        })
        ->latest()
        ->paginate(10);

    return view('admin.peminjaman.index', compact('peminjamans'));

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
            'alat_id.*'        => 'required|exists:alat,id',
            'jumlah'           => 'required|array|min:1',
            'jumlah.*'         => 'required|integer|min:1',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $peminjaman = Peminjaman::create([
                    'user_id'          => $request->user_id,
                    'tgl_pinjam'       => $request->tgl_pinjam,
                    'tgl_kembali_plan' => $request->tgl_kembali_plan,
                    'status'           => 'Diajukan',
                ]);

                foreach ($request->alat_id as $index => $alatId) {
                    $qty = $request->jumlah[$index];
                    $alat = Alat::findOrFail($alatId);

                    if ($alat->stok < $qty) {
                        throw new \Exception("Stok alat {$alat->nama_alat} tidak mencukupi!");
                    }

                    DetailPinjam::create([
                        'peminjaman_id' => $peminjaman->id,
                        'alat_id'       => $alatId,
                        'jumlah'        => $qty,
                    ]);

                    $alat->decrement('stok', $qty);
                }

                LogAktivitas::create([
                    'user_id'   => Auth::id(),
                    'aktivitas' => 'Membuat transaksi peminjaman baru (ID: ' . $peminjaman->id . ')',
                ]);
            });

            return redirect()->route('admin.peminjaman.index')->with('success', 'Transaksi peminjaman berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
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

        $peminjaman = Peminjaman::findOrFail($id);
        $statusTarget = $request->status;

        if (strtolower($statusTarget) === 'selesai') {
            $statusTarget = 'Dikembalikan';
        }

        $denda = $peminjaman->denda ?? 0;
        $tglKembaliReal = $request->tgl_kembali_real ?? now()->format('Y-m-d');

        if (strtolower($statusTarget) === 'dikembalikan' && in_array(strtolower($peminjaman->status), ['dipinjam', 'telat', 'diajukan'])) {
            $tglPlan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
            $tglReal = Carbon::parse($tglKembaliReal)->startOfDay();

            if ($tglReal->greaterThan($tglPlan)) {
                $selisihHari = $tglPlan->diffInDays($tglReal);
                $denda = $selisihHari * 5000;
            } else {
                $denda = 0;
            }

            foreach ($peminjaman->detailPinjam as $detail) {
                Alat::where('id', $detail->alat_id)->increment('stok', $detail->jumlah);
            }
        }

        $updateData = ['status' => $statusTarget];

        if (strtolower($statusTarget) === 'dikembalikan') {
            $updateData['tgl_kembali_real'] = $tglKembaliReal;
            $updateData['denda'] = $denda;
        }

        $peminjaman->update($updateData);

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Memperbarui status peminjaman ID ' . $peminjaman->id . ' menjadi ' . $statusTarget,
        ]);

        $pesanDenda = $denda > 0 
            ? " Keterlambatan dikenakan denda sebesar Rp " . number_format($denda, 0, ',', '.') 
            : " Tidak ada denda.";

        return back()->with('success', 'Status data peminjaman/pengembalian berhasil diperbarui.' . (strtolower($statusTarget) === 'dikembalikan' ? $pesanDenda : ''));
    }

    public function destroyPeminjaman($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        
        DetailPinjam::where('peminjaman_id', $peminjaman->id)->delete();
        $peminjaman->delete();

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Menghapus data peminjaman ID: ' . $id,
        ]);

        return redirect()->route('admin.peminjaman.index')->with('success', 'Data peminjaman berhasil dihapus.');
    }

    // --- TRANSAKSI PENGEMBALIAN ---
    public function indexPengembalian(Request $request)
    {
        $search = $request->input('search');

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['Dipinjam', 'Dikembalikan', 'Selesai', 'Telat', 'dipinjam', 'dikembalikan', 'selesai', 'telat'])
            ->when($search, function ($query, $search) {
                return $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10);

        return view('admin.pengembalian.index', compact('peminjamans'));
    }

    public function createPengembalian($id = null)
    {
        if ($id) {
            $peminjaman = Peminjaman::with(['user', 'detailPinjam.alat'])->findOrFail($id);
            
            $denda = 0;
            if (in_array(strtolower($peminjaman->status), ['dipinjam', 'telat'])) {
                $plan = Carbon::parse($peminjaman->tgl_kembali_plan);
                $now = Carbon::now();
                if ($now->greaterThan($plan)) {
                    $daysLate = $plan->diffInDays($now);
                    $denda = $daysLate * 5000; 
                }
            }

            return view('admin.pengembalian.create', compact('peminjaman', 'denda'));
        }

        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['Dipinjam', 'dipinjam', 'Telat', 'telat'])
            ->latest()
            ->paginate(10);

        return view('admin.pengembalian.pilih', compact('peminjamans'));
    }

   public function storePengembalian(Request $request, $id)
{
    $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);

    $request->validate([
        'tgl_kembali' => 'required|date',
        'kondisi_kembali' => 'required|string',
    ]);

    DB::transaction(function () use ($request, $peminjaman) {
        // Hitung denda otomatis
        $plan = Carbon::parse($peminjaman->tgl_kembali_plan)->startOfDay();
        $tglKembali = Carbon::parse($request->tgl_kembali)->startOfDay();
        $denda = $tglKembali->greaterThan($plan) ? $plan->diffInDays($tglKembali) * 5000 : 0;

        // Simpan record pengembalian
        \App\Models\Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tgl_kembali' => $request->tgl_kembali,
            'kondisi_kembali' => $request->kondisi_kembali,
            'denda' => $denda,
            'petugas_id' => auth()->id(),
        ]);

        // Update status dan denda di peminjaman
        $peminjaman->update([
            'status' => 'dikembalikan',
            'denda' => $denda,
        ]);

        // Kembalikan stok
        foreach ($peminjaman->detailPinjam as $detail) {
            $alat = Alat::find($detail->alat_id);
            if ($alat) {
                $alat->stok += $detail->jumlah;
                $alat->save();
            }
        }

        LogAktivitas::create([
            'user_id'   => auth()->id(),
            'aktivitas' => 'Memproses pengembalian alat untuk peminjaman ID: ' . $peminjaman->id,
        ]);
    });

    return redirect()->route('admin.pengembalian.index')->with('success', 'Pengembalian berhasil diproses dan stok telah diperbarui!');
}

    public function editPengembalian($id)
    {
        $pengembalian = Peminjaman::with(['user', 'detailPinjam.alat'])->findOrFail($id);
        return view('admin.pengembalian.edit', compact('pengembalian'));
    }

    public function updatePengembalian(Request $request, $id)
    {
        $request->validate([
            'denda' => 'nullable|numeric',
        ]);

        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->update([
            'denda' => $request->denda ?? 0,
        ]);

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Memperbarui data pengembalian/denda ID: ' . $id,
        ]);

        return redirect()->route('admin.pengembalian.index')->with('success', 'Data pengembalian berhasil diperbarui!');
    }

    public function destroyPengembalian($id)
    {
        $peminjaman = Peminjaman::findOrFail($id);
        $peminjaman->delete();

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => 'Menghapus data pengembalian ID: ' . $id,
        ]);

        return redirect()->route('admin.pengembalian.index')->with('success', 'Data pengembalian berhasil dihapus!');
    }
}