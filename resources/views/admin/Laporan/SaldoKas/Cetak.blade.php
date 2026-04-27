<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Saldo Kas</title>
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

        table.data tbody tr.saldo-sebelumnya {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        table.data tbody tr.subtotal-row {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        table.data tbody tr.total-row {
            background-color: #d1f2dd;
            font-weight: bold;
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

        /* Info Box */
        .info-box {
            border: 1px solid #999;
            padding: 8px 10px;
            margin-bottom: 10px;
            background-color: #f0f8ff;
        }

        .info-box h3 {
            font-size: 10pt;
            margin: 0 0 5px 0;
            color: #0066cc;
        }

        .info-box p {
            font-size: 8pt;
            margin: 2px 0;
            line-height: 1.4;
        }

        .info-box ul {
            margin: 5px 0 0 15px;
            padding: 0;
        }

        .info-box li {
            font-size: 7.5pt;
            margin: 2px 0;
            line-height: 1.4;
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
                    <p><strong>{{ strtoupper($identitas->badan_hukum ?? 'Akeno Multimedia Solution') }}</strong></p>
                    <p>Badan Hukum: {{ $identitas->no_badan_hukum ?? 'NO.10455BH/KWK/10.20' }}</p>
                    <p>{{ strtoupper($identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>Laporan Saldo Kas</h1>
        <div class="periode">Periode: {{ $periodeDisplay }}</div>
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Summary Stats -->
    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="label">Total Akun Kas</div>
                    <div class="value">{{ count($saldoKas) }}</div>
                </td>
                <td>
                    <div class="label">Saldo Periode Sebelumnya</div>
                    <div class="value">Rp {{ number_format($saldoPeriodeSebelumnya, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Mutasi Periode Ini</div>
                    <div class="value">Rp {{ number_format($jumlahSaldo, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Saldo Akhir</div>
                    <div class="value" style="color: #198754;">
                        Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    @if(count($saldoKas) > 0)
        <!-- Info Box -->
        <div class="info-box">
            <h3>Penjelasan Perhitungan</h3>
            <ul>
                <li><strong>Saldo Periode Sebelumnya:</strong> Total saldo semua kas sampai akhir bulan sebelumnya</li>
                <li><strong>Mutasi Periode Ini:</strong> Pemasukan - Pengeluaran + Transfer Masuk - Transfer Keluar +
                    Setoran - Penarikan - Pinjaman + Angsuran</li>
                <li><strong>Saldo Akhir:</strong> Saldo Periode Sebelumnya + Jumlah Mutasi Periode Ini</li>
            </ul>
        </div>

        <!-- Data Table -->
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th style="width: 60%;">Nama Kas</th>
                    <th style="width: 30%;">Saldo (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <!-- Saldo Periode Sebelumnya -->
                <tr class="saldo-sebelumnya">
                    <td colspan="2" class="right">
                        <strong>SALDO PERIODE SEBELUMNYA</strong>
                    </td>
                    <td class="right">
                        <strong>{{ number_format($saldoPeriodeSebelumnya, 0, ',', '.') }}</strong>
                    </td>
                </tr>

                @foreach($saldoKas as $item)
                    <tr>
                        <td class="center">{{ $item->no }}</td>
                        <td>{{ $item->nama_kas }}</td>
                        <td class="right">
                            <strong>
                                @if($item->saldo >= 0)
                                    {{ number_format($item->saldo, 0, ',', '.') }}
                                @else
                                    ({{ number_format(abs($item->saldo), 0, ',', '.') }})
                                @endif
                            </strong>
                        </td>
                    </tr>
                @endforeach

                <!-- Subtotal Row -->
                <tr class="subtotal-row">
                    <td colspan="2" class="right">
                        <strong>JUMLAH MUTASI PERIODE INI</strong>
                    </td>
                    <td class="right">
                        <strong>
                            @if($jumlahSaldo >= 0)
                                {{ number_format($jumlahSaldo, 0, ',', '.') }}
                            @else
                                ({{ number_format(abs($jumlahSaldo), 0, ',', '.') }})
                            @endif
                        </strong>
                    </td>
                </tr>

                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="2" class="right">
                        <strong style="font-size: 9.5pt;">SALDO AKHIR</strong>
                    </td>
                    <td class="right">
                        <strong style="font-size: 10pt;">
                            @if($totalSaldo >= 0)
                                {{ number_format($totalSaldo, 0, ',', '.') }}
                            @else
                                ({{ number_format(abs($totalSaldo), 0, ',', '.') }})
                            @endif
                        </strong>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Breakdown Summary -->
        <div style="border: 1px solid #000; padding: 10px; margin-bottom: 10px; background-color: #f9f9f9;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 70%; border: none; padding: 3px; font-size: 8.5pt;">
                        <strong>Detail Perhitungan Saldo:</strong>
                    </td>
                    <td style="width: 30%; border: none; padding: 3px;"></td>
                </tr>
                <tr>
                    <td style="border: none; padding: 3px; font-size: 8pt;">
                        1. Saldo Periode Sebelumnya (sampai
                        {{ \Carbon\Carbon::parse($startDate)->subDay()->translatedFormat('d F Y') }})
                    </td>
                    <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                        Rp {{ number_format($saldoPeriodeSebelumnya, 0, ',', '.') }}
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 3px; font-size: 8pt;">
                        2. Total Mutasi Periode {{ $periodeDisplay }}
                    </td>
                    <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                        @if($jumlahSaldo >= 0)
                            Rp {{ number_format($jumlahSaldo, 0, ',', '.') }}
                        @else
                            (Rp {{ number_format(abs($jumlahSaldo), 0, ',', '.') }})
                        @endif
                    </td>
                </tr>
                @if(count($saldoKas) > 0)
                    <tr>
                        <td style="border: none; padding: 3px 3px 3px 15px; font-size: 7.5pt; color: #666;">
                            Terdiri dari {{ count($saldoKas) }} akun kas aktif
                        </td>
                        <td style="border: none; padding: 3px;"></td>
                    </tr>
                @endif
                <tr style="border-top: 2px solid #000; background-color: #d1f2dd;">
                    <td style="border: none; padding: 5px; font-size: 9pt;">
                        <strong>TOTAL SALDO AKHIR (1 + 2)</strong>
                    </td>
                    <td style="border: none; padding: 5px; text-align: right; font-size: 10pt;">
                        <strong>
                            @if($totalSaldo >= 0)
                                Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                            @else
                                (Rp {{ number_format(abs($totalSaldo), 0, ',', '.') }})
                            @endif
                        </strong>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Detailed Kas List -->
        <div style="border: 1px solid #999; padding: 8px; margin-bottom: 10px; background-color: #fafafa;">
            <h3 style="font-size: 9pt; margin: 0 0 5px 0;">Rincian Per Akun Kas:</h3>
            <table style="width: 100%; border: none;">
                @foreach($saldoKas as $item)
                    <tr>
                        <td style="border: none; padding: 2px; font-size: 7.5pt; width: 5%;">
                            {{ $item->no }}.
                        </td>
                        <td style="border: none; padding: 2px; font-size: 7.5pt; width: 60%;">
                            {{ $item->nama_kas }}
                        </td>
                        <td style="border: none; padding: 2px; font-size: 7.5pt; text-align: right; width: 35%;">
                            Mutasi: Rp
                            @if($item->saldo >= 0)
                                {{ number_format($item->saldo, 0, ',', '.') }}
                            @else
                                ({{ number_format(abs($item->saldo), 0, ',', '.') }})
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>

    @else
        <!-- Empty State -->
        <div class="empty-state">
            <p>Tidak ada akun kas yang aktif pada periode ini</p>
        </div>
    @endif

</body>

</html>