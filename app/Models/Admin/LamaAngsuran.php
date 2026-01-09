<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class LamaAngsuran extends Model
{
    protected $table = 'lama_angsuran';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'lama_angsuran',
        'aktif'
    ];

    // Cast attributes untuk ENUM
    protected $casts = [
        'aktif' => 'string',
        'lama_angsuran' => 'integer',
    ];

    // Default values untuk ENUM
    protected $attributes = [
        'aktif' => 'Y',
    ];
}