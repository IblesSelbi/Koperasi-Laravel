<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Admin\DataMaster\DataAnggota;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke Role
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Relasi ke DataAnggota
     * Satu user memiliki satu data anggota
     */
    public function anggota()
    {
        return $this->hasOne(DataAnggota::class, 'user_id');
    }

    /**
     * Accessor untuk mendapatkan anggota_id
     * Menggunakan relasi, bukan mapping hardcode
     */
    public function getAnggotaIdAttribute()
    {
        return $this->anggota ? $this->anggota->id : null;
    }
}