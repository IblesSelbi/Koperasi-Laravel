<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class DataAnggota extends Model
{
    protected $table = 'Data_Anggota';

    protected $fillable = [
        'photo',
        'id_anggota',
        'username',
        'password',
        'nama',
        'jenis_kelamin',
        'tempat_lahir',
        'tanggal_lahir',
        'status',
        'departement',
        'pekerjaan',
        'agama',
        'alamat',
        'kota',
        'no_telp',
        'tanggal_registrasi',
        'jabatan',
        'aktif'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_registrasi' => 'date',
        'jenis_kelamin' => 'string',
        'status' => 'string',
        'jabatan' => 'string',
        'aktif' => 'string'
    ];

    protected $attributes = [
        'photo' => 'assets/images/profile/user-1.jpg',
        'jabatan' => 'Anggota',
        'aktif' => 'Aktif'
    ];

    protected $hidden = [
        'password'
    ];

    // Accessor untuk photo URL
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo) {
            return asset('assets/images/profile/user-1.jpg');
        }

        // kalau dari storage
        if (str_starts_with($this->photo, 'storage/')) {
            return asset($this->photo);
        }

        return asset('assets/images/profile/user-1.jpg');
    }

    // Auto hash password saat set
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

    // Generate ID Anggota otomatis
    public static function generateIdAnggota()
    {
        $lastAnggota = self::orderBy('id_anggota', 'desc')->first();

        if (!$lastAnggota) {
            return 'AG0001';
        }

        $lastNumber = (int) substr($lastAnggota->id_anggota, 2);
        $newNumber = $lastNumber + 1;

        return 'AG' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}