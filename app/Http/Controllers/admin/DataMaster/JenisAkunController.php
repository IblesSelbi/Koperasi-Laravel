<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JenisAkunController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Dummy data jenis akun
        $jenisAkun = collect([
            (object)[
                'id' => 112,
                'kd_aktiva' => '001',
                'jns_transaksi' => 'transaksi',
                'akun' => 'Aktiva',
                'pemasukan' => 'Y',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => 'BIAYA',
            ],
            (object)[
                'id' => 5,
                'kd_aktiva' => 'A4',
                'jns_transaksi' => 'Piutang Usaha',
                'akun' => 'Aktiva',
                'pemasukan' => 'Y',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 6,
                'kd_aktiva' => 'A5',
                'jns_transaksi' => 'Piutang Karyawan',
                'akun' => 'Aktiva',
                'pemasukan' => 'N',
                'pengeluaran' => 'Y',
                'aktif' => 'N',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 7,
                'kd_aktiva' => 'A6',
                'jns_transaksi' => 'Pinjaman Anggota',
                'akun' => 'Aktiva',
                'pemasukan' => '',
                'pengeluaran' => 'Y',
                'aktif' => '',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 8,
                'kd_aktiva' => 'A7',
                'jns_transaksi' => 'Piutang Anggota',
                'akun' => 'Aktiva',
                'pemasukan' => 'Y',
                'pengeluaran' => 'Y',
                'aktif' => 'N',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 9,
                'kd_aktiva' => 'A8',
                'jns_transaksi' => 'Persediaan Barang',
                'akun' => 'Aktiva',
                'pemasukan' => 'N',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 10,
                'kd_aktiva' => 'A9',
                'jns_transaksi' => 'Biaya Dibayar Dimuka',
                'akun' => 'Aktiva',
                'pemasukan' => 'N',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 11,
                'kd_aktiva' => 'A10',
                'jns_transaksi' => 'Perlengkapan Usaha',
                'akun' => 'Aktiva',
                'pemasukan' => 'N',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 12,
                'kd_aktiva' => 'A11',
                'jns_transaksi' => 'Permisalan',
                'akun' => 'Aktiva',
                'pemasukan' => 'Y',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 13,
                'kd_aktiva' => 'C',
                'jns_transaksi' => 'Aktiva Tetap Berwujud',
                'akun' => 'Aktiva',
                'pemasukan' => '',
                'pengeluaran' => '',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 14,
                'kd_aktiva' => 'C1',
                'jns_transaksi' => 'Peralatan Kantor',
                'akun' => 'Aktiva',
                'pemasukan' => 'N',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 15,
                'kd_aktiva' => 'C2',
                'jns_transaksi' => 'Inventaris Kendaraan',
                'akun' => 'Aktiva',
                'pemasukan' => 'N',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 16,
                'kd_aktiva' => 'C3',
                'jns_transaksi' => 'Mesin',
                'akun' => 'Aktiva',
                'pemasukan' => 'N',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 17,
                'kd_aktiva' => 'C4',
                'jns_transaksi' => 'Aktiva Tetap Lainnya',
                'akun' => 'Aktiva',
                'pemasukan' => 'Y',
                'pengeluaran' => 'N',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 18,
                'kd_aktiva' => 'E',
                'jns_transaksi' => 'Modal Pribadi',
                'akun' => 'Aktiva',
                'pemasukan' => '',
                'pengeluaran' => '',
                'aktif' => 'N',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 19,
                'kd_aktiva' => 'E1',
                'jns_transaksi' => 'Prive',
                'akun' => 'Aktiva',
                'pemasukan' => 'Y',
                'pengeluaran' => 'Y',
                'aktif' => 'N',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 20,
                'kd_aktiva' => 'F',
                'jns_transaksi' => 'Utang',
                'akun' => 'Pasiva',
                'pemasukan' => '',
                'pengeluaran' => '',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 21,
                'kd_aktiva' => 'F1',
                'jns_transaksi' => 'Utang Usaha',
                'akun' => 'Pasiva',
                'pemasukan' => 'Y',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 22,
                'kd_aktiva' => 'F4',
                'jns_transaksi' => 'Simpanan Sukarela',
                'akun' => 'Pasiva',
                'pemasukan' => '',
                'pengeluaran' => '',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 23,
                'kd_aktiva' => 'F5',
                'jns_transaksi' => 'Utang Pajak',
                'akun' => 'Pasiva',
                'pemasukan' => 'Y',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 24,
                'kd_aktiva' => 'H',
                'jns_transaksi' => 'Utang Jangka Panjang',
                'akun' => 'Pasiva',
                'pemasukan' => '',
                'pengeluaran' => '',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 25,
                'kd_aktiva' => 'H1',
                'jns_transaksi' => 'Utang Bank',
                'akun' => 'Pasiva',
                'pemasukan' => 'Y',
                'pengeluaran' => 'Y',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 26,
                'kd_aktiva' => 'H2',
                'jns_transaksi' => 'Obligasi',
                'akun' => 'Pasiva',
                'pemasukan' => 'Y',
                'pengeluaran' => 'Y',
                'aktif' => 'N',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 27,
                'kd_aktiva' => 'I',
                'jns_transaksi' => 'Modal',
                'akun' => 'Pasiva',
                'pemasukan' => '',
                'pengeluaran' => '',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
            (object)[
                'id' => 28,
                'kd_aktiva' => 'I1',
                'jns_transaksi' => 'Simpanan Pokok',
                'akun' => 'Pasiva',
                'pemasukan' => '',
                'pengeluaran' => '',
                'aktif' => 'Y',
                'laba_rugi' => '',
            ],
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.DataMaster.JenisAkun.JenisAkun', compact('jenisAkun', 'notifications'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // TODO: Implement store logic
        return response()->json(['success' => true, 'message' => 'Data berhasil ditambahkan']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // TODO: Implement update logic
        return response()->json(['success' => true, 'message' => 'Data berhasil diubah']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // TODO: Implement delete logic
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }

    /**
     * Export data to Excel
     */
    public function export()
    {
        // TODO: Implement Excel export
        return response('Export Excel Jenis Akun');
    }

    /**
     * Print report
     */
    public function cetak()
    {
        // TODO: Implement print view
        return response('Cetak Laporan Jenis Akun');
    }
}