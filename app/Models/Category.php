<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kategori_alat';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'deskripsi',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'id'; // Pastikan ini 'id', bukan 'kode_kategori' atau yang lain
    }

        /**
     * Relasi ke tabel laptops
     * Sebuah kategori bisa memiliki banyak laptop
     */
    public function laptops()
    {
        return $this->hasMany(Laptop::class, 'category_id', 'id');
    }

    /**
     * Get the tools for the category.
     */
    public function laptop()
    {
        return $this->hasMany(Tool::class, 'category_id');
    }

    /**
     * Get the tools count for the category.
     */
    public function getToolsCountAttribute()
    {
        return $this->tools()->count();
    }
}