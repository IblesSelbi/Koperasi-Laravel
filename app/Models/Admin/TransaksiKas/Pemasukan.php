<?php

namespace App\Models\Admin\TransaksiKas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // ← Pastikan ada ini
use App\Models\User;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\Admin\DataMaster\JenisAkun;
use Illuminate\Support\Facades\DB;

class Pemasukan extends Model
{
    use SoftDeletes; // ← Pastikan ada ini

    protected $table = 'pemasukan';

    protected $fillable = [
        'kode_transaksi',
        'tanggal_transaksi',
        'uraian',
        'untuk_kas_id',
        'dari_akun_id',
        'jumlah',
        'user_id',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'jumlah' => 'decimal:2',
    ];

    // Relasi
    public function untukKas()
    {
        return $this->belongsTo(DataKas::class, 'untuk_kas_id');
    }

    public function dariAkun()
    {
        return $this->belongsTo(JenisAkun::class, 'dari_akun_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Generate Kode - Cek termasuk yang dihapus
    public static function generateKodeTransaksi()
    {
        return DB::transaction(function () {
            $latest = self::withTrashed() // ← Ini yang penting!
                ->lockForUpdate()
                ->orderBy('kode_transaksi', 'desc')
                ->first();
            
            if ($latest) {
                $lastNumber = intval(substr($latest->kode_transaksi, 3));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            
            return 'TKD' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
        });
    }
}