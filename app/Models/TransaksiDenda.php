<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiDenda extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel di database
     */
    protected $table = 'transaksi_denda';

    /**
     * Kolom primary key
     */
    protected $primaryKey = 'id';

    /**
     * Kolom yang dapat diisi (mass assignable)
     */
    protected $fillable = [
        'peminjaman_id',
        'user_id',
        'petugas_id',
        'total_denda',
        'denda_dibayar',
        'status_pembayaran',
        'status_transaksi',
        'kondisi_barang',
        'catatan_cek',
        'bukti_pembayaran',
        'waktu_cek',
        'waktu_pembayaran',
        'waktu_selesai'
    ];

    /**
     * Tipe data casting
     */
    protected $casts = [
        'total_denda' => 'decimal:2',
        'denda_dibayar' => 'decimal:2',
        'waktu_cek' => 'datetime',
        'waktu_pembayaran' => 'datetime',
        'waktu_selesai' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];



    /**
     * Kolom yang disembunyikan saat serialize
     */
    protected $hidden = [
        'deleted_at'
    ];

    /**
     * Kolom default values
     */
    protected $attributes = [
        'total_denda' => 0,
        'denda_dibayar' => 0,
        'status_pembayaran' => 'belum_lunas',
        'status_transaksi' => 'pending',
    ];

    /**
     * Nama tabel yang digunakan untuk relasi
     */
    public function getTable()
    {
        return $this->table;
    }

    /* ==================== RELASI ==================== */

    /**
     * Relasi ke tabel peminjaman
     */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    /**
     * Relasi ke user (peminjam)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke petugas
     */
    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    /**
     * Relasi ke alat melalui peminjaman
     */
    public function alat()
    {
        return $this->hasOneThrough(
            Tool::class,
            Peminjaman::class,
            'id', // Foreign key pada tabel peminjaman
            'id', // Foreign key pada tabel tools
            'peminjaman_id', // Local key pada tabel transaksi_denda
            'laptop_id' // Local key pada tabel peminjaman
        );
    }

    /**
     * Relasi ke laptop melalui peminjaman
     */
    public function laptop()
    {
        return $this->hasOneThrough(
            Laptop::class,
            Peminjaman::class,
            'id',
            'id',
            'peminjaman_id',
            'laptop_id'
        );
    }

    /* ==================== ATTRIBUTE ACCESSORS ==================== */

    /**
     * Hitung sisa denda (computed attribute)
     */
    public function getSisaDendaAttribute()
    {
        return max(0, $this->total_denda - $this->denda_dibayar);
    }

    /**
     * Format total denda untuk display
     */
    public function getTotalDendaFormattedAttribute()
    {
        return $this->formatUang($this->total_denda);
    }

    /**
     * Format denda dibayar untuk display
     */
    public function getDendaDibayarFormattedAttribute()
    {
        return $this->formatUang($this->denda_dibayar);
    }

    /**
     * Format sisa denda untuk display
     */
    public function getSisaDendaFormattedAttribute()
    {
        return $this->formatUang($this->sisa_denda);
    }

    /**
     * Badge color untuk status pembayaran
     */
    public function getStatusPembayaranBadgeAttribute()
    {
        $badges = [
            'lunas' => 'success',
            'belum_lunas' => 'danger',
            'sebagian' => 'warning',
        ];
        
        return $badges[$this->status_pembayaran] ?? 'secondary';
    }

    /**
     * Badge color untuk status transaksi
     */
    public function getStatusTransaksiBadgeAttribute()
    {
        $badges = [
            'pending' => 'secondary',
            'proses_cek' => 'info',
            'selesai' => 'success',
            'dibatalkan' => 'dark',
        ];
        
        return $badges[$this->status_transaksi] ?? 'secondary';
    }

    /**
     * Badge color untuk kondisi barang
     */
    public function getKondisiBarangBadgeAttribute()
    {
        $badges = [
            'baik' => 'success',
            'rusak_ringan' => 'warning',
            'rusak_berat' => 'danger',
            'hilang' => 'dark',
        ];
        
        return $badges[$this->kondisi_barang] ?? 'secondary';
    }

    /**
     * Text kondisi barang yang diformat
     */
    public function getKondisiBarangTextAttribute()
    {
        $texts = [
            'baik' => 'Baik',
            'rusak_ringan' => 'Rusak Ringan',
            'rusak_berat' => 'Rusak Berat',
            'hilang' => 'Hilang',
        ];
        
        return $texts[$this->kondisi_barang] ?? 'Tidak Diketahui';
    }

    /**
     * Status transaksi lengkap
     */
    public function getStatusLengkapAttribute()
    {
        if ($this->status_pembayaran == 'lunas') {
            return 'Lunas - ' . ucfirst($this->status_transaksi);
        }
        
        return ucfirst($this->status_pembayaran) . ' - ' . ucfirst($this->status_transaksi);
    }

    /**
     * Cek apakah sudah lunas
     */
    public function getIsLunasAttribute()
    {
        return $this->status_pembayaran == 'lunas';
    }

    /**
     * Cek apakah belum lunas
     */
    public function getIsBelumLunasAttribute()
    {
        return $this->status_pembayaran == 'belum_lunas';
    }

    /**
     * Cek apakah sebagian
     */
    public function getIsSebagianAttribute()
    {
        return $this->status_pembayaran == 'sebagian';
    }

    /**
     * Persentase pembayaran
     */
    public function getPersentasePembayaranAttribute()
    {
        if ($this->total_denda == 0) return 100;
        return min(100, round(($this->denda_dibayar / $this->total_denda) * 100, 2));
    }

    /* ==================== SCOPES ==================== */

    /**
     * Scope untuk transaksi aktif (belum dihapus)
     */
    public function scopeAktif($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope untuk transaksi belum lunas
     */
    public function scopeBelumLunas($query)
    {
        return $query->where('status_pembayaran', 'belum_lunas');
    }

    /**
     * Scope untuk transaksi lunas
     */
    public function scopeLunas($query)
    {
        return $query->where('status_pembayaran', 'lunas');
    }

    /**
     * Scope untuk transaksi sebagian
     */
    public function scopeSebagian($query)
    {
        return $query->where('status_pembayaran', 'sebagian');
    }

    /**
     * Scope untuk transaksi pending
     */
    public function scopePending($query)
    {
        return $query->where('status_transaksi', 'pending');
    }

    /**
     * Scope untuk transaksi selesai
     */
    public function scopeSelesai($query)
    {
        return $query->where('status_transaksi', 'selesai');
    }

    /**
     * Scope untuk transaksi berdasarkan user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope untuk transaksi berdasarkan petugas
     */
    public function scopeByPetugas($query, $petugasId)
    {
        return $query->where('petugas_id', $petugasId);
    }

    /**
     * Scope untuk transaksi berdasarkan peminjaman
     */
    public function scopeByPeminjaman($query, $peminjamanId)
    {
        return $query->where('peminjaman_id', $peminjamanId);
    }

    /* ==================== METHODS ==================== */

    /**
     * Format uang
     */
    public function formatUang($amount)
    {
        if ($amount == 0) {
            return 'Rp 0';
        }
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Update status pembayaran dan transaksi otomatis
     */
    public function updateStatusPembayaran()
    {
        if ($this->sisa_denda <= 0) {
            $this->status_pembayaran = 'lunas';
            $this->status_transaksi = 'selesai';
            $this->waktu_selesai = now();
        } elseif ($this->denda_dibayar > 0 && $this->sisa_denda > 0) {
            $this->status_pembayaran = 'sebagian';
            $this->status_transaksi = 'proses_cek';
        } else {
            $this->status_pembayaran = 'belum_lunas';
        }
        
        $this->save();
        
        return $this;
    }

    /**
     * Proses pembayaran denda
     */
    public function prosesPembayaran($jumlah, $bukti = null, $metode = 'tunai')
    {
        // Validasi
        if ($jumlah <= 0) {
            throw new \Exception('Jumlah pembayaran harus lebih dari 0');
        }
        
        if ($jumlah > $this->sisa_denda) {
            throw new \Exception('Jumlah pembayaran melebihi sisa denda. Sisa: ' . $this->formatUang($this->sisa_denda));
        }

        // Update pembayaran
        $this->denda_dibayar += $jumlah;
        
        if ($bukti) {
            $this->bukti_pembayaran = $bukti;
        }
        
        $this->waktu_pembayaran = now();
        
        // Update status
        $this->updateStatusPembayaran();
        
        // Update peminjaman terkait jika sudah lunas
        if ($this->is_lunas && $this->peminjaman) {
            $this->peminjaman->update([
                'is_denda_dibayar' => 1
            ]);
        }
        
        \Log::info("Pembayaran denda berhasil: Transaksi ID {$this->id}, Jumlah: {$jumlah}, Sisa: {$this->sisa_denda}");
        
        return $this;
    }

    /**
     * Proses cek barang (untuk petugas)
     */
    public function prosesCekBarang($kondisi, $catatan, $petugasId = null)
    {
        // Set kondisi barang
        $this->kondisi_barang = $kondisi;
        $this->catatan_cek = $catatan;
        $this->waktu_cek = now();
        
        if ($petugasId) {
            $this->petugas_id = $petugasId;
        }
        
        // Set status transaksi
        $this->status_transaksi = 'proses_cek';
        
        // Hitung denda kerusakan
        $dendaKerusakan = $this->hitungDendaKerusakan($kondisi);
        
        // Update total denda
        $this->total_denda += $dendaKerusakan;
        
        $this->save();
        
        \Log::info("Cek barang selesai: Transaksi ID {$this->id}, Kondisi: {$kondisi}, Denda Kerusakan: {$dendaKerusakan}");
        
        return $this;
    }

    /**
     * Hitung denda kerusakan berdasarkan kondisi
     */
    public function hitungDendaKerusakan($kondisi)
    {
        $dendaMap = [
            'baik' => 0,
            'rusak_ringan' => 50000,      // Rp 50.000
            'rusak_berat' => 200000,      // Rp 200.000
            'hilang' => 1000000,          // Rp 1.000.000
        ];
        
        return $dendaMap[$kondisi] ?? 0;
    }

    /**
     * Hitung denda keterlambatan
     */
    public function hitungDendaKeterlambatan($tanggalRencana, $tanggalKembali, $tarifPerHari = 10000)
    {
        if (!$tanggalRencana || !$tanggalKembali) {
            return 0;
        }
        
        try {
            $tglRencana = \Carbon\Carbon::parse($tanggalRencana);
            $tglKembali = \Carbon\Carbon::parse($tanggalKembali);
            
            if ($tglKembali > $tglRencana) {
                $hariTerlambat = $tglRencana->diffInDays($tglKembali);
                return $hariTerlambat * $tarifPerHari;
            }
        } catch (\Exception $e) {
            \Log::error("Error hitung denda keterlambatan: " . $e->getMessage());
        }
        
        return 0;
    }

    /**
     * Batalkan transaksi
     */
    public function batalkan($alasan = null)
    {
        $this->status_transaksi = 'dibatalkan';
        
        if ($alasan) {
            $this->catatan_cek .= "\n[Dibatalkan: " . $alasan . "]";
        }
        
        $this->save();
        
        return $this;
    }

    /**
     * Generate kode transaksi (untuk referensi)
     */
    public function generateKodeTransaksi()
    {
        return 'DENDA-' . date('Ymd') . '-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get kode transaksi untuk display
     */
    public function getKodeTransaksiAttribute()
    {
        return $this->generateKodeTransaksi();
    }

    /* ==================== STATIC METHODS ==================== */

    /**
     * Buat transaksi denda baru dari pengembalian
     */
    public static function createFromPengembalian($peminjaman, $kondisi, $catatan = null, $petugasId = null)
    {
        try {
            // Cek apakah sudah ada transaksi untuk peminjaman ini
            $existing = self::where('peminjaman_id', $peminjaman->id)->first();
            if ($existing) {
                return $existing;
            }

            // Hitung denda keterlambatan
            $dendaKeterlambatan = (new self)->hitungDendaKeterlambatan(
                $peminjaman->tanggal_kembali_rencana,
                $peminjaman->tanggal_kembali
            );

            // Hitung denda kerusakan
            $dendaKerusakan = (new self)->hitungDendaKerusakan($kondisi);

            // Total denda
            $totalDenda = $dendaKeterlambatan + $dendaKerusakan;

            // Buat transaksi
            $transaksi = self::create([
                'peminjaman_id' => $peminjaman->id,
                'user_id' => $peminjaman->user_id,
                'petugas_id' => $petugasId,
                'total_denda' => $totalDenda,
                'denda_dibayar' => 0,
                'status_pembayaran' => $totalDenda > 0 ? 'belum_lunas' : 'lunas',
                'status_transaksi' => $totalDenda > 0 ? 'pending' : 'selesai',
                'kondisi_barang' => $kondisi,
                'catatan_cek' => $catatan,
                'waktu_cek' => now(),
                'waktu_selesai' => $totalDenda == 0 ? now() : null,
            ]);

            // Update peminjaman
            if ($totalDenda > 0) {
                $peminjaman->update([
                    'denda' => $totalDenda,
                    'is_denda_dibayar' => 0
                ]);
            }

            \Log::info("Transaksi denda dibuat: ID {$transaksi->id}, Peminjaman ID {$peminjaman->id}, Total Denda: {$totalDenda}");

            return $transaksi;
        } catch (\Exception $e) {
            \Log::error("Gagal membuat transaksi denda: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get statistik denda
     */
    public static function getStatistik($periode = 'bulan_ini')
    {
        $query = self::query();
        
        // Filter periode
        if ($periode == 'bulan_ini') {
            $query->whereMonth('created_at', date('m'))
                  ->whereYear('created_at', date('Y'));
        } elseif ($periode == 'tahun_ini') {
            $query->whereYear('created_at', date('Y'));
        }
        
        $total = $query->count();
        $lunas = $query->where('status_pembayaran', 'lunas')->count();
        $belumLunas = $query->where('status_pembayaran', 'belum_lunas')->count();
        $sebagian = $query->where('status_pembayaran', 'sebagian')->count();
        $totalDenda = $query->sum('total_denda');
        $totalDibayar = $query->sum('denda_dibayar');
        
        return [
            'total' => $total,
            'lunas' => $lunas,
            'belum_lunas' => $belumLunas,
            'sebagian' => $sebagian,
            'total_denda' => $totalDenda,
            'total_dibayar' => $totalDibayar,
            'sisa_denda' => $totalDenda - $totalDibayar,
            'persentase_lunas' => $total > 0 ? round(($lunas / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Auto update sisa denda sebelum save
        static::saving(function ($model) {
            // Tidak perlu update sisa_denda karena dihitung via accessor
        });

        // Event setelah dibuat
        static::created(function ($model) {
            \Log::info("Transaksi denda {$model->id} berhasil dibuat untuk peminjaman {$model->peminjaman_id}");
        });

        // Event setelah diupdate
        static::updated(function ($model) {
            \Log::info("Transaksi denda {$model->id} diupdate. Status: {$model->status_pembayaran}, Sisa: {$model->sisa_denda}");
        });
    }
}