<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    use HasFactory;

    protected $table = 'peminjaman';
    protected $guarded = ['id'];

    protected $casts = [
        'tgl_pinjam'       => 'date',
        'tgl_kembali_plan' => 'date',
        'tgl_kembali_real' => 'date',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke Detail Peminjaman (Nama asli)
    public function detailPinjam()
    {
        return $this->hasMany(DetailPinjam::class, 'peminjaman_id');
    }

    // Alias relasi dengan tambahan 's' agar cocok dengan view laporan
    public function detailPinjams()
    {
        return $this->hasMany(DetailPinjam::class, 'peminjaman_id');
    }

    // Relasi ke Pengembalian
    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'peminjaman_id');
    }
}