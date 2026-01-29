<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Pengajuan Pinjaman</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header-left {
            display: table-cell;
            width: 80px;
            vertical-align: top;
        }

        .header-left img {
            width: 70px;
            height: auto;
        }

        .header-right {
            display: table-cell;
            vertical-align: top;
            padding-left: 15px;
        }

        .header-right h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header-right p {
            font-size: 9pt;
            margin: 1px 0;
            line-height: 1.3;
        }

        /* Title */
        .title {
            text-align: center;
            margin: 15px 0 10px 0;
        }

        .title h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .title p {
            font-size: 10pt;
            margin: 0;
        }

        /* Filter Info */
        .filter-info {
            margin-bottom: 15px;
            padding: 8px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }

        .filter-info p {
            margin: 3px 0;
            font-size: 9pt;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead tr {
            background-color: #e9ecef;
        }

        table th {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 8pt;
            vertical-align: middle;
        }

        table td {
            border: 1px solid #000;
            padding: 5px 4px;
            font-size: 8pt;
            vertical-align: top;
        }

        table td.center {
            text-align: center;
        }

        table td.right {
            text-align: right;
        }

        table tfoot tr {
            background-color: #e9ecef;
            font-weight: bold;
        }

        table tfoot td {
            padding: 8px 4px;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 7pt;
            border-radius: 3px;
            font-weight: bold;
        }

        .badge-info { background-color: #d1ecf1; color: #0c5460; }
        .badge-warning { background-color: #fff3cd; color: #856404; }
        .badge-success { background-color: #d4edda; color: #155724; }
        .badge-danger { background-color: #f8d7da; color: #721c24; }
        .badge-secondary { background-color: #e2e3e5; color: #383d41; }
        .badge-primary { background-color: #cce5ff; color: #004085; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            @php
                $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();
            @endphp
            @if($identitas && $identitas->logo)
                <img src="{{ public_path($identitas->logo) }}" alt="Logo">
            @endif
        </div>
        <div class="header-right">
            <h2>{{ strtoupper($identitas->nama_lembaga ?? 'KOPERASI') }}</h2>
            <p>{{ strtoupper($identitas->badan_hukum ?? 'BINA TIRTA LESTARI') }}</p>
            <p>BADAN HUKUM : {{ $identitas->no_badan_hukum ?? 'NO.10455BH/KWK/10.20' }}</p>
            <p>{{ strtoupper($identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA') }}</p>
        </div>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>Laporan Data Pengajuan Pinjaman</h1>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>
    </div>

    <!-- Filter Info -->
    <div class="filter-info">
        <strong>Filter yang Diterapkan:</strong>
        @php
            $hasFilter = false;
            $filterTexts = [];
            
            if ($jenis) {
                $filterTexts[] = "Jenis Pinjaman: <strong>{$jenis}</strong>";
                $hasFilter = true;
            }
            
            if ($status !== '') {
                $statusLabels = [
                    '0' => 'Menunggu Konfirmasi',
                    '1' => 'Disetujui',
                    '2' => 'Ditolak',
                    '3' => 'Sudah Terlaksana',
                    '4' => 'Batal'
                ];
                $statusLabel = $statusLabels[$status] ?? $status;
                $filterTexts[] = "Status: <strong>{$statusLabel}</strong>";
                $hasFilter = true;
            }
            
            if ($bulan) {
                try {
                    $bulanDate = \Carbon\Carbon::createFromFormat('Y-m', $bulan);
                    $filterTexts[] = "Periode Bulan: <strong>" . $bulanDate->format('F Y') . " (21 bulan lalu - 20 bulan ini)</strong>";
                    $hasFilter = true;
                } catch (\Exception $e) {}
            }
            
            if ($tanggal) {
                $filterTexts[] = "Periode Tanggal: <strong>{$tanggal}</strong>";
                $hasFilter = true;
            }
            
            if (!$hasFilter) {
                $filterTexts[] = "<strong>Semua Data (Tanpa Filter)</strong>";
            }
        @endphp
        
        @foreach($filterTexts as $text)
            <p>{!! $text !!}</p>
        @endforeach
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th width="25">No.</th>
                <th width="55">ID Ajuan</th>
                <th width="70">Tgl Pengajuan</th>
                <th width="90">Anggota</th>
                <th width="60">Jenis</th>
                <th width="80">Jumlah Pinjaman</th>
                <th width="35">Bln</th>
                <th width="80">Keterangan</th>
                <th width="70">Status</th>
                <th width="70">Tgl Cair</th>
                <th width="70">Alasan/Catatan</th>
                <th width="10%">User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengajuan as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ $item->id_ajuan }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}</td>
                <td>
                    <strong>{{ $item->anggota->nama ?? '-' }}</strong><br>
                    <small>ID: {{ $item->anggota->id_anggota ?? '-' }}</small>
                </td>
                <td class="center">
                    @if($item->jenis_pinjaman == 'Biasa')
                        <span class="badge badge-info">Biasa</span>
                    @elseif($item->jenis_pinjaman == 'Darurat')
                        <span class="badge badge-warning">Darurat</span>
                    @else
                        <span class="badge badge-success">Barang</span>
                    @endif
                </td>
                <td class="right">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                <td class="center">{{ $item->jumlah_angsuran }}</td>
                <td>{{ $item->keterangan ?? '-' }}</td>
                <td class="center">
                    @if($item->status == 0)
                        <span class="badge badge-primary">Pending</span>
                    @elseif($item->status == 1)
                        <span class="badge badge-success">Disetujui</span>
                    @elseif($item->status == 2)
                        <span class="badge badge-danger">Ditolak</span>
                    @elseif($item->status == 3)
                        <span class="badge badge-info">Terlaksana</span>
                    @else
                        <span class="badge badge-secondary">Batal</span>
                    @endif
                </td>
                <td class="center">
                    @if($item->tanggal_cair)
                        {{ \Carbon\Carbon::parse($item->tanggal_cair)->format('d M Y') }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ $item->alasan ?? '-' }}</td>
                <td class="center">{{ $item->user->name ?? 'admin' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="right">Jumlah Total Pengajuan:</td>
                <td class="right">{{ number_format($pengajuan->sum('jumlah'), 0, ',', '.') }}</td>
                <td colspan="6" class="center">Total Data: {{ $pengajuan->count() }} pengajuan</td>
            </tr>
        </tfoot>
    </table>

    <!-- Summary by Status -->
    @php
        $summary = [
            'pending' => $pengajuan->where('status', 0)->count(),
            'disetujui' => $pengajuan->where('status', 1)->count(),
            'ditolak' => $pengajuan->where('status', 2)->count(),
            'terlaksana' => $pengajuan->where('status', 3)->count(),
            'batal' => $pengajuan->where('status', 4)->count(),
        ];
    @endphp

    <div style="margin-top: 20px; padding: 10px; border: 1px solid #dee2e6; border-radius: 4px; background-color: #f8f9fa;">
        <strong>Ringkasan Status:</strong>
        <p style="margin: 5px 0; font-size: 9pt;">
            Menunggu Konfirmasi: <strong>{{ $summary['pending'] }}</strong> | 
            Disetujui: <strong>{{ $summary['disetujui'] }}</strong> | 
            Ditolak: <strong>{{ $summary['ditolak'] }}</strong> | 
            Terlaksana: <strong>{{ $summary['terlaksana'] }}</strong> | 
            Batal: <strong>{{ $summary['batal'] }}</strong>
        </p>
        <p style="margin: 5px 0; font-size: 9pt;">
            <strong>Total Nilai Disetujui:</strong> Rp {{ number_format($pengajuan->where('status', 1)->sum('jumlah'), 0, ',', '.') }} |
            <strong>Total Nilai Terlaksana:</strong> Rp {{ number_format($pengajuan->where('status', 3)->sum('jumlah'), 0, ',', '.') }}
        </p>
    </div>
</body>
</html>