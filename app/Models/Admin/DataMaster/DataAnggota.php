<?php

namespace App\Models\Admin\DataMaster;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
        'aktif',
        'user_id' 
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

    // Event lifecycle untuk sinkronisasi dengan tabel users
    protected static function boot()
    {
        parent::boot();

        // Ketika data anggota dibuat, buat juga user account
        static::created(function ($anggota) {
            $anggota->createUserAccount();
        });

        // Ketika data anggota diupdate, update juga user account
        static::updated(function ($anggota) {
            $anggota->updateUserAccount();
        });

        // Ketika data anggota dihapus, hapus juga user account
        static::deleted(function ($anggota) {
            $anggota->deleteUserAccount();
        });
    }

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Accessor untuk photo URL
    public function getPhotoUrlAttribute()
    {
        if (!$this->photo || $this->photo === 'assets/images/profile/user-1.jpg') {
            return asset('assets/images/profile/user-1.jpg');
        }

        return asset('storage/' . $this->photo);
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

    // Method untuk membuat user account
    public function createUserAccount()
    {
        // Jika sudah ada user_id, skip
        if ($this->user_id) {
            return;
        }

        // Generate email dari username atau nama
        $email = $this->generateEmail();

        // Tentukan role_id berdasarkan jabatan
        // Semua anggota (baik Anggota maupun Pengurus) = role_id 2 (user)
        // Hanya admin yang role_id 1
        $roleId = 2; // Semua anggota dapat role user

        // Buat user baru
        $user = User::create([
            'name' => $this->nama,
            'email' => $email,
            'password' => $this->password, // sudah di-hash di setPasswordAttribute
            'role_id' => $roleId
        ]);

        // Update data anggota dengan user_id
        $this->update(['user_id' => $user->id]);
    }

    // Method untuk update user account
    public function updateUserAccount()
    {
        if (!$this->user_id || !$this->user) {
            return;
        }

        $updateData = [
            'name' => $this->nama,
            'email' => $this->generateEmail(),
        ];

        // Semua anggota tetap role_id = 2 (user)
        $updateData['role_id'] = 2;

        // Update password jika ada perubahan
        if ($this->isDirty('password') && !empty($this->password)) {
            $updateData['password'] = $this->password;
        }

        $this->user->update($updateData);
    }

    // Method untuk hapus user account
    public function deleteUserAccount()
    {
        if ($this->user_id && $this->user) {
            // Cek apakah user ini bukan admin (role_id = 1)
            // Kita tidak ingin menghapus akun admin
            if ($this->user->role_id != 1) {
                $this->user->delete();
            }
        }
    }
    
    // Method untuk cek apakah anggota bisa dihapus
    public function canBeDeleted()
    {
        // Cek relasi yang akan memblokir penghapusan
        $hasTransactions = false;
        
        // Cek apakah ada transaksi setoran
        if (\Schema::hasTable('setoran_tunai')) {
            $hasTransactions = $hasTransactions || \DB::table('setoran_tunai')
                ->where('anggota_id', $this->id)
                ->exists();
        }
        
        // Cek apakah ada transaksi penarikan
        if (\Schema::hasTable('penarikan_tunai')) {
            $hasTransactions = $hasTransactions || \DB::table('penarikan_tunai')
                ->where('anggota_id', $this->id)
                ->exists();
        }
        
        // Cek apakah ada pinjaman
        if (\Schema::hasTable('pinjaman')) {
            $hasTransactions = $hasTransactions || \DB::table('pinjaman')
                ->where('anggota_id', $this->id)
                ->exists();
        }
        
        // Cek apakah ada pengajuan pinjaman
        if (\Schema::hasTable('pengajuan_pinjaman')) {
            $hasTransactions = $hasTransactions || \DB::table('pengajuan_pinjaman')
                ->where('anggota_id', $this->id)
                ->exists();
        }
        
        return !$hasTransactions;
    }
    
    // Method untuk mendapatkan daftar transaksi yang mencegah penghapusan
    public function getBlockingTransactions()
    {
        $blocking = [];
        
        if (\Schema::hasTable('setoran_tunai')) {
            $count = \DB::table('setoran_tunai')->where('anggota_id', $this->id)->count();
            if ($count > 0) {
                $blocking[] = "Setoran Tunai ($count transaksi)";
            }
        }
        
        if (\Schema::hasTable('penarikan_tunai')) {
            $count = \DB::table('penarikan_tunai')->where('anggota_id', $this->id)->count();
            if ($count > 0) {
                $blocking[] = "Penarikan Tunai ($count transaksi)";
            }
        }
        
        if (\Schema::hasTable('pinjaman')) {
            $count = \DB::table('pinjaman')->where('anggota_id', $this->id)->count();
            if ($count > 0) {
                $blocking[] = "Pinjaman ($count transaksi)";
            }
        }
        
        if (\Schema::hasTable('pengajuan_pinjaman')) {
            $count = \DB::table('pengajuan_pinjaman')->where('anggota_id', $this->id)->count();
            if ($count > 0) {
                $blocking[] = "Pengajuan Pinjaman ($count transaksi)";
            }
        }
        
        return $blocking;
    }

    // Helper method untuk generate email
    private function generateEmail()
    {
        // Gunakan username sebagai email (tambahkan domain default)
        $email = strtolower($this->username) . '@gmail.com';
        
        // Atau bisa menggunakan format lain, misalnya:
        // $email = strtolower(str_replace(' ', '.', $this->nama)) . '@koperasi.local';
        
        return $email;
    }

    // Method untuk cek apakah anggota bisa login
    public function canLogin()
    {
        return $this->aktif === 'Aktif' && $this->user_id;
    }
}