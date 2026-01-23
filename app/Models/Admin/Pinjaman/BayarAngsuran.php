<?php

namespace App\Models\Admin\Pinjaman;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\User;
use Carbon\Carbon;

class BayarAngsuran extends Model
{
    use SoftDeletes;

    protected $table = 'bayar_angsuran';

    protected $fillable = [
        'kode_bayar',
        'pinjaman_id',
        'angsuran_ke',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'jumlah_angsuran',
        'jumlah_bayar',
        'denda',
        'ke_kas_id',
        'status_bayar',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'datetime',
        'tanggal_bayar' => 'datetime',
        'jumlah_angsuran' => 'decimal:2',
        'jumlah_bayar' => 'decimal:2',
        'denda' => 'decimal:2',
    ];

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
        return $this->belongsTo(DataKas::class, 'ke_kas_id');
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke DetailBayarAngsuran (pembayaran aktual) - ONE TO MANY
     */
    public function detailPembayaran()
    {
        return $this->hasMany(DetailBayarAngsuran::class, 'bayar_angsuran_id');
    }

    /**
     * Relasi ke DetailBayarAngsuran (pembayaran terakhir) - ONE TO ONE
     */
    public function pembayaranTerakhir()
    {
        return $this->hasOne(DetailBayarAngsuran::class, 'bayar_angsuran_id')->latest();
    }

    /**
     * Check apakah sudah ada pembayaran
     */
    public function getHasPembayaranAttribute()
    {
        return $this->detailPembayaran()->exists();
    }

    /**
     * Get total yang sudah dibayar dari detail pembayaran
     */
    public function getTotalDibayarAttribute()
    {
        return $this->detailPembayaran()->sum('total_bayar');
    }

    /**
     * Generate kode bayar otomatis
     */
    public static function generateKodeBayar()
    {
        $lastBayar = self::withTrashed()
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastBayar) {
            return 'BYR00001';
        }

        $lastNumber = (int) substr($lastBayar->kode_bayar, 3);
        $newNumber = $lastNumber + 1;

        return 'BYR' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Scope untuk filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status_bayar', $status);
    }

    /**
     * Scope untuk filter by pinjaman
     */
    public function scopeByPinjaman($query, $pinjamanId)
    {
        return $query->where('pinjaman_id', $pinjamanId);
    }

    /**
     * Check apakah terlambat
     */
    public function getIsTerlambatAttribute()
    {
        if ($this->status_bayar === 'Lunas' && $this->tanggal_bayar) {
            return $this->tanggal_bayar->gt($this->tanggal_jatuh_tempo);
        }

        if ($this->status_bayar === 'Belum') {
            return now()->gt($this->tanggal_jatuh_tempo);
        }

        return false;
    }

    /**
     * Get hari keterlambatan
     */
    public function getHariTerlambatAttribute()
    {
        if (!$this->is_terlambat) {
            return 0;
        }

        $jatuhTempo = Carbon::parse($this->tanggal_jatuh_tempo)->startOfDay();

        if ($this->status_bayar === 'Lunas' && $this->tanggal_bayar) {
            $tanggalBayar = Carbon::parse($this->tanggal_bayar)->startOfDay();
            return (int) $jatuhTempo->diffInDays($tanggalBayar);
        }

        $today = now()->startOfDay();
        return (int) $jatuhTempo->diffInDays($today);
    }

    /**
     * Get status keterlambatan
     */
    public function getStatusKeterlambatanAttribute()
    {
        if (!$this->is_terlambat) {
            return 'Tepat Waktu';
        }

        $hari = $this->hari_terlambat;

        if ($hari <= 7) {
            return "Terlambat {$hari} Hari";
        } elseif ($hari <= 30) {
            return "Terlambat {$hari} Hari";
        } else {
            $bulan = floor($hari / 30);
            return "Terlambat {$bulan} Bulan";
        }
    }

    /**
     * Get total pembayaran (angsuran + denda)
     */
    public function getTotalPembayaranAttribute()
    {
        return $this->jumlah_bayar + $this->denda;
    }
}