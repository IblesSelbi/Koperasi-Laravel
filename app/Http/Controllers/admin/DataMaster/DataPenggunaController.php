<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\DataPengguna;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DataPenggunaController extends Controller
{
    public function index()
    {
        $dataPengguna = DataPengguna::orderBy('id', 'desc')->get();

        return view(
            'admin.DataMaster.DataPengguna.DataPengguna',
            compact('dataPengguna')
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:data_pengguna,username',
                function ($attribute, $value, $fail) {
                    $email = $value . '@gmail.com';
                    if (User::where('email', $email)->exists()) {
                        $fail('Username sudah digunakan di sistem.');
                    }
                },
            ],
            'password' => 'required|string|min:6',
            'level' => 'required|in:admin,operator,pinjaman',
            'status' => 'required|in:Y,N',
        ]);

        DB::beginTransaction();
        
        try {
            // Simpan password asli untuk users
            $plainPassword = $data['password'];
            
            // Simpan ke data_pengguna (auto hash di model)
            $pengguna = DataPengguna::create($data);

            // Buat akun di tabel users
            User::create([
                'name' => $data['username'],
                'email' => $data['username'] . '@gmail.com', 
                'password' => Hash::make($plainPassword),
                'role_id' => 1,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $pengguna = DataPengguna::findOrFail($id);
        $oldUsername = $pengguna->username;

        $data = $request->validate([
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:data_pengguna,username,' . $id,
                function ($attribute, $value, $fail) use ($oldUsername) {
                    $email = $value . '@gmail.com';
                    $oldEmail = $oldUsername . '@gmail.com';
                    
                    if (User::where('email', $email)
                            ->where('email', '!=', $oldEmail)
                            ->exists()) {
                        $fail('Username sudah digunakan di sistem.');
                    }
                },
            ],
            'password' => 'nullable|string|min:6',
            'level' => 'required|in:admin,operator,pinjaman',
            'status' => 'required|in:Y,N',
        ]);

        DB::beginTransaction();

        try {
            // Simpan password asli jika ada
            $plainPassword = $data['password'] ?? null;
            
            // Hapus password dari array jika kosong
            if (empty($data['password'])) {
                unset($data['password']);
            }

            // Update data_pengguna
            $pengguna->update($data);

            // Update user di tabel users
            $user = User::where('email', $oldUsername . '@gmail.com')->first();
            
            if ($user) {
                $updateUserData = [
                    'name' => $data['username'],
                    'email' => $data['username'] . '@gmail.com',
                ];

                // Update password jika diisi
                if (!empty($plainPassword)) {
                    $updateUserData['password'] = Hash::make($plainPassword);
                }

                $user->update($updateUserData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil diperbarui'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $pengguna = DataPengguna::findOrFail($id);
            $username = $pengguna->username;

            // Hapus dari users dulu
            User::where('email', $username . '@gmail.com')->delete();

            // Hapus dari data_pengguna
            $pengguna->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data pengguna berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function export()
    {
        return response('Export Excel Data Pengguna');
    }

    public function cetak()
    {
        return response('Cetak Laporan Data Pengguna');
    }
}