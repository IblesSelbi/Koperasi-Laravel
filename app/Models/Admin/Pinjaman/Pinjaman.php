<?php

namespace App\Models\Admin\Pinjaman;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\Pinjaman\PengajuanPinjaman;
use App\Models\Admin\DataMaster\LamaAngsuran;
use App\Models\Admin\DataMaster\DataBarang;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\User;

class Pinjaman extends Model
{
    use SoftDeletes;

    protected $table = 'pinjaman';

    protected $fillable = [
        'kode_pinjaman',
        'pengajuan_id',
        'tanggal_pinjam',
        'anggota_id',
        'barang_id',
        'jenis_pinjaman',
        'pokok_pinjaman',
        'lama_angsuran_id',
        'angsuran_pokok',
        'bunga_persen',
        'biaya_bunga',
        'biaya_admin',
        'jumlah_angsuran',
        'dari_kas_id',
        'keterangan',
        'status_lunas',
        'user_id',
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'pokok_pinjaman' => 'decimal:2',
        'angsuran_pokok' => 'decimal:2',
        'bunga_persen' => 'decimal:2',
        'biaya_bunga' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'jumlah_angsuran' => 'decimal:2',
    ];

    // ✅ TAMBAHKAN APPENDS agar accessor otomatis ter-load
    protected $appends = [
        'total_bayar',
        'total_denda',
        'sisa_tagihan',
        'sisa_angsuran',
    ];

    // ========================================
    // RELASI
    // ========================================

    /**
     * Relasi ke Anggota
     */
    public function anggota()
    {
        return $this->belongsTo(DataAnggota::class, 'anggota_id', 'id');
    }

    /**
     * Relasi ke Pengajuan
     */
    public function pengajuan()
    {
        return $this->belongsTo(PengajuanPinjaman::class, 'pengajuan_id');
    }

    /**
     * Relasi ke Lama Angsuran
     */
    public function lamaAngsuran()
    {
        return $this->belongsTo(LamaAngsuran::class, 'lama_angsuran_id');
    }

    /**
     * Relasi ke Barang
     */
    public function barang()
    {
        return $this->belongsTo(DataBarang::class, 'barang_id');
    }

    /**
     * Relasi ke Kas
     */
    public function kas()
    {
        return $this->belongsTo(DataKas::class, 'dari_kas_id');
    }

    /**
     * Relasi ke User (yang memproses)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Jadwal Angsuran (bayar_angsuran)
     */
    public function angsuran()
    {
        return $this->hasMany(BayarAngsuran::class, 'pinjaman_id');
    }

    /**
     * ✅ PERBAIKAN: Relasi ke Detail Pembayaran Aktual
     */
    public function detailPembayaran()
    {
        return $this->hasMany(DetailBayarAngsuran::class, 'pinjaman_id');
    }

    // ========================================
    // ACCESSOR (Computed Attributes)
    // ========================================

    /**
     * ✅ PERBAIKAN: Hitung total sudah dibayar dari detail_bayar_angsuran
     */
    public function getTotalBayarAttribute()
    {
        return $this->detailPembayaran()->sum('jumlah_bayar') ?? 0;
    }

    /**
     * ✅ PERBAIKAN: Hitung total denda dari detail_bayar_angsuran
     */
    public function getTotalDendaAttribute()
    {
        return $this->detailPembayaran()->sum('denda') ?? 0;
    }

    /**
     * Hitung sisa tagihan (jumlah_angsuran - total_bayar + denda)
     */
    public function getSisaTagihanAttribute()
    {
        // Sisa tagihan = Total angsuran - Yang sudah dibayar + Denda
        return $this->jumlah_angsuran - $this->total_bayar;
    }

    /**
     * Hitung sisa angsuran (berapa bulan lagi)
     */
    public function getSisaAngsuranAttribute()
    {
        $totalAngsuran = $this->lamaAngsuran ? $this->lamaAngsuran->lama_angsuran : 0;
        $sudahBayar = $this->angsuran()->where('status_bayar', 'Lunas')->count();
        
        return $totalAngsuran - $sudahBayar;
    }

    /**
     * Get angsuran yang sudah dibayar
     */
    public function getAngsuranLunasAttribute()
    {
        return $this->angsuran()->where('status_bayar', 'Lunas')->count();
    }

    /**
     * Get angsuran yang belum dibayar
     */
    public function getAngsuranBelumAttribute()
    {
        return $this->angsuran()->where('status_bayar', 'Belum')->count();
    }

    /**
     * Check ada angsuran terlambat
     */
    public function getAdaTerlambatAttribute()
    {
        return $this->angsuran()
            ->where('status_bayar', 'Belum')
            ->where('tanggal_jatuh_tempo', '<', now())
            ->exists();
    }

    // ========================================
    // SCOPE
    // ========================================

    /**
     * Scope untuk filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_lunas', $status);
    }

    /**
     * Scope untuk filter by jenis
     */
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_pinjaman', $jenis);
    }

    // ========================================
    // HELPER METHODS
    // ========================================

    /**
     * Generate kode pinjaman otomatis
     */
    public static function generateKodePinjaman()
    {
        $lastPinjaman = self::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastPinjaman) {
            return 'PJ00001';
        }

        $lastNumber = (int) substr($lastPinjaman->kode_pinjaman, 2);
        $newNumber = $lastNumber + 1;

        return 'PJ' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}