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
        'deleted_by',        // TAMBAHAN untuk soft delete
        'alasan_hapus',      // TAMBAHAN untuk soft delete
    ];

    protected $casts = [
        'tanggal_pinjam' => 'datetime',
        'pokok_pinjaman' => 'decimal:2',
        'angsuran_pokok' => 'decimal:2',
        'bunga_persen' => 'decimal:2',
        'biaya_bunga' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'jumlah_angsuran' => 'decimal:2',
        'deleted_at' => 'datetime',  // TAMBAHAN
    ];

    // Accessor otomatis ter-load
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
     * TAMBAHAN: Relasi ke User yang menghapus
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Relasi ke Jadwal Angsuran (bayar_angsuran)
     */
    public function angsuran()
    {
        return $this->hasMany(BayarAngsuran::class, 'pinjaman_id');
    }

    /**
     * Relasi ke Detail Pembayaran Aktual
     */
    public function detailPembayaran()
    {
        return $this->hasMany(DetailBayarAngsuran::class, 'pinjaman_id');
    }

    // ========================================
    // ACCESSOR (Computed Attributes)
    // ========================================

    /**
     * Hitung total sudah dibayar dari detail_bayar_angsuran (exclude yang dihapus)
     */
    public function getTotalBayarAttribute()
    {
        return $this->detailPembayaran()
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar') ?? 0;
    }

    /**
     * Hitung total denda dari detail_bayar_angsuran (exclude yang dihapus)
     */
    public function getTotalDendaAttribute()
    {
        return $this->detailPembayaran()
            ->whereNull('deleted_at')
            ->sum('denda') ?? 0;
    }

    /**
     * Hitung sisa tagihan (jumlah_angsuran - total_bayar)
     */
    public function getSisaTagihanAttribute()
    {
        return max(0, $this->jumlah_angsuran - $this->total_bayar);
    }

    /**
     * Hitung sisa angsuran (berapa bulan lagi)
     */
    public function getSisaAngsuranAttribute()
    {
        return $this->angsuran()
            ->where('status_bayar', 'Belum')
            ->whereNull('deleted_at')
            ->count();
    }

    /**
     * Get angsuran yang sudah dibayar
     */
    public function getAngsuranLunasAttribute()
    {
        return $this->angsuran()
            ->where('status_bayar', 'Lunas')
            ->whereNull('deleted_at')
            ->count();
    }

    /**
     * Get angsuran yang belum dibayar
     */
    public function getAngsuranBelumAttribute()
    {
        return $this->angsuran()
            ->where('status_bayar', 'Belum')
            ->whereNull('deleted_at')
            ->count();
    }

    /**
     * Check ada angsuran terlambat
     */
    public function getAdaTerlambatAttribute()
    {
        return $this->angsuran()
            ->where('status_bayar', 'Belum')
            ->where('tanggal_jatuh_tempo', '<', now())
            ->whereNull('deleted_at')
            ->exists();
    }

    /**
     * TAMBAHAN: Check apakah pinjaman sudah pernah ada pembayaran
     */
    public function getSudahAdaPembayaranAttribute()
    {
        return $this->angsuran()
            ->where('status_bayar', 'Lunas')
            ->exists();
    }

    /**
     * TAMBAHAN: Format tanggal dihapus
     */
    public function getTanggalHapusFormattedAttribute()
    {
        return $this->deleted_at ? 
            $this->deleted_at->translatedFormat('d F Y H:i') : 
            null;
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

    /**
     * TAMBAHAN: Scope untuk yang aktif (tidak dihapus)
     */
    public function scopeAktif($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * TAMBAHAN: Scope untuk yang sudah dihapus
     */
    public function scopeTerhapus($query)
    {
        return $query->onlyTrashed();
    }

    /**
     * TAMBAHAN: Scope untuk filter berdasarkan range tanggal
     */
    public function scopeFilterTanggal($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_pinjam', [$startDate, $endDate]);
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

    /**
     * TAMBAHAN: Method untuk soft delete dengan alasan
     */
    public function softDeleteWithReason($alasan, $userId)
    {
        $this->deleted_by = $userId;
        $this->alasan_hapus = $alasan;
        $this->save();
        
        return $this->delete();
    }

    /**
     * TAMBAHAN: Method untuk restore pinjaman
     */
    public function restorePinjaman()
    {
        $this->deleted_by = null;
        $this->alasan_hapus = null;
        $this->save();
        
        return $this->restore();
    }

    /**
     * TAMBAHAN: Check apakah bisa dihapus
     */
    public function canDelete()
    {
        // Tidak bisa hapus jika sudah divalidasi lunas
        $sudahValidasiLunas = \App\Models\Admin\Pinjaman\PinjamanLunas::where('pinjaman_id', $this->id)
            ->exists();
        
        if ($sudahValidasiLunas) {
            return [
                'can_delete' => false,
                'reason' => 'Pinjaman sudah divalidasi lunas. Batalkan validasi lunas terlebih dahulu.'
            ];
        }

        return [
            'can_delete' => true,
            'require_reason' => $this->sudah_ada_pembayaran
        ];
    }

    /**
     * TAMBAHAN: Get info lengkap untuk riwayat
     */
    public function getRiwayatInfoAttribute()
    {
        return [
            'id' => $this->id,
            'kode' => $this->kode_pinjaman,
            'anggota' => $this->anggota->nama ?? '-',
            'jumlah' => $this->pokok_pinjaman,
            'tanggal_pinjam' => $this->tanggal_pinjam->translatedFormat('d F Y'),
            'tanggal_hapus' => $this->tanggal_hapus_formatted,
            'dihapus_oleh' => $this->deletedBy->name ?? 'System',
            'alasan' => $this->alasan_hapus ?? '-',
            'sudah_ada_pembayaran' => $this->sudah_ada_pembayaran,
            'total_sudah_dibayar' => $this->total_bayar,
        ];
    }
}