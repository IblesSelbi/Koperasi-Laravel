<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KasAnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data kas anggota
        $kasAnggota = collect([
            (object)[
                'id' => 1,
                'id_anggota' => 'member1',
                'nama' => 'FAISAL',
                'jenis_kelamin' => 'Laki-Laki',
                'jabatan' => 'Anggota',
                'departemen' => 'Produksi BOPP',
                'alamat' => 'duren sawit, jakarta timur',
                'no_telepon' => '089533222331',
                'foto' => 'https://ksp.walproject.net/assets/theme_admin/img/photo.jpg',
                'simpanan' => [
                    'sukarela' => 0,
                    'pokok' => 100000,
                    'wajib' => 0,
                    'lainnya' => 0,
                ],
                'kredit' => [
                    'pokok_pinjaman' => 10000000,
                    'tagihan_denda' => 22017600,
                    'dibayar' => 1834800,
                    'sisa_tagihan' => 20182800,
                ],
                'keterangan' => [
                    'jumlah_pinjaman' => 2,
                    'pinjaman_lunas' => 1,
                    'status_pembayaran' => 'Macet',
                    'tanggal_tempo' => '20 Ags 2025',
                ],
            ],
            (object)[
                'id' => 2,
                'id_anggota' => 'member2',
                'nama' => 'GEO HALOMOAN SIMANJUNTAK',
                'jenis_kelamin' => 'Laki-Laki',
                'jabatan' => 'Anggota',
                'departemen' => 'Produksi BOPP',
                'alamat' => 'villa mutiara jaya 2 cibititung',
                'no_telepon' => '081362787674',
                'foto' => 'https://ksp.walproject.net/uploads/anggota/6982b-foto-geoooo.jpg',
                'simpanan' => [
                    'sukarela' => 0,
                    'pokok' => -100000,
                    'wajib' => 0,
                    'lainnya' => 0,
                ],
                'kredit' => [
                    'pokok_pinjaman' => 0,
                    'tagihan_denda' => 0,
                    'dibayar' => 0,
                    'sisa_tagihan' => 0,
                ],
                'keterangan' => [
                    'jumlah_pinjaman' => 0,
                    'pinjaman_lunas' => 0,
                    'status_pembayaran' => 'Lancar',
                    'tanggal_tempo' => '-',
                ],
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.laporan.KasAnggota.KasAnggota', compact('kasAnggota', 'notifications'));
    }

    /**
     * Print laporan kas anggota with filters
     */
    public function cetakLaporan(Request $request)
    {
        $anggota = $request->get('anggota', '');
        $status = $request->get('status', '');
        $jabatan = $request->get('jabatan', '');

        // TODO: Get filtered data and generate print view
        // $kasAnggota = KasAnggota::query();
        
        // if ($anggota) $kasAnggota->where('id_anggota', $anggota);
        // if ($status) $kasAnggota->where('status_pembayaran', $status);
        // if ($jabatan) $kasAnggota->where('jabatan', $jabatan);
        
        // $kasAnggota = $kasAnggota->get();
        
        // return view('admin.laporan.cetak_kas_anggota', compact('kasAnggota', 'anggota', 'status', 'jabatan'));
        
        return response('Cetak laporan kas anggota dengan filter');
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        $includePhoto = $request->get('photo', false);
        
        // TODO: Implement Excel export using Laravel Excel
        // return Excel::download(new KasAnggotaExport($includePhoto), 'kas-anggota.xlsx');
        
        return response('Export Excel Data Kas Anggota');
    }
}