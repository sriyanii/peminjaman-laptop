<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Borrowing extends Model
{
    use HasFactory;
    protected $table = 'peminjaman';

    protected $fillable = [
        'user_id',
        'laptop_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_dikembalikan',
        'keperluan',
        'catatan',
        'status',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by',
        'rejection_reason',
        'returned_by',
        'kondisi_kembali',
        'catatan_kembali'
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'tanggal_kembali' => 'datetime',
        'tanggal_dikembalikan' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

public function laptop()
{
    return $this->belongsTo(Laptop::class, 'laptop_id');  // ← BENAR
}

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Accessor untuk cek keterlambatan
    public function getIsOverdueAttribute()
    {
        return $this->status === 'dipinjam' && 
               $this->tanggal_kembali && 
               $this->tanggal_kembali->lt(now());
    }

    // Accessor untuk jumlah hari terlambat
    public function getDaysOverdueAttribute()
    {
        if ($this->is_overdue) {
            return now()->diffInDays($this->tanggal_kembali);
        }
        return 0;
    }

    // Scope untuk peminjaman aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'dipinjam');
    }

    // Scope untuk peminjaman terlambat
    public function scopeOverdue($query)
    {
        return $query->where('status', 'dipinjam')
                    ->where('tanggal_kembali', '<', now());
    }
}