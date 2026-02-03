<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Data Anggota</title>
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
            font-size: 8pt;
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
            padding: 5px 8px;
            text-align: center;
            font-size: 7.5pt;
        }

        .summary td:last-child {
            border-right: none;
        }

        .summary .label {
            font-weight: bold;
            margin-bottom: 3px;
            color: #666;
            font-size: 7.5pt;
        }

        .summary .value {
            font-size: 11pt;
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
            padding: 5px 3px;
            font-size: 7pt;
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
            padding: 3px 3px;
            font-size: 7.5pt;
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
            padding: 5px 4px;
            font-size: 8pt;
            border: 1px solid #000;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 2px;
            font-size: 6.5pt;
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

        .badge-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .badge-primary {
            background-color: #cfe2ff;
            color: #084298;
            border: 1px solid #b6d4fe;
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
                    <p><strong>{{ strtoupper($identitas->badan_hukum ?? 'BINA TIRTA LESTARI') }}</strong></p>
                    <p>Badan Hukum: {{ $identitas->no_badan_hukum ?? 'NO.10455BH/KWK/10.20' }}</p>
                    <p>{{ strtoupper($identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>Laporan Data Anggota</h1>
        <div class="filter-info">
            <strong>Status:</strong> {{ $filterInfo['status'] }} | 
            <strong>Gender:</strong> {{ $filterInfo['gender'] }} | 
            <strong>Jabatan:</strong> {{ $filterInfo['jabatan'] }}
            @if($filterInfo['departemen'] != 'Semua')
                | <strong>Departemen:</strong> {{ $filterInfo['departemen'] }}
            @endif
        </div>
        <div class="date">Dicetak: {{ $tanggalCetak }}</div>
    </div>

    <!-- Summary Stats -->
    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="label">Total Anggota</div>
                    <div class="value">{{ $totalAnggota }}</div>
                </td>
                <td>
                    <div class="label">Aktif</div>
                    <div class="value">{{ $anggotaAktif }}</div>
                </td>
                <td>
                    <div class="label">Non Aktif</div>
                    <div class="value">{{ $anggotaNonAktif }}</div>
                </td>
                <td>
                    <div class="label">Laki-laki</div>
                    <div class="value">{{ $anggotaLakiLaki }}</div>
                </td>
                <td>
                    <div class="label">Perempuan</div>
                    <div class="value">{{ $anggotaPerempuan }}</div>
                </td>
                <td>
                    <div class="label">Pengurus</div>
                    <div class="value">{{ $pengurus }}</div>
                </td>
                <td>
                    <div class="label">Anggota</div>
                    <div class="value">{{ $anggota }}</div>
                </td>
            </tr>
        </table>
    </div>

    @if($dataAnggota->count() > 0)
        <!-- Data Table -->
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 7%;">ID Anggota</th>
                    <th style="width: 12%;">Nama</th>
                    <th style="width: 6%;">L/P</th>
                    <th style="width: 15%;">Alamat</th>
                    <th style="width: 8%;">Kota</th>
                    <th style="width: 9%;">Jabatan</th>
                    <th style="width: 10%;">Dept</th>
                    <th style="width: 8%;">Tgl Reg</th>
                    <th style="width: 7%;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataAnggota as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">
                            <span class="badge badge-primary">{{ $item->id_anggota }}</span>
                        </td>
                        <td><strong>{{ $item->nama }}</strong></td>
                        <td class="center">
                            <span class="badge badge-info">{{ substr($item->jenis_kelamin, 0, 1) }}</span>
                        </td>
                        <td>{{ Str::limit($item->alamat, 30) }}</td>
                        <td>{{ $item->kota }}</td>
                        <td class="center">{{ $item->jabatan }}</td>
                        <td>{{ $item->departement ?: '-' }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_registrasi)->format('d/m/Y') }}</td>
                        <td class="center">
                            @if($item->aktif == 'Aktif')
                                <span class="badge badge-success">AKTIF</span>
                            @else
                                <span class="badge badge-danger">NON AKTIF</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="9" class="center"><strong>TOTAL ANGGOTA</strong></td>
                    <td class="center"><strong>{{ $totalAnggota }}</strong></td>
                </tr>
            </tfoot>
        </table>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <p><strong>Tidak ada data anggota yang ditemukan</strong></p>
            <p style="font-size: 9pt; margin-top: 10px; color: #999;">
                Silakan tambahkan data anggota terlebih dahulu atau sesuaikan filter pencarian
            </p>
        </div>
    @endif

</body>

</html>