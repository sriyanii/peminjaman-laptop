<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peminjaman extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $table = 'peminjaman';
    
    protected $fillable = [
        'user_id',
        'laptop_id',        // ← ini yang benar (laptop_id, BUKAN tool_id)
        'approved_by',
        'tanggal_pinjam',
        'tanggal_kembali_rencana',
        'tanggal_kembali',
        'status',
        'tujuan',
        'keterangan',
        'alasan_ditolak',
        'catatan_pengembalian',
        'denda',
        'is_denda_dibayar',
        'waktu_approve',
        'waktu_pengambilan',
        'waktu_pengembalian'
    ];
    
    protected $casts = [
        'tanggal_pinjam' => 'date',
        'tanggal_kembali_rencana' => 'date',
        'tanggal_kembali' => 'date',
        'waktu_approve' => 'datetime',
        'waktu_pengambilan' => 'datetime',
        'waktu_pengembalian' => 'datetime',
        'denda' => 'decimal:2',
        'is_denda_dibayar' => 'boolean'
    ];
    
    // HAPUS INI jika ada - jangan eager loading default
    // protected $with = ['laptop', 'user'];
    
    /**
     * Relasi ke User (peminjam)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Relasi ke User (approver)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    /**
     * Relasi ke Laptop - INI SATU-SATUNYA YANG DIGUNAKAN
     */
    public function laptop()
    {
        return $this->belongsTo(Laptop::class, 'laptop_id');
    }
    
 public function getNamaAlatAttribute()
{
    return $this->laptop ? $this->laptop->merk . ' ' . $this->laptop->model : '-';
}

public function getKodeAlatAttribute()
{
    return $this->laptop ? $this->laptop->serial_number : '-';
}
    
    /**
     * Relasi ke TransaksiDenda
     */
    public function transaksiDenda()
    {
        return $this->hasOne(TransaksiDenda::class, 'peminjaman_id');
    }
    
    // SCOPES
    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['approved', 'aktif']);
    }
    
    public function scopeTerlambat($query)
    {
        return $query->whereIn('status', ['approved', 'aktif'])
                     ->where('tanggal_kembali_rencana', '<', now())
                     ->whereNull('tanggal_kembali');
    }
    
    public function scopeMenungguVerifikasi($query)
    {
        return $query->where('status', 'pending_pengembalian');
    }
    
    // ... method lainnya tetap sama
    
    public function getStatusIndoAttribute()
    {
        $statusMap = [
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'aktif' => 'Aktif',
            'selesai' => 'Selesai',
            'ditolak' => 'Ditolak',
            'batal' => 'Batal',
            'terlambat' => 'Terlambat',
            'pending_pengembalian' => 'Menunggu Verifikasi'
        ];

        return $statusMap[$this->status] ?? $this->status;
    }
    
    public function isTerlambat()
    {
        return $this->tanggal_kembali_rencana && 
               now()->greaterThan($this->tanggal_kembali_rencana) &&
               !$this->tanggal_kembali &&
               in_array($this->status, ['approved', 'aktif']);
    }
    
    public function hitungDenda($dendaPerHari = 10000)
    {
        if (!$this->isTerlambat() || $this->is_denda_dibayar) {
            return 0;
        }

        $hariTerlambat = now()->diffInDays($this->tanggal_kembali_rencana);
        return $hariTerlambat * $dendaPerHari;
    }
    
    public function ajukanPengembalian($tanggal_kembali, $kondisi, $catatan = null)
    {
        $this->update([
            'tanggal_kembali' => $tanggal_kembali,
            'catatan_pengembalian' => 'Kondisi: ' . $kondisi . 
                                    ($catatan ? ' | Catatan: ' . $catatan : ''),
            'status' => 'pending_pengembalian',
            'waktu_pengembalian' => now()
        ]);
        
        return $this;
    }
}