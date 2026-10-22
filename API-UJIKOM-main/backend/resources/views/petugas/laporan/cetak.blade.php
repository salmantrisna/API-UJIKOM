<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Peminjaman Alat</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2, .header p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; vertical-align: top; }
        th { background-color: #f4f4f4; }
        tfoot td { background-color: #f4f4f4; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .summary { width: 100%; margin-top: 10px; border-collapse: collapse; }
        .summary td { border: 1px solid #ddd; padding: 8px; width: 33.33%; }
        .summary .label { font-size: 10px; text-transform: uppercase; color: #666; display: block; margin-bottom: 2px; }
        .summary .value { font-size: 14px; font-weight: bold; }
        .footer { margin-top: 30px; float: right; text-align: center; }
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    @php
        $totalTransaksi = $laporans->count();
        $totalDikembalikan = $laporans->whereIn('status', ['dikembalikan', 'selesai'])->count();
        $totalDenda = $laporans->sum(fn($item) => $item->pengembalian->denda ?? 0);
    @endphp

    <div class="no-print" style="margin-bottom: 20px; background: #e2e8f0; padding: 10px; border-radius: 5px; text-align: right;">
        <button onclick="window.print()" style="background: #2563eb; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">Cetak Sekarang</button>
        <button onclick="window.close()" style="background: #64748b; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold; margin-left: 5px;">Tutup</button>
    </div>

    <div class="header">
        <h2>LAPORAN PEMINJAMAN DAN PENGEMBALIAN ALAT</h2>
        <p>Sistem Informasi Manajemen Peminjaman Alat</p>
        @if(request('dari_tanggal') || request('sampai_tanggal'))
            <p style="font-size: 11px;">
                Periode:
                {{ request('dari_tanggal') ? \Carbon\Carbon::parse(request('dari_tanggal'))->format('d M Y') : 'Awal' }}
                s/d
                {{ request('sampai_tanggal') ? \Carbon\Carbon::parse(request('sampai_tanggal'))->format('d M Y') : 'Sekarang' }}
            </p>
        @endif
        @if(request('status'))
            <p style="font-size: 11px;">Status: {{ ucfirst(request('status')) }}</p>
        @endif
    </div>

    <!-- Ringkasan -->
    <table class="summary">
        <tr>
            <td>
                <span class="label">Total Transaksi</span>
                <span class="value">{{ $totalTransaksi }}</span>
            </td>
            <td>
                <span class="label">Sudah Dikembalikan</span>
                <span class="value">{{ $totalDikembalikan }}</span>
            </td>
            <td>
                <span class="label">Total Pemasukan Denda</span>
                <span class="value">Rp {{ number_format($totalDenda, 0, ',', '.') }}</span>
            </td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="20%">Peminjam</th>
                <th width="15%">Tgl Pinjam</th>
                <th width="15%">Rencana Kembali</th>
                <th width="15%">Status</th>
                <th width="20%">Detail Alat</th>
                <th width="10%">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($laporans as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
                <td>{{ $item->tgl_pinjam }}</td>
                <td>{{ $item->tgl_kembali_plan }}</td>
                <td>{{ ucfirst($item->status) }}</td>
                <td>
                    <ul style="margin: 0; padding-left: 15px;">
                        @foreach($item->detailPinjams as $detail)
                            <li>{{ $detail->alat->nama_alat ?? '-' }} ({{ $detail->jumlah }})</li>
                        @endforeach
                    </ul>
                </td>
                <td>Rp {{ number_format($item->pengembalian->denda ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">Tidak ada data laporan.</td>
            </tr>
            @endforelse
        </tbody>
        @if($laporans->count() > 0)
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">Total Pemasukan Denda</td>
                <td>Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Baleendah, {{ date('d F Y') }}</p>
        <p>Petugas Pengelola,</p>
        <br><br><br>
        <p><b>{{ auth()->user()->name }}</b></p>
    </div>

</body>
</html>