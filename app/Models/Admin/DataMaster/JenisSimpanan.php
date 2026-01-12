<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Model;

class JenisSimpanan extends Model
{
    protected $table = 'jenis_simpanan';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'jenis_simpanan',
        'jumlah',
        'tampil'
    ];
}
