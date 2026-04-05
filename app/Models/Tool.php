<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory;

    protected $table = 'tools';

    protected $fillable = [
        'kode_alat',
        'nama_alat',
        'merk',
        'type',
        'serial_number',
        'foto', // <--- TAMBAHKAN INI
        'category_id',
        'status',
        'kondisi',
        'lokasi',
        'tanggal_pembelian',
        'harga',
        'masa_garansi',
        'spesifikasi',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'harga' => 'decimal:2',
    ];

    /**
     * Relasi dengan kategori
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriAlat::class, 'category_id');
    }

    /**
     * Relasi dengan category (alternatif)
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope untuk alat tersedia
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'tersedia');
    }

    /**
     * Aksesor untuk nama kategori
     */
    public function getNamaKategoriAttribute()
    {
        return $this->kategori ? $this->kategori->nama_kategori : '-';
    }

    /**
     * Aksesor untuk URL foto
     */
    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return asset('images/no-image.png');
    }

    /**
     * Cek apakah alat punya foto
     */
    public function getHasFotoAttribute()
    {
        return !empty($this->foto);
    }
}