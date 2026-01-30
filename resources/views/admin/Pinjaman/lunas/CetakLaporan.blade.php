<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pinjaman Lunas</title>
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
            background-color: #e7f1ff;
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
            background-color: #e7f1ff;
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

        .badge-success {
            background-color: #d4edda;
            color: #39814a;
        }

        .badge-primary {
            background-color: #cce5ff;
            color: #004085;
        }

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .badge-secondary {
            background-color: #e2e3e5;
            color: #383d41;
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

        .summary-highlight {
            background-color: #d4edda;
            padding: 5px;
            border-left: 3px solid #28a745;
            margin-top: 5px;
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
        <h1>Laporan Pinjaman Lunas</h1>
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</p>
    </div>

    <!-- Filter Info -->
    <div class="filter-info">
        <strong>Filter yang Diterapkan:</strong>
        @php
            $hasFilter = false;
            $filterTexts = [];

            if (!empty($kode)) {
                $filterTexts[] = "Kode Lunas: <strong>{$kode}</strong>";
                $hasFilter = true;
            }

            if (!empty($nama)) {
                $filterTexts[] = "Nama Anggota: <strong>{$nama}</strong>";
                $hasFilter = true;
            }

            if (!empty($tanggal)) {
                $filterTexts[] = "Periode Tanggal Lunas: <strong>{$tanggal}</strong>";
                $hasFilter = true;
            }

            if (!$hasFilter) {
                $filterTexts[] = "<strong>Semua Data Pinjaman Lunas</strong>";
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
                <th width="8%">Kode Lunas</th>
                <th width="13%">Anggota</th>
                <th width="6%">Dept</th>
                <th width="8%">Tgl Pinjam</th>
                <th width="8%">Tgl Tempo</th>
                <th width="8%">Tgl Lunas</th>
                <th width="4%">Lama</th>
                <th width="10%">Total Tagihan</th>
                <th width="10%">Total Denda</th>
                <th width="10%">Total Dibayar</th>
                <th width="6%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pinjamanLunas as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">
                        <span style="font-weight: bold; color: #0d6efd;">{{ $item->kode }}</span>
                    </td>
                    <td>
                        <strong>{{ $item->anggota_nama }}</strong><br>
                        <small>ID: {{ $item->anggota_id }}</small>
                    </td>
                    <td class="center">
                        <span class="badge badge-secondary">{{ $item->anggota_departemen }}</span>
                    </td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_tempo)->format('d/m/Y') }}</td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_lunas)->format('d/m/Y') }}</td>
                    <td class="center">
                        <span class="badge badge-info">{{ $item->lama_pinjaman }} Bln</span>
                    </td>
                    <td class="right">{{ number_format($item->total_tagihan, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->total_denda, 0, ',', '.') }}</td>
                    <td class="right">
                        <strong>{{ number_format($item->sudah_dibayar, 0, ',', '.') }}</strong>
                    </td>
                    <td class="center">
                        <span class="badge badge-success">LUNAS</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="center">Tidak ada data pinjaman lunas</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8" class="right">Jumlah Total:</td>
                <td class="right">{{ number_format($pinjamanLunas->sum('total_tagihan'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($pinjamanLunas->sum('total_denda'), 0, ',', '.') }}</td>
                <td class="right">{{ number_format($pinjamanLunas->sum('sudah_dibayar'), 0, ',', '.') }}</td>
                <td class="center">{{ $pinjamanLunas->count() }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Summary -->
    @php
        $totalPinjaman = $pinjamanLunas->count();
        $totalTagihan = $pinjamanLunas->sum('total_tagihan');
        $totalDenda = $pinjamanLunas->sum('total_denda');
        $totalDibayar = $pinjamanLunas->sum('sudah_dibayar');
        
        // Hitung rata-rata lama cicilan
        $rataLamaCicilan = $totalPinjaman > 0 ? round($pinjamanLunas->sum('lama_pinjaman') / $totalPinjaman, 1) : 0;
        
        // Hitung yang kena denda
        $yangKenaDenda = $pinjamanLunas->where('total_denda', '>', 0)->count();
        $persenKenaDenda = $totalPinjaman > 0 ? round(($yangKenaDenda / $totalPinjaman) * 100, 1) : 0;
    @endphp

    <div class="summary">
        <strong>Ringkasan Laporan Pinjaman Lunas:</strong>
        
        <div class="summary-highlight">
            <p>
                <strong>Total Pinjaman yang Sudah Lunas:</strong> {{ $totalPinjaman }} pinjaman
            </p>
            <p>
                <strong>Total Nilai Tagihan:</strong> Rp {{ number_format($totalTagihan, 0, ',', '.') }}
            </p>
            <p>
                <strong>Total Denda Terkumpul:</strong> Rp {{ number_format($totalDenda, 0, ',', '.') }}
            </p>
            <p>
                <strong>Total Dana Terkumpul:</strong> Rp {{ number_format($totalDibayar, 0, ',', '.') }}
            </p>
        </div>
        
        <p style="margin-top: 8px;">
            <strong>Statistik Tambahan:</strong>
        </p>
        <p>
            • Rata-rata Lama Cicilan: <strong>{{ $rataLamaCicilan }} bulan</strong>
        </p>
        <p>
            • Pinjaman dengan Denda: <strong>{{ $yangKenaDenda }}</strong> dari {{ $totalPinjaman }} ({{ $persenKenaDenda }}%)
        </p>
        <p>
            • Rata-rata Nilai Pinjaman: <strong>Rp {{ number_format($totalPinjaman > 0 ? $totalTagihan / $totalPinjaman : 0, 0, ',', '.') }}</strong>
        </p>
        
        @if($totalDenda > 0)
            <p style="margin-top: 5px; color: #856404;">
                ⚠ <em>Catatan: Ada {{ $yangKenaDenda }} pinjaman yang dikenakan denda dengan total Rp {{ number_format($totalDenda, 0, ',', '.') }}</em>
            </p>
        @endif
    </div>

    <!-- Footer with signature area (optional) -->
    <div style="margin-top: 20px; page-break-inside: avoid;">
        <table style="border: none; width: 100%;">
            <tr style="border: none;">
                <td style="border: none; width: 60%;"></td>
                <td style="border: none; width: 40%; text-align: center;">
                    <p style="margin-bottom: 50px;">
                        {{ $identitas->kota ?? 'Bandung' }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </p>
                    <p style="border-bottom: 1px solid #000; display: inline-block; padding: 0 50px;">
                        &nbsp;
                    </p>
                    <p><small>Pimpinan/Penanggung Jawab</small></p>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>