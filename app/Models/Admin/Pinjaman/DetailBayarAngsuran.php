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
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'jumlah_bayar' => 'decimal:2',
        'denda' => 'decimal:2',
        'total_bayar' => 'decimal:2',
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
            
            // Auto hitung total bayar
            $model->total_bayar = $model->jumlah_bayar + $model->denda;
        });

        static::updating(function ($model) {
            // Auto hitung total bayar saat update
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
        return $this->belongsTo(DataKas::class, 'ke_kas_id');
    }

    /**
     * Relasi ke User (petugas yang input)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

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
}