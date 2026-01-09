<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DataAnggotaController extends Controller
{
    public function index()
    {
        $dataAnggota = DataAnggota::orderBy('id', 'desc')->get();

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
        ]);
    }


    public function store(Request $request)
    {
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

        // Generate ID Anggota
        $data['id_anggota'] = DataAnggota::generateIdAnggota();

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $fileName = 'anggota_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/anggota', $fileName);
            $data['photo'] = 'storage/anggota/' . $fileName;
        }

        DataAnggota::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Data anggota berhasil ditambahkan'
        ], 200);
    }

    public function update(Request $request, $id)
    {
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

        // Hapus password dari data jika kosong
        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika bukan default
            if ($anggota->photo && $anggota->photo !== 'assets/images/profile/user-1.jpg') {
                $oldPhotoPath = str_replace('storage/', 'public/', $anggota->photo);
                Storage::delete($oldPhotoPath);
            }

            $file = $request->file('photo');
            $fileName = 'anggota_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/anggota', $fileName);
            $data['photo'] = 'storage/anggota/' . $fileName;
        }

        $anggota->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Data anggota berhasil diperbarui'
        ], 200);
    }

    public function destroy($id)
    {
        $anggota = DataAnggota::findOrFail($id);

        // Hapus foto jika bukan default
        if ($anggota->photo && $anggota->photo !== 'assets/images/profile/user-1.jpg') {
            $photoPath = str_replace('storage/', 'public/', $anggota->photo);
            Storage::delete($photoPath);
        }

        $anggota->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data anggota berhasil dihapus'
        ]);
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
            try {
                // Generate ID Anggota
                $row['id_anggota'] = DataAnggota::generateIdAnggota();

                // Set default values
                $row['password'] = '12345678'; // Default password minimal 8 karakter
                $row['tanggal_registrasi'] = date('Y-m-d');
                $row['jabatan'] = $row['jabatan'] ?? 'Anggota';
                $row['aktif'] = 'Aktif';

                DataAnggota::create($row);

                $results[] = [
                    'status' => 'success',
                    'id_anggota' => $row['id_anggota'],
                    'username' => $row['username'],
                    'nama' => $row['nama'],
                    'jenis_kelamin' => $row['jenis_kelamin'] ?? '-',
                    'alamat' => $row['alamat'] ?? '-',
                    'kota' => $row['kota'] ?? '-',
                    'jabatan' => $row['jabatan'],
                    'keterangan' => 'Berhasil diimport'
                ];

                $successCount++;
            } catch (\Exception $e) {
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