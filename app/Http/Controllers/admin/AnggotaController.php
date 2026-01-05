<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data anggota
        $anggota = collect([
            (object)[
                'id' => 1,
                'id_anggota' => 'member1',
                'nama' => 'FAISAL',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1995-05-07',
                'jenis_kelamin' => 'L',
                'jabatan' => 'Anggota',
                'departemen' => 'Produksi BOPP',
                'alamat' => 'Duren sawit, jakarta timur',
                'no_telepon' => '089533222331',
                'status' => 'Aktif',
                'tanggal_registrasi' => '2023-05-11',
                'foto' => 'assets/images/profile/user-1.jpg',
            ],
            (object)[
                'id' => 2,
                'id_anggota' => 'member2',
                'nama' => 'GEO HALOMOAN SIMANJUNTAK',
                'tempat_lahir' => 'Tiga bolon pane',
                'tanggal_lahir' => '1997-05-14',
                'jenis_kelamin' => 'L',
                'jabatan' => 'Anggota',
                'departemen' => 'Produksi BOPP',
                'alamat' => 'Villa mutiara jaya 2 cibititung',
                'no_telepon' => '081362787674',
                'status' => 'Aktif',
                'tanggal_registrasi' => '2023-05-22',
                'foto' => 'assets/images/profile/user-2.jpg',
            ],
            (object)[
                'id' => 3,
                'id_anggota' => 'anggota',
                'nama' => 'WIDI ALJATSIYAH',
                'tempat_lahir' => 'Lampung',
                'tanggal_lahir' => '2023-06-16',
                'jenis_kelamin' => 'L',
                'jabatan' => 'Anggota',
                'departemen' => '-',
                'alamat' => 'Jl Padasuka RT/RW 03/014 Kp cihandiwung',
                'no_telepon' => '089660688594',
                'status' => 'Aktif',
                'tanggal_registrasi' => '2023-06-05',
                'foto' => 'assets/images/profile/user-3.jpg',
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Laporan.anggota.Anggota', compact('anggota', 'notifications'));
    }

    /**
     * Print laporan anggota with filters
     */
    public function cetakLaporan(Request $request)
    {
        $status = $request->get('status', '');
        $gender = $request->get('gender', '');
        $jabatan = $request->get('jabatan', '');

        // TODO: Get filtered data and generate print view
        // $anggota = Anggota::query();
        
        // if ($status) $anggota->where('status', $status);
        // if ($gender) $anggota->where('jenis_kelamin', $gender);
        // if ($jabatan) $anggota->where('jabatan', $jabatan);
        
        // $anggota = $anggota->get();
        
        // return view('admin.laporan.cetak_anggota', compact('anggota', 'status', 'gender', 'jabatan'));
        
        return response('Cetak laporan anggota dengan filter');
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        // TODO: Implement Excel export using Laravel Excel
        // return Excel::download(new AnggotaExport, 'data-anggota.xlsx');
        
        return response('Export Excel Data Anggota');
    }

    /**
     * Export to PDF
     */
    public function exportPDF()
    {
        // TODO: Implement PDF export using DomPDF or similar
        // $anggota = Anggota::all();
        // $pdf = PDF::loadView('admin.laporan.export_pdf_anggota', compact('anggota'));
        // return $pdf->download('data-anggota.pdf');
        
        return response('Export PDF Data Anggota');
    }
}