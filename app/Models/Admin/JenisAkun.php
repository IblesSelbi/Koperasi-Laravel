<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class JenisAkun extends Model
{
    protected $table = 'jenis_akun';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'kd_aktiva',
        'jns_transaksi',
        'akun',
        'pemasukan',
        'pengeluaran',
        'aktif',
        'laba_rugi'
    ];

    // Cast attributes untuk ENUM
    protected $casts = [
        'pemasukan' => 'string',
        'pengeluaran' => 'string',
        'aktif' => 'string',
    ];

    // Default values untuk ENUM
    protected $attributes = [
        'pemasukan' => 'Y',
        'pengeluaran' => 'Y',
        'aktif' => 'Y',
    ];
}