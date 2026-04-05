<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Laptop extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'laptops';
    
    protected $fillable = [
        'merk',
        'model',
        'processor',
        'ram',
        'storage',
        'serial_number',
        'status',
        'kondisi',
        'tahun_pembelian',
        'keterangan',
        'gambar',
        'lokasi',
        'warna',
        'os',
        'baterai_kondisi',
        'garansi',
        'garansi_berakhir',
        'harga_beli',
        'harga_sewa_harian',
        'is_active',
        'category_id',
    ];
    
    protected $casts = [
        'tahun_pembelian' => 'integer',
        'garansi_berakhir' => 'date',
        'harga_beli' => 'decimal:2',
        'harga_sewa_harian' => 'decimal:2',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
    
    /**
     * Relasi ke Peminjaman
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'laptop_id');
    }

    public function category()
{
    return $this->belongsTo(Category::class, 'category_id');
}
    
    /**
     * Scope untuk laptop tersedia
     */
    public function scopeTersedia($query)
    {
        return $query->where('status', 'tersedia')->where('is_active', true);
    }
    
    /**
     * Scope untuk laptop dipinjam
     */
    public function scopeDipinjam($query)
    {
        return $query->where('status', 'dipinjam');
    }
    
    /**
     * Scope untuk laptop rusak/maintenance
     */
    public function scopeRusak($query)
    {
        return $query->whereIn('status', ['rusak', 'maintenance']);
    }
    
    /**
     * Accessor untuk nama lengkap laptop
     */
    public function getNamaLengkapAttribute()
    {
        return $this->merk . ' ' . $this->model;
    }
    
    /**
     * Accessor untuk spesifikasi singkat
     */
    public function getSpesifikasiSingkatAttribute()
    {
        return $this->processor . ' | ' . $this->ram . ' | ' . $this->storage;
    }
}