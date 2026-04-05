<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriAlat extends Model
{
    use HasFactory;

    protected $table = 'kategori_alat';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'deskripsi'
    ];

    /**
     * Relasi dengan tools
     */
    public function laptop()
    {
        return $this->hasMany(Tool::class, 'category_id');
    }

    /**
     * Aksesor untuk jumlah alat
     */
    public function getJumlahAlatAttribute()
    {
        return $this->tools()->count();
    }
}