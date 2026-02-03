<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
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

        .summary .value.profit {
            color: #198754;
        }

        .summary .value.loss {
            color: #dc3545;
        }

        /* Section Header */
        .section-header {
            background-color: #e8e8e8;
            border: 1px solid #000;
            padding: 5px 8px;
            margin-bottom: 3px;
            margin-top: 10px;
        }

        .section-header.estimasi {
            background-color: #cfe2ff;
        }

        .section-header.pendapatan {
            background-color: #d1e7dd;
        }

        .section-header.biaya {
            background-color: #f8d7da;
        }

        .section-header h3 {
            font-size: 10pt;
            margin: 0;
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
            padding: 5px 5px;
            font-size: 7.5pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }

        table.data tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        table.data tbody tr.subtotal-row {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        table.data tbody tr.total-row {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        table.data td {
            border: 1px solid #000;
            padding: 4px 5px;
            font-size: 8pt;
            vertical-align: middle;
        }

        table.data td.center {
            text-align: center;
        }

        table.data td.right {
            text-align: right;
        }

        /* Result Box */
        .result-box {
            border: 2px solid #000;
            padding: 10px;
            margin-top: 10px;
            text-align: center;
        }

        .result-box.profit {
            background-color: #d1f2dd;
            border-color: #198754;
        }

        .result-box.loss {
            background-color: #f8d7da;
            border-color: #dc3545;
        }

        .result-box h3 {
            font-size: 11pt;
            margin: 0 0 5px 0;
            font-weight: bold;
        }

        .result-box .amount {
            font-size: 16pt;
            font-weight: bold;
            margin-top: 5px;
        }

        .result-box .amount.profit {
            color: #198754;
        }

        .result-box .amount.loss {
            color: #dc3545;
        }

        /* Info Box */
        .info-box {
            border: 1px solid #999;
            padding: 8px 10px;
            margin-bottom: 10px;
            background-color: #fff3cd;
        }

        .info-box p {
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
            padding: 20px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .empty-state p {
            font-size: 8pt;
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
        <h1>Laporan Laba Rugi</h1>
        <div class="periode">Periode: {{ $periodeText }}</div>
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Summary Stats -->
    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="label">Total Pendapatan</div>
                    <div class="value">Rp {{ number_format($jumlahPendapatan, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Total Biaya</div>
                    <div class="value">Rp {{ number_format($jumlahBiaya, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">{{ $labaRugi >= 0 ? 'Laba Bersih' : 'Rugi Bersih' }}</div>
                    <div class="value {{ $labaRugi >= 0 ? 'profit' : 'loss' }}">
                        Rp {{ number_format(abs($labaRugi), 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Estimasi Data Pinjaman -->
    <div class="section-header estimasi">
        <h3>ESTIMASI DATA PINJAMAN</h3>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 62%;">Keterangan</th>
                <th style="width: 30%;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($estimasiPinjaman as $item)
                <tr>
                    <td class="center">{{ $item->no }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="right">
                        <strong>{{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty-state">
                        <p>Tidak ada data pinjaman</p>
                    </td>
                </tr>
            @endforelse
            @if($estimasiPinjaman->isNotEmpty())
                <tr class="subtotal-row">
                    <td colspan="2" class="right">Jumlah Tagihan</td>
                    <td class="right">
                        <strong>{{ number_format($jumlahTagihan, 0, ',', '.') }}</strong>
                    </td>
                </tr>
                <tr class="total-row">
                    <td colspan="2" class="right">
                        <strong>Estimasi Pendapatan Pinjaman</strong>
                    </td>
                    <td class="right">
                        <strong>{{ number_format($estimasiPendapatanPinjaman, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Pendapatan -->
    <div class="section-header pendapatan">
        <h3>PENDAPATAN</h3>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 62%;">Keterangan</th>
                <th style="width: 30%;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pendapatanList as $item)
                <tr>
                    <td class="center">{{ $item->no }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="right">
                        <strong>{{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty-state">
                        <p>Tidak ada pendapatan</p>
                    </td>
                </tr>
            @endforelse
            @if($pendapatanList->isNotEmpty())
                <tr class="total-row">
                    <td colspan="2" class="right">
                        <strong>JUMLAH PENDAPATAN</strong>
                    </td>
                    <td class="right">
                        <strong style="font-size: 9pt;">{{ number_format($jumlahPendapatan, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Biaya-biaya -->
    <div class="section-header biaya">
        <h3>BIAYA-BIAYA</h3>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 62%;">Keterangan</th>
                <th style="width: 30%;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($biayaList as $item)
                <tr>
                    <td class="center">{{ $item->no }}</td>
                    <td>{{ $item->keterangan }}</td>
                    <td class="right">
                        <strong>{{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="empty-state">
                        <p>Tidak ada biaya</p>
                    </td>
                </tr>
            @endforelse
            @if($biayaList->isNotEmpty())
                <tr class="total-row">
                    <td colspan="2" class="right">
                        <strong>JUMLAH BIAYA</strong>
                    </td>
                    <td class="right">
                        <strong style="font-size: 9pt;">{{ number_format($jumlahBiaya, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Result Box -->
    <div class="result-box {{ $labaRugi >= 0 ? 'profit' : 'loss' }}">
        <h3>{{ $labaRugi >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }}</h3>
        <div class="amount {{ $labaRugi >= 0 ? 'profit' : 'loss' }}">
            {{ $labaRugi < 0 ? '(' : '' }}Rp
            {{ number_format(abs($labaRugi), 0, ',', '.') }}{{ $labaRugi < 0 ? ')' : '' }}
        </div>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <p><strong>Perhitungan:</strong> Laba/Rugi = Total Pendapatan - Total Biaya</p>
        <p><strong>Catatan:</strong> Angka dalam kurung ( ) menunjukkan kerugian</p>
    </div>

    <!-- Breakdown Summary -->
    <div style="border: 1px solid #000; padding: 10px; margin-bottom: 10px; background-color: #f9f9f9;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 70%; border: none; padding: 3px; font-size: 8.5pt;">
                    <strong>Rincian Perhitungan Laba Rugi:</strong>
                </td>
                <td style="width: 30%; border: none; padding: 3px;"></td>
            </tr>
            <tr>
                <td style="border: none; padding: 3px; font-size: 8pt;">
                    1. Total Pendapatan ({{ count($pendapatanList) }} item)
                </td>
                <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                    Rp {{ number_format($jumlahPendapatan, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 3px; font-size: 8pt;">
                    2. Total Biaya ({{ count($biayaList) }} item)
                </td>
                <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                    (Rp {{ number_format($jumlahBiaya, 0, ',', '.') }})
                </td>
            </tr>
            <tr style="border-top: 2px solid #000; background-color: {{ $labaRugi >= 0 ? '#d1f2dd' : '#f8d7da' }};">
                <td style="border: none; padding: 5px; font-size: 9pt;">
                    <strong>{{ $labaRugi >= 0 ? 'LABA BERSIH' : 'RUGI BERSIH' }} (1 - 2)</strong>
                </td>
                <td style="border: none; padding: 5px; text-align: right; font-size: 10pt;">
                    <strong>
                        {{ $labaRugi < 0 ? '(' : '' }}Rp
                        {{ number_format(abs($labaRugi), 0, ',', '.') }}{{ $labaRugi < 0 ? ')' : '' }}
                    </strong>
                </td>
            </tr>
        </table>
    </div>

</body>

</html>