<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\DataAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DataAnggotaController extends Controller
{
    public function index()
    {
        $dataAnggota = DataAnggota::with('user')->orderBy('id', 'desc')->get();

        return view('admin.DataMaster.DataAnggota.DataAnggota', compact('dataAnggota'));
    }

    public function edit($id)
    {
        $anggota = DataAnggota::findOrFail($id);

        return response()->json([
            'id' => $anggota->id,
            'nama' => $anggota->nama,
            'username' => $anggota->username,
            'jenis_kelamin' => $anggota->jenis_kelamin,
            'tempat_lahir' => $anggota->tempat_lahir,
            'tanggal_lahir' => $anggota->tanggal_lahir
                ? \Carbon\Carbon::parse($anggota->tanggal_lahir)->format('Y-m-d')
                : null,
            'status' => $anggota->status,
            'departement' => $anggota->departement,
            'pekerjaan' => $anggota->pekerjaan,
            'agama' => $anggota->agama,
            'alamat' => $anggota->alamat,
            'kota' => $anggota->kota,
            'no_telp' => $anggota->no_telp,
            'tanggal_registrasi' => $anggota->tanggal_registrasi
                ? \Carbon\Carbon::parse($anggota->tanggal_registrasi)->format('Y-m-d')
                : null,
            'jabatan' => $anggota->jabatan,
            'aktif' => $anggota->aktif,
            'has_user' => $anggota->user_id ? true : false,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:Data_Anggota,username',
                'password' => 'required|string|min:8|max:255',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'tempat_lahir' => 'required|string|max:225',
                'tanggal_lahir' => 'required|date',
                'status' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati,Lainnya',
                'departement' => 'nullable|string|max:100',
                'pekerjaan' => 'nullable|string|max:100',
                'agama' => 'nullable|string|max:50',
                'alamat' => 'required|string',
                'kota' => 'required|string|max:255',
                'no_telp' => 'nullable|string|max:12',
                'tanggal_registrasi' => 'required|date',
                'jabatan' => 'required|in:Anggota,Pengurus',
                'aktif' => 'required|in:Aktif,Non Aktif',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ], [
                'nama.required' => 'Nama lengkap wajib diisi',
                'username.required' => 'Username wajib diisi',
                'username.unique' => 'Username sudah digunakan',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
                'tempat_lahir.required' => 'Tempat lahir wajib diisi',
                'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
                'alamat.required' => 'Alamat wajib diisi',
                'kota.required' => 'Kota wajib diisi',
                'tanggal_registrasi.required' => 'Tanggal registrasi wajib diisi',
                'jabatan.required' => 'Jabatan wajib dipilih',
                'aktif.required' => 'Status aktif wajib dipilih',
                'photo.image' => 'File harus berupa gambar',
                'photo.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
                'photo.max' => 'Ukuran foto maksimal 2MB'
            ]);

            // Gunakan transaction untuk memastikan data konsisten
            DB::beginTransaction();

            try {
                // Generate ID Anggota
                $data['id_anggota'] = DataAnggota::generateIdAnggota();

                // Handle photo upload
                if ($request->hasFile('photo')) {
                    $file = $request->file('photo');
                    $fileName = 'anggota_' . time() . '.' . $file->extension();
                    $path = $file->storeAs('anggota', $fileName, 'public');
                    $data['photo'] = $path;

                    Log::info('Photo uploaded:', [
                        'path' => $path,
                        'full_path' => storage_path('app/public/' . $path),
                        'file_exists' => file_exists(storage_path('app/public/' . $path))
                    ]);
                }

                // Simpan password plain untuk user (akan di-hash di model)
                $plainPassword = $data['password'];

                // Buat data anggota (akan otomatis membuat user account via model event)
                $anggota = DataAnggota::create($data);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data anggota dan akun user berhasil dibuat. Username: ' . $anggota->username . ', Password: (sesuai yang diinput)'
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error storing data anggota: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $anggota = DataAnggota::findOrFail($id);

            $data = $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:Data_Anggota,username,' . $id,
                'password' => 'nullable|string|min:8|max:255',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'tempat_lahir' => 'required|string|max:225',
                'tanggal_lahir' => 'required|date',
                'status' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati,Lainnya',
                'departement' => 'nullable|string|max:100',
                'pekerjaan' => 'nullable|string|max:100',
                'agama' => 'nullable|string|max:50',
                'alamat' => 'required|string',
                'kota' => 'required|string|max:255',
                'no_telp' => 'nullable|string|max:12',
                'tanggal_registrasi' => 'required|date',
                'jabatan' => 'required|in:Anggota,Pengurus',
                'aktif' => 'required|in:Aktif,Non Aktif',
                'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
            ], [
                'nama.required' => 'Nama lengkap wajib diisi',
                'username.required' => 'Username wajib diisi',
                'username.unique' => 'Username sudah digunakan',
                'password.min' => 'Password minimal 8 karakter',
                'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih',
                'tempat_lahir.required' => 'Tempat lahir wajib diisi',
                'tanggal_lahir.required' => 'Tanggal lahir wajib diisi',
                'alamat.required' => 'Alamat wajib diisi',
                'kota.required' => 'Kota wajib diisi',
                'tanggal_registrasi.required' => 'Tanggal registrasi wajib diisi',
                'jabatan.required' => 'Jabatan wajib dipilih',
                'aktif.required' => 'Status aktif wajib dipilih',
                'photo.image' => 'File harus berupa gambar',
                'photo.mimes' => 'Format foto harus JPG, JPEG, atau PNG',
                'photo.max' => 'Ukuran foto maksimal 2MB'
            ]);

            DB::beginTransaction();

            try {
                // Hapus password dari data jika kosong
                if (empty($data['password'])) {
                    unset($data['password']);
                }

                // Handle photo upload
                if ($request->hasFile('photo')) {
                    // Hapus foto lama jika bukan default
                    if ($anggota->photo && 
                        $anggota->photo !== 'assets/images/profile/user-1.jpg' && 
                        Storage::disk('public')->exists($anggota->photo)) {
                        
                        Storage::disk('public')->delete($anggota->photo);
                        
                        Log::info('Old photo deleted:', [
                            'path' => $anggota->photo
                        ]);
                    }

                    // Upload foto baru
                    $file = $request->file('photo');
                    $fileName = 'anggota_' . time() . '.' . $file->extension();
                    $path = $file->storeAs('anggota', $fileName, 'public');
                    $data['photo'] = $path;

                    Log::info('New photo uploaded:', [
                        'path' => $path,
                        'full_path' => storage_path('app/public/' . $path),
                        'file_exists' => file_exists(storage_path('app/public/' . $path))
                    ]);
                }

                // Update data anggota (akan otomatis update user account via model event)
                $anggota->update($data);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data anggota dan akun user berhasil diperbarui'
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating data anggota: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $anggota = DataAnggota::findOrFail($id);

            // Cek apakah anggota bisa dihapus
            if (!$anggota->canBeDeleted()) {
                $blockingTransactions = $anggota->getBlockingTransactions();
                
                return response()->json([
                    'success' => false,
                    'message' => 'Data anggota tidak dapat dihapus karena masih memiliki transaksi aktif',
                    'blocking_transactions' => $blockingTransactions,
                    'suggestion' => 'Silakan ubah status menjadi "Non Aktif" jika ingin menonaktifkan anggota ini'
                ], 400);
            }

            DB::beginTransaction();

            try {
                // Hapus foto jika bukan default
                if ($anggota->photo && 
                    $anggota->photo !== 'assets/images/profile/user-1.jpg' && 
                    Storage::disk('public')->exists($anggota->photo)) {
                    
                    Storage::disk('public')->delete($anggota->photo);
                    
                    Log::info('Photo deleted on destroy:', [
                        'path' => $anggota->photo
                    ]);
                }

                // Hapus data anggota (akan otomatis hapus user account via model event)
                $anggota->delete();

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data anggota dan akun user berhasil dihapus'
                ]);

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Log::error('Error deleting data anggota: ' . $e->getMessage());
            
            // Cek apakah error karena foreign key constraint
            if (strpos($e->getMessage(), 'foreign key constraint') !== false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data anggota tidak dapat dihapus karena masih memiliki transaksi terkait. Silakan ubah status menjadi "Non Aktif" sebagai gantinya.',
                    'error_detail' => $e->getMessage()
                ], 400);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Method baru untuk menonaktifkan anggota (soft disable)
    public function deactivate($id)
    {
        try {
            $anggota = DataAnggota::findOrFail($id);
            
            DB::beginTransaction();
            
            try {
                // Update status menjadi Non Aktif
                $anggota->update(['aktif' => 'Non Aktif']);
                
                // Update juga status user jika ada
                if ($anggota->user) {
                    // Anda bisa tambahkan kolom status di tabel users jika diperlukan
                    // atau biarkan user tetap bisa login tapi dengan akses terbatas
                }
                
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Anggota berhasil dinonaktifkan'
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            Log::error('Error deactivating anggota: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function showImport()
    {
        return view('admin.DataMaster.DataAnggota.ImportAnggota');
    }

    public function processImport(Request $request)
    {
        try {
            $validated = $request->validate([
                'data' => 'required|array',
                'data.*.username' => 'required|string|max:255|unique:Data_Anggota,username',
                'data.*.nama' => 'required|string|max:255',
                'data.*.jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'data.*.tempat_lahir' => 'nullable|string|max:225',
                'data.*.tanggal_lahir' => 'nullable|date',
                'data.*.status' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati,Lainnya',
                'data.*.departement' => 'nullable|string|max:100',
                'data.*.pekerjaan' => 'nullable|string|max:100',
                'data.*.agama' => 'nullable|string|max:50',
                'data.*.alamat' => 'required|string',
                'data.*.kota' => 'required|string|max:255',
                'data.*.no_telp' => 'nullable|string|max:12',
                'data.*.jabatan' => 'nullable|in:Anggota,Pengurus',
            ], [
                'data.*.username.required' => 'Username wajib diisi',
                'data.*.username.unique' => 'Username sudah digunakan',
                'data.*.nama.required' => 'Nama lengkap wajib diisi',
                'data.*.jenis_kelamin.required' => 'Jenis kelamin wajib diisi',
                'data.*.alamat.required' => 'Alamat wajib diisi',
                'data.*.kota.required' => 'Kota wajib diisi',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi data gagal',
                'errors' => $e->errors()
            ], 422);
        }

        $successCount = 0;
        $failCount = 0;
        $results = [];

        foreach ($request->data as $index => $row) {
            DB::beginTransaction();
            
            try {
                // Generate ID Anggota
                $row['id_anggota'] = DataAnggota::generateIdAnggota();

                // Set default values
                $row['password'] = '12345678'; // Default password minimal 8 karakter
                $row['tanggal_registrasi'] = date('Y-m-d');
                $row['jabatan'] = $row['jabatan'] ?? 'Anggota';
                $row['aktif'] = 'Aktif';

                // Buat data anggota (akan otomatis membuat user account)
                $anggota = DataAnggota::create($row);

                DB::commit();

                $results[] = [
                    'status' => 'success',
                    'id_anggota' => $row['id_anggota'],
                    'username' => $row['username'],
                    'nama' => $row['nama'],
                    'jenis_kelamin' => $row['jenis_kelamin'] ?? '-',
                    'alamat' => $row['alamat'] ?? '-',
                    'kota' => $row['kota'] ?? '-',
                    'jabatan' => $row['jabatan'],
                    'keterangan' => 'Berhasil diimport (user account dibuat otomatis)'
                ];

                $successCount++;
            } catch (\Exception $e) {
                DB::rollBack();
                
                $results[] = [
                    'status' => 'failed',
                    'id_anggota' => '-',
                    'username' => $row['username'] ?? '',
                    'nama' => $row['nama'] ?? '',
                    'jenis_kelamin' => $row['jenis_kelamin'] ?? '-',
                    'alamat' => $row['alamat'] ?? '-',
                    'kota' => $row['kota'] ?? '-',
                    'jabatan' => $row['jabatan'] ?? '-',
                    'keterangan' => $e->getMessage()
                ];

                $failCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "$successCount data berhasil diimport" . ($failCount > 0 ? ", $failCount data gagal" : ""),
            'successCount' => $successCount,
            'failCount' => $failCount,
            'results' => $results
        ]);
    }

    public function export()
    {
        // TODO: Implement Excel export
        return response('Export Excel Data Anggota');
    }

    public function cetak()
    {
        // TODO: Implement print view
        return response('Cetak Laporan Data Anggota');
    }
}