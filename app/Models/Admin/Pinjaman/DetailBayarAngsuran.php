<?php

namespace App\Models\Admin\Pinjaman;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\User;
use Carbon\Carbon;

class DetailBayarAngsuran extends Model
{
    use SoftDeletes;

    protected $table = 'detail_bayar_angsuran';

    protected $fillable = [
        'kode_bayar',
        'bayar_angsuran_id',
        'pinjaman_id',
        'angsuran_ke',
        'tanggal_bayar',
        'jumlah_bayar',
        'denda',
        'total_bayar',
        'ke_kas_id',
        'bukti_transfer',
        'status_verifikasi',
        'catatan_verifikasi',
        'verified_at',
        'verified_by',
        'keterangan',
        'user_id',
    ];

    // ✅ PERBAIKAN: Jangan cast jumlah_bayar sebagai decimal di sini
    // Biarkan Laravel handle sesuai database schema
    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'verified_at' => 'datetime',
    ];

    /**
     * Boot method untuk auto-generate kode dan hitung total
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_bayar)) {
                $model->kode_bayar = self::generateKodeBayar();
            }

            // ✅ PERBAIKAN: Pastikan nilai numerik
            $model->jumlah_bayar = (float) $model->jumlah_bayar;
            $model->denda = (float) ($model->denda ?? 0);
            $model->total_bayar = $model->jumlah_bayar + $model->denda;
        });

        static::updating(function ($model) {
            // ✅ Auto hitung total bayar saat update
            $model->jumlah_bayar = (float) $model->jumlah_bayar;
            $model->denda = (float) ($model->denda ?? 0);
            $model->total_bayar = $model->jumlah_bayar + $model->denda;
        });
    }

    /**
     * Generate kode bayar otomatis (TBY00001 format)
     */
    public static function generateKodeBayar()
    {
        $lastBayar = self::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastBayar) {
            return 'TBY00001';
        }

        $lastNumber = (int) substr($lastBayar->kode_bayar, 3);
        $newNumber = $lastNumber + 1;

        return 'TBY' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    // =============================================
    // RELASI
    // =============================================

    /**
     * Relasi ke BayarAngsuran (jadwal angsuran)
     */
    public function angsuran()
    {
        return $this->belongsTo(BayarAngsuran::class, 'bayar_angsuran_id');
    }

    /**
     * Relasi ke Pinjaman
     */
    public function pinjaman()
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }

    /**
     * Relasi ke Kas
     */
    public function kas()
    {
        return $this->belongsTo(DataKas::class, 'ke_kas_id', 'id');
    }

    /**
     * Relasi ke User (petugas yang input)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User yang verifikasi
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // =============================================
    // SCOPES
    // =============================================

    /**
     * Scope: Pembayaran yang pending verifikasi
     */
    public function scopePendingVerification($query)
    {
        return $query->where('status_verifikasi', 'pending');
    }

    /**
     * Scope: Pembayaran yang sudah approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status_verifikasi', 'approved');
    }

    /**
     * Scope: Pembayaran yang ditolak
     */
    public function scopeRejected($query)
    {
        return $query->where('status_verifikasi', 'rejected');
    }

    /**
     * Scope: Filter by pinjaman
     */
    public function scopeByPinjaman($query, $pinjamanId)
    {
        return $query->where('pinjaman_id', $pinjamanId);
    }

    /**
     * Scope: Filter by tanggal range
     */
    public function scopeByTanggalRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_bayar', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter by kode bayar
     */
    public function scopeByKodeBayar($query, $kode)
    {
        return $query->where('kode_bayar', 'like', '%' . $kode . '%');
    }

    // =============================================
    // HELPER METHODS - PEMBAYARAN ONLINE
    // =============================================

    /**
     * Cek apakah pembayaran pending
     */
    public function isPending()
    {
        return $this->status_verifikasi === 'pending';
    }

    /**
     * Cek apakah pembayaran sudah approved
     */
    public function isApproved()
    {
        return $this->status_verifikasi === 'approved';
    }

    /**
     * Cek apakah pembayaran ditolak
     */
    public function isRejected()
    {
        return $this->status_verifikasi === 'rejected';
    }

    /**
     * Cek apakah pembayaran via transfer (dari DataKas)
     * ⭐ INI YANG PENTING! Deteksi dari kas_id
     */
    public function isTransfer()
    {
        return $this->kas && $this->kas->transfer_kas === 'Y';
    }

    /**
     * Cek apakah pembayaran tunai (dari DataKas)
     */
    public function isTunai()
    {
        return !$this->isTransfer();
    }

    /**
     * Get URL bukti transfer
     */
    public function getBuktiTransferUrlAttribute()
    {
        if ($this->bukti_transfer) {
            return asset('storage/' . $this->bukti_transfer);
        }
        return null;
    }

    /**
     * Get badge status verifikasi
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning"><i class="ti ti-clock-hour-4"></i> Menunggu Verifikasi</span>',
            'approved' => '<span class="badge bg-success"><i class="ti ti-check"></i> Terverifikasi</span>',
            'rejected' => '<span class="badge bg-danger"><i class="ti ti-x"></i> Ditolak</span>',
        ];

        return $badges[$this->status_verifikasi] ?? '';
    }

    /**
     * Get badge tipe pembayaran (dari kas)
     */
    public function getTipeBadgeAttribute()
    {
        if ($this->isTransfer()) {
            return '<span class="badge bg-info"><i class="ti ti-credit-card"></i> Transfer - ' . $this->kas->nama_kas . '</span>';
        }

        return '<span class="badge bg-primary"><i class="ti ti-cash"></i> Tunai - ' . ($this->kas ? $this->kas->nama_kas : 'Kas') . '</span>';
    }

    // =============================================
    // ACCESSOR - EXISTING
    // =============================================

    /**
     * Accessor: Format waktu bayar
     */
    public function getWaktuBayarAttribute()
    {
        return $this->tanggal_bayar ? $this->tanggal_bayar->format('H:i') : '-';
    }

    /**
     * Accessor: Status keterlambatan
     */
    public function getStatusKeterlambatanAttribute()
    {
        if (!$this->angsuran) {
            return '-';
        }

        $jatuhTempo = Carbon::parse($this->angsuran->tanggal_jatuh_tempo);
        $tanggalBayar = Carbon::parse($this->tanggal_bayar);

        if ($tanggalBayar->lte($jatuhTempo)) {
            return 'Tepat Waktu';
        }

        $hari = $jatuhTempo->diffInDays($tanggalBayar);

        if ($hari <= 7) {
            return "Terlambat {$hari} Hari";
        } elseif ($hari <= 30) {
            return "Terlambat {$hari} Hari";
        } else {
            $bulan = floor($hari / 30);
            return "Terlambat {$bulan} Bulan";
        }
    }
}