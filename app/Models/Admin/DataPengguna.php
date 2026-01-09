<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class DataPengguna extends Model
{
    protected $table = 'data_pengguna';

    protected $primaryKey = 'id';
    
    protected $fillable = [
        'username',
        'password',
        'level',
        'status'
    ];

    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'level' => 'string',
        'status' => 'string'
    ];

    protected $attributes = [
        'level' => 'operator',
        'status' => 'Y'
    ];

    // Auto hash password saat create/update
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }
}