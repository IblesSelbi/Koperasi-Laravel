<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\JenisAkun;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\Admin\TransaksiKas\Pemasukan;
use App\Models\Admin\TransaksiKas\Pengeluaran;
use App\Models\Admin\TransaksiKas\Transfer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NeracaSaldoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get date range from request or default to current year
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

        // Get all active jenis akun grouped by kategori
        $jenisAkunList = JenisAkun::where('aktif', 'Y')
            ->orderBy('kd_aktiva', 'asc')
            ->get();

        // Build neraca saldo data
        $neracaSaldo = collect();
        $kategoriMap = [
            'A' => 'A. Aktiva Lancar',
            'C' => 'C. Aktiva Tetap Berwujud',
            'F' => 'F. Utang',
            'H' => 'H. Utang Jangka Panjang',
            'I' => 'I. Modal',
            'J' => 'J. Pendapatan',
            'K' => 'K. Beban',
        ];

        $currentKategori = '';
        
        foreach ($jenisAkunList as $akun) {
            // Get first letter of kode akun for kategori
            $kodePrefix = substr($akun->kd_aktiva, 0, 1);
            $kategori = $kategoriMap[$kodePrefix] ?? 'Lainnya';

            // Add kategori header if new kategori
            if ($kategori !== $currentKategori) {
                $currentKategori = $kategori;
                $neracaSaldo->push((object)[
                    'kategori' => $kategori,
                    'is_header' => true,
                    'debet' => 0,
                    'kredit' => 0,
                ]);
            }

            // Calculate debet and kredit for this akun
            $saldo = $this->calculateSaldoAkun($akun->id, $tglDari, $tglSamp);

            // Add akun detail
            $neracaSaldo->push((object)[
                'kategori' => $kategori,
                'kode_akun' => $akun->kd_aktiva,
                'nama_akun' => $akun->jns_transaksi,
                'is_header' => false,
                'debet' => $saldo['debet'],
                'kredit' => $saldo['kredit'],
                'akun_type' => $akun->akun,
                'laba_rugi' => $akun->laba_rugi,
            ]);
        }

        // Add kas accounts to Aktiva Lancar
        $this->addKasToNeracaSaldo($neracaSaldo, $tglDari, $tglSamp);

        $notifications = collect([
            (object)[
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
            'totalKredit',
            'tglDari',
            'tglSamp'
        ));
    }

    /**
     * Calculate saldo for specific akun
     */
    private function calculateSaldoAkun($akunId, $tglDari, $tglSamp)
    {
        $totalDebet = 0;
        $totalKredit = 0;

        // Get pemasukan (where dari_akun_id matches) - DIPERBAIKI
        $pemasukan = Pemasukan::where('dari_akun_id', $akunId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        // Get pengeluaran (where untuk_akun_id matches) - DIPERBAIKI
        $pengeluaran = Pengeluaran::where('untuk_akun_id', $akunId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        // Determine debet or kredit based on akun type
        $jenisAkun = JenisAkun::find($akunId);
        
        if ($jenisAkun) {
            if ($jenisAkun->akun === 'Aktiva') {
                // Aktiva: Debet (+), Kredit (-)
                $totalDebet = $pemasukan;
                $totalKredit = $pengeluaran;
            } else {
                // Pasiva: Kredit (+), Debet (-)
                $totalKredit = $pemasukan;
                $totalDebet = $pengeluaran;
            }
        }

        return [
            'debet' => $totalDebet,
            'kredit' => $totalKredit,
        ];
    }

    /**
     * Add kas accounts to neraca saldo
     */
    private function addKasToNeracaSaldo(&$neracaSaldo, $tglDari, $tglSamp)
    {
        $kasAccounts = DataKas::where('aktif', 'Y')->get();

        foreach ($kasAccounts as $kas) {
            $saldo = $this->calculateSaldoKas($kas->id, $tglDari, $tglSamp);

            // Find Aktiva Lancar section and insert after header
            $aktivaIndex = $neracaSaldo->search(function ($item) {
                return $item->is_header && $item->kategori === 'A. Aktiva Lancar';
            });

            if ($aktivaIndex !== false) {
                $neracaSaldo->splice($aktivaIndex + 1, 0, [
                    (object)[
                        'kategori' => 'A. Aktiva Lancar',
                        'kode_akun' => 'KAS-' . $kas->id,
                        'nama_akun' => $kas->nama_kas,
                        'is_header' => false,
                        'debet' => $saldo > 0 ? $saldo : 0,
                        'kredit' => $saldo < 0 ? abs($saldo) : 0,
                        'akun_type' => 'Aktiva',
                    ]
                ]);
            }
        }
    }

    /**
     * Calculate saldo for kas account
     */
    private function calculateSaldoKas($kasId, $tglDari, $tglSamp)
    {
        // Pemasukan to this kas
        $pemasukan = Pemasukan::where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        // Pengeluaran from this kas
        $pengeluaran = Pengeluaran::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        // Transfer in
        $transferIn = Transfer::where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        // Transfer out
        $transferOut = Transfer::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        return $pemasukan - $pengeluaran + $transferIn - $transferOut;
    }

    /**
     * Print laporan neraca saldo with filters
     */
    public function cetakLaporan(Request $request)
    {
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

        // Get data using same logic as index
        $jenisAkunList = JenisAkun::where('aktif', 'Y')
            ->orderBy('kd_aktiva', 'asc')
            ->get();

        $neracaSaldo = collect();
        $kategoriMap = [
            'A' => 'A. Aktiva Lancar',
            'C' => 'C. Aktiva Tetap Berwujud',
            'F' => 'F. Utang',
            'H' => 'H. Utang Jangka Panjang',
            'I' => 'I. Modal',
            'J' => 'J. Pendapatan',
            'K' => 'K. Beban',
        ];

        $currentKategori = '';
        
        foreach ($jenisAkunList as $akun) {
            $kodePrefix = substr($akun->kd_aktiva, 0, 1);
            $kategori = $kategoriMap[$kodePrefix] ?? 'Lainnya';

            if ($kategori !== $currentKategori) {
                $currentKategori = $kategori;
                $neracaSaldo->push((object)[
                    'kategori' => $kategori,
                    'is_header' => true,
                    'debet' => 0,
                    'kredit' => 0,
                ]);
            }

            $saldo = $this->calculateSaldoAkun($akun->id, $tglDari, $tglSamp);

            $neracaSaldo->push((object)[
                'kategori' => $kategori,
                'kode_akun' => $akun->kd_aktiva,
                'nama_akun' => $akun->jns_transaksi,
                'is_header' => false,
                'debet' => $saldo['debet'],
                'kredit' => $saldo['kredit'],
            ]);
        }

        $this->addKasToNeracaSaldo($neracaSaldo, $tglDari, $tglSamp);

        $totalDebet = $neracaSaldo->where('is_header', false)->sum('debet');
        $totalKredit = $neracaSaldo->where('is_header', false)->sum('kredit');

        return view('admin.Laporan.neracaSaldo.CetakNeracaSaldo', compact(
            'neracaSaldo',
            'totalDebet',
            'totalKredit',
            'tglDari',
            'tglSamp'
        ));
    }

    /**
     * Get data for filtering (AJAX)
     */
    public function getData(Request $request)
    {
        $tglDari = $request->get('tgl_dari');
        $tglSamp = $request->get('tgl_samp');

        // Return filtered data if needed
        return response()->json([
            'status' => 'success',
            'data' => [],
            'message' => 'Data berhasil diambil'
        ]);
    }
}