<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Jenis Akun</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            color: #000;
        }

        /* Header */
        .header {
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
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
            width: 50px;
            height: 50px;
        }

        .header h2 {
            font-size: 13pt;
            margin: 0 0 2px 0;
            font-weight: bold;
        }

        .header p {
            font-size: 8pt;
            margin: 0;
            line-height: 1.3;
        }

        /* Title */
        .title {
            text-align: center;
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 6px;
            background-color: #f5f5f5;
        }

        .title h1 {
            font-size: 13pt;
            margin: 0 0 3px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .title .filter-info {
            font-size: 8pt;
            margin: 3px 0;
            color: #444;
        }

        .title .date {
            font-size: 7.5pt;
            color: #666;
        }

        /* Summary Stats */
        .summary {
            border: 1px solid #999;
            padding: 5px 8px;
            margin-bottom: 8px;
            background-color: #fafafa;
        }

        .summary table {
            width: 100%;
            border: none;
        }

        .summary td {
            border: none;
            border-right: 1px solid #ccc;
            padding: 3px 8px;
            text-align: center;
            font-size: 7pt;
        }

        .summary td:last-child {
            border-right: none;
        }

        .summary .label {
            font-weight: bold;
            margin-bottom: 2px;
            color: #666;
            font-size: 6.5pt;
        }

        .summary .value {
            font-size: 10pt;
            font-weight: bold;
        }

        /* Data Table */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        table.data thead {
            background-color: #d0d0d0;
            color: #000;
        }

        table.data th {
            border: 1px solid #000;
            padding: 4px 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        table.data tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        table.data td {
            border: 1px solid #000;
            padding: 3px 3px;
            font-size: 7pt;
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
            padding: 4px;
            font-size: 7.5pt;
            border: 1px solid #000;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 2px;
            font-size: 6.5pt;
            font-weight: bold;
        }

        .badge-primary {
            background-color: #cfe2ff;
            color: #084298;
            border: 1px solid #b6d4fe;
        }

        .badge-success {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #badbcc;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #842029;
            border: 1px solid #f5c2c7;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #664d03;
            border: 1px solid #ffecb5;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .empty-state p {
            font-size: 11pt;
            color: #666;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 60px;">
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
                <td style="padding-left: 10px;">
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
        <h1>Laporan Jenis Akun</h1>
        <div class="filter-info">
            <strong>Filter:</strong> Akun: {{ $filterInfo['akun'] }} | Status: {{ $filterInfo['status'] }}
        </div>
        <div class="date">Dicetak: {{ $tanggalCetak }}</div>
    </div>

    <!-- Summary Stats -->
    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="label">Total Jenis Akun</div>
                    <div class="value">{{ $totalJenisAkun }}</div>
                </td>
                <td>
                    <div class="label">Aktiva</div>
                    <div class="value">{{ $aktiva }}</div>
                </td>
                <td>
                    <div class="label">Pasiva</div>
                    <div class="value">{{ $pasiva }}</div>
                </td>
                <td>
                    <div class="label">Akun Aktif</div>
                    <div class="value">{{ $akunAktif }}</div>
                </td>
                <td>
                    <div class="label">Akun Tidak Aktif</div>
                    <div class="value">{{ $akunTidakAktif }}</div>
                </td>
                <td>
                    <div class="label">Pendapatan</div>
                    <div class="value">{{ $pendapatan }}</div>
                </td>
                <td>
                    <div class="label">Biaya</div>
                    <div class="value">{{ $biaya }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if($jenisAkun->count() > 0)
        <!-- Data Table -->
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 8%;">Kode Aktiva</th>
                    <th style="width: 25%;">Jenis Transaksi</th>
                    <th style="width: 10%;">Akun</th>
                    <th style="width: 8%;">Pemasukan</th>
                    <th style="width: 8%;">Pengeluaran</th>
                    <th style="width: 8%;">Status</th>
                    <th style="width: 12%;">Laba Rugi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jenisAkun as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">
                            <span class="badge badge-primary">{{ $item->kd_aktiva }}</span>
                        </td>
                        <td><strong>{{ $item->jns_transaksi }}</strong></td>
                        <td class="center">
                            <strong>{{ $item->akun }}</strong>
                        </td>
                        <td class="center">
                            @if($item->pemasukan == 'Y')
                                <span class="badge badge-success">YA</span>
                            @else
                                <span class="badge badge-danger">TIDAK</span>
                            @endif
                        </td>
                        <td class="center">
                            @if($item->pengeluaran == 'Y')
                                <span class="badge badge-success">YA</span>
                            @else
                                <span class="badge badge-danger">TIDAK</span>
                            @endif
                        </td>
                        <td class="center">
                            @if($item->aktif == 'Y')
                                <span class="badge badge-success">AKTIF</span>
                            @else
                                <span class="badge badge-danger">NON-AKTIF</span>
                            @endif
                        </td>
                        <td class="center">
                            @if($item->laba_rugi)
                                <span class="badge badge-warning">{{ $item->laba_rugi }}</span>
                            @else
                                <span style="color: #999;">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="right"><strong>TOTAL KESELURUHAN</strong></td>
                    <td class="center">
                        <strong>Aktiva: {{ $aktiva }} | Pasiva: {{ $pasiva }}</strong>
                    </td>
                    <td colspan="2" class="center">
                        <strong>Aktif: {{ $akunAktif }} | Non-Aktif: {{ $akunTidakAktif }}</strong>
                    </td>
                    <td colspan="2" class="center">
                        <strong>{{ $totalJenisAkun }} Jenis Akun</strong>
                    </td>
                </tr>
            </tfoot>
        </table>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <p><strong>Tidak ada data jenis akun yang ditemukan</strong></p>
            <p style="font-size: 8pt; margin-top: 10px; color: #999;">
                Silakan tambahkan data jenis akun terlebih dahulu
            </p>
        </div>
    @endif

</body>

</html>