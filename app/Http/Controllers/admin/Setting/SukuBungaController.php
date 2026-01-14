<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Controller;
use App\Models\Admin\Setting\SukuBunga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SukuBungaController extends Controller
{
    /**
     * Display the form.
     */
    public function index()
    {
        $sukuBunga = SukuBunga::getSetting();

        $notifications = collect([
            (object) [
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Setting.SukuBunga.SukuBunga', compact('sukuBunga', 'notifications'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'pinjaman_bunga_tipe' => 'required|in:A,B',
            'bg_pinjam' => 'required|numeric|min:0',
            'biaya_adm' => 'nullable|numeric|min:0',
            'denda' => 'nullable|numeric|min:0',
            'denda_hari' => 'nullable|integer|min:1|max:31',
            'dana_cadangan' => 'nullable|numeric|min:0',
            'jasa_usaha' => 'nullable|numeric|min:0',
            'jasa_anggota' => 'nullable|numeric|min:0',
            'jasa_modal' => 'nullable|numeric|min:0',
            'dana_pengurus' => 'nullable|numeric|min:0',
            'dana_karyawan' => 'nullable|numeric|min:0',
            'dana_pend' => 'nullable|numeric|min:0',
            'dana_sosial' => 'nullable|numeric|min:0',
            'pjk_pph' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // VALIDASI TOTAL 100% - DINONAKTIFKAN
            // Uncomment jika ingin mengaktifkan validasi backend
            /*
            $totalSHU = ($validated['dana_cadangan'] ?? 0) + 
                        ($validated['jasa_usaha'] ?? 0) + 
                        ($validated['jasa_anggota'] ?? 0) + 
                        ($validated['jasa_modal'] ?? 0) + 
                        ($validated['dana_pengurus'] ?? 0) + 
                        ($validated['dana_karyawan'] ?? 0) + 
                        ($validated['dana_pend'] ?? 0) + 
                        ($validated['dana_sosial'] ?? 0);

            if ($totalSHU > 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Total persentase pembagian SHU tidak boleh melebihi 100%'
                ], 422);
            }
            */

            $sukuBunga = SukuBunga::getSetting();
            $sukuBunga->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Setting biaya dan administrasi berhasil diupdate'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating suku bunga: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data'
            ], 500);
        }
    }
}