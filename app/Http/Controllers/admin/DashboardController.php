<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Data statistik - semua 0 untuk development
        $stats = [
            'pinjaman' => [
                'transaksi_bulan_ini' => 0,
                'jml_tagihan' => 0,
                'sisa_tagihan' => 0,
            ],
            'simpanan' => [
                'simpanan_anggota' => 0,
                'penarikan_tunai' => 0,
                'jumlah_simpanan' => 0,
            ],
            'kas' => [
                'debet' => 0,
                'kredit' => 0,
                'jumlah' => 0,
            ],
            'anggota' => [
                'aktif' => 0,
                'tidak_aktif' => 0,
                'total' => 0,
            ],
            'peminjam' => [
                'total' => 0,
                'lunas' => 0,
                'belum_lunas' => 0,
            ],
            'pengguna' => [
                'aktif' => 0,
                'non_aktif' => 0,
                'total' => 0,
            ],
        ];

        // Notifikasi jatuh tempo - kosong
        $notifications = collect([]);

        return view('admin.dashboard', compact('stats', 'notifications'));
    }
}