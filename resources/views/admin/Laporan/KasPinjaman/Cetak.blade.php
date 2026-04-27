<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kas Pinjaman</title>
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
                    <p><strong>{{ strtoupper($identitas->badan_hukum ?? 'Akeno Multimedia Solution') }}</strong></p>
                    <p>Badan Hukum: {{ $identitas->no_badan_hukum ?? 'NO.10455BH/KWK/10.20' }}</p>
                    <p>{{ strtoupper($identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>Laporan Kas Pinjaman</h1>
        <div class="periode">Periode: {{ $periodeText }}</div>
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Summary Stats -->
    <div class="summary">
        @if(isset($summary) && is_object($summary))
            <table>
                <tr>
                    <td>
                        <div class="label">Jumlah Peminjam</div>
                        <div class="value">{{ $summary->jumlah_peminjam ?? 0 }}</div>
                    </td>
                    <td>
                        <div class="label">Peminjam Lunas</div>
                        <div class="value">{{ $summary->peminjam_lunas ?? 0 }}</div>
                    </td>
                    <td>
                        <div class="label">Belum Lunas</div>
                        <div class="value">{{ $summary->belum_lunas ?? 0 }}</div>
                    </td>
                </tr>
            </table>
        @else
            <div class="text-muted">Summary tidak tersedia</div>
        @endif
    </div>


    @if(count($kasPinjaman) > 0)
        <!-- Info Box -->
        <div class="info-box">
            <h3>Informasi Laporan</h3>
            <p><strong>Periode Laporan:</strong> {{ $periodeText }}</p>
            <p><strong>Keterangan:</strong> Laporan ini menampilkan ringkasan data kas pinjaman termasuk pokok pinjaman,
                tagihan, denda, pembayaran, dan sisa tagihan.</p>
        </div>

        <!-- Data Table -->
        <table class="data">
            <thead>
                <tr>
                    <th style="width: 10%;">No</th>
                    <th style="width: 60%;">Keterangan</th>
                    <th style="width: 30%;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kasPinjaman as $item)
                    @if($item->no <= 3)
                        <tr>
                            <td class="center">{{ $item->no }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td class="right">
                                <strong>{{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @endif
                @endforeach

                <!-- Subtotal Row -->
                <tr class="subtotal-row">
                    <td class="center">-</td>
                    <td><strong>Jumlah Tagihan + Denda</strong></td>
                    <td class="right">
                        <strong>{{ number_format($jumlahTagihanDenda, 0, ',', '.') }}</strong>
                    </td>
                </tr>

                @foreach($kasPinjaman as $item)
                    @if($item->no == 4)
                        <tr>
                            <td class="center">{{ $item->no }}</td>
                            <td>{{ $item->keterangan }}</td>
                            <td class="right">
                                <strong>{{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @endif
                @endforeach

                @foreach($kasPinjaman as $item)
                    @if($item->no == 5)
                        <tr class="total-row">
                            <td class="center"><strong>{{ $item->no }}</strong></td>
                            <td><strong>{{ $item->keterangan }}</strong></td>
                            <td class="right">
                                <strong style="font-size: 10pt;">{{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <!-- Breakdown Summary -->
        <div style="border: 1px solid #000; padding: 10px; margin-bottom: 10px; background-color: #f9f9f9;">
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 70%; border: none; padding: 3px; font-size: 8.5pt;">
                        <strong>Ringkasan Perhitungan:</strong>
                    </td>
                    <td style="width: 30%; border: none; padding: 3px;"></td>
                </tr>
                @foreach($kasPinjaman as $item)
                    @if($item->no == 1)
                        <tr>
                            <td style="border: none; padding: 3px; font-size: 8pt;">
                                • {{ $item->keterangan }}
                            </td>
                            <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                @endforeach
                @foreach($kasPinjaman as $item)
                    @if($item->no == 2)
                        <tr>
                            <td style="border: none; padding: 3px; font-size: 8pt;">
                                • {{ $item->keterangan }}
                            </td>
                            <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                @endforeach
                @foreach($kasPinjaman as $item)
                    @if($item->no == 3)
                        <tr>
                            <td style="border: none; padding: 3px; font-size: 8pt;">
                                • {{ $item->keterangan }}
                            </td>
                            <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr style="border-top: 1px solid #999;">
                    <td style="border: none; padding: 3px; font-size: 8pt;">
                        <strong>Total Tagihan & Denda</strong>
                    </td>
                    <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                        <strong>Rp {{ number_format($jumlahTagihanDenda, 0, ',', '.') }}</strong>
                    </td>
                </tr>
                @foreach($kasPinjaman as $item)
                    @if($item->no == 4)
                        <tr>
                            <td style="border: none; padding: 3px; font-size: 8pt;">
                                • {{ $item->keterangan }}
                            </td>
                            <td style="border: none; padding: 3px; text-align: right; font-size: 8pt;">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                @endforeach
                <tr style="border-top: 2px solid #000; background-color: #d1f2dd;">
                    <td style="border: none; padding: 5px; font-size: 9pt;">
                        <strong>SISA TAGIHAN YANG HARUS DIBAYAR</strong>
                    </td>
                    <td style="border: none; padding: 5px; text-align: right; font-size: 10pt;">
                        @foreach($kasPinjaman as $item)
                            @if($item->no == 5)
                                <strong>Rp {{ number_format($item->jumlah, 0, ',', '.') }}</strong>
                            @endif
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>

    @else
        <!-- Empty State -->
        <div class="empty-state">
            <p>Tidak ada data pinjaman pada periode ini</p>
        </div>
    @endif

</body>

</html>