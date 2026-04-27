<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Neraca Saldo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
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
            padding-bottom: 5px;
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
            width: 50px;
            height: 50px;
        }

        .header h2 {
            font-size: 14pt;
            margin: 0 0 2px 0;
            font-weight: bold;
        }

        .header p {
            font-size: 9pt;
            margin: 0;
            line-height: 1.3;
        }

        /* Title */
        .title {
            text-align: center;
            border: 2px solid #000;
            padding: 10px;
            margin-bottom: 8px;
            background-color: #e0e0e0;
        }

        .title h1 {
            font-size: 14pt;
            margin: 0 0 4px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .title .periode {
            font-size: 11pt;
            font-weight: bold;
            margin: 4px 0;
        }

        .title .date {
            font-size: 8pt;
            color: #666;
        }

        /* Info Bar */
        .info-bar {
            margin-bottom: 8px;
            border: 1px solid #999;
            background: #fafafa;
            padding: 6px 10px;
            font-size: 8pt;
        }

        /* Data Table */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.data thead {
            background-color: #4a4a4a;
            color: white;
        }

        table.data th {
            border: 1px solid #000;
            padding: 6px 5px;
            font-size: 9pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        table.data tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        table.data tbody tr.kategori-header {
            background-color: #d5d5d5;
            font-weight: bold;
        }

        table.data td {
            border: 1px solid #999;
            padding: 4px 5px;
            font-size: 8.5pt;
            vertical-align: middle;
        }

        table.data td.center {
            text-align: center;
        }

        table.data td.right {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        table.data tfoot {
            background-color: #6a6a6a;
            color: white;
            font-weight: bold;
        }

        table.data tfoot td {
            padding: 6px 5px;
            font-size: 9pt;
            border: 1px solid #000;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            padding: 8px;
            border: 1px solid #999;
            font-size: 8pt;
            background-color: #fafafa;
        }

        .footer table {
            width: 100%;
        }

        .footer td {
            padding: 4px;
        }

        .text-bold {
            font-weight: bold;
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
        <h1>Laporan Neraca Saldo</h1>
        <div class="periode">Periode: {{ $periodeTampil }}</div>
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Info Bar -->
    <div class="info-bar">
        <table style="width: 100%;">
            <tr>
                <td style="width: 70%;">
                    <strong>Keterangan:</strong>
                    Laporan ini menampilkan neraca saldo dari tanggal
                    {{ \Carbon\Carbon::parse($tglDari)->translatedFormat('d F Y') }} sampai
                    {{ \Carbon\Carbon::parse($tglSamp)->translatedFormat('d F Y') }}.
                </td>
                <td style="width: 30%; text-align: right;">
                    <strong>Total Akun:</strong> {{ $neracaSaldo->where('is_header', false)->count() }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 12%;">Kode Akun</th>
                <th style="width: 45%;">Nama Akun</th>
                <th style="width: 17.5%;">Debet (Rp)</th>
                <th style="width: 17.5%;">Kredit (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 1;
            @endphp

            @foreach($neracaSaldo as $item)
                @if($item->is_header)
                    <!-- Kategori Header -->
                    <tr class="kategori-header">
                        <td class="center">
                            <i style="font-style: normal;">§</i>
                        </td>
                        <td colspan="2">
                            <strong>{{ $item->kategori }}</strong>
                        </td>
                        <td class="right">
                            {{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '' }}
                        </td>
                        <td class="right">
                            {{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '' }}
                        </td>
                    </tr>
                @else
                    <!-- Data Row -->
                    <tr>
                        <td class="center">{{ $no++ }}</td>
                        <td class="center">{{ $item->kode_akun ?? '-' }}</td>
                        <td>{{ $item->nama_akun }}</td>
                        <td class="right">
                            {{ $item->debet > 0 ? number_format($item->debet, 0, ',', '.') : '0' }}
                        </td>
                        <td class="right">
                            {{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '0' }}
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="center">
                    <strong>JUMLAH TOTAL</strong>
                </td>
                <td class="right">
                    <strong>{{ number_format($totalDebet, 0, ',', '.') }}</strong>
                </td>
                <td class="right">
                    <strong>{{ number_format($totalKredit, 0, ',', '.') }}</strong>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="center">
                    <strong>SELISIH</strong>
                </td>
                <td colspan="2" class="center">
                    @php
                        $selisih = $totalDebet - $totalKredit;
                    @endphp
                    <strong>
                        {{ $selisih >= 0 ? '+' : '-' }} Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                    </strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer -->
    <div class="footer">
        <table>
            <tr>
                <td style="width: 60%;">
                    <strong>Catatan:</strong><br>
                    - Total Debet: Rp {{ number_format($totalDebet, 0, ',', '.') }}<br>
                    - Total Kredit: Rp {{ number_format($totalKredit, 0, ',', '.') }}<br>
                    - Periode laporan: {{ \Carbon\Carbon::parse($tglDari)->translatedFormat('d F Y') }} -
                    {{ \Carbon\Carbon::parse($tglSamp)->translatedFormat('d F Y') }}
                </td>
                <td style="width: 40%; text-align: right;">
                    <strong>Mengetahui,</strong><br><br><br><br>
                    <strong>(_____________________)</strong><br>
                    Bendahara
                </td>
            </tr>
        </table>
    </div>

</body>

</html>