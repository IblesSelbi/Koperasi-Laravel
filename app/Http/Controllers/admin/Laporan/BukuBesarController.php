<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\DataKas;
use App\Models\Admin\TransaksiKas\Pemasukan;
use App\Models\Admin\TransaksiKas\Pengeluaran;
use App\Models\Admin\TransaksiKas\Transfer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BukuBesarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get periode filter (default: current month)
        $periode = $request->get('periode', Carbon::now()->format('Y-m'));
        
        // Parse periode
        list($year, $month) = explode('-', $periode);
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');

        // Get all active kas accounts
        $kasAccounts = DataKas::where('aktif', 'Y')->get();

        // Prepare buku besar data for each kas account
        $bukuBesarData = [];
        $totalSaldo = 0;
        $totalAkun = 0;

        foreach ($kasAccounts as $kas) {
            $transaksi = $this->getTransaksiByKas($kas->id, $startDate, $endDate);
            
            if ($transaksi->isNotEmpty()) {
                $totalAkun++;
                $saldoAkhir = $transaksi->last()->saldo ?? 0;
                $totalSaldo += $saldoAkhir;

                $bukuBesarData[] = [
                    'kas' => $kas,
                    'transaksi' => $transaksi,
                    'saldo_awal' => $this->getSaldoAwal($kas->id, $startDate),
                    'saldo_akhir' => $saldoAkhir,
                    'total_debet' => $transaksi->sum('debet'),
                    'total_kredit' => $transaksi->sum('kredit'),
                ];
            }
        }

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Laporan.bukuBesar.BukuBesar', compact(
            'bukuBesarData',
            'notifications',
            'totalAkun',
            'totalSaldo',
            'periode',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get transaksi by kas account
     */
    private function getTransaksiByKas($kasId, $startDate, $endDate)
    {
        $transaksi = collect();

        // 1. Pemasukan (Debet) - untuk_kas_id
        $pemasukan = Pemasukan::with(['dariAkun'])
            ->where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object)[
                    'tanggal' => $item->tanggal_transaksi,
                    'jenis_transaksi' => 'Pemasukan - ' . ($item->dariAkun->nama_akun ?? '-'),
                    'keterangan' => $item->uraian,
                    'debet' => $item->jumlah,
                    'kredit' => 0,
                    'saldo' => 0, // Will be calculated
                    'type' => 'pemasukan'
                ];
            });

        // 2. Pengeluaran (Kredit) - dari_kas_id
        $pengeluaran = Pengeluaran::with(['untukAkun'])
            ->where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object)[
                    'tanggal' => $item->tanggal_transaksi,
                    'jenis_transaksi' => 'Pengeluaran - ' . ($item->untukAkun->nama_akun ?? '-'),
                    'keterangan' => $item->uraian,
                    'debet' => 0,
                    'kredit' => $item->jumlah,
                    'saldo' => 0, // Will be calculated
                    'type' => 'pengeluaran'
                ];
            });

        // 3. Transfer Keluar (Kredit) - dari_kas_id
        $transferKeluar = Transfer::with(['untukKas'])
            ->where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object)[
                    'tanggal' => $item->tanggal_transaksi,
                    'jenis_transaksi' => 'Transfer Keluar',
                    'keterangan' => $item->uraian . ' ke ' . ($item->untukKas->nama_kas ?? '-'),
                    'debet' => 0,
                    'kredit' => $item->jumlah,
                    'saldo' => 0, // Will be calculated
                    'type' => 'transfer_out'
                ];
            });

        // 4. Transfer Masuk (Debet) - untuk_kas_id
        $transferMasuk = Transfer::with(['dariKas'])
            ->where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object)[
                    'tanggal' => $item->tanggal_transaksi,
                    'jenis_transaksi' => 'Transfer Masuk',
                    'keterangan' => $item->uraian . ' dari ' . ($item->dariKas->nama_kas ?? '-'),
                    'debet' => $item->jumlah,
                    'kredit' => 0,
                    'saldo' => 0, // Will be calculated
                    'type' => 'transfer_in'
                ];
            });

        // Gabungkan semua transaksi
        $transaksi = $transaksi
            ->concat($pemasukan)
            ->concat($pengeluaran)
            ->concat($transferKeluar)
            ->concat($transferMasuk)
            ->sortBy('tanggal')
            ->values();

        // Calculate running saldo
        $saldoAwal = $this->getSaldoAwal($kasId, $startDate);
        $runningSaldo = $saldoAwal;

        $transaksi = $transaksi->map(function ($item) use (&$runningSaldo) {
            $runningSaldo += ($item->debet - $item->kredit);
            $item->saldo = $runningSaldo;
            return $item;
        });

        return $transaksi;
    }

    /**
     * Get saldo awal (before start date)
     */
    private function getSaldoAwal($kasId, $startDate)
    {
        // Pemasukan sebelum periode
        $totalPemasukan = Pemasukan::where('untuk_kas_id', $kasId)
            ->where('tanggal_transaksi', '<', $startDate)
            ->sum('jumlah');

        // Pengeluaran sebelum periode
        $totalPengeluaran = Pengeluaran::where('dari_kas_id', $kasId)
            ->where('tanggal_transaksi', '<', $startDate)
            ->sum('jumlah');

        // Transfer Masuk sebelum periode
        $totalTransferMasuk = Transfer::where('untuk_kas_id', $kasId)
            ->where('tanggal_transaksi', '<', $startDate)
            ->sum('jumlah');

        // Transfer Keluar sebelum periode
        $totalTransferKeluar = Transfer::where('dari_kas_id', $kasId)
            ->where('tanggal_transaksi', '<', $startDate)
            ->sum('jumlah');

        return $totalPemasukan - $totalPengeluaran + $totalTransferMasuk - $totalTransferKeluar;
    }

    /**
     * Print laporan buku besar with filters
     */
    public function cetakLaporan(Request $request)
    {
        $periode = $request->get('periode', Carbon::now()->format('Y-m'));
        
        list($year, $month) = explode('-', $periode);
        $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');

        $kasAccounts = DataKas::where('aktif', 'Y')->get();
        $bukuBesarData = [];
        $totalSaldo = 0;

        foreach ($kasAccounts as $kas) {
            $transaksi = $this->getTransaksiByKas($kas->id, $startDate, $endDate);
            
            if ($transaksi->isNotEmpty()) {
                $saldoAkhir = $transaksi->last()->saldo ?? 0;
                $totalSaldo += $saldoAkhir;

                $bukuBesarData[] = [
                    'kas' => $kas,
                    'transaksi' => $transaksi,
                    'saldo_awal' => $this->getSaldoAwal($kas->id, $startDate),
                    'saldo_akhir' => $saldoAkhir,
                    'total_debet' => $transaksi->sum('debet'),
                    'total_kredit' => $transaksi->sum('kredit'),
                ];
            }
        }

        return view('admin.Laporan.bukuBesar.CetakBukuBesar', compact(
            'bukuBesarData',
            'totalSaldo',
            'periode',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        // TODO: Implement Excel export using Laravel Excel
        $periode = $request->get('periode');
        $summary = $request->get('summary', true);
        $separate = $request->get('separate', true);
        $chart = $request->get('chart', false);
        
        return response('Export Excel Buku Besar periode: ' . $periode);
    }

    /**
     * Get data for filtering (AJAX)
     */
    public function getData(Request $request)
    {
        $periode = $request->get('periode');
        
        return response()->json([
            'status' => 'success',
            'data' => []
        ]);
    }
}