<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\Setting\IdentitasKoperasi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

// Models
use App\Models\Admin\DataMaster\{DataAnggota, DataKas, JenisAkun, JenisSimpanan};
use App\Models\Admin\Pinjaman\{Pinjaman, BayarAngsuran, DetailBayarAngsuran};
use App\Models\Admin\Simpanan\{SetoranTunai, PenarikanTunai};
use App\Models\Admin\TransaksiKas\{Pemasukan, Pengeluaran, Transfer};
use App\Models\Admin\Setting\SukuBunga;

class CetakLaporanController extends Controller
{
    // ==================== LAPORAN ANGGOTA ====================

    /**
     * Cetak Laporan Anggota
     */
    public function cetakAnggota(Request $request)
    {
        $aktif = $request->get('aktif') ?? $request->get('status') ?? '';
        $gender = $request->get('gender', '');
        $jabatan = $request->get('jabatan', '');
        $departemen = $request->get('departemen', '');

        $query = DataAnggota::query();

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

        $anggota = $query
            ->orderBy('tanggal_registrasi', 'desc')
            ->orderBy('nama', 'asc')
            ->get();

        // Debug: Uncomment untuk cek data
        // dd($anggota->toArray());

        $totalAnggota = $anggota->count();
        $anggotaAktif = $anggota->where('aktif', 'Aktif')->count();
        $anggotaNonAktif = $anggota->where('aktif', 'Non Aktif')->count();
        $anggotaLakiLaki = $anggota->where('jenis_kelamin', 'Laki-laki')->count();
        $anggotaPerempuan = $anggota->where('jenis_kelamin', 'Perempuan')->count();

        $filterInfo = [
            'status' => $aktif ?: 'Semua',
            'gender' => $gender ?: 'Semua',
            'jabatan' => $jabatan ?: 'Semua',
            'departemen' => $departemen ?: 'Semua',
        ];

        $identitas = IdentitasKoperasi::first();

        $pdf = Pdf::loadView(
            'admin.Laporan.anggota.Cetak',
            compact(
                'anggota',
                'totalAnggota',
                'anggotaAktif',
                'anggotaNonAktif',
                'anggotaLakiLaki',
                'anggotaPerempuan',
                'filterInfo',
                'identitas'
            )
        );

        // Sesuaikan dengan CSS di view (landscape)
        $pdf->setPaper('a4', 'landscape');

        // Tambahkan options untuk debugging
        $pdf->setOption('enable-local-file-access', true);

        return $pdf->stream('Laporan_Data_Anggota.pdf');
    }



    // ==================== LAPORAN BUKU BESAR ====================

    /**
     * Cetak Laporan Buku Besar
     */
    public function cetakBukuBesar(Request $request)
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
                    'saldo_awal' => $this->getSaldoAwalKas($kas->id, $startDate),
                    'saldo_akhir' => $saldoAkhir,
                    'total_debet' => $transaksi->sum('debet'),
                    'total_kredit' => $transaksi->sum('kredit'),
                ];
            }
        }

        // Generate PDF
        $pdf = Pdf::loadView(
            'admin.laporan.bukuBesar.Cetak',
            compact(
                'bukuBesarData',
                'totalSaldo',
                'periode',
                'startDate',
                'endDate'
            )
        );

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf->stream('Laporan_Buku_Besar_' . $periode . '.pdf');
    }

    /**
     * Get transaksi by kas account (helper for Buku Besar)
     */
    private function getTransaksiByKas($kasId, $startDate, $endDate)
    {
        $transaksi = collect();

        // Pemasukan (Debet)
        $pemasukan = Pemasukan::with(['dariAkun'])
            ->where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tanggal_transaksi,
                    'jenis_transaksi' => 'Pemasukan - ' . ($item->dariAkun->nama_akun ?? '-'),
                    'keterangan' => $item->uraian,
                    'debet' => $item->jumlah,
                    'kredit' => 0,
                    'saldo' => 0,
                    'type' => 'pemasukan'
                ];
            });

        // Pengeluaran (Kredit)
        $pengeluaran = Pengeluaran::with(['untukAkun'])
            ->where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tanggal_transaksi,
                    'jenis_transaksi' => 'Pengeluaran - ' . ($item->untukAkun->nama_akun ?? '-'),
                    'keterangan' => $item->uraian,
                    'debet' => 0,
                    'kredit' => $item->jumlah,
                    'saldo' => 0,
                    'type' => 'pengeluaran'
                ];
            });

        // Transfer Keluar (Kredit)
        $transferKeluar = Transfer::with(['untukKas'])
            ->where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tanggal_transaksi,
                    'jenis_transaksi' => 'Transfer Keluar',
                    'keterangan' => $item->uraian . ' ke ' . ($item->untukKas->nama_kas ?? '-'),
                    'debet' => 0,
                    'kredit' => $item->jumlah,
                    'saldo' => 0,
                    'type' => 'transfer_out'
                ];
            });

        // Transfer Masuk (Debet)
        $transferMasuk = Transfer::with(['dariKas'])
            ->where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tanggal_transaksi,
                    'jenis_transaksi' => 'Transfer Masuk',
                    'keterangan' => $item->uraian . ' dari ' . ($item->dariKas->nama_kas ?? '-'),
                    'debet' => $item->jumlah,
                    'kredit' => 0,
                    'saldo' => 0,
                    'type' => 'transfer_in'
                ];
            });

        $transaksi = $transaksi
            ->concat($pemasukan)
            ->concat($pengeluaran)
            ->concat($transferKeluar)
            ->concat($transferMasuk)
            ->sortBy('tanggal')
            ->values();

        // Calculate running saldo
        $saldoAwal = $this->getSaldoAwalKas($kasId, $startDate);
        $runningSaldo = $saldoAwal;

        $transaksi = $transaksi->map(function ($item) use (&$runningSaldo) {
            $runningSaldo += ($item->debet - $item->kredit);
            $item->saldo = $runningSaldo;
            return $item;
        });

        return $transaksi;
    }

    /**
     * Get saldo awal kas (helper for Buku Besar)
     */
    private function getSaldoAwalKas($kasId, $startDate)
    {
        $totalPemasukan = Pemasukan::where('untuk_kas_id', $kasId)
            ->where('tanggal_transaksi', '<', $startDate)
            ->sum('jumlah');

        $totalPengeluaran = Pengeluaran::where('dari_kas_id', $kasId)
            ->where('tanggal_transaksi', '<', $startDate)
            ->sum('jumlah');

        $totalTransferMasuk = Transfer::where('untuk_kas_id', $kasId)
            ->where('tanggal_transaksi', '<', $startDate)
            ->sum('jumlah');

        $totalTransferKeluar = Transfer::where('dari_kas_id', $kasId)
            ->where('tanggal_transaksi', '<', $startDate)
            ->sum('jumlah');

        return $totalPemasukan - $totalPengeluaran + $totalTransferMasuk - $totalTransferKeluar;
    }

    // ==================== LAPORAN JATUH TEMPO ====================

    /**
     * Cetak Laporan Jatuh Tempo
     */
    public function cetakJatuhTempo(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));

        $year = substr($periode, 0, 4);
        $month = substr($periode, 5, 2);

        $angsuran = BayarAngsuran::with(['pinjaman.anggota', 'pinjaman.lamaAngsuran'])
            ->whereYear('tanggal_jatuh_tempo', $year)
            ->whereMonth('tanggal_jatuh_tempo', $month)
            ->where('status_bayar', 'Belum')
            ->whereNull('deleted_at')
            ->whereHas('pinjaman', function ($query) {
                $query->whereNull('deleted_at')
                    ->where('status_lunas', 'Belum');
            })
            ->orderBy('tanggal_jatuh_tempo', 'asc')
            ->get();

        $jatuhTempo = $angsuran->map(function ($item) {
            $pinjaman = $item->pinjaman;

            $totalDibayar = DetailBayarAngsuran::where('pinjaman_id', $pinjaman->id)
                ->whereNull('deleted_at')
                ->sum('total_bayar');

            // Hitung selisih hari
            $tempo = Carbon::parse($item->tanggal_jatuh_tempo)->startOfDay();
            $now = Carbon::now()->startOfDay();

            $selisihHari = $now->diffInDays($tempo, false);

            return (object) [
                'kode_pinjam' => $pinjaman->kode_pinjaman,
                'id_anggota' => $pinjaman->anggota->id_anggota ?? '-',
                'nama_anggota' => $pinjaman->anggota->nama ?? 'Unknown',
                'tanggal_pinjam' => $pinjaman->tanggal_pinjam->format('Y-m-d'),
                'tanggal_tempo' => $item->tanggal_jatuh_tempo->format('Y-m-d'),
                'selisih_hari' => $selisihHari,
                'lama_pinjam' => $pinjaman->lamaAngsuran->lama_angsuran ?? 0,
                'jumlah_tagihan' => $pinjaman->jumlah_angsuran,
                'dibayar' => $totalDibayar,
                'sisa_tagihan' => $pinjaman->jumlah_angsuran - $totalDibayar,
                'angsuran_ke' => $item->angsuran_ke,
            ];
        });

        $totalTagihan = $jatuhTempo->sum('jumlah_tagihan');
        $totalDibayar = $jatuhTempo->sum('dibayar');
        $sisaTagihan = $jatuhTempo->sum('sisa_tagihan');

        // Hitung status tempo
        $lewatTempo = $jatuhTempo->filter(fn($item) => $item->selisih_hari < 0)->count();
        $segeraTempo = $jatuhTempo->filter(fn($item) => $item->selisih_hari >= 0 && $item->selisih_hari <= 7)->count();
        $normal = $jatuhTempo->filter(fn($item) => $item->selisih_hari > 7)->count();

        // Ambil identitas koperasi
        $identitas = IdentitasKoperasi::first();

        // Format periode untuk tampilan
        $periodeTampil = Carbon::createFromFormat('Y-m', $periode)->isoFormat('MMMM YYYY');

        // Generate PDF
        $pdf = Pdf::loadView(
            'admin.laporan.JatuhTempo.Cetak',
            compact(
                'jatuhTempo',
                'periode',
                'periodeTampil',
                'totalTagihan',
                'totalDibayar',
                'sisaTagihan',
                'lewatTempo',
                'segeraTempo',
                'normal',
                'identitas'
            )
        );

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf->stream('Laporan_Jatuh_Tempo_' . $periode . '.pdf');
    }

    // ==================== LAPORAN KAS ANGGOTA ====================

    /**
     * Cetak Laporan Kas Anggota
     */
    public function cetakKasAnggota(Request $request)
    {
        $anggota = $request->get('anggota', '');
        $status = $request->get('status', '');
        $jabatan = $request->get('jabatan', '');

        $query = DataAnggota::where('aktif', 'Aktif');

        if ($anggota)
            $query->where('id_anggota', $anggota);
        if ($jabatan)
            $query->where('jabatan', $jabatan);

        $anggotaList = $query->orderBy('nama', 'asc')->get();

        $kasAnggota = $anggotaList->map(function ($anggota) {
            $setoranTunai = SetoranTunai::where('anggota_id', $anggota->id)
                ->with('jenisSimpanan')
                ->get();

            $simpananSukarela = $setoranTunai
                ->where('jenisSimpanan.jenis_simpanan', 'Simpanan Sukarela')
                ->sum('jumlah');

            $simpananPokok = $setoranTunai
                ->where('jenisSimpanan.jenis_simpanan', 'Simpanan Pokok')
                ->sum('jumlah');

            $simpananWajib = $setoranTunai
                ->where('jenisSimpanan.jenis_simpanan', 'Simpanan Wajib')
                ->sum('jumlah');

            $simpananLainnya = $setoranTunai
                ->whereNotIn('jenisSimpanan.jenis_simpanan', [
                    'Simpanan Sukarela',
                    'Simpanan Pokok',
                    'Simpanan Wajib'
                ])
                ->sum('jumlah');

            $pinjaman = Pinjaman::where('anggota_id', $anggota->id)
                ->whereNull('deleted_at')
                ->get();

            $pokokPinjaman = $pinjaman->sum('pokok_pinjaman');
            $totalTagihan = $pinjaman->sum('jumlah_angsuran');

            $totalDibayar = DetailBayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->whereNull('deleted_at')
                ->sum('total_bayar');

            $totalDenda = DetailBayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->whereNull('deleted_at')
                ->sum('denda');

            $sisaTagihan = $totalTagihan - $totalDibayar;

            $jumlahPinjaman = $pinjaman->count();
            $pinjamanLunas = $pinjaman->where('status_lunas', 'Lunas')->count();

            $angsuranTerlambat = BayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->where('status_bayar', 'Belum')
                ->where('tanggal_jatuh_tempo', '<', now())
                ->whereNull('deleted_at')
                ->count();

            $statusPembayaran = $angsuranTerlambat > 0 ? 'Macet' : 'Lancar';

            $tanggalTempo = BayarAngsuran::whereIn('pinjaman_id', $pinjaman->pluck('id'))
                ->where('status_bayar', 'Belum')
                ->whereNull('deleted_at')
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->first();

            return (object) [
                'id_anggota' => $anggota->id_anggota,
                'nama' => $anggota->nama,
                'jenis_kelamin' => $anggota->jenis_kelamin,
                'jabatan' => $anggota->jabatan,
                'departemen' => $anggota->departement ?? '-',
                'alamat' => $anggota->alamat,
                'no_telepon' => $anggota->no_telp ?? '-',
                'foto' => $anggota->photo
                    ? asset('storage/' . $anggota->photo)
                    : asset('assets/images/profile/user-1.jpg'),
                'simpanan' => [
                    'sukarela' => $simpananSukarela,
                    'pokok' => $simpananPokok,
                    'wajib' => $simpananWajib,
                    'lainnya' => $simpananLainnya,
                ],
                'kredit' => [
                    'pokok_pinjaman' => $pokokPinjaman,
                    'tagihan_denda' => $totalTagihan + $totalDenda,
                    'dibayar' => $totalDibayar,
                    'sisa_tagihan' => $sisaTagihan > 0 ? $sisaTagihan : 0,
                ],
                'keterangan' => [
                    'jumlah_pinjaman' => $jumlahPinjaman,
                    'pinjaman_lunas' => $pinjamanLunas,
                    'status_pembayaran' => $statusPembayaran,
                    'tanggal_tempo' => $tanggalTempo
                        ? Carbon::parse($tanggalTempo->tanggal_jatuh_tempo)->translatedFormat('d M Y')
                        : '-',
                ],
            ];
        });

        if ($status) {
            $kasAnggota = $kasAnggota->filter(function ($item) use ($status) {
                return $item->keterangan['status_pembayaran'] === $status;
            });
        }

        // ===== TAMBAHKAN INI =====
        // Info filter untuk header PDF
        $anggotaNama = 'Semua';
        if ($anggota) {
            $dataAnggota = DataAnggota::where('id_anggota', $anggota)->first();
            $anggotaNama = $dataAnggota ? $dataAnggota->nama : 'Semua';
        }

        $filterInfo = [
            'anggota' => $anggotaNama,
            'status' => $status ?: 'Semua',
            'jabatan' => $jabatan ?: 'Semua',
        ];

        // Ambil identitas koperasi
        $identitas = IdentitasKoperasi::first();

        // ===== GENERATE PDF =====
        $pdf = Pdf::loadView(
            'admin.laporan.KasAnggota.Cetak',
            compact('kasAnggota', 'filterInfo', 'identitas')
        );

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf->stream('Laporan_Kas_Anggota_' . date('Y-m-d_His') . '.pdf');
    }

    // ==================== LAPORAN KAS PINJAMAN ====================

    /**
     * Cetak Laporan Kas Pinjaman
     */
    public function cetakKasPinjaman(Request $request)
    {
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

        $pokokPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');

        $tagihanPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('jumlah_angsuran');

        $tagihanDenda = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('denda');

        $sudahDibayar = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');

        $sisaTagihan = ($tagihanPinjaman + $tagihanDenda) - $sudahDibayar;

        $kasPinjaman = collect([
            (object) [
                'no' => 1,
                'keterangan' => 'Pokok Pinjaman',
                'jumlah' => $pokokPinjaman,
            ],
            (object) [
                'no' => 2,
                'keterangan' => 'Tagihan Pinjaman',
                'jumlah' => $tagihanPinjaman,
            ],
            (object) [
                'no' => 3,
                'keterangan' => 'Tagihan Denda',
                'jumlah' => $tagihanDenda,
            ],
            (object) [
                'no' => 4,
                'keterangan' => 'Tagihan Sudah Dibayar',
                'jumlah' => $sudahDibayar,
            ],
            (object) [
                'no' => 5,
                'keterangan' => 'Sisa Tagihan',
                'jumlah' => $sisaTagihan,
            ],
        ]);

        $jumlahPeminjam = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->distinct('anggota_id')
            ->count('anggota_id');

        $peminjamLunas = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->where('status_lunas', 'Lunas')
            ->whereNull('deleted_at')
            ->distinct('anggota_id')
            ->count('anggota_id');

        $belumLunas = $jumlahPeminjam - $peminjamLunas;

        $summary = (object) [
            'jumlah_peminjam' => $jumlahPeminjam,
            'peminjam_lunas' => $peminjamLunas,
            'belum_lunas' => $belumLunas,
        ];

        $jumlahTagihanDenda = $tagihanPinjaman + $tagihanDenda;

        return view('admin.Laporan.KasPinjaman.CetakKasPinjaman', compact(
            'kasPinjaman',
            'summary',
            'jumlahTagihanDenda',
            'tglDari',
            'tglSamp'
        ));
    }

    // ==================== LAPORAN KAS SIMPANAN ====================

    /**
     * Cetak Laporan Kas Simpanan
     */
    public function cetakKasSimpanan(Request $request)
    {
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $jenisSimpananList = JenisSimpanan::where('tampil', 'Y')
            ->orderBy('jenis_simpanan', 'asc')
            ->get();

        $kasSimpanan = collect();
        $totalSimpanan = 0;
        $totalPenarikan = 0;
        $totalJumlah = 0;
        $no = 1;

        foreach ($jenisSimpananList as $jenis) {
            $simpanan = SetoranTunai::where('jenis_simpanan_id', $jenis->id)
                ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
                ->sum('jumlah');

            $penarikan = PenarikanTunai::where('jenis_simpanan_id', $jenis->id)
                ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
                ->sum('jumlah');

            $jumlah = $simpanan - $penarikan;

            $kasSimpanan->push((object) [
                'no' => $no++,
                'jenis_akun' => $jenis->jenis_simpanan,
                'simpanan' => $simpanan,
                'penarikan' => $penarikan,
                'jumlah' => $jumlah,
            ]);

            $totalSimpanan += $simpanan;
            $totalPenarikan += $penarikan;
            $totalJumlah += $jumlah;
        }

        return view('admin.Laporan.KasSimpanan.CetakKasSimpanan', compact(
            'kasSimpanan',
            'totalSimpanan',
            'totalPenarikan',
            'totalJumlah',
            'tglDari',
            'tglSamp'
        ));
    }

    // ==================== LAPORAN LABA RUGI ====================

    /**
     * Cetak Laporan Laba Rugi
     */
    public function cetakLabaRugi(Request $request)
    {
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

        // Estimasi Pinjaman
        $jumlahPinjaman = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');

        $pendapatanAdmin = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->sum('biaya_admin');

        $pendapatanBunga = Pinjaman::whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
            ->whereNull('deleted_at')
            ->selectRaw('SUM(biaya_bunga * (SELECT lama_angsuran FROM lama_angsuran WHERE id = pinjaman.lama_angsuran_id)) as total_bunga')
            ->value('total_bunga') ?? 0;

        $pendapatanDenda = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('denda');

        $estimasiPinjaman = collect([
            (object) [
                'no' => 1,
                'keterangan' => 'Jumlah Pinjaman',
                'jumlah' => $jumlahPinjaman,
            ],
            (object) [
                'no' => 2,
                'keterangan' => 'Pendapatan Biaya Administrasi',
                'jumlah' => $pendapatanAdmin,
            ],
            (object) [
                'no' => 3,
                'keterangan' => 'Pendapatan Biaya Bunga',
                'jumlah' => $pendapatanBunga,
            ],
            (object) [
                'no' => 4,
                'keterangan' => 'Pendapatan Denda',
                'jumlah' => $pendapatanDenda,
            ],
        ]);

        $jumlahTagihan = $jumlahPinjaman + $pendapatanAdmin + $pendapatanBunga + $pendapatanDenda;
        $estimasiPendapatanPinjaman = $pendapatanAdmin + $pendapatanBunga + $pendapatanDenda;

        $sudahDibayar = DetailBayarAngsuran::whereHas('pinjaman', function ($query) use ($tglDari, $tglSamp) {
            $query->whereBetween('tanggal_pinjam', [$tglDari, $tglSamp])
                ->whereNull('deleted_at');
        })
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');

        $pendapatanPinjamanRealisasi = $sudahDibayar - $jumlahPinjaman;

        // Pendapatan
        $pendapatanList = collect();
        $no = 1;

        $pendapatanList->push((object) [
            'no' => $no++,
            'keterangan' => 'Pendapatan Pinjaman',
            'jumlah' => $pendapatanPinjamanRealisasi,
        ]);

        $dataPendapatan = JenisAkun::leftJoin('pemasukan', function ($join) use ($tglDari, $tglSamp) {
            $join->on('jenis_akun.id', '=', 'pemasukan.dari_akun_id')
                ->whereBetween('pemasukan.tanggal_transaksi', [$tglDari, $tglSamp])
                ->whereNull('pemasukan.deleted_at');
        })
            ->where('jenis_akun.laba_rugi', 'PENDAPATAN')
            ->where('jenis_akun.aktif', 'Y')
            ->select(
                'jenis_akun.id',
                'jenis_akun.jns_transaksi',
                DB::raw('COALESCE(SUM(pemasukan.jumlah), 0) as total')
            )
            ->groupBy('jenis_akun.id', 'jenis_akun.jns_transaksi')
            ->orderBy('jenis_akun.jns_transaksi')
            ->get();

        foreach ($dataPendapatan as $item) {
            $pendapatanList->push((object) [
                'no' => $no++,
                'keterangan' => $item->jns_transaksi,
                'jumlah' => $item->total,
            ]);
        }

        $jumlahPendapatan = $pendapatanList->sum('jumlah');

        // Biaya
        $biayaList = collect();
        $noBiaya = 1;

        $dataBiaya = JenisAkun::leftJoin('pengeluaran', function ($join) use ($tglDari, $tglSamp) {
            $join->on('jenis_akun.id', '=', 'pengeluaran.untuk_akun_id')
                ->whereBetween('pengeluaran.tanggal_transaksi', [$tglDari, $tglSamp])
                ->whereNull('pengeluaran.deleted_at');
        })
            ->where('jenis_akun.laba_rugi', 'BIAYA')
            ->where('jenis_akun.aktif', 'Y')
            ->select(
                'jenis_akun.id',
                'jenis_akun.jns_transaksi',
                DB::raw('COALESCE(SUM(pengeluaran.jumlah), 0) as total')
            )
            ->groupBy('jenis_akun.id', 'jenis_akun.jns_transaksi')
            ->orderBy('jenis_akun.jns_transaksi')
            ->get();

        foreach ($dataBiaya as $item) {
            $biayaList->push((object) [
                'no' => $noBiaya++,
                'keterangan' => $item->jns_transaksi,
                'jumlah' => $item->total,
            ]);
        }

        $jumlahBiaya = $biayaList->sum('jumlah');
        $labaRugi = $jumlahPendapatan - $jumlahBiaya;

        return view('admin.Laporan.LabaRugi.CetakLabaRugi', compact(
            'estimasiPinjaman',
            'pendapatanList',
            'biayaList',
            'jumlahTagihan',
            'estimasiPendapatanPinjaman',
            'jumlahPendapatan',
            'jumlahBiaya',
            'labaRugi',
            'tglDari',
            'tglSamp'
        ));
    }

    // ==================== LAPORAN NERACA SALDO ====================

    /**
     * Cetak Laporan Neraca Saldo
     */
    public function cetakNeracaSaldo(Request $request)
    {
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

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
                $neracaSaldo->push((object) [
                    'kategori' => $kategori,
                    'is_header' => true,
                    'debet' => 0,
                    'kredit' => 0,
                ]);
            }

            $saldo = $this->calculateSaldoAkun($akun->id, $tglDari, $tglSamp);

            $neracaSaldo->push((object) [
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

        // Ambil identitas koperasi
        $identitas = IdentitasKoperasi::first();

        // Format periode untuk tampilan
        $periodeTampil = Carbon::parse($tglDari)->translatedFormat('d F Y') . ' - ' .
            Carbon::parse($tglSamp)->translatedFormat('d F Y');

        // Generate PDF
        $pdf = Pdf::loadView(
            'admin.laporan.neracaSaldo.Cetak',
            compact(
                'neracaSaldo',
                'totalDebet',
                'totalKredit',
                'tglDari',
                'tglSamp',
                'periodeTampil',
                'identitas'
            )
        );

        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf->stream('Laporan_Neraca_Saldo_' . $tglDari . '_' . $tglSamp . '.pdf');
    }

    /**
     * Calculate saldo for specific akun (helper for Neraca Saldo)
     */
    private function calculateSaldoAkun($akunId, $tglDari, $tglSamp)
    {
        $totalDebet = 0;
        $totalKredit = 0;

        $pemasukan = Pemasukan::where('dari_akun_id', $akunId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        $pengeluaran = Pengeluaran::where('untuk_akun_id', $akunId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        $jenisAkun = JenisAkun::find($akunId);

        if ($jenisAkun) {
            if ($jenisAkun->akun === 'Aktiva') {
                $totalDebet = $pemasukan;
                $totalKredit = $pengeluaran;
            } else {
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
     * Add kas accounts to neraca saldo (helper)
     */
    private function addKasToNeracaSaldo(&$neracaSaldo, $tglDari, $tglSamp)
    {
        $kasAccounts = DataKas::where('aktif', 'Y')->get();

        foreach ($kasAccounts as $kas) {
            $saldo = $this->calculateSaldoKasNeraca($kas->id, $tglDari, $tglSamp);

            $aktivaIndex = $neracaSaldo->search(function ($item) {
                return $item->is_header && $item->kategori === 'A. Aktiva Lancar';
            });

            if ($aktivaIndex !== false) {
                $neracaSaldo->splice($aktivaIndex + 1, 0, [
                    (object) [
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
     * Calculate saldo for kas account (helper for Neraca Saldo)
     */
    private function calculateSaldoKasNeraca($kasId, $tglDari, $tglSamp)
    {
        $pemasukan = Pemasukan::where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        $pengeluaran = Pengeluaran::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        $transferIn = Transfer::where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        $transferOut = Transfer::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$tglDari, $tglSamp])
            ->sum('jumlah');

        return $pemasukan - $pengeluaran + $transferIn - $transferOut;
    }

    // ==================== LAPORAN SALDO KAS ====================

    /**
     * Cetak Laporan Saldo Kas
     */
    public function cetakSaldoKas(Request $request)
    {
        $periode = $request->get('periode', Carbon::now()->format('Y-m'));

        $periodeCarbon = Carbon::parse($periode . '-01');
        $startDate = $periodeCarbon->copy()->startOfMonth();
        $endDate = $periodeCarbon->copy()->endOfMonth();
        $periodeSebelumnya = $periodeCarbon->copy()->subMonth()->endOfMonth();

        $kasList = DataKas::where('aktif', 'Y')
            ->orderBy('nama_kas', 'asc')
            ->get();

        $saldoKas = collect();
        $no = 1;
        $saldoPeriodeSebelumnya = 0;

        foreach ($kasList as $kas) {
            $saldoAwalKas = $this->hitungSaldoSampai($kas->id, $periodeSebelumnya);
            $saldoPeriodeSebelumnya += $saldoAwalKas;

            $mutasiPeriode = $this->hitungMutasiPeriode($kas->id, $startDate, $endDate);

            $saldoKas->push((object) [
                'no' => $no++,
                'nama_kas' => $kas->nama_kas,
                'saldo' => $mutasiPeriode,
            ]);
        }

        $jumlahSaldo = $saldoKas->sum('saldo');
        $totalSaldo = $saldoPeriodeSebelumnya + $jumlahSaldo;
        $periodeDisplay = $periodeCarbon->locale('id')->isoFormat('MMMM YYYY');

        return view('admin.Laporan.SaldoKas.CetakSaldoKas', compact(
            'saldoKas',
            'saldoPeriodeSebelumnya',
            'jumlahSaldo',
            'totalSaldo',
            'periode',
            'periodeDisplay'
        ));
    }

    /**
     * Hitung saldo kas sampai tanggal tertentu (helper for Saldo Kas)
     */
    private function hitungSaldoSampai($kasId, $tanggal)
    {
        $saldo = 0;

        $pemasukan = Pemasukan::where('untuk_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo += $pemasukan;

        $pengeluaran = Pengeluaran::where('dari_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo -= $pengeluaran;

        $transferMasuk = Transfer::where('untuk_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo += $transferMasuk;

        $transferKeluar = Transfer::where('dari_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo -= $transferKeluar;

        $setoran = SetoranTunai::where('untuk_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo += $setoran;

        $penarikan = PenarikanTunai::where('dari_kas_id', $kasId)
            ->where('tanggal_transaksi', '<=', $tanggal)
            ->sum('jumlah');
        $saldo -= $penarikan;

        $pinjaman = Pinjaman::where('dari_kas_id', $kasId)
            ->where('tanggal_pinjam', '<=', $tanggal)
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');
        $saldo -= $pinjaman;

        $angsuran = DetailBayarAngsuran::where('ke_kas_id', $kasId)
            ->where('tanggal_bayar', '<=', $tanggal)
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');
        $saldo += $angsuran;

        return $saldo;
    }

    /**
     * Hitung mutasi kas dalam periode tertentu (helper for Saldo Kas)
     */
    private function hitungMutasiPeriode($kasId, $startDate, $endDate)
    {
        $mutasi = 0;

        $pemasukan = Pemasukan::where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi += $pemasukan;

        $pengeluaran = Pengeluaran::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi -= $pengeluaran;

        $transferMasuk = Transfer::where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi += $transferMasuk;

        $transferKeluar = Transfer::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi -= $transferKeluar;

        $setoran = SetoranTunai::where('untuk_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi += $setoran;

        $penarikan = PenarikanTunai::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->sum('jumlah');
        $mutasi -= $penarikan;

        $pinjaman = Pinjaman::where('dari_kas_id', $kasId)
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('pokok_pinjaman');
        $mutasi -= $pinjaman;

        $angsuran = DetailBayarAngsuran::where('ke_kas_id', $kasId)
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');
        $mutasi += $angsuran;

        return $mutasi;
    }

    // ==================== LAPORAN SHU ====================

    /**
     * Cetak Laporan SHU
     */
    public function cetakSHU(Request $request)
    {
        $anggotaId = $request->get('anggota_id', '');
        $tglDari = $request->get('tgl_dari', Carbon::now()->startOfYear()->format('Y-m-d'));
        $tglSamp = $request->get('tgl_samp', Carbon::now()->endOfYear()->format('Y-m-d'));

        try {
            $sukuBunga = SukuBunga::getSetting();

            $pendapatan = $this->hitungPendapatanSHU($tglDari, $tglSamp, $anggotaId);
            $beban = $this->hitungBebanSHU($tglDari, $tglSamp, $anggotaId);

            $shuSebelumPajak = $pendapatan['total'] - $beban['total'];
            $persenPajak = $sukuBunga->pjk_pph ?? 5;
            $pajakPPh = $shuSebelumPajak * ($persenPajak / 100);
            $shuSetelahPajak = $shuSebelumPajak - $pajakPPh;

            $danaCadangan = $shuSetelahPajak * (($sukuBunga->dana_cadangan ?? 40) / 100);
            $jasaAnggota = $shuSetelahPajak * (($sukuBunga->jasa_anggota ?? 40) / 100);
            $danaPengurus = $shuSetelahPajak * (($sukuBunga->dana_pengurus ?? 5) / 100);
            $danaKaryawan = $shuSetelahPajak * (($sukuBunga->dana_karyawan ?? 5) / 100);
            $danaPendidikan = $shuSetelahPajak * (($sukuBunga->dana_pend ?? 5) / 100);
            $danaSosial = $shuSetelahPajak * (($sukuBunga->dana_sosial ?? 5) / 100);

            $jasaUsaha = $jasaAnggota * (($sukuBunga->jasa_usaha ?? 70) / 100);
            $jasaModal = $jasaAnggota * (($sukuBunga->jasa_modal ?? 30) / 100);

            if ($anggotaId) {
                $totalPendapatanAnggota = $this->hitungPendapatanAnggotaSHU($tglDari, $tglSamp, $anggotaId);
                $totalSimpananAnggota = $this->hitungSimpananAnggotaSHU($tglDari, $tglSamp, $anggotaId);
                $anggota = DataAnggota::find($anggotaId);
            } else {
                $totalPendapatanAnggota = $pendapatan['total'];
                $totalSimpananAnggota = $this->hitungTotalSimpananSHU($tglDari, $tglSamp);
                $anggota = null;
            }

            return view('admin.Laporan.shu.cetak_shu', compact(
                'shuSebelumPajak',
                'pajakPPh',
                'shuSetelahPajak',
                'danaCadangan',
                'jasaAnggota',
                'danaPengurus',
                'danaKaryawan',
                'danaPendidikan',
                'danaSosial',
                'jasaUsaha',
                'jasaModal',
                'totalPendapatanAnggota',
                'totalSimpananAnggota',
                'tglDari',
                'tglSamp',
                'anggota',
                'pendapatan',
                'beban'
            ));

        } catch (\Exception $e) {
            Log::error('Error printing SHU: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mencetak laporan.');
        }
    }

    /**
     * Hitung Total Pendapatan (helper for SHU)
     */
    private function hitungPendapatanSHU($tglDari, $tglSamp, $anggotaId = null)
    {
        $startDate = Carbon::parse($tglDari)->startOfDay();
        $endDate = Carbon::parse($tglSamp)->endOfDay();

        $queryBunga = DetailBayarAngsuran::with('angsuran.pinjaman')
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->whereNull('deleted_at');

        if ($anggotaId) {
            $queryBunga->whereHas('pinjaman', function ($q) use ($anggotaId) {
                $q->where('anggota_id', $anggotaId);
            });
        }

        $pendapatanBunga = $queryBunga->get()->sum(function ($item) {
            return $item->pinjaman->biaya_bunga ?? 0;
        });

        $queryDenda = DetailBayarAngsuran::whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->whereNull('deleted_at');

        if ($anggotaId) {
            $queryDenda->whereHas('pinjaman', function ($q) use ($anggotaId) {
                $q->where('anggota_id', $anggotaId);
            });
        }

        $pendapatanDenda = $queryDenda->sum('denda');

        $queryPemasukan = Pemasukan::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at');

        $pendapatanLainLain = $queryPemasukan->sum('jumlah');

        $totalPendapatan = $pendapatanBunga + $pendapatanDenda + $pendapatanLainLain;

        return [
            'bunga' => $pendapatanBunga,
            'denda' => $pendapatanDenda,
            'lain_lain' => $pendapatanLainLain,
            'total' => $totalPendapatan
        ];
    }

    /**
     * Hitung Total Beban (helper for SHU)
     */
    private function hitungBebanSHU($tglDari, $tglSamp, $anggotaId = null)
    {
        $startDate = Carbon::parse($tglDari)->startOfDay();
        $endDate = Carbon::parse($tglSamp)->endOfDay();

        $queryPengeluaran = Pengeluaran::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at');

        $bebanOperasional = $queryPengeluaran->sum('jumlah');

        $queryAdmin = DetailBayarAngsuran::with('pinjaman')
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->whereNull('deleted_at');

        if ($anggotaId) {
            $queryAdmin->whereHas('pinjaman', function ($q) use ($anggotaId) {
                $q->where('anggota_id', $anggotaId);
            });
        }

        $bebanAdministrasi = $queryAdmin->get()->sum(function ($item) {
            return $item->pinjaman->biaya_admin ?? 0;
        });

        $totalBeban = $bebanOperasional + $bebanAdministrasi;

        return [
            'operasional' => $bebanOperasional,
            'administrasi' => $bebanAdministrasi,
            'total' => $totalBeban
        ];
    }

    /**
     * Hitung Pendapatan Anggota Tertentu (helper for SHU)
     */
    private function hitungPendapatanAnggotaSHU($tglDari, $tglSamp, $anggotaId)
    {
        $startDate = Carbon::parse($tglDari)->startOfDay();
        $endDate = Carbon::parse($tglSamp)->endOfDay();

        $totalBayar = DetailBayarAngsuran::whereHas('pinjaman', function ($q) use ($anggotaId) {
            $q->where('anggota_id', $anggotaId);
        })
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah_bayar');

        return $totalBayar;
    }

    /**
     * Hitung Total Simpanan Anggota Tertentu (helper for SHU)
     */
    private function hitungSimpananAnggotaSHU($tglDari, $tglSamp, $anggotaId)
    {
        $startDate = Carbon::parse($tglDari)->startOfDay();
        $endDate = Carbon::parse($tglSamp)->endOfDay();

        $totalSetoran = SetoranTunai::where('anggota_id', $anggotaId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $totalPenarikan = PenarikanTunai::where('anggota_id', $anggotaId)
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        return $totalSetoran - $totalPenarikan;
    }

    /**
     * Hitung Total Simpanan Semua Anggota (helper for SHU)
     */
    private function hitungTotalSimpananSHU($tglDari, $tglSamp)
    {
        $startDate = Carbon::parse($tglDari)->startOfDay();
        $endDate = Carbon::parse($tglSamp)->endOfDay();

        $totalSetoran = SetoranTunai::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        $totalPenarikan = PenarikanTunai::whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->sum('jumlah');

        return $totalSetoran - $totalPenarikan;
    }

    // ==================== LAPORAN TRANSAKSI KAS ====================

    /**
     * Cetak Laporan Transaksi Kas
     */
    public function cetakTransaksiKas(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfYear()->format('Y-m-d'));
        $format = $request->get('format', 'lengkap');

        $transaksiKas = $this->getTransaksiKasData($startDate, $endDate);
        $saldoSebelumnya = $this->calculateSaldoSebelumnya($startDate);

        $totalDebet = $transaksiKas->sum('debet');
        $totalKredit = $transaksiKas->sum('kredit');
        $saldoAkhir = $saldoSebelumnya + $totalDebet - $totalKredit;

        // Ambil identitas koperasi
        $identitas = IdentitasKoperasi::first();

        // Format tanggal untuk tampilan
        $periodeTampil = Carbon::parse($startDate)->translatedFormat('d F Y') . ' - ' .
            Carbon::parse($endDate)->translatedFormat('d F Y');

        // Generate PDF
        $pdf = Pdf::loadView(
            'admin.laporan.TransaksiKas.Cetak',
            compact(
                'transaksiKas',
                'startDate',
                'endDate',
                'periodeTampil',
                'format',
                'saldoSebelumnya',
                'totalDebet',
                'totalKredit',
                'saldoAkhir',
                'identitas'
            )
        );

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf->stream('Laporan_Transaksi_Kas_' . $startDate . '_' . $endDate . '.pdf');
    }

    /**
     * Get combined transaction data (helper for Transaksi Kas)
     */
    private function getTransaksiKasData($startDate, $endDate)
    {
        $transaksiKas = collect();

        $pemasukan = Pemasukan::with(['untukKas', 'dariAkun'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => 'PM-' . $item->id,
                    'kode_transaksi' => $item->kode_transaksi,
                    'tanggal_transaksi' => $item->tanggal_transaksi,
                    'akun_transaksi' => $item->dariAkun->nama_akun ?? '-',
                    'keterangan' => $item->uraian,
                    'dari_kas' => $item->dariAkun->nama_akun ?? '-',
                    'untuk_kas' => $item->untukKas->nama_kas ?? '-',
                    'debet' => $item->jumlah,
                    'kredit' => 0,
                    'saldo' => 0,
                    'type' => 'pemasukan'
                ];
            });

        $pengeluaran = Pengeluaran::with(['dariKas', 'untukAkun'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => 'PK-' . $item->id,
                    'kode_transaksi' => $item->kode_transaksi,
                    'tanggal_transaksi' => $item->tanggal_transaksi,
                    'akun_transaksi' => $item->untukAkun->nama_akun ?? '-',
                    'keterangan' => $item->uraian,
                    'dari_kas' => $item->dariKas->nama_kas ?? '-',
                    'untuk_kas' => $item->untukAkun->nama_akun ?? '-',
                    'debet' => 0,
                    'kredit' => $item->jumlah,
                    'saldo' => 0,
                    'type' => 'pengeluaran'
                ];
            });

        $transfer = Transfer::with(['dariKas', 'untukKas'])
            ->whereBetween('tanggal_transaksi', [$startDate, $endDate])
            ->get()
            ->flatMap(function ($item) {
                return [
                    (object) [
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
                    (object) [
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

        $transaksiKas = $transaksiKas
            ->concat($pemasukan)
            ->concat($pengeluaran)
            ->concat($transfer)
            ->sortBy('tanggal_transaksi')
            ->values();

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
     * Calculate saldo before start date (helper for Transaksi Kas)
     */
    private function calculateSaldoSebelumnya($startDate)
    {
        $totalPemasukan = Pemasukan::where('tanggal_transaksi', '<', $startDate)->sum('jumlah');
        $totalPengeluaran = Pengeluaran::where('tanggal_transaksi', '<', $startDate)->sum('jumlah');

        return $totalPemasukan - $totalPengeluaran;
    }

    // ==================== LAPORAN KREDIT MACET ====================

    /**
     * Cetak Laporan Kredit Macet
     */
    public function cetakKreditMacet(Request $request)
    {
        $periode = $request->get('periode', date('Y-m'));

        // Parse periode
        [$year, $month] = explode('-', $periode);
        $endDate = Carbon::createFromFormat('Y-m', $periode)->endOfMonth();

        // Query untuk mendapatkan kredit macet
        $query = BayarAngsuran::with([
            'pinjaman.anggota',
            'pinjaman.lamaAngsuran'
        ])
            ->where('status_bayar', 'Belum')
            ->where('tanggal_jatuh_tempo', '<=', $endDate)
            ->where('tanggal_jatuh_tempo', '<', Carbon::now());

        $angsuranMacet = $query->orderBy('tanggal_jatuh_tempo', 'asc')->get();

        // Group by pinjaman untuk menghitung total per anggota
        $kreditMacetGrouped = $angsuranMacet->groupBy('pinjaman_id')->map(function ($items) {
            $pinjaman = $items->first()->pinjaman;

            // Hitung total yang sudah dibayar dari pinjaman ini
            $totalDibayar = DetailBayarAngsuran::where('pinjaman_id', $pinjaman->id)
                ->whereNull('deleted_at')
                ->sum('total_bayar');

            // Hitung sisa tagihan
            $sisaTagihan = $pinjaman->jumlah_angsuran - $totalDibayar;

            $angsuranPertama = $items
                ->whereNotNull('tanggal_jatuh_tempo')
                ->sortBy('tanggal_jatuh_tempo')
                ->first();

            if ($angsuranPertama) {
                $tempo = Carbon::parse($angsuranPertama->tanggal_jatuh_tempo)->startOfDay();
                $now = Carbon::now()->startOfDay();

                $hariTerlambat = $now->greaterThan($tempo)
                    ? $tempo->diffInDays($now)
                    : 0;
            } else {
                $hariTerlambat = 0;
            }


            return (object) [
                'id' => $pinjaman->id,
                'kode_pinjam' => $pinjaman->kode_pinjaman,
                'id_anggota' => $pinjaman->anggota->id_anggota ?? '-',
                'nama_anggota' => $pinjaman->anggota->nama ?? 'Unknown',
                'tanggal_pinjam' => $pinjaman->tanggal_pinjam->format('Y-m-d'),
                'tanggal_tempo' => $angsuranPertama->tanggal_jatuh_tempo,
                'lama_pinjam' => $pinjaman->lamaAngsuran->lama_angsuran ?? 0,
                'pokok_pinjaman' => $pinjaman->pokok_pinjaman,
                'jumlah_tagihan' => $pinjaman->jumlah_angsuran,
                'dibayar' => $totalDibayar,
                'sisa_tagihan' => $sisaTagihan,
                'hari_terlambat' => $hariTerlambat,
                'jumlah_angsuran_terlambat' => $items->count(),
                'no_telepon' => $pinjaman->anggota->no_telp ?? '-',
            ];
        })->values();

        $kreditMacet = $kreditMacetGrouped;

        // Calculate totals
        $totalPokok = $kreditMacet->sum('pokok_pinjaman');
        $totalTagihan = $kreditMacet->sum('jumlah_tagihan');
        $totalDibayar = $kreditMacet->sum('dibayar');
        $sisaTagihan = $kreditMacet->sum('sisa_tagihan');

        // Kategorikan tingkat keterlambatan
        $sangatBerat = $kreditMacet->filter(fn($item) => $item->hari_terlambat > 90)->count();
        $berat = $kreditMacet->filter(fn($item) => $item->hari_terlambat > 60 && $item->hari_terlambat <= 90)->count();
        $sedang = $kreditMacet->filter(fn($item) => $item->hari_terlambat > 30 && $item->hari_terlambat <= 60)->count();
        $ringan = $kreditMacet->filter(fn($item) => $item->hari_terlambat <= 30)->count();

        // Ambil identitas koperasi
        $identitas = IdentitasKoperasi::first();

        // Format periode untuk tampilan
        $periodeTampil = Carbon::createFromFormat('Y-m', $periode)->isoFormat('MMMM YYYY');

        // Generate PDF
        $pdf = Pdf::loadView(
            'admin.laporan.KreditMacet.Cetak',
            compact(
                'kreditMacet',
                'periode',
                'periodeTampil',
                'totalPokok',
                'totalTagihan',
                'totalDibayar',
                'sisaTagihan',
                'sangatBerat',
                'berat',
                'sedang',
                'ringan',
                'identitas'
            )
        );

        $pdf->setPaper('a4', 'landscape');
        $pdf->setOption('enable-local-file-access', true);

        return $pdf->stream('Laporan_Kredit_Macet_' . $periode . '.pdf');
    }
}