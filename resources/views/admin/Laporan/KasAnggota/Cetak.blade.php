<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Kas Anggota</title>
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
            background-color: #6f6f6f;
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

        /* Simple Badges */
        .badge {
            display: inline-block;
            padding: 1px 4px;
            border: 1px solid #000;
            font-size: 6.5pt;
            font-weight: bold;
        }

        .badge-success {
            background-color: #d4edda;
        }

        .badge-danger {
            background-color: #f8d7da;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                <td style="width: 60px;">
                    @php
                        $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();
                    @endphp
                    @if($identitas && $identitas->logo)
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
        <h1>Laporan Kas Anggota Koperasi</h1>
        <div class="date">Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }} WIB</div>
    </div>

    <!-- Stats -->
    @php
        $totalAnggota = $kasAnggota->count();
        $totalSimpananSukarela = $kasAnggota->sum('simpanan.sukarela');
        $totalSimpananPokok = $kasAnggota->sum('simpanan.pokok');
        $totalSimpananWajib = $kasAnggota->sum('simpanan.wajib');
        $totalPokokPinjaman = $kasAnggota->sum('kredit.pokok_pinjaman');
        $totalSisaTagihan = $kasAnggota->sum('kredit.sisa_tagihan');
        $anggotaMacet = $kasAnggota->where('keterangan.status_pembayaran', 'Macet')->count();
        $anggotaLancar = $kasAnggota->where('keterangan.status_pembayaran', 'Lancar')->count();
    @endphp

    <div class="stats">
        <table>
            <tr>
                <td>
                    <div class="label">Total Anggota</div>
                    <div class="value">{{ $totalAnggota }}</div>
                </td>
                <td>
                    <div class="label">Total Simpanan</div>
                    <div class="value">
                        {{ number_format($totalSimpananPokok + $totalSimpananWajib + $totalSimpananSukarela, 0, ',', '.') }}
                    </div>
                </td>
                <td>
                    <div class="label">Total Pinjaman</div>
                    <div class="value">{{ number_format($totalPokokPinjaman, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Sisa Tagihan</div>
                    <div class="value">{{ number_format($totalSisaTagihan, 0, ',', '.') }}</div>
                </td>
                <td>
                    <div class="label">Status Bayar</div>
                    <div class="value">{{ $anggotaLancar }} / {{ $anggotaMacet }}</div>
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
                        Anggota: <strong>{{ $filterInfo['anggota'] }}</strong> |
                        Status: <strong>{{ $filterInfo['status'] }}</strong> |
                        Jabatan: <strong>{{ $filterInfo['jabatan'] }}</strong>
                    </div>
                </td>
                <td style="width: 35%; text-align: center;">
                    <div class="title-small">Ringkasan</div>
                    <div style="font-size: 8pt; font-weight: bold; margin-top: 2px;">
                        Anggota: {{ $totalAnggota }} | Lancar: {{ $anggotaLancar }} | Macet: {{ $anggotaMacet }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Data Table -->
    <table class="data">
        <thead>
            <tr>
                <th rowspan="2" style="width: 18px;">No</th>
                <th rowspan="2" style="width: 45px;">ID</th>
                <th rowspan="2" style="width: 85px;">Nama</th>
                <th rowspan="2" style="width: 18px;">JK</th>
                <th rowspan="2" style="width: 50px;">Jabatan</th>
                <th rowspan="2" style="width: 55px;">Dept</th>
                <th colspan="4" style="background-color: #333;">Simpanan (Rp)</th>
                <th colspan="4" style="background-color: #333;">Kredit/Pinjaman (Rp)</th>
                <th colspan="4" style="background-color: #333;">Keterangan</th>
            </tr>
            <tr>
                <!-- Simpanan -->
                <th style="width: 50px;">Sukarela</th>
                <th style="width: 50px;">Pokok</th>
                <th style="width: 50px;">Wajib</th>
                <th style="width: 50px;">Lainnya</th>
                <!-- Kredit -->
                <th style="width: 55px;">Pokok</th>
                <th style="width: 55px;">Tagihan</th>
                <th style="width: 55px;">Dibayar</th>
                <th style="width: 55px;">Sisa</th>
                <!-- Keterangan -->
                <th style="width: 30px;">Jml</th>
                <th style="width: 30px;">Lunas</th>
                <th style="width: 40px;">Status</th>
                <th style="width: 50px;">Tempo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($kasAnggota as $index => $item)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td class="center"><strong>{{ $item->id_anggota }}</strong></td>
                    <td>
                        <strong>{{ $item->nama }}</strong><br>
                        <span style="font-size: 6pt; color: #666;">{{ $item->departemen }}</span>
                    </td>
                    <td class="center">
                        <span class="badge">{{ $item->jenis_kelamin == 'Laki-laki' ? 'L' : 'P' }}</span>
                    </td>
                    <td class="center">
                        <span class="badge">{{ $item->jabatan }}</span>
                    </td>
                    <td class="center" style="font-size: 6.5pt;">{{ $item->departemen }}</td>

                    <!-- Simpanan -->
                    <td class="right">{{ number_format($item->simpanan['sukarela'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->simpanan['pokok'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->simpanan['wajib'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->simpanan['lainnya'], 0, ',', '.') }}</td>

                    <!-- Kredit -->
                    <td class="right">{{ number_format($item->kredit['pokok_pinjaman'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->kredit['tagihan_denda'], 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($item->kredit['dibayar'], 0, ',', '.') }}</td>
                    <td class="right" style="font-weight: bold;">
                        {{ number_format($item->kredit['sisa_tagihan'], 0, ',', '.') }}
                    </td>

                    <!-- Keterangan -->
                    <td class="center">{{ $item->keterangan['jumlah_pinjaman'] }}</td>
                    <td class="center">{{ $item->keterangan['pinjaman_lunas'] }}</td>
                    <td class="center">
                        <span
                            class="badge {{ $item->keterangan['status_pembayaran'] == 'Lancar' ? 'badge-success' : 'badge-danger' }}">
                            {{ $item->keterangan['status_pembayaran'] }}
                        </span>
                    </td>
                    <td class="center" style="font-size: 6.5pt;">{{ $item->keterangan['tanggal_tempo'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="18" class="center" style="padding: 15px;">Tidak ada data kas anggota</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="right"><strong>TOTAL:</strong></td>
                <td class="right"><strong>{{ number_format($totalSimpananSukarela, 0, ',', '.') }}</strong></td>
                <td class="right"><strong>{{ number_format($totalSimpananPokok, 0, ',', '.') }}</strong></td>
                <td class="right"><strong>{{ number_format($totalSimpananWajib, 0, ',', '.') }}</strong></td>
                <td class="right">
                    <strong>{{ number_format($kasAnggota->sum('simpanan.lainnya'), 0, ',', '.') }}</strong>
                </td>
                <td class="right"><strong>{{ number_format($totalPokokPinjaman, 0, ',', '.') }}</strong></td>
                <td class="right">
                    <strong>{{ number_format($kasAnggota->sum('kredit.tagihan_denda'), 0, ',', '.') }}</strong>
                </td>
                <td class="right"><strong>{{ number_format($kasAnggota->sum('kredit.dibayar'), 0, ',', '.') }}</strong>
                </td>
                <td class="right"><strong>{{ number_format($totalSisaTagihan, 0, ',', '.') }}</strong></td>
                <td colspan="4" class="center">
                    <strong>TOTAL ANGGOTA: {{ $totalAnggota }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

</body>

</html>