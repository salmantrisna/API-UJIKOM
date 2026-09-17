<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Alat - Panel Peminjam</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8f9fa; overflow-x: hidden; }
        /* Sidebar Styling ala Panel Petugas */
        .sidebar {
            width: 260px;
            min-height: 100vh;
            background-color: #111c2e;
            color: #fff;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar .brand-box {
            padding: 24px 20px;
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar .nav-link {
            color: #a0aec0;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255,255,255,0.08);
            border-left: 4px solid #3182ce;
        }
        .sidebar .user-box {
            padding: 20px;
            border-top: 1px solid rgba(255,255,255,0.08);
            font-size: 0.85rem;
            color: #a0aec0;
        }
        /* Main Content Wrapper */
        .main-content {
            margin-left: 260px;
            padding: 30px;
        }
        .top-navbar {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-left: 260px;
        }
        .card { border: none; border-radius: 10px; box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05); }
        .table th { background-color: #f8f9fa; font-weight: 600; color: #495057; }
        .table td { vertical-align: middle; }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div>
            <div class="brand-box text-uppercase">
                📦 PANEL PEMINJAM
            </div>
            <div class="nav flex-column py-3">
                <a href="{{ route('peminjam.katalog') }}" class="nav-link active">Katalog Alat</a>
                <a href="{{ route('peminjam.riwayat') }}" class="nav-link">Riwayat Peminjaman</a>
            </div>
        </div>
        <div class="user-box">
            <div class="text-white fw-semibold mb-1">{{ Auth::user()->name ?? 'Peminjam' }}</div>
            <div>ROLE: PEMINJAM</div>
        </div>
    </div>

    <!-- Top Navbar -->
    <div class="top-navbar">
        <h5 class="fw-bold text-dark m-0">Katalog Alat Tersedia</h5>
        <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-danger btn-sm px-3 fw-semibold">Logout</button>
        </form>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('peminjam.ajukan') }}" method="POST">
            
            <!-- Card Form Tanggal -->
            <div class="card mb-4">
                <div class="card-body p-4">
                    <label class="form-label fw-semibold text-secondary">Rencana Tanggal Kembali</label>
                    <input type="date" name="tgl_kembali_plan" class="form-control w-50" required value="{{ old('tgl_kembali_plan') }}">
                </div>
            </div>

            <!-- Card Tabel Katalog -->
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="60">Pilih</th>
                                    <th>Nama Alat</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Stok Tersedia</th>
                                    <th width="140" class="text-center">Jumlah Pinjam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($alats as $index => $alat)
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="alat_id[{{ $index }}]" value="{{ $alat->id }}" class="form-check-input">
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $alat->nama_alat }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $alat->kategori->nama_kategori ?? '-' }}</span></td>
                                        <td class="text-center"><span class="badge bg-success bg-opacity-10 text-success px-2 py-1">{{ $alat->stok }} Unit</span></td>
                                        <td>
                                            <input type="number" name="jumlah[{{ $index }}]" class="form-control form-control-sm text-center" value="1" min="1" max="{{ $alat->stok }}">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">Tidak ada alat yang tersedia saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white p-3 border-top-0">
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">Ajukan Peminjaman</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>