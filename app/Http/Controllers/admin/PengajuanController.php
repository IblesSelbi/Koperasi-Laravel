<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $pengajuan = collect([
            (object)[
                'id' => 1,
                'id_ajuan' => 'AJ001',
                'tanggal_pengajuan' => '2025-12-15',
                'anggota_id' => '001234',
                'anggota_nama' => 'Budi Santoso',
                'anggota_departemen' => 'Departemen IT',
                'jenis_pinjaman' => 'Biasa',
                'jumlah' => 10000000,
                'jumlah_angsuran' => 12,
                'keterangan' => 'Untuk renovasi rumah',
                'status' => 0, // 0=pending, 1=disetujui, 2=ditolak, 3=terlaksana, 4=batal
                'sisa_pinjaman' => 2,
                'sisa_angsuran' => 8,
                'sisa_tagihan' => 5000000,
            ],
            (object)[
                'id' => 2,
                'id_ajuan' => 'AJ002',
                'tanggal_pengajuan' => '2025-12-14',
                'tanggal_cair' => '2025-12-14',
                'anggota_id' => '001235',
                'anggota_nama' => 'Siti Aminah',
                'anggota_departemen' => 'Departemen Keuangan',
                'jenis_pinjaman' => 'Darurat',
                'jumlah' => 5000000,
                'jumlah_angsuran' => 6,
                'keterangan' => 'Keperluan medis mendesak',
                'status' => 1,
                'sisa_pinjaman' => 1,
                'sisa_angsuran' => 4,
                'sisa_tagihan' => 3000000,
            ]
        ]);

        $notifications = collect([
            (object)[
                'nama' => 'Hartati',
                'tanggal_jatuh_tempo' => '2025-06-16',
                'sisa_tagihan' => 1575000,
            ]
        ]);

        return view('admin.pengajuan.Pengajuan', compact('pengajuan', 'notifications'));
    }

    /**
     * Process action (setujui, tolak, pending, batal, terlaksana, belum, hapus)
     */
    public function aksi(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer',
            'aksi' => 'required|string|in:setujui,tolak,pending,batal,terlaksana,belum,hapus',
            'alasan' => 'nullable|string',
            'tgl_cair' => 'nullable|date',
        ]);

        // TODO: Process based on action
        // $pengajuan = Pengajuan::findOrFail($validated['id']);

        switch ($validated['aksi']) {
            case 'setujui':
                // $pengajuan->status = 1;
                // $pengajuan->tanggal_cair = $validated['tgl_cair'];
                // $pengajuan->catatan = $validated['alasan'];
                $message = 'Pengajuan berhasil disetujui';
                break;

            case 'tolak':
                // $pengajuan->status = 2;
                // $pengajuan->alasan_penolakan = $validated['alasan'];
                $message = 'Pengajuan berhasil ditolak';
                break;

            case 'pending':
                // $pengajuan->status = 0;
                // $pengajuan->catatan = $validated['alasan'];
                $message = 'Pengajuan berhasil di-pending';
                break;

            case 'batal':
                // $pengajuan->status = 4;
                $message = 'Pengajuan berhasil dibatalkan';
                break;

            case 'terlaksana':
                // $pengajuan->status = 3;
                $message = 'Pengajuan berhasil ditandai terlaksana';
                break;

            case 'belum':
                // $pengajuan->status = 1; // Kembali ke disetujui
                $message = 'Status pengajuan berhasil dikembalikan';
                break;

            case 'hapus':
                // $pengajuan->delete();
                $message = 'Pengajuan berhasil dihapus';
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Aksi tidak valid'
                ], 400);
        }

        // $pengajuan->save();

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Print single pengajuan
     */
    public function cetak($id)
    {
        // TODO: Get data and generate print view
        // $pengajuan = Pengajuan::with('anggota')->findOrFail($id);
        
        // return view('admin.pinjaman.cetak_pengajuan', compact('pengajuan'));
        
        return response('Cetak pengajuan ID: ' . $id);
    }

    /**
     * Print laporan with filters
     */
    public function cetakLaporan(Request $request)
    {
        $jenis = $request->get('jenis', '');
        $status = $request->get('status', '');
        $bulan = $request->get('bulan', '');
        $tanggal = $request->get('tanggal', '');

        // TODO: Get filtered data and generate print view
        // $query = Pengajuan::with('anggota');
        
        // Apply filters...
        
        // $pengajuan = $query->get();
        
        // return view('admin.pinjaman.cetak_laporan', compact('pengajuan', 'jenis', 'status', 'bulan', 'tanggal'));
        
        return response('Cetak laporan dengan filter');
    }

    /**
     * Export to Excel
     */
    public function exportExcel()
    {
        // TODO: Implement Excel export using Laravel Excel
        // return Excel::download(new PengajuanExport, 'pengajuan-pinjaman.xlsx');
        
        return response('Export Excel');
    }

    /**
     * Export to PDF
     */
    public function exportPDF()
    {
        // TODO: Implement PDF export using DomPDF or similar
        // $pengajuan = Pengajuan::with('anggota')->get();
        // $pdf = PDF::loadView('admin.pinjaman.export_pdf', compact('pengajuan'));
        // return $pdf->download('pengajuan-pinjaman.pdf');
        
        return response('Export PDF');
    }
}