<?php

namespace App\Models\Admin\Pinjaman;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPinjamanLunas extends Model
{
    protected $table = 'detail_pinjaman_lunas';

    protected $fillable = [
        'kode_bayar',
        'pinjaman_lunas_id',
        'angsuran_ke',
        'tanggal_bayar',
        'angsuran_pokok',
        'biaya_bunga',
        'biaya_admin',
        'jumlah_angsuran',
        'denda',
        'total_bayar',
        'status_bayar',
        'user_nama',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'datetime',
        'angsuran_pokok' => 'decimal:2',
        'biaya_bunga' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'jumlah_angsuran' => 'decimal:2',
        'denda' => 'decimal:2',
        'total_bayar' => 'decimal:2',
    ];

    /**
     * Boot method untuk auto-generate kode bayar
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_bayar)) {
                $model->kode_bayar = self::generateKodeBayar();
            }
        });
    }

    /**
     * Generate kode bayar otomatis: TBY00001, TBY00002, dst
     */
    public static function generateKodeBayar(): string
    {
        // Ambil kode terakhir
        $lastKode = self::orderBy('kode_bayar', 'desc')->first();

        if (!$lastKode) {
            return 'TBY00001';
        }

        // Extract angka dari kode terakhir (TBY00001 -> 1)
        $lastNumber = (int) substr($lastKode->kode_bayar, 3);
        $newNumber = $lastNumber + 1;

        // Format dengan leading zeros (5 digit)
        return 'TBY' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi ke PinjamanLunas
     */
    public function pinjamanLunas(): BelongsTo
    {
        return $this->belongsTo(PinjamanLunas::class, 'pinjaman_lunas_id');
    }

    /**
     * Scope untuk filter by pinjaman lunas
     */
    public function scopeByPinjamanLunas($query, $pinjamanLunasId)
    {
        return $query->where('pinjaman_lunas_id', $pinjamanLunasId);
    }

    /**
     * Scope untuk order by angsuran ke
     */
    public function scopeOrderByAngsuran($query)
    {
        return $query->orderBy('angsuran_ke', 'asc');
    }

    /**
     * Accessor untuk format tanggal bayar
     */
    public function getTanggalBayarFormattedAttribute()
    {
        return $this->tanggal_bayar ? $this->tanggal_bayar->format('d-m-Y H:i') : '-';
    }
}