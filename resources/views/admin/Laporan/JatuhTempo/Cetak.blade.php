<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Jatuh Tempo</title>
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
            padding: 4px 3px;
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

        .badge-danger {
            background-color: #f8d7da;
        }

        .badge-warning {
            background-color: #fff3cd;
        }

        .badge-info {
            background-color: #cff4fc;
        }

        .badge-secondary {
            background-color: #e2e3e5;
        }

        /* Status Tempo */
        .status-lewat {
            color: #dc3545;
            font-weight: bold;
        }

        .status-dekat {
            color: #ffc107;
            font-weight: bold;
        }

        .status-normal {
            color: #0dcaf0;
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
        <h1>Laporan Jatuh Tempo Pembayaran Kredit</h1>
        <div class="periode">Periode: {{ $periodeTampil }}</div>
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Stats -->
    <div class="stats">
        <table>
            <tr>
                <td>
                    <div class="label">Total Data</div>
                    <div class="value">{{ $jatuhTempo->count() }}</div>
                </td>
                <td>
                    <div class="label">Total Tagihan</div>
                    <div class="value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Total Dibayar</div>
                    <div class="value">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Sisa Tagihan</div>
                    <div class="value" style="color: #dc3545;">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Persentase Bayar</div>
                    @php
                        $persentase = $totalTagihan > 0 ? ($totalDibayar / $totalTagihan) * 100 : 0;
                    @endphp
                    <div class="value">{{ number_format($persentase, 1) }}%</div>
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
                    Laporan ini menampilkan data pinjaman yang akan jatuh tempo pada periode {{ $periodeTampil }}.
                    Data mencakup {{ $jatuhTempo->count() }} pinjaman dengan total tagihan Rp
                    {{ number_format($totalTagihan, 0, ',', '.') }}.
                </td>
                <td style="width: 30%; text-align: right;">
                    <strong>Status Tempo:</strong>
                    <span class="status-lewat">Lewat Tempo</span> |
                    <span class="status-dekat">Segera Tempo (≤7 hari)</span> |
                    <span class="status-normal">Normal</span>
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data">
        <thead>
            <tr>
                <th style="width: 3%;">No</th>
                <th style="width: 8%;">Kode Pinjam</th>
                <th style="width: 7%;">ID Anggota</th>
                <th style="width: 15%;">Nama Anggota</th>
                <th style="width: 8%;">Tgl Pinjam</th>
                <th style="width: 9%;">Tgl Tempo</th>
                <th style="width: 7%;">Status</th>
                <th style="width: 6%;">Lama</th>
                <th style="width: 5%;">Ang Ke</th>
                <th style="width: 11%;">Jml Tagihan</th>
                <th style="width: 10%;">Dibayar</th>
                <th style="width: 11%;">Sisa Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jatuhTempo as $index => $item)
                @php
                    $diff = (int) $item->selisih_hari;

                    if ($diff < 0) {
                        $statusClass = 'status-lewat';
                        $statusText = 'Lewat ' . abs($diff) . ' hari';
                        $badgeClass = 'badge-danger';
                    } elseif ($diff <= 7) {
                        $statusClass = 'status-dekat';
                        $statusText = $diff . ' hari lagi';
                        $badgeClass = 'badge-warning';
                    } else {
                        $statusClass = 'status-normal';
                        $statusText = $diff . ' hari lagi';
                        $badgeClass = 'badge-info';
                    }
                @endphp

                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center">
                        <span class="badge badge-primary">{{ $item->kode_pinjam }}</span>
                    </td>
                    <td class="center">{{ $item->id_anggota }}</td>
                    <td><strong>{{ $item->nama_anggota }}</strong></td>
                    <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}</td>
                    <td class="center">
                        {{ \Carbon\Carbon::parse($item->tanggal_tempo)->format('d/m/Y') }}
                    </td>
                    <td class="center {{ $statusClass }}">
                        <span class="badge {{ $badgeClass }}">{{ $statusText }}</span>
                    </td>
                    <td class="center">
                        <span class="badge badge-secondary">{{ $item->lama_pinjam }} Bln</span>
                    </td>
                    <td class="center">{{ $item->angsuran_ke }}</td>
                    <td class="right"><strong>{{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</strong></td>
                    <td class="right">{{ number_format($item->dibayar, 0, ',', '.') }}</td>
                    <td class="right" style="font-weight: bold; color: #dc3545;">
                        {{ number_format($item->sisa_tagihan, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="12" class="center" style="padding: 20px;">
                        Tidak ada pinjaman yang jatuh tempo pada periode ini
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if($jatuhTempo->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="9" class="right"><strong>TOTAL:</strong></td>
                    <td class="right"><strong>{{ number_format($totalTagihan, 0, ',', '.') }}</strong></td>
                    <td class="right"><strong>{{ number_format($totalDibayar, 0, ',', '.') }}</strong></td>
                    <td class="right" style="color: #dc3545;">
                        <strong>{{ number_format($sisaTagihan, 0, ',', '.') }}</strong>
                    </td>
                </tr>
                <tr>
                    <td colspan="9" class="right"><strong>PERSENTASE PEMBAYARAN:</strong></td>
                    <td colspan="3" class="center">
                        <strong>{{ number_format($persentase, 2) }}% dari total tagihan</strong>
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>

    @if($jatuhTempo->count() > 0)
        <!-- Footer Info -->
        <div style="margin-top: 10px; padding: 5px; border: 1px solid #000; font-size: 7pt;">
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%; padding: 3px;">
                        <strong>Catatan:</strong><br>
                        - Data pinjaman yang ditampilkan adalah pinjaman dengan status belum lunas<br>
                        - Angsuran yang ditampilkan adalah yang belum dibayar pada periode {{ $periodeTampil }}
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