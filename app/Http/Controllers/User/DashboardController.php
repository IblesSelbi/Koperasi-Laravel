<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data anggota (hardcoded untuk development)
        $anggota = [
            'id_anggota' => 'AG0001',
            'nama' => 'FAISAL',
            'gender' => 'Laki-Laki',
            'jabatan' => 'Anggota',
            'alamat' => 'Duren Sawit, Jakarta Timur',
            'no_telp' => '089533222331',
        ];

        // Data pengajuan terakhir
        $pengajuan_terakhir = [
            'tanggal' => '06 November 2025 jam 08:10',
            'nominal' => 1000000,
            'status' => 'Disetujui',
            'keterangan' => 'Cair 06 Nov 2025',
        ];

        // Data simpanan
        $simpanan = [
            'sukarela' => 0,
            'pokok' => 100000,
            'wajib' => 0,
            'lainnya' => 0,
            'total' => 100000,
        ];

        // Data pinjaman
        $pinjaman = [
            'pokok' => 10000000,
            'tagihan_total' => 22017600,
            'dibayar' => 1834800,
            'sisa_tagihan' => 20182800,
        ];

        // Data keterangan
        $keterangan = [
            'jumlah_pinjaman' => 2,
            'pinjaman_lunas' => 1,
            'status_pembayaran' => 'Macet',
            'status_color' => 'danger',
            'tanggal_tempo' => '20 Ags 2025',
        ];

        return view('user.dashboard', compact(
            'anggota',
            'pengajuan_terakhir',
            'simpanan',
            'pinjaman',
            'keterangan'
        ));
    }
}