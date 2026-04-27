<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Sisa Hasil Usaha (SHU)</title>
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

        .title .anggota-info {
            font-size: 9pt;
            margin: 3px 0;
            color: #0066cc;
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
            font-size: 11pt;
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

        .section-header h3 {
            font-size: 9.5pt;
            margin: 0;
            font-weight: bold;
        }

        /* Data Table */
        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        table.data tbody tr {
            background-color: #fff;
        }

        table.data tbody tr:nth-child(even) {
            background-color: #f7f7f7;
        }

        table.data tbody tr.header-row {
            background-color: #e8e8e8;
            font-weight: bold;
        }

        table.data tbody tr.section-title {
            background-color: #fff;
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

        table.data td.right {
            text-align: right;
        }

        table.data td.center {
            text-align: center;
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
            background-color: #f0f8ff;
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
                    <p><strong>{{ strtoupper($identitas->badan_hukum ?? 'Akeno Multimedia Solution') }}</strong></p>
                    <p>Badan Hukum: {{ $identitas->no_badan_hukum ?? 'NO.10455BH/KWK/10.20' }}</p>
                    <p>{{ strtoupper($identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>Laporan Sisa Hasil Usaha (SHU)</h1>
        <div class="periode">Periode: {{ $periodeText }}</div>
        @if($anggota)
            <div class="anggota-info">Anggota: {{ $anggota->id_anggota }} - {{ $anggota->nama }}</div>
        @else
            <div class="anggota-info">Semua Anggota</div>
        @endif
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Summary Stats -->
    <div class="summary">
        <table>
            <tr>
                <td>
                    <div class="label">Total Pendapatan</div>
                    <div class="value">Rp {{ number_format($pendapatan['total'], 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Total Beban</div>
                    <div class="value">Rp {{ number_format($beban['total'], 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">SHU Setelah Pajak</div>
                    <div class="value {{ $shuSetelahPajak >= 0 ? 'profit' : 'loss' }}">
                        Rp {{ number_format(abs($shuSetelahPajak), 0, ',', '.') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Info Box -->
    <div class="info-box">
        <p><strong>Komponen Pendapatan:</strong> Bunga: Rp {{ number_format($pendapatan['bunga'], 0, ',', '.') }} |
            Denda: Rp {{ number_format($pendapatan['denda'], 0, ',', '.') }}</p>
        <p><strong>Komponen Beban:</strong> Operasional: Rp {{ number_format($beban['operasional'], 0, ',', '.') }} |
            Administrasi: Rp {{ number_format($beban['administrasi'], 0, ',', '.') }}</p>
    </div>

    <!-- Data Table -->
    <table class="data">
        <tbody>
            <!-- SHU Sebelum & Setelah Pajak -->
            <tr class="header-row">
                <td colspan="2"><strong>SHU Sebelum Pajak</strong></td>
                <td class="right"><strong>{{ number_format($shuSebelumPajak, 0, ',', '.') }}</strong></td>
            </tr>
            <tr class="header-row">
                <td colspan="2"><strong>Pajak PPh ({{ $persenPajak }}%)</strong></td>
                <td class="right"><strong>{{ number_format($pajakPPh, 0, ',', '.') }}</strong></td>
            </tr>
            <tr class="header-row">
                <td colspan="2"><strong>SHU Setelah Pajak</strong></td>
                <td class="right"><strong>{{ number_format($shuSetelahPajak, 0, ',', '.') }}</strong></td>
            </tr>

            <!-- Pembagian SHU untuk Dana-dana -->
            <tr class="section-title">
                <td colspan="3" style="padding-top: 8px; padding-bottom: 4px;">
                    <strong>PEMBAGIAN SHU UNTUK DANA-DANA</strong>
                </td>
            </tr>
            <tr>
                <td style="width: 60%;">Dana Cadangan</td>
                <td class="right" style="width: 15%;">{{ $persenDanaCadangan }} %</td>
                <td class="right" style="width: 25%;">{{ number_format($danaCadangan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Jasa Anggota</td>
                <td class="right">{{ $persenJasaAnggota }} %</td>
                <td class="right">{{ number_format($jasaAnggota, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Dana Pengurus</td>
                <td class="right">{{ $persenDanaPengurus }} %</td>
                <td class="right">{{ number_format($danaPengurus, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Dana Karyawan</td>
                <td class="right">{{ $persenDanaKaryawan }} %</td>
                <td class="right">{{ number_format($danaKaryawan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Dana Pendidikan</td>
                <td class="right">{{ $persenDanaPendidikan }} %</td>
                <td class="right">{{ number_format($danaPendidikan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Dana Sosial</td>
                <td class="right">{{ $persenDanaSosial }} %</td>
                <td class="right">{{ number_format($danaSosial, 0, ',', '.') }}</td>
            </tr>

            <!-- Pembagian SHU Anggota -->
            <tr class="section-title">
                <td colspan="3" style="padding-top: 8px; padding-bottom: 4px;">
                    <strong>PEMBAGIAN SHU ANGGOTA</strong>
                </td>
            </tr>
            <tr>
                <td>Jasa Usaha</td>
                <td class="right">{{ $persenJasaUsaha }} %</td>
                <td class="right">{{ number_format($jasaUsaha, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Jasa Modal</td>
                <td class="right">{{ $persenJasaModal }} %</td>
                <td class="right">{{ number_format($jasaModal, 0, ',', '.') }}</td>
            </tr>

            <!-- Total -->
            <tr class="total-row">
                <td><strong>Total Pendapatan Anggota</strong></td>
                <td colspan="2" class="right">
                    <strong>{{ number_format($totalPendapatanAnggota, 0, ',', '.') }}</strong>
                </td>
            </tr>
            <tr class="total-row">
                <td><strong>Total Simpanan Anggota</strong></td>
                <td colspan="2" class="right">
                    <strong>{{ number_format($totalSimpananAnggota, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Result Box -->
    <div class="result-box {{ $shuSetelahPajak >= 0 ? 'profit' : 'loss' }}">
        <h3>SHU SETELAH PAJAK</h3>
        <div class="amount {{ $shuSetelahPajak >= 0 ? 'profit' : 'loss' }}">
            {{ $shuSetelahPajak < 0 ? '(' : '' }}Rp
            {{ number_format(abs($shuSetelahPajak), 0, ',', '.') }}{{ $shuSetelahPajak < 0 ? ')' : '' }}
        </div>
    </div>

    <!-- Breakdown Summary -->
    <div
        style="border: 1px solid #000; padding: 10px; margin-top: 10px; margin-bottom: 10px; background-color: #f9f9f9;">
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 70%; border: none; padding: 3px; font-size: 8.5pt;">
                    <strong>Rincian Perhitungan SHU:</strong>
                </td>
                <td style="width: 30%; border: none; padding: 3px;"></td>
            </tr>
            <tr>
                <td style="border: none; padding: 3px; font-size: 8pt;">
                    1. Total Pendapatan
                </td>
                <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                    Rp {{ number_format($pendapatan['total'], 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 3px; font-size: 8pt;">
                    2. Total Beban
                </td>
                <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                    (Rp {{ number_format($beban['total'], 0, ',', '.') }})
                </td>
            </tr>
            <tr style="border-top: 1px solid #999;">
                <td style="border: none; padding: 3px; font-size: 8pt;">
                    <strong>SHU Sebelum Pajak (1 - 2)</strong>
                </td>
                <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                    <strong>Rp {{ number_format($shuSebelumPajak, 0, ',', '.') }}</strong>
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 3px; font-size: 8pt;">
                    3. Pajak PPh ({{ $persenPajak }}%)
                </td>
                <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                    (Rp {{ number_format($pajakPPh, 0, ',', '.') }})
                </td>
            </tr>
            <tr
                style="border-top: 2px solid #000; background-color: {{ $shuSetelahPajak >= 0 ? '#d1f2dd' : '#f8d7da' }};">
                <td style="border: none; padding: 5px; font-size: 9pt;">
                    <strong>SHU SETELAH PAJAK</strong>
                </td>
                <td style="border: none; padding: 5px; text-align: right; font-size: 10pt;">
                    <strong>
                        {{ $shuSetelahPajak < 0 ? '(' : '' }}Rp
                        {{ number_format(abs($shuSetelahPajak), 0, ',', '.') }}{{ $shuSetelahPajak < 0 ? ')' : '' }}
                    </strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Allocation Details -->
    <div style="border: 1px solid #999; padding: 8px; margin-bottom: 10px; background-color: #fafafa;">
        <h3 style="font-size: 9pt; margin: 0 0 5px 0;">Alokasi Pembagian SHU:</h3>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="border: none; padding: 2px; font-size: 7.5pt; width: 50%;">
                    • Dana Cadangan ({{ $persenDanaCadangan }}%)
                </td>
                <td style="border: none; padding: 2px; font-size: 7.5pt; text-align: right; width: 50%;">
                    Rp {{ number_format($danaCadangan, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px; font-size: 7.5pt;">
                    • Jasa Anggota ({{ $persenJasaAnggota }}%)
                </td>
                <td style="border: none; padding: 2px; font-size: 7.5pt; text-align: right;">
                    Rp {{ number_format($jasaAnggota, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 2px 2px 15px; font-size: 7pt; color: #666;">
                    ◦ Jasa Usaha ({{ $persenJasaUsaha }}% dari Jasa Anggota)
                </td>
                <td style="border: none; padding: 2px; font-size: 7pt; text-align: right; color: #666;">
                    Rp {{ number_format($jasaUsaha, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px 2px 2px 15px; font-size: 7pt; color: #666;">
                    ◦ Jasa Modal ({{ $persenJasaModal }}% dari Jasa Anggota)
                </td>
                <td style="border: none; padding: 2px; font-size: 7pt; text-align: right; color: #666;">
                    Rp {{ number_format($jasaModal, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px; font-size: 7.5pt;">
                    • Dana Pengurus ({{ $persenDanaPengurus }}%)
                </td>
                <td style="border: none; padding: 2px; font-size: 7.5pt; text-align: right;">
                    Rp {{ number_format($danaPengurus, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px; font-size: 7.5pt;">
                    • Dana Karyawan ({{ $persenDanaKaryawan }}%)
                </td>
                <td style="border: none; padding: 2px; font-size: 7.5pt; text-align: right;">
                    Rp {{ number_format($danaKaryawan, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px; font-size: 7.5pt;">
                    • Dana Pendidikan ({{ $persenDanaPendidikan }}%)
                </td>
                <td style="border: none; padding: 2px; font-size: 7.5pt; text-align: right;">
                    Rp {{ number_format($danaPendidikan, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px; font-size: 7.5pt;">
                    • Dana Sosial ({{ $persenDanaSosial }}%)
                </td>
                <td style="border: none; padding: 2px; font-size: 7.5pt; text-align: right;">
                    Rp {{ number_format($danaSosial, 0, ',', '.') }}
                </td>
            </tr>
        </table>
    </div>

</body>

</html>