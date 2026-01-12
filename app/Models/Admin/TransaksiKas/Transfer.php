<?php

namespace App\Models\Admin\TransaksiKas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Support\Facades\DB;

class Transfer extends Model
{
    use SoftDeletes;

    protected $table = 'transfer';

    protected $fillable = [
        'kode_transaksi',
        'tanggal_transaksi',
        'uraian',
        'dari_kas_id',
        'untuk_kas_id',
        'jumlah',
        'user_id',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'jumlah' => 'decimal:2',
    ];

    // Relasi ke Data Kas (Dari)
    public function dariKas()
    {
        return $this->belongsTo(DataKas::class, 'dari_kas_id');
    }

    // Relasi ke Data Kas (Untuk)
    public function untukKas()
    {
        return $this->belongsTo(DataKas::class, 'untuk_kas_id');
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
            
            return 'TRF' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}