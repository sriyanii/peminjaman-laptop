<?php
// app/Models/Peminjaman.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peminjaman extends Model
{
    use SoftDeletes;
    
    protected $table = 'peminjaman';
    
    protected $fillable = [
        'user_id',
        'laptop_id',
        'kode_peminjaman',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali',
        'lama_hari',
        'harga_sewa',
        'status',
        'kondisi_alat_saat_kembali',
        'keterangan_rusak',
        'denda',
        'total_tagihan',
        'catatan',
        'catatan_pengembalian',
        'approved_by',
        'waktu_approve',
        'alasan_ditolak'
    ];
    
    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali' => 'date',
        'waktu_approve' => 'datetime'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function laptop()
    {
        return $this->belongsTo(Laptop::class);
    }
    
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    public function transaksi()
    {
        return $this->hasOne(Transaksi::class);
    }
    
    public function denda()
    {
        return $this->hasOne(Denda::class);
    }
}