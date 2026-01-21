<?php

namespace App\Http\Controllers\Admin\Pinjaman;

use App\Http\Controllers\Controller;
use App\Models\Admin\Pinjaman\Pinjaman;
use App\Models\Admin\Pinjaman\PinjamanLunas;
use App\Models\Admin\Pinjaman\DetailPinjamanLunas;
use App\Models\Admin\Pinjaman\BayarAngsuran;
use App\Models\Admin\Pinjaman\DetailBayarAngsuran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PinjamanLunasController extends Controller
{
    /**
     * Display a listing of paid off loans
     */
    /**
     * Display a listing of paid off loans
     */
    public function index(Request $request)
    {
        try {
            $query = PinjamanLunas::with([
                'pinjaman.anggota',
                'pinjaman.lamaAngsuran',
                'user'
            ])
                ->whereNull('deleted_at'); // ✅ Explicit filter untuk data yang tidak soft deleted

            // Filter by Kode
            if ($request->filled('kode')) {
                $query->filterKode($request->kode);
            }

            // Filter by Nama Anggota
            if ($request->filled('nama')) {
                $query->whereHas('pinjaman.anggota', function ($q) use ($request) {
                    $q->where('nama', 'like', '%' . $request->nama . '%');
                });
            }

            // Filter by Tanggal Range
            if ($request->filled('tanggal')) {
                $dates = explode(' - ', $request->tanggal);
                if (count($dates) === 2) {
                    try {
                        $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                        $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                        $query->filterTanggal($startDate, $endDate);
                    } catch (\Exception $e) {
                        Log::warning('Invalid tanggal format', [
                            'input' => $request->tanggal,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            $pinjamanLunas = $query->orderBy('tanggal_lunas', 'desc')->get();

            // Format data untuk view dengan validasi
            foreach ($pinjamanLunas as $item) {
                // ✅ VALIDASI: Skip jika pinjaman null
                if (!$item->pinjaman) {
                    Log::warning('Pinjaman null ditemukan', [
                        'pinjaman_lunas_id' => $item->id,
                        'kode_lunas' => $item->kode_lunas
                    ]);
                    continue;
                }

                $pinjaman = $item->pinjaman;

                // ✅ VALIDASI: Skip jika anggota null
                if (!$pinjaman->anggota) {
                    Log::warning('Anggota null ditemukan', [
                        'pinjaman_id' => $pinjaman->id,
                        'kode_pinjaman' => $pinjaman->kode_pinjaman
                    ]);
                    $item->anggota_id = '-';
                    $item->anggota_nama = 'Unknown';
                    $item->anggota_departemen = '-';
                    $item->anggota_foto = 'assets/images/profile/user-1.jpg';
                } else {
                    $item->anggota_id = $pinjaman->anggota->id_anggota ?? '-';
                    $item->anggota_nama = $pinjaman->anggota->nama ?? 'Unknown';
                    $item->anggota_departemen = $pinjaman->anggota->departement ?? '-';
                    $item->anggota_foto = $pinjaman->anggota->photo ?? 'assets/images/profile/user-1.jpg';
                }

                // Set properties lainnya
                $item->kode = $item->kode_lunas;
                $item->tanggal_pinjam_display = $pinjaman->tanggal_pinjam;
                $item->tanggal_tempo = Carbon::parse($pinjaman->tanggal_pinjam)
                    ->addMonths($item->lama_cicilan)
                    ->format('Y-m-d H:i:s');
                $item->lama_pinjaman = $item->lama_cicilan;
                $item->total_tagihan = $pinjaman->pokok_pinjaman + ($pinjaman->biaya_bunga * $item->lama_cicilan);
                $item->sudah_dibayar = $item->total_dibayar;
                $item->status_lunas = 'Lunas';
                $item->user_name = $item->user->name ?? 'System';
            }

            // ✅ Filter data yang valid (pinjaman tidak null)
            $pinjamanLunas = $pinjamanLunas->filter(function ($item) {
                return $item->pinjaman !== null;
            });

            // Notifikasi angsuran yang akan jatuh tempo (untuk pinjaman aktif)
            $notifications = BayarAngsuran::with(['pinjaman.anggota'])
                ->where('status_bayar', 'Belum')
                ->whereBetween('tanggal_jatuh_tempo', [now(), now()->addDays(7)])
                ->orderBy('tanggal_jatuh_tempo', 'asc')
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'nama' => $item->pinjaman->anggota->nama ?? 'Unknown',
                        'tanggal_jatuh_tempo' => $item->tanggal_jatuh_tempo->translatedFormat('d F Y'),
                        'sisa_tagihan' => $item->jumlah_angsuran,
                    ];
                });

            return view('admin.Pinjaman.lunas.PinjamanLunas', compact('pinjamanLunas', 'notifications'));

        } catch (\Exception $e) {
            Log::error('Error di PinjamanLunas index', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan saat memuat data pinjaman lunas. Silakan coba lagi. Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the detail of a specific paid off loan
     */
    public function show($id)
    {
        try {
            $pinjamanLunas = PinjamanLunas::with([
                'pinjaman.anggota',
                'pinjaman.lamaAngsuran',
                'pinjaman.angsuran',
                'user'
            ])->findOrFail($id);

            $pinjaman = $pinjamanLunas->pinjaman;

            // Data pinjaman untuk view
            $pinjamanData = (object) [
                'id' => $pinjamanLunas->id,
                'kode' => $pinjamanLunas->kode_lunas,
                'tanggal_pinjam' => $pinjaman->tanggal_pinjam->format('Y-m-d'),
                'tanggal_tempo' => Carbon::parse($pinjaman->tanggal_pinjam)
                    ->addMonths($pinjamanLunas->lama_cicilan)
                    ->format('Y-m-d'),
                'lama_pinjaman' => $pinjamanLunas->lama_cicilan,

                // Data Anggota
                'anggota_id' => $pinjaman->anggota->id_anggota ?? '-',
                'anggota_nama' => $pinjaman->anggota->nama ?? 'Unknown',
                'anggota_departemen' => $pinjaman->anggota->departement ?? '-',
                'anggota_ttl' => ($pinjaman->anggota->tempat_lahir ?? '-') . ', ' .
                    Carbon::parse($pinjaman->anggota->tanggal_lahir)->translatedFormat('d F Y'),
                'anggota_kota' => $pinjaman->anggota->kota ?? '-',
                'anggota_foto' => $pinjaman->anggota->photo ?? 'assets/images/profile/user-1.jpg',

                // Data Keuangan
                'pokok_pinjaman' => $pinjaman->pokok_pinjaman,
                'angsuran_pokok' => $pinjaman->angsuran_pokok,
                'biaya_bunga' => $pinjaman->biaya_bunga,
                'jumlah_angsuran' => $pinjaman->jumlah_angsuran,
                'sudah_dibayar' => $pinjamanLunas->total_dibayar,
                'jumlah_denda' => $pinjamanLunas->total_denda,
                'sisa_tagihan' => 0,
                'status_lunas' => 'Lunas',
            ];

            // Simulasi tagihan dari bayar_angsuran
            $simulasi = $pinjaman->angsuran()
                ->orderBy('angsuran_ke', 'asc')
                ->get()
                ->map(function ($item) use ($pinjaman) {
                    return (object) [
                        'bulan_ke' => $item->angsuran_ke,
                        'angsuran_pokok' => $pinjaman->angsuran_pokok,
                        'angsuran_bunga' => $pinjaman->biaya_bunga,
                        'biaya_admin' => $pinjaman->biaya_admin ?? 0,
                        'jumlah_angsuran' => $item->jumlah_angsuran,
                        'tanggal_tempo' => $item->tanggal_jatuh_tempo->translatedFormat('d F Y'),
                        'status' => $item->status_bayar
                    ];
                });

            // Ambil dari detail_bayar_angsuran
            $transaksi = DetailBayarAngsuran::with(['angsuran', 'user'])
                ->where('pinjaman_id', $pinjaman->id)
                ->whereNull('deleted_at')
                ->orderBy('angsuran_ke', 'asc')
                ->get()
                ->map(function ($item, $index) use ($pinjamanLunas) {
                    $lamaAngsuran = $pinjamanLunas->lama_cicilan;

                    return (object) [
                        'no' => $index + 1,
                        'kode_bayar' => $item->kode_bayar,
                        'tanggal_bayar' => Carbon::parse($item->tanggal_bayar)->format('d M Y - H:i'),
                        'angsuran_ke' => $item->angsuran_ke,
                        'jenis_pembayaran' => $item->angsuran_ke == $lamaAngsuran ? 'Pelunasan' : 'Angsuran',
                        'jumlah_bayar' => $item->total_bayar,
                        'denda' => $item->denda ?? 0,
                        'user' => $item->user->name ?? 'System'
                    ];
                });

            return view('admin.Pinjaman.lunas.DetailPinjamanLunas', [
                'pinjaman' => $pinjamanData,
                'simulasi' => $simulasi,
                'transaksi' => $transaksi,
                'notifications' => collect()
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning('Pinjaman lunas tidak ditemukan', ['id' => $id]);
            return redirect()->route('pinjaman.lunas')
                ->with('error', 'Data pinjaman lunas tidak ditemukan.');

        } catch (\Exception $e) {
            Log::error('Error di PinjamanLunas show', [
                'id' => $id,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return redirect()->route('pinjaman.lunas')
                ->with('error', 'Terjadi kesalahan saat memuat detail pinjaman lunas.');
        }
    }

    /**
     * Print laporan pinjaman lunas
     */
    public function cetakLaporan(Request $request)
    {
        try {
            $kode = $request->get('kode', '');
            $nama = $request->get('nama', '');
            $tanggal = $request->get('tanggal', '');

            $query = PinjamanLunas::with(['pinjaman.anggota']);

            if ($kode) {
                $query->filterKode($kode);
            }

            if ($nama) {
                $query->whereHas('pinjaman.anggota', function ($q) use ($nama) {
                    $q->where('nama', 'like', '%' . $nama . '%');
                });
            }

            if ($tanggal) {
                $dates = explode(' - ', $tanggal);
                if (count($dates) === 2) {
                    try {
                        $startDate = Carbon::createFromFormat('d/m/Y', trim($dates[0]))->startOfDay();
                        $endDate = Carbon::createFromFormat('d/m/Y', trim($dates[1]))->endOfDay();
                        $query->filterTanggal($startDate, $endDate);
                    } catch (\Exception $e) {
                        Log::warning('Invalid date format in cetakLaporan', [
                            'tanggal' => $tanggal,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }

            $pinjamanLunas = $query->orderBy('tanggal_lunas', 'desc')
                ->get()
                ->map(function ($item) {
                    return (object) [
                        'kode' => $item->kode_lunas,
                        'tanggal_pinjam' => $item->pinjaman->tanggal_pinjam->format('d-m-Y'),
                        'anggota_nama' => $item->pinjaman->anggota->nama ?? 'Unknown',
                        'total_tagihan' => $item->total_dibayar,
                        'status_lunas' => 'Lunas',
                    ];
                });

            return view('admin.Pinjaman.lunas.cetak_laporan_lunas', compact('pinjamanLunas', 'kode', 'nama', 'tanggal'));

        } catch (\Exception $e) {
            Log::error('Error di cetakLaporan', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return back()->with('error', 'Gagal mencetak laporan. Silakan coba lagi.');
        }
    }

    /**
     * Batalkan Pelunasan (Admin Only)
     */
    public function batalkanLunas(Request $request, $id)
    {
        // Validasi role admin
        if (Auth::user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat membatalkan pelunasan.'
            ], 403);
        }

        // Validasi input
        try {
            $request->validate([
                'alasan' => 'required|string|min:10|max:500'
            ], [
                'alasan.required' => 'Alasan pembatalan wajib diisi',
                'alasan.min' => 'Alasan minimal 10 karakter',
                'alasan.max' => 'Alasan maksimal 500 karakter'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $pinjamanLunas = PinjamanLunas::with('pinjaman')->findOrFail($id);
            $pinjaman = $pinjamanLunas->pinjaman;

            // Validasi: pinjaman masih ada
            if (!$pinjaman) {
                throw new \Exception('Data pinjaman tidak ditemukan');
            }

            Log::info('Memulai pembatalan pelunasan', [
                'pinjaman_lunas_id' => $pinjamanLunas->id,
                'kode_lunas' => $pinjamanLunas->kode_lunas,
                'user_id' => Auth::id()
            ]);

            // Proses batalkan
            $pinjamanLunas->batalkanDenganAlasan($request->alasan, Auth::id());

            // Update status pinjaman
            $pinjaman->update([
                'status_lunas' => 'Belum'
            ]);

            Log::info('Pembatalan pelunasan berhasil', [
                'pinjaman_id' => $pinjaman->id,
                'kode_pinjaman' => $pinjaman->kode_pinjaman
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Validasi pelunasan berhasil dibatalkan. Pinjaman kembali ke status "Belum Lunas".',
                'redirect' => route('pinjaman.bayar.detail', $pinjaman->id)
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Pinjaman lunas tidak ditemukan untuk dibatalkan', ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Data pinjaman lunas tidak ditemukan.'
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error batalkan pelunasan', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pelunasan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Riwayat Pembatalan Pelunasan
     */
    public function riwayatBatal()
    {
        try {
            // Hanya admin yang bisa akses
            if (Auth::user()->role_id !== 1) {
                abort(403, 'Akses ditolak. Hanya admin yang dapat mengakses halaman ini.');
            }

            $riwayatBatal = PinjamanLunas::onlyTrashed()
                ->with(['pinjaman.anggota', 'user', 'deletedBy'])
                ->orderBy('deleted_at', 'desc')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'kode_lunas' => $item->kode_lunas,
                        'kode_pinjaman' => $item->pinjaman->kode_pinjaman ?? '-',
                        'anggota' => $item->pinjaman->anggota->nama ?? '-',
                        'total_dibayar' => $item->total_dibayar,

                        'tanggal_lunas_obj' => Carbon::parse($item->tanggal_lunas),
                        'tanggal_batal_obj' => Carbon::parse($item->deleted_at),

                        'tanggal_lunas' => Carbon::parse($item->tanggal_lunas)->translatedFormat('d F Y'),
                        'tanggal_batal' => Carbon::parse($item->deleted_at)->translatedFormat('d F Y H:i'),

                        'dibatalkan_oleh' => $item->deletedBy->name ?? 'System',
                        'alasan_batal' => $item->alasan_batal ?? '-',
                        'divalidasi_oleh' => $item->user->name ?? 'System',
                    ];
                });

            return view('admin.Pinjaman.lunas.RiwayatBatal', compact('riwayatBatal'));

        } catch (\Exception $e) {
            Log::error('Error di riwayatBatal', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return redirect()->route('pinjaman.lunas')
                ->with('error', 'Terjadi kesalahan saat memuat riwayat pembatalan.');
        }
    }

    /**
     * Restore Pelunasan
     */
    public function restorePelunasan($id)
    {
        // Validasi admin
        if (Auth::user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Akses ditolak. Hanya admin yang dapat memulihkan pelunasan.'
            ], 403);
        }

        DB::beginTransaction();
        try {
            $pinjamanLunas = PinjamanLunas::onlyTrashed()->findOrFail($id);

            // Restore pelunasan
            $pinjamanLunas->deleted_by = null;
            $pinjamanLunas->alasan_batal = null;
            $pinjamanLunas->save();
            $pinjamanLunas->restore();

            // Update status pinjaman kembali ke Lunas
            if ($pinjamanLunas->pinjaman) {
                $pinjamanLunas->pinjaman->update([
                    'status_lunas' => 'Lunas'
                ]);
            }

            Log::info('Pelunasan berhasil dipulihkan', [
                'id' => $id,
                'kode_lunas' => $pinjamanLunas->kode_lunas,
                'user_id' => Auth::id()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pelunasan berhasil dipulihkan. Status pinjaman kembali ke "Lunas".'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::warning('Data pelunasan tidak ditemukan untuk restore', ['id' => $id]);

            return response()->json([
                'success' => false,
                'message' => 'Data pelunasan tidak ditemukan.'
            ], 404);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error restore pelunasan', [
                'id' => $id,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memulihkan pelunasan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        try {
            // TODO: Implement Excel export using Laravel Excel
            return response()->download(public_path('dummy.xlsx'));
        } catch (\Exception $e) {
            Log::error('Error export Excel', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal export Excel.');
        }
    }

    /**
     * Export to PDF
     */
    public function exportPDF()
    {
        try {
            // TODO: Implement PDF export using DomPDF
            return response()->download(public_path('dummy.pdf'));
        } catch (\Exception $e) {
            Log::error('Error export PDF', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal export PDF.');
        }
    }
}