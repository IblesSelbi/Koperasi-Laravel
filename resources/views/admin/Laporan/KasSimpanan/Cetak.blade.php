<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kas Simpanan</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 portrait;
            margin: 10mm;
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
            font-size: 14pt;
            margin: 0 0 3px 0;
            font-weight: bold;
            text-transform: uppercase;
        }

        .title .periode {
            font-size: 10pt;
            font-weight: bold;
            margin: 3px 0;
        }

        .title .date {
            font-size: 8pt;
            color: #666;
        }

        /* Summary Stats */
        .summary {
            border: 1px solid #999;
            padding: 8px;
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
        }

        .summary .value {
            font-size: 12pt;
            font-weight: bold;
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
            padding: 6px 5px;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        table.data tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        table.data td {
            border: 1px solid #000;
            padding: 5px 5px;
            font-size: 8.5pt;
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
            padding: 3px;
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

    @php
        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();
        $periodeText = \Carbon\Carbon::parse($tglDari)->locale('id')->translatedFormat('d F Y') . ' - ' . \Carbon\Carbon::parse($tglSamp)->locale('id')->translatedFormat('d F Y');
    @endphp

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
                    <p><strong>{{ strtoupper($identitas->badan_hukum ?? 'BINA TIRTA LESTARI') }}</strong></p>
                    <p>Badan Hukum: {{ $identitas->no_badan_hukum ?? 'NO.10455BH/KWK/10.20' }}</p>
                    <p>{{ strtoupper($identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>Laporan Kas Simpanan</h1>
        <div class="periode">Periode: {{ $periodeText }}</div>
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Summary Stats -->
    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="label">Total Jenis Simpanan</div>
                    <div class="value">{{ count($kasSimpanan) }}</div>
                </td>
                <td>
                    <div class="label">Total Simpanan</div>
                    <div class="value">Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Total Penarikan</div>
                    <div class="value">Rp {{ number_format($totalPenarikan, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Saldo Bersih</div>
                    <div class="value">
                        Rp {{ number_format($totalJumlah, 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if(count($kasSimpanan) > 0)
        <!-- Data Table -->
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 8%;">No</th>
                    <th style="width: 37%;">Jenis Akun</th>
                    <th style="width: 18%;">Simpanan (Rp)</th>
                    <th style="width: 18%;">Penarikan (Rp)</th>
                    <th style="width: 19%;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kasSimpanan as $item)
                    <tr>
                        <td class="center">{{ $item->no }}</td>
                        <td>{{ $item->jenis_akun }}</td>
                        <td class="right">
                            @if($item->simpanan > 0)
                                <strong>{{ number_format($item->simpanan, 0, ',', '.') }}</strong>
                            @else
                                -
                            @endif
                        </td>
                        <td class="right">
                            @if($item->penarikan > 0)
                                <strong>{{ number_format($item->penarikan, 0, ',', '.') }}</strong>
                            @else
                                -
                            @endif
                        </td>
                        <td class="right">
                            <strong>{{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="right"><strong>TOTAL KESELURUHAN</strong></td>
                    <td class="right">
                        <strong>{{ number_format($totalSimpanan, 0, ',', '.') }}</strong>
                    </td>
                    <td class="right">
                        <strong>{{ number_format($totalPenarikan, 0, ',', '.') }}</strong>
                    </td>
                    <td class="right">
                        <strong>{{ number_format($totalJumlah, 0, ',', '.') }}</strong>
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
                        - Periode laporan: {{ $periodeText }}<br>
                        - Total jenis simpanan: {{ count($kasSimpanan) }} jenis<br>
                        - Laporan ini mencakup semua transaksi simpanan dan penarikan<br>
                        - Saldo bersih = Total Simpanan - Total Penarikan
                    </td>
                    <td style="width: 40%; text-align: right;">
                        <strong>Mengetahui,</strong><br><br><br><br>
                        <strong>(_____________________)</strong><br>
                        Bendahara
                    </td>
                </tr>
            </table>
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <p>Tidak ada data simpanan pada periode ini</p>
        </div>
    @endif

</body>

</html>