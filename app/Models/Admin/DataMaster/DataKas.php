<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Model;

class DataKas extends Model
{
    protected $table = 'data_kas';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nama_kas',
        'aktif',
        'simpanan',
        'penarikan',
        'pinjaman',
        'angsuran',
        'pemasukan_kas',
        'pengeluaran_kas',
        'transfer_kas'
    ];

    // Cast attributes untuk ENUM
    protected $casts = [
        'aktif' => 'string',
        'simpanan' => 'string',
        'penarikan' => 'string',
        'pinjaman' => 'string',
        'angsuran' => 'string',
        'pemasukan_kas' => 'string',
        'pengeluaran_kas' => 'string',
        'transfer_kas' => 'string',
    ];

    // Default values untuk ENUM
    protected $attributes = [
        'aktif' => 'Y',
        'simpanan' => 'Y',
        'penarikan' => 'Y',
        'pinjaman' => 'Y',
        'angsuran' => 'Y',
        'pemasukan_kas' => 'Y',
        'pengeluaran_kas' => 'Y',
        'transfer_kas' => 'Y',
    ];
}