<?php

namespace App\Http\Controllers\Admin\Laporan;

use App\Http\Controllers\Controller;
use App\Models\Admin\DataMaster\DataAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnggotaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            // Query builder
            $query = DataAnggota::query();

            // Filter berdasarkan status aktif
            if ($request->filled('status')) {
                $query->where('aktif', $request->status);
            }

            // Filter berdasarkan jenis kelamin
            if ($request->filled('gender')) {
                $query->where('jenis_kelamin', $request->gender);
            }

            // Filter berdasarkan jabatan
            if ($request->filled('jabatan')) {
                $query->where('jabatan', $request->jabatan);
            }

            // Filter berdasarkan departemen
            if ($request->filled('departemen')) {
                $query->where('departement', $request->departemen);
            }

            // Search berdasarkan nama atau ID anggota
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('nama', 'LIKE', "%{$search}%")
                      ->orWhere('id_anggota', 'LIKE', "%{$search}%")
                      ->orWhere('username', 'LIKE', "%{$search}%");
                });
            }

            // Ambil data dengan sorting
            $anggota = $query->orderBy('tanggal_registrasi', 'desc')
                            ->orderBy('nama', 'asc')
                            ->get();

            // Hitung statistik
            $totalAnggota = $anggota->count();
            $anggotaAktif = $anggota->where('aktif', 'Aktif')->count();
            $anggotaNonAktif = $anggota->where('aktif', 'Non Aktif')->count();
            $anggotaLakiLaki = $anggota->where('jenis_kelamin', 'Laki-laki')->count();
            $anggotaPerempuan = $anggota->where('jenis_kelamin', 'Perempuan')->count();

            // Data untuk notifikasi (jika ada)
            $notifications = collect();

            return view('admin.Laporan.anggota.Anggota', compact(
                'anggota',
                'notifications',
                'totalAnggota',
                'anggotaAktif',
                'anggotaNonAktif',
                'anggotaLakiLaki',
                'anggotaPerempuan'
            ));

        } catch (\Exception $e) {
            Log::error('Error in LaporanAnggotaController@index: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat memuat data laporan anggota.');
        }
    }

    /**
     * Print laporan anggota with filters
     */
    public function cetakLaporan(Request $request)
    {
        try {
            $status = $request->get('status', '');
            $gender = $request->get('gender', '');
            $jabatan = $request->get('jabatan', '');
            $departemen = $request->get('departemen', '');

            // Query builder
            $query = DataAnggota::query();

            // Apply filters
            if ($status) {
                $query->where('aktif', $status);
            }

            if ($gender) {
                $query->where('jenis_kelamin', $gender);
            }

            if ($jabatan) {
                $query->where('jabatan', $jabatan);
            }

            if ($departemen) {
                $query->where('departement', $departemen);
            }

            // Get filtered data
            $anggota = $query->orderBy('tanggal_registrasi', 'desc')
                            ->orderBy('nama', 'asc')
                            ->get();

            // Statistik
            $totalAnggota = $anggota->count();
            $anggotaAktif = $anggota->where('aktif', 'Aktif')->count();
            $anggotaNonAktif = $anggota->where('aktif', 'Non Aktif')->count();

            // Info filter yang digunakan
            $filterInfo = [
                'status' => $status ?: 'Semua',
                'gender' => $gender ? ($gender == 'Laki-laki' ? 'Laki-laki' : 'Perempuan') : 'Semua',
                'jabatan' => $jabatan ?: 'Semua',
                'departemen' => $departemen ?: 'Semua',
            ];

            return view('admin.Laporan.anggota.CetakAnggota', compact(
                'anggota',
                'totalAnggota',
                'anggotaAktif',
                'anggotaNonAktif',
                'filterInfo'
            ));

        } catch (\Exception $e) {
            Log::error('Error in LaporanAnggotaController@cetakLaporan: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mencetak laporan.');
        }
    }

    /**
     * Export to Excel
     */
    public function exportExcel(Request $request)
    {
        try {
            // TODO: Implement Excel export using Laravel Excel
            // Install package: composer require maatwebsite/excel
            
            $status = $request->get('status', '');
            $gender = $request->get('gender', '');
            $jabatan = $request->get('jabatan', '');
            $departemen = $request->get('departemen', '');

            // Query builder
            $query = DataAnggota::query();

            // Apply filters
            if ($status) $query->where('aktif', $status);
            if ($gender) $query->where('jenis_kelamin', $gender);
            if ($jabatan) $query->where('jabatan', $jabatan);
            if ($departemen) $query->where('departement', $departemen);

            $anggota = $query->orderBy('nama', 'asc')->get();

            // Untuk sementara return download CSV manual
            $filename = 'laporan-anggota-' . date('Y-m-d-His') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function() use ($anggota) {
                $file = fopen('php://output', 'w');
                
                // BOM untuk UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
                
                // Header
                fputcsv($file, [
                    'No',
                    'ID Anggota',
                    'Username',
                    'Nama Lengkap',
                    'Jenis Kelamin',
                    'Tempat Lahir',
                    'Tanggal Lahir',
                    'Status Perkawinan',
                    'Departemen',
                    'Pekerjaan',
                    'Agama',
                    'Alamat',
                    'Kota',
                    'No Telepon',
                    'Jabatan',
                    'Tanggal Registrasi',
                    'Status Aktif'
                ]);

                // Data
                foreach ($anggota as $index => $item) {
                    fputcsv($file, [
                        $index + 1,
                        $item->id_anggota,
                        $item->username,
                        $item->nama,
                        $item->jenis_kelamin,
                        $item->tempat_lahir,
                        $item->tanggal_registrasi->translatedFormat('d F Y'),
                        $item->status ?: '-',
                        $item->departement ?: '-',
                        $item->pekerjaan ?: '-',
                        $item->agama ?: '-',
                        $item->alamat,
                        $item->kota,
                        $item->no_telp ?: '-',
                        $item->jabatan,
                        $item->tanggal_registrasi->translatedFormat('d F Y'),
                        $item->aktif
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Error in LaporanAnggotaController@exportExcel: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export Excel.');
        }
    }

    /**
     * Export to PDF
     */
    public function exportPDF(Request $request)
    {
        try {
            // TODO: Implement PDF export using DomPDF or similar
            // Install package: composer require barryvdh/laravel-dompdf
            
            $status = $request->get('status', '');
            $gender = $request->get('gender', '');
            $jabatan = $request->get('jabatan', '');
            $departemen = $request->get('departemen', '');

            // Query builder
            $query = DataAnggota::query();

            // Apply filters
            if ($status) $query->where('aktif', $status);
            if ($gender) $query->where('jenis_kelamin', $gender);
            if ($jabatan) $query->where('jabatan', $jabatan);
            if ($departemen) $query->where('departement', $departemen);

            $anggota = $query->orderBy('nama', 'asc')->get();

            // Info filter
            $filterInfo = [
                'status' => $status ?: 'Semua',
                'gender' => $gender ? ($gender == 'Laki-laki' ? 'Laki-laki' : 'Perempuan') : 'Semua',
                'jabatan' => $jabatan ?: 'Semua',
                'departemen' => $departemen ?: 'Semua',
            ];

            // Statistik
            $totalAnggota = $anggota->count();
            $anggotaAktif = $anggota->where('aktif', 'Aktif')->count();

            // Untuk sementara redirect ke cetak dengan auto print
            return view('admin.Laporan.anggota.ExportPDF', compact(
                'anggota',
                'filterInfo',
                'totalAnggota',
                'anggotaAktif'
            ));

            // Jika sudah install DomPDF:
            // $pdf = PDF::loadView('admin.Laporan.anggota.ExportPDF', compact('anggota', 'filterInfo', 'totalAnggota', 'anggotaAktif'));
            // return $pdf->download('laporan-anggota-' . date('Y-m-d') . '.pdf');

        } catch (\Exception $e) {
            Log::error('Error in LaporanAnggotaController@exportPDF: ' . $e->getMessage());
            
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF.');
        }
    }
}