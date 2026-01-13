<?php

namespace App\Models\Admin\Pinjaman;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Models\Admin\DataMaster\LamaAngsuran;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengajuanPinjaman extends Model
{
    use SoftDeletes;

    protected $table = 'pengajuan_pinjaman';

    protected $fillable = [
        'id_ajuan',
        'tanggal_pengajuan',
        'anggota_id',
        'jenis_pinjaman',
        'jumlah',
        'lama_angsuran_id',
        'keterangan',
        'status',
        'tanggal_cair',
        'alasan',
        'approved_by',
        'user_id',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'datetime',
        'tanggal_cair' => 'date',
        'jumlah' => 'decimal:2',
        'status' => 'integer',
    ];

    protected $appends = [
        'anggota_nama',
        'anggota_id_anggota',
        'anggota_departemen',
        'jumlah_angsuran',
        'status_label',
        'status_badge',
    ];

    /**
     * ========================================
     * RELATIONSHIPS
     * ========================================
     */
    
    public function anggota()
    {
        return $this->belongsTo(DataAnggota::class, 'anggota_id');
    }

    public function lamaAngsuran()
    {
        return $this->belongsTo(LamaAngsuran::class, 'lama_angsuran_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * ========================================
     * ACCESSORS
     * ========================================
     */
    
    public function getAnggotaNamaAttribute()
    {
        return $this->anggota->nama ?? '-';
    }

    public function getAnggotaIdAnggotaAttribute()
    {
        return $this->anggota->id_anggota ?? '-';
    }

    public function getAnggotaDepartemenAttribute()
    {
        return $this->anggota->departement ?? '-';
    }

    public function getJumlahAngsuranAttribute()
    {
        return $this->lamaAngsuran->lama_angsuran ?? 0;
    }

    public function getStatusLabelAttribute()
    {
        return self::getStatusLabel($this->status);
    }

    public function getStatusBadgeAttribute()
    {
        return self::getStatusBadgeClass($this->status);
    }

    public function getAngsuranPerBulanAttribute()
    {
        if ($this->jumlah_angsuran > 0) {
            return ceil($this->jumlah / $this->jumlah_angsuran);
        }
        return 0;
    }

    /**
     * ========================================
     * STATIC METHODS
     * ========================================
     */
    
    /**
     * Generate ID Ajuan dengan format: B.YY.MM.XXX
     * B = Biasa, D = Darurat, G = Barang
     */
    public static function generateIdAjuan($jenis)
    {
        return DB::transaction(function () use ($jenis) {
            $prefix = match($jenis) {
                'Biasa' => 'B',
                'Darurat' => 'D',
                'Barang' => 'G',
                default => 'B',
            };

            $now = Carbon::now();
            $year = $now->format('y');
            $month = $now->format('m');

            $pattern = "{$prefix}.{$year}.{$month}.%";
            
            $latest = self::withTrashed()
                ->where('id_ajuan', 'like', $pattern)
                ->lockForUpdate()
                ->orderBy('id_ajuan', 'desc')
                ->first();

            $newNumber = $latest ? intval(substr($latest->id_ajuan, -3)) + 1 : 1;

            return sprintf('%s.%s.%s.%03d', $prefix, $year, $month, $newNumber);
        });
    }

    public static function getStatusLabel($status)
    {
        return match($status) {
            0 => 'Menunggu Konfirmasi',
            1 => 'Disetujui',
            2 => 'Ditolak',
            3 => 'Terlaksana',
            4 => 'Batal',
            default => 'Unknown',
        };
    }

    public static function getStatusBadgeClass($status)
    {
        return match($status) {
            0 => 'bg-primary-subtle text-primary',
            1 => 'bg-success-subtle text-success',
            2 => 'bg-danger-subtle text-danger',
            3 => 'bg-info-subtle text-info',
            4 => 'bg-secondary-subtle text-secondary',
            default => 'bg-light text-dark',
        };
    }

    /**
     * ========================================
     * SCOPES
     * ========================================
     */
    
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_pinjaman', $jenis);
    }

    public function scopePending($query)
    {
        return $query->where('status', 0);
    }

    public function scopeDisetujui($query)
    {
        return $query->where('status', 1);
    }

    public function scopeTerlaksana($query)
    {
        return $query->where('status', 3);
    }

    /**
     * ========================================
     * HELPER METHODS
     * ========================================
     */
    
    public function canBeEditedByUser()
    {
        return $this->status === 0;
    }

    public function canBeCancelledByUser()
    {
        return $this->status === 0;
    }

    public function canBeApproved()
    {
        return $this->status === 0;
    }

    public function canBeRejected()
    {
        return in_array($this->status, [0, 1]);
    }

    public function canBeMarkedTerlaksana()
    {
        return $this->status === 1;
    }
}