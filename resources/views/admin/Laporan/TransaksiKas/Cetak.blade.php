<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi Kas</title>
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

        /* Title */
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

        .title .periode {
            font-size: 10pt;
            font-weight: bold;
            margin: 3px 0;
        }

        .title .date {
            font-size: 7.5pt;
        }

        /* Stats */
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
            font-size: 11pt;
            font-weight: bold;
        }

        /* Info Bar */
        .info-bar {
            margin-bottom: 6px;
            border: 1px solid #000;
            padding: 5px 8px;
        }

        .info-bar table {
            width: 100%;
        }

        .info-bar td {
            padding: 2px 5px;
            font-size: 7.5pt;
        }

        .info-bar strong {
            font-weight: bold;
        }

        /* Data Table */
        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data thead {
            background-color: #000;
            color: white;
        }

        table.data th {
            border: 1px solid #000;
            padding: 5px 3px;
            font-size: 6.5pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        table.data tbody tr:nth-child(even) {
            background-color: #f5f5f5;
        }

        table.data tbody tr.saldo-awal {
            background-color: #e8f4f8;
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
            padding: 5px;
            font-size: 7.5pt;
            border: 1px solid #000;
        }

        /* Badge */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border: 1px solid #000;
            font-size: 6.5pt;
            font-weight: bold;
        }

        .badge-primary {
            background-color: #cfe2ff;
        }

        .badge-success {
            background-color: #d1e7dd;
        }

        .badge-danger {
            background-color: #f8d7da;
        }

        .badge-info {
            background-color: #cff4fc;
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
                    <p><strong>{{ strtoupper($identitas->badan_hukum ?? 'BINA TIRTA LESTARI') }}</strong></p>
                    <p>Badan Hukum: {{ $identitas->no_badan_hukum ?? 'NO.10455BH/KWK/10.20' }}</p>
                    <p>{{ strtoupper($identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>Laporan Transaksi Kas</h1>
        <div class="periode">Periode: {{ $periodeTampil }}</div>
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Stats -->
    <div class="stats">
        <table>
            <tr>
                <td>
                    <div class="label">Total Transaksi</div>
                    <div class="value">{{ $transaksiKas->count() }}</div>
                </td>
                <td>
                    <div class="label">Saldo Awal</div>
                    <div class="value">Rp {{ number_format($saldoSebelumnya, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Total Debet</div>
                    <div class="value">Rp {{ number_format($totalDebet, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Total Kredit</div>
                    <div class="value">Rp {{ number_format($totalKredit, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Saldo Akhir</div>
                    <div class="value">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Selisih</div>
                    @php
                        $selisih = $totalDebet - $totalKredit;
                    @endphp
                    <div class="value" style="color: {{ $selisih >= 0 ? '#198754' : '#dc3545' }}">
                        Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Info Bar -->
    <div class="info-bar">
        <table>
            <tr>
                <td style="width: 70%;">
                    <strong>Keterangan:</strong>
                    Laporan ini menampilkan {{ $transaksiKas->count() }} transaksi kas dari tanggal 
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} sampai 
                    {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}.
                </td>
                <td style="width: 30%; text-align: right;">
                    <strong>Format Laporan:</strong> {{ ucfirst($format) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 9%;">Kode</th>
                <th style="width: 8%;">Tanggal</th>
                <th style="width: 20%;">Akun Transaksi</th>
                <th style="width: 10%;">Dari Kas</th>
                <th style="width: 10%;">Untuk Kas</th>
                <th style="width: 13%;">Debet (Rp)</th>
                <th style="width: 13%;">Kredit (Rp)</th>
                <th style="width: 14%;">Saldo (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <!-- Saldo Awal -->
            <tr class="saldo-awal">
                <td class="center">-</td>
                <td class="center">-</td>
                <td class="center">-</td>
                <td colspan="3"><strong>SALDO AWAL</strong></td>
                <td class="right">-</td>
                <td class="right">-</td>
                <td class="right"><strong>{{ number_format($saldoSebelumnya, 0, ',', '.') }}</strong></td>
            </tr>

            @forelse($transaksiKas as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">
                        <span class="badge badge-primary">{{ $item->kode_transaksi }}</span>
                    </td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d/m/Y') }}</td>
                    <td>
                        <strong>{{ $item->akun_transaksi }}</strong><br>
                        <span style="font-size: 6pt; color: #666;">{{ $item->keterangan }}</span>
                    </td>
                    <td class="center">
                        @if($item->dari_kas && $item->dari_kas != '-')
                            <span class="badge badge-success">{{ $item->dari_kas }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="center">
                        @if($item->untuk_kas && $item->untuk_kas != '-')
                            <span class="badge badge-danger">{{ $item->untuk_kas }}</span>
                        @else
                            -
                        @endif
                    </td>
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
                    <td class="right"><strong>{{ number_format($item->saldo, 0, ',', '.') }}</strong></td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="center" style="padding: 20px;">
                        Tidak ada transaksi kas pada periode ini
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($transaksiKas->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="6" class="right"><strong>TOTAL:</strong></td>
                    <td class="right"><strong>{{ number_format($totalDebet, 0, ',', '.') }}</strong></td>
                    <td class="right"><strong>{{ number_format($totalKredit, 0, ',', '.') }}</strong></td>
                    <td class="right"><strong>{{ number_format($saldoAkhir, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td colspan="6" class="right"><strong>SELISIH (DEBET - KREDIT):</strong></td>
                    <td colspan="3" class="center">
                        <strong style="color: {{ $selisih >= 0 ? '#198754' : '#dc3545' }}">
                            {{ $selisih >= 0 ? '+' : '-' }} Rp {{ number_format(abs($selisih), 0, ',', '.') }}
                        </strong>
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    @if($transaksiKas->count() > 0)
        <!-- Footer Info -->
        <div style="margin-top: 10px; padding: 5px; border: 1px solid #000; font-size: 7pt;">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%; padding: 3px;">
                        <strong>Catatan:</strong><br>
                        - Saldo awal: Rp {{ number_format($saldoSebelumnya, 0, ',', '.') }}<br>
                        - Saldo akhir: Rp {{ number_format($saldoAkhir, 0, ',', '.') }}<br>
                        - Periode transaksi: {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y') }}
                    </td>
                    <td style="width: 50%; padding: 3px; text-align: right;">
                        <strong>Tanda Tangan,</strong><br><br><br><br>
                        <strong>(_____________________)</strong><br>
                        Bendahara
                    </td>
                </tr>
            </table>
        </div>
    @endif

</body>

</html>