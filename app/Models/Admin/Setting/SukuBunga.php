<?php

namespace App\Models\Admin\Setting;

use Illuminate\Database\Eloquent\Model;

class SukuBunga extends Model
{
    protected $table = 'suku_bunga';

    protected $fillable = [
        'pinjaman_bunga_tipe',
        'bg_pinjam',
        'biaya_adm',
        'denda',
        'denda_hari',
        'dana_cadangan',
        'jasa_usaha',
        'jasa_anggota',
        'jasa_modal',
        'dana_pengurus',
        'dana_karyawan',
        'dana_pend',
        'dana_sosial',
        'pjk_pph',
    ];

    protected $casts = [
        'bg_pinjam' => 'decimal:2',
        'biaya_adm' => 'decimal:2',
        'denda' => 'decimal:2',
        'dana_cadangan' => 'decimal:2',
        'jasa_usaha' => 'decimal:2',
        'jasa_anggota' => 'decimal:2',
        'jasa_modal' => 'decimal:2',
        'dana_pengurus' => 'decimal:2',
        'dana_karyawan' => 'decimal:2',
        'dana_pend' => 'decimal:2',
        'dana_sosial' => 'decimal:2',
        'pjk_pph' => 'decimal:2',
    ];

    /**
     * Get setting instance (singleton)
     */
    public static function getSetting()
    {
        $setting = self::first();
        
        if (!$setting) {
            $setting = self::create([
                'pinjaman_bunga_tipe' => 'B',
                'bg_pinjam' => 5.00,
                'denda_hari' => 15,
            ]);
        }
        
        return $setting;
    }
}