<?php

namespace App\Models\Admin\Setting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IdentitasKoperasi extends Model
{
    use HasFactory;

    protected $table = 'identitas_koperasi';

    protected $fillable = [
        'nama_lembaga',
        'nama_ketua',
        'hp_ketua',
        'alamat',
        'telepon',
        'kota',
        'email',
        'web',
        'logo',
    ];

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute()
    {
        return asset($this->logo);
    }
}