<?php

namespace App\Http\Controllers\Admin\TransaksiKas;

use App\Http\Controllers\Controller;
use App\Models\Admin\TransaksiKas\Pengeluaran;
use App\Models\Admin\DataMaster\JenisAkun;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PengeluaranController extends Controller
{
    public function index()
    {
        $pengeluaran = Pengeluaran::with(['dariKas', 'untukAkun', 'user'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $total_pengeluaran = $pengeluaran->sum('jumlah');

        // Filter: Kas aktif dan pengeluaran_kas = Y
        $kas_list = DataKas::where('aktif', 'Y')
            ->where('pengeluaran_kas', 'Y')
            ->get();

        // Filter: Akun aktif dan pengeluaran = Y
        $akun_list = JenisAkun::where('aktif', 'Y')
            ->where('pengeluaran', 'Y')
            ->get();

        return view('admin.TransaksiKas.pengeluaran.Pengeluaran', compact(
            'pengeluaran',
            'total_pengeluaran',
            'kas_list',
            'akun_list'
        ));
    }

    // Method show untuk edit
    public function show($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        return response()->json($pengeluaran);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'uraian' => 'required|string|max:500',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'untuk_akun_id' => 'required|exists:jenis_akun,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        // Generate kode transaksi otomatis
        $validated['kode_transaksi'] = Pengeluaran::generateKodeTransaksi();
        $validated['user_id'] = Auth::id();

        Pengeluaran::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pengeluaran berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'uraian' => 'required|string|max:500',
            'dari_kas_id' => 'required|exists:data_kas,id',
            'untuk_akun_id' => 'required|exists:jenis_akun,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        $pengeluaran->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pengeluaran berhasil diupdate!'
        ]);
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pengeluaran berhasil dihapus!'
        ]);
    }

    /**
     * Cetak Laporan Pengeluaran Kas (PDF)
     */
    public function cetakLaporan(Request $request)
    {
        $query = Pengeluaran::with(['dariKas', 'untukAkun', 'user']);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_transaksi', [$request->start_date, $request->end_date]);
        }

        $pengeluaran = $query->orderBy('tanggal_transaksi', 'asc')->get();
        $total_pengeluaran = $pengeluaran->sum('jumlah');
        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        $periode = 'Periode ' . ($request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d F Y') : '01 Januari ' . date('Y'));
        $periode .= ' - ' . ($request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d F Y') : '31 Desember ' . date('Y'));

        $pdf = Pdf::loadView('admin.TransaksiKas.pengeluaran.cetak', compact('pengeluaran', 'total_pengeluaran', 'identitas', 'periode'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Pengeluaran_Kas.pdf');
    }
}