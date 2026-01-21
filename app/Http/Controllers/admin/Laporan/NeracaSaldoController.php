<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NeracaSaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $neracaSaldo = collect([

            // A. Aktiva Lancar
            (object) ['kategori' => 'A. Aktiva Lancar', 'is_header' => true, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'A. Aktiva Lancar', 'kode_akun' => 'A1', 'nama_akun' => 'Kas Tunai', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'A. Aktiva Lancar', 'kode_akun' => 'A2', 'nama_akun' => 'Kas Besar', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'A. Aktiva Lancar', 'kode_akun' => 'A3', 'nama_akun' => 'Transfer', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'A. Aktiva Lancar', 'kode_akun' => '001', 'nama_akun' => 'transaksi', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'A. Aktiva Lancar', 'kode_akun' => 'A4', 'nama_akun' => 'Piutang Usaha', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'A. Aktiva Lancar', 'kode_akun' => 'A6', 'nama_akun' => 'Pinjaman Anggota', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'A. Aktiva Lancar', 'kode_akun' => 'A8', 'nama_akun' => 'Persediaan Barang', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'A. Aktiva Lancar', 'kode_akun' => 'A9', 'nama_akun' => 'Biaya Dibayar Dimuka', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'A. Aktiva Lancar', 'kode_akun' => 'A10', 'nama_akun' => 'Perlengkapan Usaha', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'A. Aktiva Lancar', 'kode_akun' => 'A11', 'nama_akun' => 'Permisalan', 'is_header' => false, 'debet' => 0, 'kredit' => 0],

            // C. Aktiva Tetap Berwujud
            (object) ['kategori' => 'C. Aktiva Tetap Berwujud', 'is_header' => true, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'C. Aktiva Tetap Berwujud', 'kode_akun' => 'C1', 'nama_akun' => 'Peralatan Kantor', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'C. Aktiva Tetap Berwujud', 'kode_akun' => 'C2', 'nama_akun' => 'Inventaris Kendaraan', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'C. Aktiva Tetap Berwujud', 'kode_akun' => 'C3', 'nama_akun' => 'Mesin', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'C. Aktiva Tetap Berwujud', 'kode_akun' => 'C4', 'nama_akun' => 'Aktiva Tetap Lainnya', 'is_header' => false, 'debet' => 0, 'kredit' => 0],

            // F. Utang
            (object) ['kategori' => 'F. Utang', 'is_header' => true, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'F. Utang', 'kode_akun' => 'F1', 'nama_akun' => 'Utang Usaha', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'F. Utang', 'kode_akun' => 'F4', 'nama_akun' => 'Simpanan Sukarela', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'F. Utang', 'kode_akun' => 'F5', 'nama_akun' => 'Utang Pajak', 'is_header' => false, 'debet' => 0, 'kredit' => 0],

            // H. Utang Jangka Panjang
            (object) ['kategori' => 'H. Utang Jangka Panjang', 'is_header' => true, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'H. Utang Jangka Panjang', 'kode_akun' => 'H1', 'nama_akun' => 'Utang Bank', 'is_header' => false, 'debet' => 0, 'kredit' => 0],

            // I. Modal
            (object) ['kategori' => 'I. Modal', 'is_header' => true, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'I. Modal', 'kode_akun' => 'I1', 'nama_akun' => 'Simpanan Pokok', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'I. Modal', 'kode_akun' => 'I2', 'nama_akun' => 'Simpanan Wajib', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'I. Modal', 'kode_akun' => 'I3', 'nama_akun' => 'Modal Awal', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'I. Modal', 'kode_akun' => 'I5', 'nama_akun' => 'Modal Sumbangan', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'I. Modal', 'kode_akun' => 'I6', 'nama_akun' => 'Modal Cadangan', 'is_header' => false, 'debet' => 0, 'kredit' => 0],

            // J. Pendapatan
            (object) ['kategori' => 'J. Pendapatan', 'is_header' => true, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'J. Pendapatan', 'kode_akun' => 'J1', 'nama_akun' => 'Pembayaran Angsuran', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'J. Pendapatan', 'kode_akun' => 'J2', 'nama_akun' => 'Pendapatan Lainnya', 'is_header' => false, 'debet' => 0, 'kredit' => 0],

            // K. Beban
            (object) ['kategori' => 'K. Beban', 'is_header' => true, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'K. Beban', 'kode_akun' => 'K2', 'nama_akun' => 'Beban Gaji Karyawan', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'K. Beban', 'kode_akun' => 'K3', 'nama_akun' => 'Biaya Listrik dan Air', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'K. Beban', 'kode_akun' => 'K4', 'nama_akun' => 'Biaya Transportasi', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
            (object) ['kategori' => 'K. Beban', 'kode_akun' => 'K10', 'nama_akun' => 'Biaya Lainnya', 'is_header' => false, 'debet' => 0, 'kredit' => 0],
        ]);


        $notifications = collect([
            (object) [
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        // Calculate totals
        $totalDebet = $neracaSaldo->where('is_header', false)->sum('debet');
        $totalKredit = $neracaSaldo->where('is_header', false)->sum('kredit');

        return view('admin.Laporan.neracaSaldo.NeracaSaldo', compact(
            'neracaSaldo',
            'notifications',
            'totalDebet',
            'totalKredit'
        ));
    }

    /**
     * Print laporan neraca saldo with filters
     */
    public function cetakLaporan(Request $request)
    {
        $tglDari = $request->get('tgl_dari', '');
        $tglSamp = $request->get('tgl_samp', '');

        // TODO: Get filtered data and generate print view
        // $neracaSaldo = NeracaSaldo::whereBetween('tanggal', [$tglDari, $tglSamp])
        //     ->orderBy('kode_akun', 'asc')
        //     ->get();

        // return view('admin.laporan.cetak_neraca_saldo', compact('neracaSaldo', 'tglDari', 'tglSamp'));

        return response('Cetak laporan neraca saldo dari ' . $tglDari . ' sampai ' . $tglSamp);
    }

    /**
     * Get data for filtering
     */
    public function getData(Request $request)
    {
        // TODO: Implement filtering logic
        // $tglDari = $request->get('tgl_dari');
        // $tglSamp = $request->get('tgl_samp');

        return response()->json([
            'status' => 'success',
            'data' => []
        ]);
    }
}