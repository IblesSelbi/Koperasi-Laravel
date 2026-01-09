<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class DataBarang extends Model
{
    protected $table = 'data_barang';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nama_barang',
        'type',
        'merk',
        'harga',
        'jumlah',
        'keterangan'
    ];

    // Cast attributes
    protected $casts = [
        'harga' => 'decimal:2',
        'jumlah' => 'integer',
    ];
}