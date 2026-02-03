<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\DataAnggota;
use App\Exports\Admin\DataMaster\DataAnggotaExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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

            DB::beginTransaction();

            try {
                // Generate ID Anggota
                $data['id_anggota'] = DataAnggota::generateIdAnggota();

                // Handle photo upload
                $photoPath = null;
                if ($request->hasFile('photo')) {
                    $file = $request->file('photo');
                    $fileName = 'anggota_' . time() . '.' . $file->extension();
                    $photoPath = $file->storeAs('anggota', $fileName, 'public');
                    $data['photo'] = $photoPath;

                    Log::info('Photo uploaded:', [
                        'path' => $photoPath,
                        'full_path' => storage_path('app/public/' . $photoPath),
                        'file_exists' => file_exists(storage_path('app/public/' . $photoPath))
                    ]);
                }

                // Buat data anggota (akan otomatis membuat user account via model event)
                $anggota = DataAnggota::create($data);

                // ✅ SINKRONISASI: Update foto di user jika foto di-upload
                if ($photoPath && $anggota->user) {
                    $anggota->user->update([
                        'profile_image' => $photoPath
                    ]);

                    Log::info('Photo synced to User on create', [
                        'anggota_id' => $anggota->id,
                        'user_id' => $anggota->user_id,
                        'photo_path' => $photoPath
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data anggota dan akun user berhasil dibuat. Username: ' . $anggota->username
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
            ]);

            DB::beginTransaction();

            try {
                // Handle photo upload
                if ($request->hasFile('photo')) {
                    // Hapus foto lama jika ada
                    if ($anggota->photo && Storage::disk('public')->exists($anggota->photo)) {
                        Storage::disk('public')->delete($anggota->photo);
                    }

                    $file = $request->file('photo');
                    $fileName = 'anggota_' . time() . '.' . $file->extension();
                    $photoPath = $file->storeAs('anggota', $fileName, 'public');
                    $data['photo'] = $photoPath;
                }

                // Hapus password dari array jika kosong
                if (empty($data['password'])) {
                    unset($data['password']);
                }

                // Update data anggota
                $anggota->update($data);

                // Update user jika ada
                if ($anggota->user) {
                    $userData = [
                        'name' => $data['username'],
                    ];

                    if (!empty($data['password'])) {
                        $userData['password'] = bcrypt($data['password']);
                    }

                    if (isset($data['photo'])) {
                        $userData['profile_image'] = $data['photo'];
                    }

                    $anggota->user->update($userData);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Data anggota berhasil diperbarui'
                ]);

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
        DB::beginTransaction();

        try {
            $anggota = DataAnggota::findOrFail($id);

            // Hapus foto jika ada
            if ($anggota->photo && Storage::disk('public')->exists($anggota->photo)) {
                Storage::disk('public')->delete($anggota->photo);
            }

            // Hapus user jika ada
            if ($anggota->user) {
                $anggota->user->delete();
            }

            // Hapus data anggota
            $anggota->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data anggota berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting anggota: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deactivate($id)
    {
        try {
            $anggota = DataAnggota::findOrFail($id);

            DB::beginTransaction();

            try {
                // Update status menjadi Non Aktif
                $anggota->update(['aktif' => 'Non Aktif']);

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
        // Log untuk debugging
        Log::info('Import Request Data:', $request->all());

        // Validasi request harus ada key 'data' dan harus array
        if (!$request->has('data') || !is_array($request->data)) {
            return response()->json([
                'success' => false,
                'message' => 'Format data tidak valid. Data harus berupa array.',
                'errors' => ['data' => ['Format data salah']]
            ], 422);
        }

        // Cek apakah data kosong
        if (empty($request->data)) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak boleh kosong',
                'errors' => ['data' => ['Data kosong']]
            ], 422);
        }

        $successCount = 0;
        $failCount = 0;
        $updateCount = 0;
        $insertCount = 0;
        $results = [];

        foreach ($request->data as $index => $row) {
            DB::beginTransaction();

            try {
                // Validasi per-row dengan Validator
                $validator = Validator::make($row, [
                    'username' => 'required|string|max:255',
                    'nama' => 'required|string|max:255',
                    'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                    'tempat_lahir' => 'nullable|string|max:225',
                    'tanggal_lahir' => 'nullable|date',
                    'status' => 'nullable|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati,Lainnya',
                    'departement' => 'nullable|string|max:100',
                    'pekerjaan' => 'nullable|string|max:100',
                    'agama' => 'nullable|string|max:50',
                    'alamat' => 'required|string',
                    'kota' => 'required|string|max:255',
                    'no_telp' => 'nullable|string|max:12',
                    'jabatan' => 'nullable|in:Anggota,Pengurus',
                ], [
                    'username.required' => 'Username wajib diisi',
                    'nama.required' => 'Nama lengkap wajib diisi',
                    'jenis_kelamin.required' => 'Jenis kelamin wajib diisi',
                    'jenis_kelamin.in' => 'Jenis kelamin harus Laki-laki atau Perempuan',
                    'alamat.required' => 'Alamat wajib diisi',
                    'kota.required' => 'Kota wajib diisi',
                ]);

                if ($validator->fails()) {
                    $errorMessages = implode(', ', $validator->errors()->all());

                    $results[] = [
                        'status' => 'failed',
                        'id_anggota' => '-',
                        'username' => $row['username'] ?? '',
                        'nama' => $row['nama'] ?? '',
                        'jenis_kelamin' => $row['jenis_kelamin'] ?? '-',
                        'alamat' => $row['alamat'] ?? '-',
                        'kota' => $row['kota'] ?? '-',
                        'jabatan' => $row['jabatan'] ?? '-',
                        'keterangan' => $errorMessages
                    ];

                    $failCount++;
                    continue;
                }

                // ✅ CEK APAKAH USERNAME SUDAH ADA
                $existingAnggota = DataAnggota::where('username', $row['username'])->first();

                if ($existingAnggota) {
                    // ========================================
                    // MODE UPDATE - Username sudah ada
                    // ========================================

                    Log::info('Checking if data changed for:', [
                        'username' => $row['username'],
                        'id' => $existingAnggota->id
                    ]);

                    // Prepare data untuk update
                    $updateData = [
                        'nama' => $row['nama'],
                        'jenis_kelamin' => $row['jenis_kelamin'],
                        'tempat_lahir' => $row['tempat_lahir'] ?? $existingAnggota->tempat_lahir,
                        'tanggal_lahir' => $row['tanggal_lahir'] ?? $existingAnggota->tanggal_lahir,
                        'status' => $row['status'] ?? $existingAnggota->status,
                        'departement' => $row['departement'] ?? $existingAnggota->departement,
                        'pekerjaan' => $row['pekerjaan'] ?? $existingAnggota->pekerjaan,
                        'agama' => $row['agama'] ?? $existingAnggota->agama,
                        'alamat' => $row['alamat'],
                        'kota' => $row['kota'],
                        'no_telp' => $row['no_telp'] ?? $existingAnggota->no_telp,
                        'jabatan' => $row['jabatan'] ?? $existingAnggota->jabatan,
                    ];

                    // ✅ CEK APAKAH ADA PERUBAHAN DATA
                    $hasChanges = false;
                    $changedFields = [];

                    foreach ($updateData as $field => $newValue) {
                        $oldValue = $existingAnggota->$field;

                        // Special handling untuk tanggal
                        if (in_array($field, ['tanggal_lahir', 'tanggal_registrasi'])) {
                            try {
                                // Normalize kedua tanggal ke format Y-m-d untuk perbandingan
                                $oldDate = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('Y-m-d') : null;
                                $newDate = $newValue ? \Carbon\Carbon::parse($newValue)->format('Y-m-d') : null;

                                if ($oldDate !== $newDate) {
                                    $hasChanges = true;
                                    $changedFields[] = $field;

                                    Log::info('Date change detected', [
                                        'field' => $field,
                                        'old' => $oldDate,
                                        'new' => $newDate
                                    ]);
                                }
                            } catch (\Exception $e) {
                                // Jika parsing gagal, anggap tidak ada perubahan
                                Log::warning('Date parsing failed', [
                                    'field' => $field,
                                    'old_value' => $oldValue,
                                    'new_value' => $newValue,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        } else {
                            // Normalize untuk perbandingan field lainnya
                            $oldValueNormalized = $oldValue === null ? '' : trim($oldValue);
                            $newValueNormalized = $newValue === null ? '' : trim($newValue);

                            if ($oldValueNormalized !== $newValueNormalized) {
                                $hasChanges = true;
                                $changedFields[] = $field;

                                Log::info('Field change detected', [
                                    'field' => $field,
                                    'old' => $oldValueNormalized,
                                    'new' => $newValueNormalized
                                ]);
                            }
                        }
                    }

                    // Jika TIDAK ADA perubahan, skip (tidak masuk ke results)
                    if (!$hasChanges) {
                        Log::info('No changes detected, skipping:', [
                            'username' => $row['username']
                        ]);

                        // Tidak masuk counter success/fail, ini "skipped"
                        DB::commit();
                        continue; // Skip ke row berikutnya
                    }

                    // Ada perubahan, lakukan update
                    Log::info('Changes detected, updating:', [
                        'username' => $row['username'],
                        'changed_fields' => $changedFields
                    ]);

                    $existingAnggota->update($updateData);

                    // ✅ SINKRONISASI: Update data di user table jika ada
                    if ($existingAnggota->user) {
                        $existingAnggota->user->update([
                            'name' => $row['username'],
                        ]);

                        Log::info('User data synced on update', [
                            'anggota_id' => $existingAnggota->id,
                            'user_id' => $existingAnggota->user_id
                        ]);
                    }

                    DB::commit();

                    // Format perubahan untuk ditampilkan
                    $changedFieldsText = implode(', ', array_map(function ($field) {
                        return ucfirst(str_replace('_', ' ', $field));
                    }, $changedFields));

                    $results[] = [
                        'status' => 'success',
                        'id_anggota' => $existingAnggota->id_anggota,
                        'username' => $row['username'],
                        'nama' => $row['nama'],
                        'jenis_kelamin' => $row['jenis_kelamin'] ?? '-',
                        'alamat' => $row['alamat'] ?? '-',
                        'kota' => $row['kota'] ?? '-',
                        'jabatan' => $row['jabatan'],
                        'keterangan' => "✏️ Diperbarui ($changedFieldsText)"
                    ];

                    $successCount++;
                    $updateCount++;

                } else {
                    // ========================================
                    // MODE INSERT - Username belum ada
                    // ========================================

                    Log::info('Creating new anggota:', [
                        'username' => $row['username']
                    ]);

                    // Generate ID Anggota
                    $row['id_anggota'] = DataAnggota::generateIdAnggota();

                    // Set default values
                    $row['password'] = 'UserKoperasi'; // Password default
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
                        'keterangan' => '✅ Data berhasil ditambahkan (INSERT) - Password: UserKoperasi'
                    ];

                    $successCount++;
                    $insertCount++;
                }

            } catch (\Exception $e) {
                DB::rollBack();

                Log::error('Import error for row ' . $index, [
                    'row' => $row,
                    'error' => $e->getMessage()
                ]);

                $results[] = [
                    'status' => 'failed',
                    'id_anggota' => '-',
                    'username' => $row['username'] ?? '',
                    'nama' => $row['nama'] ?? '',
                    'jenis_kelamin' => $row['jenis_kelamin'] ?? '-',
                    'alamat' => $row['alamat'] ?? '-',
                    'kota' => $row['kota'] ?? '-',
                    'jabatan' => $row['jabatan'] ?? '-',
                    'keterangan' => '❌ ' . $e->getMessage()
                ];

                $failCount++;
            }
        }

        // Hitung data yang tidak berubah (skipped)
        $totalProcessed = count($request->data);
        $skippedCount = $totalProcessed - ($insertCount + $updateCount + $failCount);

        // Buat summary message yang informatif
        $summaryMessages = [];
        if ($insertCount > 0) {
            $summaryMessages[] = "$insertCount data baru ditambahkan";
        }
        if ($updateCount > 0) {
            $summaryMessages[] = "$updateCount data diperbarui";
        }
        if ($skippedCount > 0) {
            $summaryMessages[] = "$skippedCount data tidak berubah (dilewati)";
        }
        if ($failCount > 0) {
            $summaryMessages[] = "$failCount data gagal";
        }

        $message = implode(', ', $summaryMessages);

        return response()->json([
            'success' => true,
            'message' => $message,
            'successCount' => $successCount,
            'failCount' => $failCount,
            'insertCount' => $insertCount,
            'updateCount' => $updateCount,
            'skippedCount' => $skippedCount,
            'totalProcessed' => $totalProcessed,
            'results' => $results
        ]);
    }

    public function export()
    {
        try {
            // Debug: Log bahwa method dipanggil
            Log::info('Export method called');

            $export = new DataAnggotaExport();
            $result = $export->export();

            // Debug: Log hasil export
            Log::info('Export result:', $result);

            return response()->download(
                $result['path'],
                $result['filename'],
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            )->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // ✅ Log error detail
            Log::error('Error exporting data anggota: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            // ✅ Return error yang lebih informatif
            return back()->with('error', 'Gagal mengekspor data: ' . $e->getMessage());
        }
    }
}