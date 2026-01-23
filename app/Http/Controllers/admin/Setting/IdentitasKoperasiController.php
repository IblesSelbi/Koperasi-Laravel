<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Admin\Setting\IdentitasKoperasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class IdentitasKoperasiController extends Controller
{
    /**
     * Display the form.
     */
    public function index()
    {
        // Ambil data identitas koperasi (hanya ada 1 record)
        $identitas = IdentitasKoperasi::first();

        // Jika belum ada data, buat data default
        if (!$identitas) {
            $identitas = IdentitasKoperasi::create([
                'nama_lembaga' => 'KOPERASI SIMPAN PINJAM',
                'nama_ketua' => 'Nama Pimpinan',
                'hp_ketua' => '081234567890',
                'alamat' => 'Alamat Koperasi',
                'telepon' => '021-1234567',
                'kota' => 'Kota',
                'email' => 'info@koperasi.id',
                'web' => 'www.koperasi.id',
                'logo' => 'assets/images/logos/logo-placeholder.png',
            ]);
        }

        return view('admin.Setting.IdentitasKoperasi.IdentitasKoperasi', compact('identitas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_lembaga' => 'required|string|max:255',
            'nama_ketua' => 'required|string|max:255',
            'hp_ketua' => 'required|string|max:255',
            'alamat' => 'required|string|max:500',
            'telepon' => 'nullable|string|max:255',
            'kota' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'web' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048', // max 2MB
        ], [
            'nama_lembaga.required' => 'Nama koperasi wajib diisi',
            'nama_ketua.required' => 'Nama pimpinan wajib diisi',
            'hp_ketua.required' => 'No HP pimpinan wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'kota.required' => 'Kota/Kabupaten wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'logo.image' => 'File harus berupa gambar',
            'logo.mimes' => 'Format logo harus JPG, PNG, atau GIF',
            'logo.max' => 'Ukuran logo maksimal 2MB',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan validasi. Silakan periksa kembali input Anda.');
        }

        try {
            // Ambil data identitas (hanya 1 record)
            $identitas = IdentitasKoperasi::first();

            // Data yang akan diupdate
            $data = [
                'nama_lembaga' => $request->nama_lembaga,
                'nama_ketua' => $request->nama_ketua,
                'hp_ketua' => $request->hp_ketua,
                'alamat' => $request->alamat,
                'telepon' => $request->telepon,
                'kota' => $request->kota,
                'email' => $request->email,
                'web' => $request->web,
            ];

            // Handle upload logo
            if ($request->hasFile('logo')) {
                // Hapus logo lama jika bukan logo default
                if ($identitas && $identitas->logo !== 'assets/images/logos/logo-placeholder.png') {
                    $oldLogoPath = public_path($identitas->logo);
                    if (File::exists($oldLogoPath)) {
                        File::delete($oldLogoPath);
                    }
                }

                // Upload logo baru
                $file = $request->file('logo');
                $filename = 'logo-koperasi-' . time() . '.' . $file->getClientOriginalExtension();
                $path = 'assets/images/logos/';

                // Buat folder jika belum ada
                if (!File::exists(public_path($path))) {
                    File::makeDirectory(public_path($path), 0777, true);
                }

                // Pindahkan file
                $file->move(public_path($path), $filename);
                $data['logo'] = $path . $filename;
            }

            // Update atau create data
            if ($identitas) {
                $identitas->update($data);
                $message = 'Data koperasi berhasil diupdate!';
            } else {
                IdentitasKoperasi::create($data);
                $message = 'Data koperasi berhasil ditambahkan!';
            }

            return redirect()->route('setting.identitas')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}