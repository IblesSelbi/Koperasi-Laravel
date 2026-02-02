<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Buku Besar</title>
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

        .title .periode {
            font-size: 10pt;
            font-weight: bold;
            margin: 3px 0;
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
            font-size: 7.5pt;
        }

        .summary td:last-child {
            border-right: none;
        }

        .summary .label {
            font-weight: bold;
            margin-bottom: 2px;
            color: #666;
        }

        .summary .value {
            font-size: 11pt;
            font-weight: bold;
        }

        /* Kas Section */
        .kas-section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .kas-header {
            background-color: #e8e8e8;
            border: 1px solid #000;
            padding: 5px 8px;
            margin-bottom: 3px;
        }

        .kas-header table {
            width: 100%;
        }

        .kas-header h3 {
            font-size: 10pt;
            margin: 0;
            font-weight: bold;
        }

        .kas-header .saldo-info {
            font-size: 7.5pt;
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

        table.data tbody tr.saldo-awal {
            background-color: #e8e8e8;
            font-weight: bold;
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

        /* Total Saldo */
        .total-saldo {
            border: 1px solid #000;
            padding: 6px 10px;
            margin-top: 10px;
            background-color: #f0f0f0;
            text-align: center;
        }

        .total-saldo h3 {
            font-size: 11pt;
            margin: 0;
            font-weight: bold;
        }

        .total-saldo .amount {
            font-size: 14pt;
            font-weight: bold;
            margin-top: 3px;
        }

        /* Footer */
        .footer {
            margin-top: 10px;
            padding: 5px;
            border: 1px solid #999;
            font-size: 7pt;
            background-color: #fafafa;
        }

        .footer table {
            width: 100%;
        }

        .footer td {
            padding: 3px;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border: 1px solid #999;
            background-color: #f0f0f0;
            font-size: 6.5pt;
            font-weight: bold;
        }

        .text-muted {
            color: #666;
        }
    </style>
</head>

<body>

    @php
        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();
        $periodeText = \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('F Y');
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
        <h1>Laporan Buku Besar</h1>
        <div class="periode">Periode: {{ $periodeText }}</div>
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Summary Stats -->
    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="label">Total Akun Kas</div>
                    <div class="value">{{ count($bukuBesarData) }}</div>
                </td>
                <td>
                    <div class="label">Total Transaksi</div>
                    @php
                        $totalTransaksi = 0;
                        foreach($bukuBesarData as $data) {
                            $totalTransaksi += $data['transaksi']->count();
                        }
                    @endphp
                    <div class="value">{{ $totalTransaksi }}</div>
                </td>
                <td>
                    <div class="label">Total Debet</div>
                    @php
                        $grandTotalDebet = 0;
                        foreach($bukuBesarData as $data) {
                            $grandTotalDebet += $data['total_debet'];
                        }
                    @endphp
                    <div class="value">Rp {{ number_format($grandTotalDebet, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Total Kredit</div>
                    @php
                        $grandTotalKredit = 0;
                        foreach($bukuBesarData as $data) {
                            $grandTotalKredit += $data['total_kredit'];
                        }
                    @endphp
                    <div class="value">Rp {{ number_format($grandTotalKredit, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Total Saldo</div>
                    <div class="value">
                        Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @forelse($bukuBesarData as $index => $data)
        <!-- Kas Section -->
        <div class="kas-section">
            <!-- Kas Header -->
            <div class="kas-header">
                <table>
                    <tr>
                        <td style="width: 50%;">
                            <h3>{{ strtoupper($data['kas']->nama_kas) }}</h3>
                        </td>
                        <td style="width: 25%; text-align: right;">
                            <span class="saldo-info">Saldo Awal: Rp {{ number_format($data['saldo_awal'], 0, ',', '.') }}</span>
                        </td>
                        <td style="width: 25%; text-align: right;">
                            <span class="saldo-info">
                                Saldo Akhir: Rp {{ number_format($data['saldo_akhir'], 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Data Table -->
            <table class="data">
                <thead>
                    <tr>
                        <th style="width: 4%;">No</th>
                        <th style="width: 9%;">Tanggal</th>
                        <th style="width: 18%;">Jenis Transaksi</th>
                        <th style="width: 35%;">Keterangan</th>
                        <th style="width: 11%;">Debet (Rp)</th>
                        <th style="width: 11%;">Kredit (Rp)</th>
                        <th style="width: 12%;">Saldo (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Saldo Awal -->
                    <tr class="saldo-awal">
                        <td class="center">-</td>
                        <td class="center">-</td>
                        <td colspan="2"><strong>SALDO AWAL PERIODE</strong></td>
                        <td class="right">-</td>
                        <td class="right">-</td>
                        <td class="right"><strong>{{ number_format($data['saldo_awal'], 0, ',', '.') }}</strong></td>
                    </tr>

                    @foreach($data['transaksi'] as $idx => $item)
                        <tr>
                            <td class="center">{{ $idx + 1 }}</td>
                            <td class="center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $item->jenis_transaksi }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td class="right">
                                @if($item->debet > 0)
                                    <strong>{{ number_format($item->debet, 0, ',', '.') }}</strong>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="right">
                                @if($item->kredit > 0)
                                    <strong>{{ number_format($item->kredit, 0, ',', '.') }}</strong>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="right">
                                <strong>
                                    {{ number_format($item->saldo, 0, ',', '.') }}
                                </strong>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="right"><strong>TOTAL MUTASI PERIODE</strong></td>
                        <td class="right">
                            <strong>{{ number_format($data['total_debet'], 0, ',', '.') }}</strong>
                        </td>
                        <td class="right">
                            <strong>{{ number_format($data['total_kredit'], 0, ',', '.') }}</strong>
                        </td>
                        <td class="right">
                            <strong>
                                {{ number_format($data['saldo_akhir'], 0, ',', '.') }}
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @empty
        <div style="text-align: center; padding: 40px; border: 1px solid #ddd;">
            <p style="font-size: 12pt; color: #666;">Tidak ada data transaksi pada periode ini</p>
        </div>
    @endforelse

    @if(count($bukuBesarData) > 0)
        <!-- Total Saldo Keseluruhan -->
        <div class="total-saldo">
            <h3>TOTAL SALDO KAS & BANK</h3>
            <div class="amount">
                Rp {{ number_format($totalSaldo, 0, ',', '.') }}
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <table>
                <tr>
                    <td style="width: 60%;">
                        <strong>Catatan:</strong><br>
                        - Periode laporan: {{ $periodeText }}<br>
                        - Total akun kas: {{ count($bukuBesarData) }} akun<br>
                        - Total transaksi: {{ $totalTransaksi }} transaksi<br>
                        - Laporan ini mencakup semua mutasi kas dan bank
                    </td>
                    <td style="width: 40%; text-align: right;">
                        <strong>Mengetahui,</strong><br><br><br><br>
                        <strong>(_____________________)</strong><br>
                        Bendahara
                    </td>
                </tr>
            </table>
        </div>
    @endif

</body>

</html>