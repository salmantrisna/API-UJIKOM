<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman - Panel Peminjam</title>
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
                <a href="{{ route('peminjam.katalog') }}" class="nav-link">Katalog Alat</a>
                <a href="{{ route('peminjam.riwayat') }}" class="nav-link active">Riwayat Peminjaman</a>
            </div>
        </div>
        <div class="user-box">
            <div class="text-white fw-semibold mb-1">{{ Auth::user()->name ?? 'Peminjam' }}</div>
            <div>ROLE: PEMINJAM</div>
        </div>
    </div>

    <!-- Top Navbar -->
    <div class="top-navbar">
        <h5 class="fw-bold text-dark m-0">Riwayat Peminjaman Alat Anda</h5>
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

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="60">No</th>
                                <th>Tanggal Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Daftar Alat & Jumlah</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($peminjamans as $index => $peminjaman)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($peminjaman->tgl_pinjam)->translatedFormat('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan)->translatedFormat('d M Y') }}</td>
                                    <td>
                                        <ul class="mb-0 ps-3">
                                            @foreach($peminjaman->detailPinjams as $detail)
                                                <li>
                                                    <span class="fw-semibold">{{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}</span> 
                                                    <span class="text-muted">({{ $detail->jumlah }} unit)</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td class="text-center">
                                        @if($peminjaman->status == 'diajukan')
                                            <span class="badge bg-warning text-dark px-2 py-1">Diajukan</span>
                                        @elseif($peminjaman->status == 'disetujui')
                                            <span class="badge bg-success px-2 py-1">Disetujui</span>
                                        @elseif($peminjaman->status == 'ditolak')
                                            <span class="badge bg-danger px-2 py-1">Ditolak</span>
                                        @else
                                            <span class="badge bg-secondary px-2 py-1">{{ ucfirst($peminjaman->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat peminjaman alat.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>