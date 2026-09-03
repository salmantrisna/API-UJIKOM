<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'peminjam' => $this->whenLoaded('user', fn() => $this->user?->name),
            'tgl_pinjam' => $this->tgl_pinjam?->format('Y-m-d'),
            'tgl_kembali_plan' => $this->tgl_kembali_plan?->format('Y-m-d'),
            'status' => $this->status,
            'item_dipinjam' => $this->whenLoaded('detailPinjam', function () {
                return $this->detailPinjam->map(function ($detail) {
                    return [
                        'nama_alat' => $detail->alat?->nama_alat ?? 'Alat Dihapus/Tidak Ditemukan',
                        'jumlah' => (int) $detail->jumlah,
                    ];
                });
            }),
            'info_pengembalian' => $this->whenLoaded('pengembalian', function () {
                if (!$this->pengembalian) return null;
                return [
                    'tgl_kembali_real' => $this->pengembalian->tgl_kembali_real?->format('Y-m-d'),
                    'denda' => (float) $this->pengembalian->denda,
                    'petugas' => $this->pengembalian->petugas?->name ?? 'Sistem',
                ];
            }),
        ];
    }
}