<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'role',
        'phone',
        'address',
        'saldo',
        'is_active',
        'status'
    ];

   /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relasi ke transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Relasi ke peminjaman
    public function borrowings()
    {
        return $this->hasMany(Borrowing::class);
    }

    // Format saldo
    public function getSaldoFormattedAttribute()
    {
        return 'Rp ' . number_format($this->saldo, 0, ',', '.');
    }

    // Cek apakah saldo cukup
    public function hasSufficientBalance($amount)
    {
        return $this->saldo >= $amount;
    }

    // Update saldo
    public function updateBalance($type, $amount)
    {
        if ($type === 'deposit' || $type === 'refund') {
            $this->saldo += $amount;
        } elseif ($type === 'withdraw' || $type === 'payment' || $type === 'penalty') {
            $this->saldo -= $amount;
        }
        $this->save();
    }

 

    /**
     * Scope untuk user aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk user berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Relasi dengan peminjaman
     */
    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class);
    }

    /**
     * Cek apakah user aktif
     */
    public function isActive()
    {
        return $this->is_active && $this->status === 'active';
    }

    /**
     * Cek apakah user admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user petugas
     */
    public function isPetugas()
    {
        return $this->role === 'petugas';
    }

    /**
     * Cek apakah user biasa
     */
    public function isUser()
    {
        return $this->role === 'user';
    }
}