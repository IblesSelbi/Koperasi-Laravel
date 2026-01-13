<?php

namespace App\Models\Admin\Simpanan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\DataMaster\JenisSimpanan;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Support\Facades\DB;

class PenarikanTunai extends Model
{
    use SoftDeletes;

    protected $table = 'penarikan_tunai';

    protected $fillable = [
        'kode_transaksi',
        'tanggal_transaksi',
        'anggota_id',
        'jenis_simpanan_id',
        'jumlah',
        'dari_kas_id',
        'nama_penarik',
        'no_identitas',
        'alamat',
        'keterangan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'jumlah' => 'decimal:2',
    ];

    // Relasi ke Data Anggota
    public function anggota()
    {
        return $this->belongsTo(DataAnggota::class, 'anggota_id');
    }

    // Relasi ke Jenis Simpanan
    public function jenisSimpanan()
    {
        return $this->belongsTo(JenisSimpanan::class, 'jenis_simpanan_id');
    }

    // Relasi ke Data Kas
    public function dariKas()
    {
        return $this->belongsTo(DataKas::class, 'dari_kas_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Generate Kode Transaksi Otomatis (PNR00001, PNR00002, ...)
    public static function generateKodeTransaksi()
    {
        return DB::transaction(function () {
            $latest = self::withTrashed()
                ->lockForUpdate()
                ->orderBy('kode_transaksi', 'desc')
                ->first();
            
            if ($latest) {
                $lastNumber = intval(substr($latest->kode_transaksi, 3));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            return 'TRK' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}