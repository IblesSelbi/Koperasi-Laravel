<?php

namespace App\Http\Controllers\Admin\TransaksiKas;

use App\Http\Controllers\Controller;
use App\Models\Admin\TransaksiKas\Pemasukan;
use App\Models\Admin\DataMaster\JenisAkun;
use App\Models\Admin\DataMaster\DataKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class PemasukanController extends Controller
{
    public function index()
    {
        $pemasukan = Pemasukan::with(['untukKas', 'dariAkun', 'user'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->get();

        $total_pemasukan = $pemasukan->sum('jumlah');

        // Filter: Kas aktif dan pemasukan_kas = Y
        $kas_list = DataKas::where('aktif', 'Y')
            ->where('pemasukan_kas', 'Y')
            ->get();

        // Filter: Akun aktif dan pemasukan = Y
        $akun_list = JenisAkun::where('aktif', 'Y')
            ->where('pemasukan', 'Y')
            ->get();

        return view('admin.TransaksiKas.pemasukan.Pemasukan', compact(
            'pemasukan',
            'total_pemasukan',
            'kas_list',
            'akun_list'
        ));
    }

    // Tambahkan method show untuk edit
    public function show($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        return response()->json($pemasukan);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'uraian' => 'required|string|max:500',
            'untuk_kas_id' => 'required|exists:data_kas,id',
            'dari_akun_id' => 'required|exists:jenis_akun,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        // Generate kode transaksi otomatis
        $validated['kode_transaksi'] = Pemasukan::generateKodeTransaksi();
        $validated['user_id'] = Auth::id();

        Pemasukan::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pemasukan berhasil ditambahkan!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $pemasukan = Pemasukan::findOrFail($id);

        $validated = $request->validate([
            'tanggal_transaksi' => 'required|date',
            'uraian' => 'required|string|max:500',
            'untuk_kas_id' => 'required|exists:data_kas,id',
            'dari_akun_id' => 'required|exists:jenis_akun,id',
            'jumlah' => 'required|numeric|min:0',
        ]);

        $pemasukan->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data pemasukan berhasil diupdate!'
        ]);
    }

    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pemasukan berhasil dihapus!'
        ]);
    }

    /**
     * Cetak Laporan Pemasukan Kas (PDF)
     */
    public function cetakLaporan(Request $request)
    {
        $query = Pemasukan::with(['untukKas', 'dariAkun', 'user']);

        // Filter berdasarkan tanggal jika ada
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('tanggal_transaksi', [$request->start_date, $request->end_date]);
        }

        $pemasukan = $query->orderBy('tanggal_transaksi', 'asc')->get();
        $total_pemasukan = $pemasukan->sum('jumlah');
        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();

        // Periode untuk header
        $periode = 'Periode ' . ($request->start_date ? \Carbon\Carbon::parse($request->start_date)->format('d F Y') : '01 Januari ' . date('Y'));
        $periode .= ' - ' . ($request->end_date ? \Carbon\Carbon::parse($request->end_date)->format('d F Y') : '31 Desember ' . date('Y'));

        $pdf = Pdf::loadView('admin.TransaksiKas.pemasukan.cetak', compact('pemasukan', 'total_pemasukan', 'identitas', 'periode'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Laporan_Pemasukan_Kas.pdf');
    }
}