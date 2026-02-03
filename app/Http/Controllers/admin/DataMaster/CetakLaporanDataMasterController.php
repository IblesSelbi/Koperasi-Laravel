<?php

namespace App\Http\Controllers\Admin\DataMaster;

use App\Http\Controllers\Controller;
use App\Models\Admin\Setting\IdentitasKoperasi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

// Models Data Master
use App\Models\Admin\DataMaster\{
    DataAnggota,
    DataBarang,
    DataKas,
    DataPengguna,
    JenisAkun,
    JenisSimpanan,
    LamaAngsuran
};

class CetakLaporanDataMasterController extends Controller
{
    // ==================== LAPORAN DATA ANGGOTA ====================

    /**
     * Cetak Laporan Data Anggota
     */
    public function cetakDataAnggota(Request $request)
    {
        try {
            $aktif = $request->get('aktif', '');
            $gender = $request->get('gender', '');
            $jabatan = $request->get('jabatan', '');
            $departemen = $request->get('departemen', '');

            $query = DataAnggota::query();

            // Filter berdasarkan parameter
            if ($aktif !== '' && $aktif !== null) {
                $query->where('aktif', $aktif);
            }

            if ($gender !== '' && $gender !== null) {
                $query->where('jenis_kelamin', $gender);
            }

            if ($jabatan !== '' && $jabatan !== null) {
                $query->where('jabatan', $jabatan);
            }

            if ($departemen !== '' && $departemen !== null) {
                $query->where('departement', $departemen);
            }

            $dataAnggota = $query
                ->orderBy('tanggal_registrasi', 'desc')
                ->orderBy('nama', 'asc')
                ->get();

            // Statistik
            $totalAnggota = $dataAnggota->count();
            $anggotaAktif = $dataAnggota->where('aktif', 'Aktif')->count();
            $anggotaNonAktif = $dataAnggota->where('aktif', 'Non Aktif')->count();
            $anggotaLakiLaki = $dataAnggota->where('jenis_kelamin', 'Laki-laki')->count();
            $anggotaPerempuan = $dataAnggota->where('jenis_kelamin', 'Perempuan')->count();
            $pengurus = $dataAnggota->where('jabatan', 'Pengurus')->count();
            $anggota = $dataAnggota->where('jabatan', 'Anggota')->count();

            $filterInfo = [
                'status' => $aktif ?: 'Semua',
                'gender' => $gender ?: 'Semua',
                'jabatan' => $jabatan ?: 'Semua',
                'departemen' => $departemen ?: 'Semua',
            ];

            $identitas = IdentitasKoperasi::first();
            $tanggalCetak = Carbon::now()->format('d-m-Y H:i:s');

            $pdf = Pdf::loadView(
                'admin.DataMaster.DataAnggota.Cetak',
                compact(
                    'dataAnggota',
                    'totalAnggota',
                    'anggotaAktif',
                    'anggotaNonAktif',
                    'anggotaLakiLaki',
                    'anggotaPerempuan',
                    'pengurus',
                    'anggota',
                    'filterInfo',
                    'identitas',
                    'tanggalCetak'
                )
            );

            $pdf->setPaper('a4', 'landscape');
            $pdf->setOption('enable-local-file-access', true);

            return $pdf->stream('Laporan_Data_Anggota_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error cetak data anggota: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak laporan');
        }
    }

    // ==================== LAPORAN DATA BARANG ====================

    /**
     * Cetak Laporan Data Barang
     */
    public function cetakDataBarang(Request $request)
    {
        try {
            $dataBarang = DataBarang::orderBy('nama_barang', 'asc')->get();

            // Statistik
            $totalBarang = $dataBarang->count();
            $totalNilai = $dataBarang->sum(function ($item) {
                return $item->harga * $item->jumlah;
            });
            $totalUnit = $dataBarang->sum('jumlah');

            $identitas = IdentitasKoperasi::first();
            $tanggalCetak = Carbon::now()->format('d-m-Y H:i:s');

            $pdf = Pdf::loadView(
                'admin.DataMaster.DataBarang.Cetak',
                compact(
                    'dataBarang',
                    'totalBarang',
                    'totalNilai',
                    'totalUnit',
                    'identitas',
                    'tanggalCetak'
                )
            );

            $pdf->setPaper('a4', 'landscape');
            $pdf->setOption('enable-local-file-access', true);

            return $pdf->stream('Laporan_Data_Barang_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error cetak data barang: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak laporan');
        }
    }

    // ==================== LAPORAN DATA KAS ====================

    /**
     * Cetak Laporan Data Kas
     */
    public function cetakDataKas(Request $request)
    {
        try {
            $aktif = $request->get('aktif', '');

            $query = DataKas::query();

            if ($aktif !== '' && $aktif !== null) {
                $query->where('aktif', $aktif);
            }

            $dataKas = $query->orderBy('nama_kas', 'asc')->get();

            // Statistik
            $totalKas = $dataKas->count();
            $kasAktif = $dataKas->where('aktif', 'Y')->count();
            $kasTidakAktif = $dataKas->where('aktif', 'T')->count();

            $filterInfo = [
                'status' => $aktif === 'Y' ? 'Aktif' : ($aktif === 'T' ? 'Tidak Aktif' : 'Semua'),
            ];

            $identitas = IdentitasKoperasi::first();
            $tanggalCetak = Carbon::now()->format('d-m-Y H:i:s');

            $pdf = Pdf::loadView(
                'admin.DataMaster.DataKas.Cetak',
                compact(
                    'dataKas',
                    'totalKas',
                    'kasAktif',
                    'kasTidakAktif',
                    'filterInfo',
                    'identitas',
                    'tanggalCetak'
                )
            );

            $pdf->setPaper('a4', 'landscape');
            $pdf->setOption('enable-local-file-access', true);

            return $pdf->stream('Laporan_Data_Kas_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error cetak data kas: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak laporan');
        }
    }

    // ==================== LAPORAN DATA PENGGUNA ====================

    /**
     * Cetak Laporan Data Pengguna
     */
    public function cetakDataPengguna(Request $request)
    {
        try {
            $level = $request->get('level', '');
            $status = $request->get('status', '');

            $query = DataPengguna::query();

            if ($level !== '' && $level !== null) {
                $query->where('level', $level);
            }

            if ($status !== '' && $status !== null) {
                $query->where('status', $status);
            }

            $dataPengguna = $query->orderBy('username', 'asc')->get();

            // Statistik
            $totalPengguna = $dataPengguna->count();
            $penggunaAktif = $dataPengguna->where('status', 'Y')->count();
            $penggunaTidakAktif = $dataPengguna->where('status', 'N')->count();
            $admin = $dataPengguna->where('level', 'admin')->count();
            $operator = $dataPengguna->where('level', 'operator')->count();
            $pinjaman = $dataPengguna->where('level', 'pinjaman')->count();

            $filterInfo = [
                'level' => $level ?: 'Semua',
                'status' => $status === 'Y' ? 'Aktif' : ($status === 'N' ? 'Tidak Aktif' : 'Semua'),
            ];

            $identitas = IdentitasKoperasi::first();
            $tanggalCetak = Carbon::now()->format('d-m-Y H:i:s');

            $pdf = Pdf::loadView(
                'admin.DataMaster.DataPengguna.Cetak',
                compact(
                    'dataPengguna',
                    'totalPengguna',
                    'penggunaAktif',
                    'penggunaTidakAktif',
                    'admin',
                    'operator',
                    'pinjaman',
                    'filterInfo',
                    'identitas',
                    'tanggalCetak'
                )
            );

            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('enable-local-file-access', true);

            return $pdf->stream('Laporan_Data_Pengguna_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error cetak data pengguna: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak laporan');
        }
    }

    // ==================== LAPORAN JENIS AKUN ====================

    /**
     * Cetak Laporan Jenis Akun
     */
    public function cetakJenisAkun(Request $request)
    {
        try {
            $akun = $request->get('akun', '');
            $aktif = $request->get('aktif', '');

            $query = JenisAkun::query();

            if ($akun !== '' && $akun !== null) {
                $query->where('akun', $akun);
            }

            if ($aktif !== '' && $aktif !== null) {
                $query->where('aktif', $aktif);
            }

            $jenisAkun = $query->orderBy('kd_aktiva', 'asc')->get();

            // Statistik
            $totalJenisAkun = $jenisAkun->count();
            $aktiva = $jenisAkun->where('akun', 'Aktiva')->count();
            $pasiva = $jenisAkun->where('akun', 'Pasiva')->count();
            $akunAktif = $jenisAkun->where('aktif', 'Y')->count();
            $akunTidakAktif = $jenisAkun->where('aktif', 'T')->count();
            $pendapatan = $jenisAkun->where('laba_rugi', 'PENDAPATAN')->count();
            $biaya = $jenisAkun->where('laba_rugi', 'BIAYA')->count();

            $filterInfo = [
                'akun' => $akun ?: 'Semua',
                'status' => $aktif === 'Y' ? 'Aktif' : ($aktif === 'T' ? 'Tidak Aktif' : 'Semua'),
            ];

            $identitas = IdentitasKoperasi::first();
            $tanggalCetak = Carbon::now()->format('d-m-Y H:i:s');

            $pdf = Pdf::loadView(
                'admin.DataMaster.JenisAkun.Cetak',
                compact(
                    'jenisAkun',
                    'totalJenisAkun',
                    'aktiva',
                    'pasiva',
                    'akunAktif',
                    'akunTidakAktif',
                    'pendapatan',
                    'biaya',
                    'filterInfo',
                    'identitas',
                    'tanggalCetak'
                )
            );

            $pdf->setPaper('a4', 'landscape');
            $pdf->setOption('enable-local-file-access', true);

            return $pdf->stream('Laporan_Jenis_Akun_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error cetak jenis akun: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak laporan');
        }
    }

    // ==================== LAPORAN JENIS SIMPANAN ====================

    /**
     * Cetak Laporan Jenis Simpanan
     */
    public function cetakJenisSimpanan(Request $request)
    {
        try {
            $tampil = $request->get('tampil', '');

            $query = JenisSimpanan::query();

            if ($tampil !== '' && $tampil !== null) {
                $query->where('tampil', $tampil);
            }

            $jenisSimpanan = $query->orderBy('jenis_simpanan', 'asc')->get();

            // Statistik
            $totalJenisSimpanan = $jenisSimpanan->count();
            $totalJumlah = $jenisSimpanan->sum('jumlah');
            $tampilY = $jenisSimpanan->where('tampil', 'Y')->count();
            $tampilT = $jenisSimpanan->where('tampil', 'T')->count();

            $filterInfo = [
                'tampil' => $tampil === 'Y' ? 'Tampil' : ($tampil === 'T' ? 'Tidak Tampil' : 'Semua'),
            ];

            $identitas = IdentitasKoperasi::first();
            $tanggalCetak = Carbon::now()->format('d-m-Y H:i:s');

            $pdf = Pdf::loadView(
                'admin.DataMaster.JenisSimpanan.Cetak',
                compact(
                    'jenisSimpanan',
                    'totalJenisSimpanan',
                    'totalJumlah',
                    'tampilY',
                    'tampilT',
                    'filterInfo',
                    'identitas',
                    'tanggalCetak'
                )
            );

            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('enable-local-file-access', true);

            return $pdf->stream('Laporan_Jenis_Simpanan_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error cetak jenis simpanan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak laporan');  
        }
    }

    // ==================== LAPORAN LAMA ANGSURAN ====================

    /**
     * Cetak Laporan Lama Angsuran
     */
    public function cetakLamaAngsuran(Request $request)
    {
        try {
            $aktif = $request->get('aktif', '');

            $query = LamaAngsuran::query();

            if ($aktif !== '' && $aktif !== null) {
                $query->where('aktif', $aktif);
            }

            $lamaAngsuran = $query->orderBy('lama_angsuran', 'asc')->get();

            // Statistik
            $totalLamaAngsuran = $lamaAngsuran->count();
            $angsuranAktif = $lamaAngsuran->where('aktif', 'Y')->count();
            $angsuranTidakAktif = $lamaAngsuran->where('aktif', 'T')->count();
            $minAngsuran = $lamaAngsuran->min('lama_angsuran');
            $maxAngsuran = $lamaAngsuran->max('lama_angsuran');

            $filterInfo = [
                'status' => $aktif === 'Y' ? 'Aktif' : ($aktif === 'T' ? 'Tidak Aktif' : 'Semua'),
            ];

            $identitas = IdentitasKoperasi::first();
            $tanggalCetak = Carbon::now()->format('d-m-Y H:i:s');

            $pdf = Pdf::loadView(
                'admin.DataMaster.LamaAngsuran.Cetak',
                compact(
                    'lamaAngsuran',
                    'totalLamaAngsuran',
                    'angsuranAktif',
                    'angsuranTidakAktif',
                    'minAngsuran',
                    'maxAngsuran',
                    'filterInfo',
                    'identitas',
                    'tanggalCetak'
                )
            );

            $pdf->setPaper('a4', 'portrait');
            $pdf->setOption('enable-local-file-access', true);

            return $pdf->stream('Laporan_Lama_Angsuran_' . date('YmdHis') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error cetak lama angsuran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak laporan');
        }
    }
}