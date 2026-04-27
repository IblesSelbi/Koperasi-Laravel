<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Data Kas</title>
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
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header table {
            width: 100%;
            border: none;
        }

        .header td {
            border: none;
            vertical-align: top;
            padding: 0;
        }

        .header img {
            width: 60px;
            height: 60px;
        }

        .header h2 {
            font-size: 14pt;
            margin: 0 0 3px 0;
            font-weight: bold;
        }

        .header p {
            font-size: 9pt;
            margin: 0;
            line-height: 1.4;
        }

        /* Title */
        .title {
            text-align: center;
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 8px;
            background-color: #f5f5f5;
        }

        .title h1 {
            font-size: 14pt;
            margin: 0 0 5px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .title .filter-info {
            font-size: 9pt;
            margin: 3px 0;
            color: #444;
        }

        .title .date {
            font-size: 8pt;
            color: #666;
            margin-top: 5px;
        }

        /* Summary Stats */
        .summary {
            border: 1px solid #999;
            padding: 8px 10px;
            margin-bottom: 10px;
            background-color: #fafafa;
        }

        .summary table {
            width: 100%;
            border: none;
        }

        .summary td {
            border: none;
            border-right: 1px solid #ccc;
            padding: 5px 10px;
            text-align: center;
            font-size: 8pt;
        }

        .summary td:last-child {
            border-right: none;
        }

        .summary .label {
            font-weight: bold;
            margin-bottom: 3px;
            color: #666;
            font-size: 8pt;
        }

        .summary .value {
            font-size: 12pt;
            font-weight: bold;
            color: #000;
        }

        /* Data Table */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.data thead {
            background-color: #d0d0d0;
            color: #000;
        }

        table.data th {
            border: 1px solid #000;
            padding: 6px 4px;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        table.data tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        table.data tbody tr:hover {
            background-color: #e8e8e8;
        }

        table.data td {
            border: 1px solid #000;
            padding: 4px 4px;
            font-size: 8pt;
            vertical-align: middle;
        }

        table.data td.center {
            text-align: center;
        }

        table.data td.right {
            text-align: right;
        }

        table.data tfoot {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        table.data tfoot td {
            padding: 6px 5px;
            font-size: 8.5pt;
            border: 1px solid #000;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .empty-state p {
            font-size: 12pt;
            color: #666;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 70px;">
                    @if($identitas && $identitas->logo)
                        @php
                            $logoPath = public_path($identitas->logo);
                            if (file_exists($logoPath)) {
                                $logoData = base64_encode(file_get_contents($logoPath));
                                $logoMime = mime_content_type($logoPath);
                                $logoSrc = "data:{$logoMime};base64,{$logoData}";
                            }
                        @endphp
                        @if(isset($logoSrc))
                            <img src="{{ $logoSrc }}" alt="Logo">
                        @endif
                    @endif
                </td>
                <td style="padding-left: 15px;">
                    <h2>{{ strtoupper($identitas->nama_lembaga ?? 'KOPERASI') }}</h2>
                    <p><strong>{{ strtoupper($identitas->badan_hukum ?? 'Akeno Multimedia Solution') }}</strong></p>
                    <p>Badan Hukum: {{ $identitas->no_badan_hukum ?? 'NO.10455BH/KWK/10.20' }}</p>
                    <p>{{ strtoupper($identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>Laporan Data Kas</h1>
        <div class="filter-info">
            <strong>Status:</strong> {{ $filterInfo['status'] }}
        </div>
        <div class="date">Dicetak: {{ $tanggalCetak }}</div>
    </div>

    <!-- Summary Stats -->
    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="label">Total Data Kas</div>
                    <div class="value">{{ $totalKas }}</div>
                </td>
                <td>
                    <div class="label">Kas Aktif (Y)</div>
                    <div class="value">{{ $kasAktif }}</div>
                </td>
                <td>
                    <div class="label">Kas Tidak Aktif (T)</div>
                    <div class="value">{{ $kasTidakAktif }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if($dataKas->count() > 0)
        <!-- Data Table -->
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 18%;">Nama Kas</th>
                    <th style="width: 8%;">Aktif</th>
                    <th style="width: 9%;">Simpanan</th>
                    <th style="width: 9%;">Penarikan</th>
                    <th style="width: 9%;">Pinjaman</th>
                    <th style="width: 9%;">Angsuran</th>
                    <th style="width: 11%;">Pemasukan Kas</th>
                    <th style="width: 11%;">Pengeluaran Kas</th>
                    <th style="width: 12%;">Transfer Kas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataKas as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td><strong>{{ $item->nama_kas }}</strong></td>
                        <td class="center">
                            @if($item->aktif == 'Y')
                                <span class="badge badge-success">AKTIF</span>
                            @else
                                <span class="badge badge-danger">TIDAK</span>
                            @endif
                        </td>
                        <td class="center">
                            @if($item->simpanan == 'Y')
                                <span class="badge badge-success">Y</span>
                            @else
                                <span class="badge badge-danger">T</span>
                            @endif
                        </td>
                        <td class="center">
                            @if($item->penarikan == 'Y')
                                <span class="badge badge-success">Y</span>
                            @else
                                <span class="badge badge-danger">T</span>
                            @endif
                        </td>
                        <td class="center">
                            @if($item->pinjaman == 'Y')
                                <span class="badge badge-success">Y</span>
                            @else
                                <span class="badge badge-danger">T</span>
                            @endif
                        </td>
                        <td class="center">
                            @if($item->angsuran == 'Y')
                                <span class="badge badge-success">Y</span>
                            @else
                                <span class="badge badge-danger">T</span>
                            @endif
                        </td>
                        <td class="center">
                            @if($item->pemasukan_kas == 'Y')
                                <span class="badge badge-success">Y</span>
                            @else
                                <span class="badge badge-danger">T</span>
                            @endif
                        </td>
                        <td class="center">
                            @if($item->pengeluaran_kas == 'Y')
                                <span class="badge badge-success">Y</span>
                            @else
                                <span class="badge badge-danger">T</span>
                            @endif
                        </td>
                        <td class="center">
                            @if($item->transfer_kas == 'Y')
                                <span class="badge badge-success">Y</span>
                            @else
                                <span class="badge badge-danger">T</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="center"><strong>TOTAL DATA KAS</strong></td>
                    <td class="center"><strong>{{ $totalKas }}</strong></td>
                    <td class="center" colspan="2">
                        <strong>Aktif: {{ $kasAktif }}</strong>
                    </td>
                    <td class="center" colspan="5">
                        <strong>Tidak Aktif: {{ $kasTidakAktif }}</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <p><strong>Tidak ada data kas yang ditemukan</strong></p>
            <p style="font-size: 9pt; margin-top: 10px; color: #999;">
                Silakan tambahkan data kas terlebih dahulu atau sesuaikan filter pencarian
            </p>
        </div>
    @endif

</body>

</html>