<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    use HasFactory;

    // Tentukan nama tabel
    protected $table = 'alat';
    
    protected $fillable = [
        'kode_alat',
        'nama_alat',
        'kategori_id',
        'merk',
        'model',
        'tahun_produksi',
        'spesifikasi',
        'serial_number',
        'kondisi',
        'status',
        'harga_sewa_perhari',
        'denda_perhari',
        'stok',
        'dipinjam',
        'foto'
    ];

    protected $casts = [
        'spesifikasi' => 'array',
        'harga_sewa_perhari' => 'decimal:2',
        'denda_perhari' => 'decimal:2'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriAlat::class, 'kategori_id');
    }
}