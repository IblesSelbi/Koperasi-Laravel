<?php

namespace App\Models\Admin\TransaksiKas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\Admin\DataMaster\JenisAkun;
use Illuminate\Support\Facades\DB;

class Pengeluaran extends Model
{
    use SoftDeletes;

    protected $table = 'pengeluaran';

    protected $fillable = [
        'kode_transaksi',
        'tanggal_transaksi',
        'uraian',
        'dari_kas_id',
        'untuk_akun_id',
        'jumlah',
        'user_id',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'jumlah' => 'decimal:2',
    ];

    // Relasi ke Data Kas
    public function dariKas()
    {
        return $this->belongsTo(DataKas::class, 'dari_kas_id');
    }

    // Relasi ke Jenis Akun
    public function untukAkun()
    {
        return $this->belongsTo(JenisAkun::class, 'untuk_akun_id');
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Generate Kode Transaksi Otomatis (termasuk yang dihapus)
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
            
            return 'TKK' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}