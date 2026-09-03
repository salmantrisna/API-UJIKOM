@extends('layouts.app')

@section('content')
<div style="padding: 20px; font-family: Arial, sans-serif;">
    
    <!-- Welcome Alert -->
    <div style="background-color: #d1e7dd; color: #0f5132; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #badbcc;">
        <div>
            ✅ Selamat datang, <strong>{{ Auth::user()->name ?? 'Bagus Karim' }}</strong>! Anda login sebagai hak akses <span style="background-color: #212529; color: white; padding: 3px 8px; border-radius: 4px; font-size: 12px;">{{ strtoupper(Auth::user()->role ?? 'ADMIN') }}</span>
        </div>
        <div style="color: #6c757d; font-size: 14px;">
            {{ now()->format('d M Y') }}
        </div>
    </div>

    <!-- Kotak Statistik -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 4px solid #0d6efd;">
            <div style="color: #6c757d; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;">Total Alat</div>
            <div style="font-size: 24px; font-weight: bold; color: #333;">{{ \App\Models\Alat::count() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 4px solid #198754;">
            <div style="color: #6c757d; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;">Sedang Dipinjam</div>
            <div style="font-size: 24px; font-weight: bold; color: #333;">{{ \App\Models\Peminjaman::whereIn('status', ['Dipinjam', 'dipinjam', 'Telat', 'telat'])->count() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 4px solid #ffc107;">
            <div style="color: #6c757d; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;">Pending Request</div>
            <div style="font-size: 24px; font-weight: bold; color: #333;">{{ \App\Models\Peminjaman::whereIn('status', ['Diajukan', 'diajukan', 'Pending', 'pending'])->count() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-left: 4px solid #0dcaf0;">
            <div style="color: #6c757d; font-size: 12px; font-weight: bold; text-transform: uppercase; margin-bottom: 5px;">Total User</div>
            <div style="font-size: 24px; font-weight: bold; color: #333;">{{ \App\Models\User::count() }}</div>
        </div>
    </div>

    <!-- Tabel Log Aktivitas -->
    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="padding: 15px 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-size: 16px; font-weight: bold; color: #333;">Log Aktivitas Terbaru</h3>
            <div style="font-size: 12px; color: #6c757d;">
                <span style="margin-right: 10px;">🔵 Import / Update</span>
                <span style="margin-right: 10px;">🟢 Approve / Tambah</span>
                <span style="margin-right: 10px;">🟡 Request</span>
                <span>🔴 Return / Hapus</span>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="background-color: #f8f9fa; color: #6c757d; border-bottom: 1px solid #eee;">
                        <th style="padding: 12px 20px; width: 20%;">Waktu</th>
                        <th style="padding: 12px 20px; width: 20%;">User</th>
                        <th style="padding: 12px 20px;">Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs ?? [] as $log)
                        @php
                            $act = strtolower($log->aktivitas);
                            // Menentukan warna badge berdasarkan kata kunci dalam teks aktivitas
                            if (str_contains($act, 'hapus') || str_contains($act, 'pengembalian') || str_contains($act, 'return')) {
                                $badgeBg = '#dc3545'; $badgeText = 'Return/Delete';
                            } elseif (str_contains($act, 'menyetujui') || str_contains($act, 'menambahkan')) {
                                $badgeBg = '#198754'; $badgeText = 'Approve/Add';
                            } elseif (str_contains($act, 'membuat') || str_contains($act, 'mengajukan')) {
                                $badgeBg = '#ffc107'; $badgeText = 'Request'; $fontColor = '#000';
                            } else {
                                $badgeBg = '#0d6efd'; $badgeText = 'Action';
                            }
                            $fontColor = $fontColor ?? '#fff';
                        @endphp
                        <tr style="border-bottom: 1px solid #f2f2f2;">
                            <td style="padding: 12px 20px; color: #555;">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td style="padding: 12px 20px; font-weight: bold;">{{ $log->user->name ?? 'System' }}</td>
                            <td style="padding: 12px 20px;">
                                <span style="background: {{ $badgeBg }}; color: {{ $fontColor }}; padding: 2px 6px; border-radius: 4px; font-size: 11px; margin-right: 5px;">
                                    {{ $badgeText }}
                                </span> 
                                {{ $log->aktivitas }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="padding: 20px; text-align: center; color: #6c757d;">Belum ada log aktivitas yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection