<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\TransaksiKas\Pemasukan;
use App\Models\Admin\TransaksiKas\Pengeluaran;
use App\Models\Admin\TransaksiKas\Transfer;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransaksiKasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get date filters
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Set default dates to current year if not provided
        if (!$startDate || !$endDate) {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
        }

        // Ambil data transaksi kas gabungan
        $transaksiKas = $this->getTransaksiKasData($startDate, $endDate);

        // Calculate saldo sebelumnya (transactions before start date)
        $saldoSebelumnya = $this->calculateSaldoSebelumnya($startDate);

        // Calculate totals
        $totalDebet = $transaksiKas->sum('debet');
        $totalKredit = $transaksiKas->sum('kredit');
        $saldoAkhir = $saldoSebelumnya + $totalDebet - $totalKredit;
        $totalData = $transaksiKas->count();

        // Notifications (contoh, bisa disesuaikan)
        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.Laporan.transaksiKas.TransaksiKas', compact(
            'transaksiKas',
            'notifications',
            'saldoSebelumnya',
            'totalDebet',
            'totalKredit',
            'saldoAkhir',
            'totalData',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Get combined transaction data
     */
    private function getTransaksiKasData($startDate, $endDate)
    {
        $transaksiKas = collect();

        // 1. Pemasukan (Debet)
        $pemasukan = Pemasukan::with(['untukKas', 'dariAkun'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object)[
                    'id' => 'PM-' . $item->id,
                    'kode_transaksi' => $item->kode_transaksi,
                    'tanggal_transaksi' => $item->tanggal_transaksi,
                    'akun_transaksi' => $item->dariAkun->nama_akun ?? '-',
                    'keterangan' => $item->uraian,
                    'dari_kas' => $item->dariAkun->nama_akun ?? '-',
                    'untuk_kas' => $item->untukKas->nama_kas ?? '-',
                    'debet' => $item->jumlah,
                    'kredit' => 0,
                    'saldo' => 0, // Will be calculated later
                    'type' => 'pemasukan'
                ];
            });

        // 2. Pengeluaran (Kredit)
        $pengeluaran = Pengeluaran::with(['dariKas', 'untukAkun'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object)[
                    'id' => 'PK-' . $item->id,
                    'kode_transaksi' => $item->kode_transaksi,
                    'tanggal_transaksi' => $item->tanggal_transaksi,
                    'akun_transaksi' => $item->untukAkun->nama_akun ?? '-',
                    'keterangan' => $item->uraian,
                    'dari_kas' => $item->dariKas->nama_kas ?? '-',
                    'untuk_kas' => $item->untukAkun->nama_akun ?? '-',
                    'debet' => 0,
                    'kredit' => $item->jumlah,
                    'saldo' => 0, // Will be calculated later
                    'type' => 'pengeluaran'
                ];
            });

        // 3. Transfer (Kredit dari kas asal, Debet ke kas tujuan)
        // Untuk laporan kas, kita tampilkan sebagai 2 baris terpisah
        $transfer = Transfer::with(['dariKas', 'untukKas'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->flatMap(function ($item) {
                return [
                    // Transfer keluar (Kredit)
                    (object)[
                        'id' => 'TF-OUT-' . $item->id,
                        'kode_transaksi' => $item->kode_transaksi,
                        'tanggal_transaksi' => $item->tanggal_transaksi,
                        'akun_transaksi' => 'Transfer Kas Keluar',
                        'keterangan' => $item->uraian . ' (Keluar)',
                        'dari_kas' => $item->dariKas->nama_kas ?? '-',
                        'untuk_kas' => $item->untukKas->nama_kas ?? '-',
                        'debet' => 0,
                        'kredit' => $item->jumlah,
                        'saldo' => 0,
                        'type' => 'transfer_out'
                    ],
                    // Transfer masuk (Debet)
                    (object)[
                        'id' => 'TF-IN-' . $item->id,
                        'kode_transaksi' => $item->kode_transaksi,
                        'tanggal_transaksi' => $item->tanggal_transaksi,
                        'akun_transaksi' => 'Transfer Kas Masuk',
                        'keterangan' => $item->uraian . ' (Masuk)',
                        'dari_kas' => $item->dariKas->nama_kas ?? '-',
                        'untuk_kas' => $item->untukKas->nama_kas ?? '-',
                        'debet' => $item->jumlah,
                        'kredit' => 0,
                        'saldo' => 0,
                        'type' => 'transfer_in'
                    ]
                ];
            });

        // Gabungkan semua transaksi
        $transaksiKas = $transaksiKas
            ->concat($pemasukan)
            ->concat($pengeluaran)
            ->concat($transfer)
            ->sortBy('tanggal_transaksi')
            ->values();

        // Calculate running saldo
        $saldoSebelumnya = $this->calculateSaldoSebelumnya($startDate);
        $runningSaldo = $saldoSebelumnya;

        $transaksiKas = $transaksiKas->map(function ($item) use (&$runningSaldo) {
            $runningSaldo += ($item->debet - $item->kredit);
            $item->saldo = $runningSaldo;
            return $item;
        });

        return $transaksiKas;
    }

    /**
     * Calculate saldo before start date
     */
    private function calculateSaldoSebelumnya($startDate)
    {
        $totalPemasukan = Pemasukan::where('tanggal_transaksi', '<', $startDate)->sum('jumlah');
        $totalPengeluaran = Pengeluaran::where('tanggal_transaksi', '<', $startDate)->sum('jumlah');
        
        // Transfer tidak mempengaruhi total saldo (hanya perpindahan antar kas)
        
        return $totalPemasukan - $totalPengeluaran;
    }

    /**
     * Print laporan transaksi kas with filters
     */
    public function cetakLaporan(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $format = $request->get('format', 'lengkap');

        $transaksiKas = $this->getTransaksiKasData($startDate, $endDate);
        $saldoSebelumnya = $this->calculateSaldoSebelumnya($startDate);
        
        $totalDebet = $transaksiKas->sum('debet');
        $totalKredit = $transaksiKas->sum('kredit');
        $saldoAkhir = $saldoSebelumnya + $totalDebet - $totalKredit;

        return view('admin.Laporan.transaksiKas.CetakTransaksiKas', compact(
            'transaksiKas',
            'startDate',
            'endDate',
            'format',
            'saldoSebelumnya',
            'totalDebet',
            'totalKredit',
            'saldoAkhir'
        ));
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        // TODO: Implement Excel export using Laravel Excel
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $format = $request->get('format', 'xlsx');
        $template = $request->get('template', 'standard');
        
        return response('Export Excel Transaksi Kas: ' . $startDate . ' - ' . $endDate);
    }

    /**
     * Export to PDF
     */
    public function exportPDF(Request $request)
    {
        // TODO: Implement PDF export using DomPDF or similar
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $orientasi = $request->get('orientasi', 'portrait');
        $paper = $request->get('paper', 'A4');
        
        return response('Export PDF Transaksi Kas: ' . $startDate . ' - ' . $endDate);
    }

    /**
     * Get data for filtering (AJAX)
     */
    public function getData(Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $transaksiKas = $this->getTransaksiKasData($startDate, $endDate);
        $saldoSebelumnya = $this->calculateSaldoSebelumnya($startDate);
        
        $totalDebet = $transaksiKas->sum('debet');
        $totalKredit = $transaksiKas->sum('kredit');
        $saldoAkhir = $saldoSebelumnya + $totalDebet - $totalKredit;

        return response()->json([
            'status' => 'success',
            'data' => $transaksiKas,
            'summary' => [
                'saldo_sebelumnya' => $saldoSebelumnya,
                'total_debet' => $totalDebet,
                'total_kredit' => $totalKredit,
                'saldo_akhir' => $saldoAkhir,
                'total_data' => $transaksiKas->count()
            ]
        ]);
    }
}