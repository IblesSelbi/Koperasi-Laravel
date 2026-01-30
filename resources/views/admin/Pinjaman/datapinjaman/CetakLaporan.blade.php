<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Pinjaman Anggota</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 12mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            color: #000;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .header-left {
            display: table-cell;
            width: 70px;
            vertical-align: top;
        }

        .header-left img {
            width: 60px;
            height: auto;
        }

        .header-right {
            display: table-cell;
            vertical-align: top;
            padding-left: 12px;
        }

        .header-right h2 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header-right p {
            font-size: 8pt;
            margin: 1px 0;
            line-height: 1.2;
        }

        /* Title */
        .title {
            text-align: center;
            margin: 10px 0 8px 0;
        }

        .title h1 {
            font-size: 13pt;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .title p {
            font-size: 9pt;
            margin: 0;
        }

        /* Filter Info */
        .filter-info {
            margin-bottom: 10px;
            padding: 6px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 3px;
        }

        .filter-info p {
            margin: 2px 0;
            font-size: 8pt;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table thead tr {
            background-color: #e9ecef;
        }

        table th {
            border: 1px solid #000;
            padding: 5px 3px;
            text-align: center;
            font-weight: bold;
            font-size: 7.5pt;
            vertical-align: middle;
        }

        table td {
            border: 1px solid #000;
            padding: 4px 3px;
            font-size: 7.5pt;
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
            padding: 6px 3px;
            font-size: 8pt;
        }

        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 6.5pt;
            border-radius: 2px;
            font-weight: bold;
        }

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-secondary {
            background-color: #e2e3e5;
            color: #383d41;
        }

        .badge-primary {
            background-color: #cce5ff;
            color: #004085;
        }

        /* Summary */
        .summary {
            margin-top: 12px;
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 3px;
            background-color: #f8f9fa;
            page-break-inside: avoid;
        }

        .summary p {
            margin: 3px 0;
            font-size: 8pt;
        }

        .summary strong {
            font-size: 8.5pt;
        }
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
            <p>{{ strtoupper($identitas->alamat ?? 'AKENO MULTIMEDIA SOLUTION, VILA BANDUNG INDAH 40393 JAWA WEST JAVA') }}</p>
        </div>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>Laporan Data Pinjaman Anggota</h1>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->format('d F Y H:i') }}</p>
    </div>

    <!-- Filter Info -->
    <div class="filter-info">
        <strong>Filter yang Diterapkan:</strong>
        @php
            $hasFilter = false;
            $filterTexts = [];

            if (!empty($status) && $status !== '') {
                $statusLabel = $status == 'Lunas' ? 'Lunas' : 'Belum Lunas';
                $filterTexts[] = "Status Lunas: <strong>{$statusLabel}</strong>";
                $hasFilter = true;
            }

            if (!empty($kode)) {
                $filterTexts[] = "Kode Pinjaman: <strong>{$kode}</strong>";
                $hasFilter = true;
            }

            if (!empty($nama)) {
                $filterTexts[] = "Nama Anggota: <strong>{$nama}</strong>";
                $hasFilter = true;
            }

            if (!empty($tanggal)) {
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
                <th width="3%">No.</th>
                <th width="7%">Kode</th>
                <th width="7%">Tgl Pinjam</th>
                <th width="12%">Anggota</th>
                <th width="6%">Jenis</th>
                <th width="9%">Pokok Pinjaman</th>
                <th width="3%">Bln</th>
                <th width="8%">Angsuran/Bln</th>
                <th width="9%">Total Angsuran</th>
                <th width="9%">Sudah Dibayar</th>
                <th width="9%">Sisa Tagihan</th>
                <th width="6%">Status</th>
                <th width="8%">User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pinjaman as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">
                        <span style="font-weight: bold; color: #0d6efd;">{{ $item->kode_pinjaman }}</span>
                    </td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d M Y') }}</td>
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
                    <td class="right">{{ number_format($item->pokok_pinjaman, 0, ',', '.') }}</td>
                    <td class="center">{{ $item->lamaAngsuran->lama_angsuran ?? '-' }}</td>
                    <td class="right">{{ number_format($item->angsuran_pokok + $item->biaya_bunga, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->jumlah_angsuran, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->total_bayar ?? 0, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->sisa_tagihan ?? 0, 0, ',', '.') }}</td>
                    <td class="center">
                        @if($item->status_lunas == 'Lunas')
                            <span class="badge badge-success">Lunas</span>
                        @else
                            <span class="badge badge-danger">Belum</span>
                        @endif
                    </td>
                    <td class="center">{{ $item->user->name ?? 'admin' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="center">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" class="right">Jumlah Total:</td>
                <td class="right">{{ number_format($pinjaman->sum('pokok_pinjaman'), 0, ',', '.') }}</td>
                <td></td>
                <td></td>
                <td class="right">{{ number_format($pinjaman->sum('jumlah_angsuran'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($pinjaman->sum('total_bayar'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($pinjaman->sum('sisa_tagihan'), 0, ',', '.') }}</td>
                <td colspan="2" class="center">Total: {{ $pinjaman->count() }} pinjaman</td>
            </tr>
        </tfoot>
    </table>

    <!-- Summary by Status -->
    @php
        $summary = [
            'lunas' => $pinjaman->where('status_lunas', 'Lunas')->count(),
            'belum_lunas' => $pinjaman->where('status_lunas', 'Belum')->count(),
        ];

        $summaryNilai = [
            'lunas' => $pinjaman->where('status_lunas', 'Lunas')->sum('pokok_pinjaman'),
            'belum_lunas' => $pinjaman->where('status_lunas', 'Belum')->sum('pokok_pinjaman'),
        ];
        
        $summaryJenis = [
            'biasa' => $pinjaman->where('jenis_pinjaman', 'Biasa')->count(),
            'darurat' => $pinjaman->where('jenis_pinjaman', 'Darurat')->count(),
            'barang' => $pinjaman->where('jenis_pinjaman', 'Barang')->count(),
        ];
    @endphp

    <div class="summary">
        <strong>Ringkasan Status:</strong>
        <p>
            Lunas: <strong>{{ $summary['lunas'] }}</strong> pinjaman (Rp {{ number_format($summaryNilai['lunas'], 0, ',', '.') }}) | 
            Belum Lunas: <strong>{{ $summary['belum_lunas'] }}</strong> pinjaman (Rp {{ number_format($summaryNilai['belum_lunas'], 0, ',', '.') }})
        </p>
        <p>
            <strong>Per Jenis:</strong>
            Biasa: {{ $summaryJenis['biasa'] }} | 
            Darurat: {{ $summaryJenis['darurat'] }} | 
            Barang: {{ $summaryJenis['barang'] }}
        </p>
        <p>
            <strong>Total Piutang (Belum Lunas):</strong> Rp {{ number_format($pinjaman->where('status_lunas', 'Belum')->sum('sisa_tagihan'), 0, ',', '.') }}
        </p>
    </div>
</body>

</html>