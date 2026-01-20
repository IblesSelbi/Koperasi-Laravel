<?php

namespace App\Models\Admin\Pinjaman;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PinjamanLunas extends Model
{
    use SoftDeletes;

    protected $table = 'pinjaman_lunas';

    protected $fillable = [
        'kode_lunas',
        'pinjaman_id',
        'tanggal_lunas',
        'total_pokok',
        'total_bunga',
        'total_denda',
        'total_dibayar',
        'lama_cicilan',
        'total_angsuran',
        'keterangan',
        'user_id',
        'deleted_by',
        'alasan_batal',
    ];

    protected $casts = [
        'tanggal_lunas' => 'datetime', 
        'total_pokok' => 'decimal:2',
        'total_bunga' => 'decimal:2',
        'total_denda' => 'decimal:2',
        'total_dibayar' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    /**
     * Boot method untuk auto-generate kode
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->kode_lunas)) {
                $model->kode_lunas = self::generateKode();
            }
        });
    }

    /**
     * Generate kode lunas otomatis: TPJ00001, TPJ00002, dst
     */
    public static function generateKode(): string
    {
        $lastKode = self::withTrashed()->orderBy('kode_lunas', 'desc')->first();

        if (!$lastKode) {
            return 'TPJ00001';
        }

        $lastNumber = (int) substr($lastKode->kode_lunas, 3);
        $newNumber = $lastNumber + 1;

        return 'TPJ' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi ke Pinjaman (Many to One)
     */
    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class, 'pinjaman_id');
    }

    /**
     * Relasi ke User yang validasi (Many to One)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Relasi ke User yang membatalkan (Many to One)
     */
    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }

    /**
     * Relasi ke DetailPinjamanLunas (One to Many)
     */
    public function detailPembayaran(): HasMany
    {
        return $this->hasMany(DetailPinjamanLunas::class, 'pinjaman_lunas_id');
    }

    /**
     * FIXED: Method batalkan dengan alasan
     * Harus update dulu SEBELUM delete()
     */
    public function batalkanDenganAlasan($alasan, $userId)
    {
        // Step 1: Update kolom tracking DULU
        $this->update([
            'deleted_by' => $userId,
            'alasan_batal' => $alasan
        ]);

        // Step 2: Baru soft delete
        return $this->delete();
    }

    /**
     * Scope untuk filter tanggal
     */
    public function scopeFilterTanggal($query, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('tanggal_lunas', [$startDate, $endDate]);
        }
        return $query;
    }

    /**
     * Scope untuk filter kode
     */
    public function scopeFilterKode($query, $kode)
    {
        if ($kode) {
            return $query->where('kode_lunas', 'like', '%' . $kode . '%');
        }
        return $query;
    }
}