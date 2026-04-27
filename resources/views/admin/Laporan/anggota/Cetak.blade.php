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
            margin: 10mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            color: #000;
        }

        /* Header - Simple & Clean */
        .header {
            border-bottom: 3px solid #000;
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

        /* Title - Simple Black */
        .title {
            text-align: center;
            border: 2px solid #000;
            padding: 8px;
            margin-bottom: 6px;
        }

        .title h1 {
            font-size: 13pt;
            margin: 0 0 3px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .title .date {
            font-size: 7.5pt;
        }

        /* Stats - Simple Table */
        .stats {
            border: 1px solid #000;
            padding: 5px 8px;
            margin-bottom: 6px;
        }

        .stats table {
            width: 100%;
            border: none;
        }

        .stats td {
            border: none;
            border-right: 1px solid #ddd;
            padding: 3px 8px;
            text-align: center;
            font-size: 7.5pt;
        }

        .stats td:last-child {
            border-right: none;
        }

        .stats .label {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .stats .value {
            font-size: 12pt;
            font-weight: bold;
        }

        /* Info Bar - Clean */
        .info-bar {
            margin-bottom: 6px;
        }

        .info-bar table {
            width: 100%;
            border: 1px solid #000;
        }

        .info-bar td {
            border: none;
            border-right: 1px solid #000;
            padding: 5px 8px;
            vertical-align: middle;
        }

        .info-bar td:last-child {
            border-right: none;
        }

        .info-bar .title-small {
            font-size: 7.5pt;
            font-weight: bold;
            margin-bottom: 3px;
            text-transform: uppercase;
        }

        .info-bar .content {
            font-size: 7pt;
        }

        /* Data Table - Professional */
        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data thead {
            background-color: #6d6d6d;
            color: white;
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
            background-color: #f5f5f5;
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

        table.data tfoot {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        table.data tfoot td {
            padding: 5px;
            font-size: 7.5pt;
            border: 1px solid #000;
        }

        /* Simple Badges */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border: 1px solid #000;
            font-size: 6.5pt;
            font-weight: bold;
        }

        /* Striped rows only */
        .no-border {
            border: none !important;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 60px;">
                    @if(isset($identitas) && $identitas && $identitas->logo)
                        <img src="{{ public_path($identitas->logo) }}" alt="Logo">
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
        <h1>Laporan Data Anggota Koperasi</h1>
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Stats -->
    <div class="stats">
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
                    <div class="label">Laki-Laki</div>
                    <div class="value">{{ $anggotaLakiLaki ?? 0 }}</div>
                </td>
                <td>
                    <div class="label">Perempuan</div>
                    <div class="value">{{ $anggotaPerempuan ?? 0 }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Filter & Summary -->
    <div class="info-bar">
        <table>
            <tr>
                <td style="width: 65%;">
                    <div class="title-small">Filter yang Diterapkan</div>
                    <div class="content">
                        Status: <strong>{{ $filterInfo['status'] }}</strong> |
                        Jenis Kelamin: <strong>{{ $filterInfo['gender'] }}</strong> |
                        Jabatan: <strong>{{ $filterInfo['jabatan'] }}</strong> |
                        Departemen: <strong>{{ $filterInfo['departemen'] }}</strong>
                    </div>
                </td>
                <td style="width: 35%; text-align: center;">
                    <div class="title-small">Ringkasan Data</div>
                    <div style="font-size: 8pt; font-weight: bold; margin-top: 2px;">
                        Total: {{ $totalAnggota }} | Aktif: {{ $anggotaAktif }} | Non Aktif: {{ $anggotaNonAktif }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 20px;">No</th>
                <th style="width: 50px;">ID Anggota</th>
                <th style="width: 100px;">Nama Lengkap</th>
                <th style="width: 22px;">JK</th>
                <th style="width: 75px;">Tempat/Tgl Lahir</th>
                <th style="width: 50px;">Status</th>
                <th style="width: 60px;">Departemen</th>
                <th style="width: 60px;">Pekerjaan</th>
                <th style="width: 120px;">Alamat</th>
                <th style="width: 55px;">No. Telepon</th>
                <th style="width: 55px;">Jabatan</th>
                <th style="width: 50px;">Tgl Registrasi</th>
                <th style="width: 35px;">Aktif</th>
            </tr>
        </thead>
        <tbody>
            @forelse($anggota as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center"><strong>{{ $item->id_anggota }}</strong></td>
                    <td>
                        <strong>{{ $item->nama }}</strong><br>
                        <span style="font-size: 6.5pt; color: #666;">{{ $item->username }}</span>
                    </td>
                    <td class="center">
                        <span class="badge">{{ $item->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</span>
                    </td>
                    <td>
                        {{ $item->tempat_lahir }}<br>
                        <span style="font-size: 6.5pt; color: #666;">
                            {{ $item->tanggal_lahir ? \Carbon\Carbon::parse($item->tanggal_lahir)->translatedFormat('d M Y') : '-' }}
                        </span>
                    </td>
                    <td class="center">{{ $item->status ?? '-' }}</td>
                    <td class="center">{{ $item->departement ?? '-' }}</td>
                    <td class="center">{{ $item->pekerjaan ?? '-' }}</td>
                    <td>{{ $item->alamat }}, {{ $item->kota }}</td>
                    <td class="center">{{ $item->no_telp ?? '-' }}</td>
                    <td class="center">
                        <span class="badge">{{ $item->jabatan }}</span>
                    </td>
                    <td class="center">
                        {{ \Carbon\Carbon::parse($item->tanggal_registrasi)->translatedFormat('d M Y') }}
                    </td>
                    <td class="center">
                        <strong>{{ $item->aktif == 'Aktif' ? 'Y' : 'N' }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="center" style="padding: 15px;">Tidak ada data anggota</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="13" class="center">
                    <strong>TOTAL DATA: {{ $totalAnggota }} ANGGOTA | AKTIF: {{ $anggotaAktif }} | NON AKTIF:
                        {{ $anggotaNonAktif }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

</body>

</html>