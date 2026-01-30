<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Transaksi Pembayaran Kredit</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
            line-height: 1.4;
        }

        /* Header dengan Logo */
        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
        }

        .header-left {
            display: table-cell;
            width: 80px;
            vertical-align: top;
        }

        .header-left img {
            width: 70px;
            height: auto;
        }

        .header-right {
            display: table-cell;
            vertical-align: top;
            padding-left: 15px;
        }

        .header-right h2 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .header-right p {
            font-size: 9pt;
            margin: 1px 0;
        }

        /* Title */
        .title {
            text-align: center;
            margin: 15px 0;
        }

        .title h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Info Section */
        .info-section {
            margin-bottom: 15px;
        }

        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 3px;
        }

        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-table {
            width: 100%;
            font-size: 9.5pt;
        }

        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .info-table td.label {
            width: 130px;
        }

        .info-table td.separator {
            width: 15px;
        }

        .info-table td.value {
            font-weight: normal;
        }

        .info-table td.value strong {
            font-weight: bold;
        }

        /* Detail Pembayaran Box */
        .detail-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 15px 0;
            background: #f9f9f9;
        }

        .detail-box h3 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .detail-grid {
            display: table;
            width: 100%;
        }

        .detail-col {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
        }

        .detail-item {
            margin-bottom: 5px;
            font-size: 9.5pt;
        }

        .detail-item .label {
            display: inline-block;
            width: 140px;
        }

        .detail-item .value {
            font-weight: normal;
        }

        /* Simulasi Tagihan */
        .simulasi-section {
            margin: 15px 0;
        }

        .simulasi-section h3 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        table.simulasi {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        table.simulasi thead {
            background-color: #e0e0e0;
        }

        table.simulasi th {
            border: 1px solid #000;
            padding: 6px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
        }

        table.simulasi td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 9pt;
        }

        table.simulasi td.center {
            text-align: center;
        }

        table.simulasi td.right {
            text-align: right;
        }

        table.simulasi tfoot {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        table.simulasi tfoot td {
            padding: 6px 5px;
        }

        /* Data Pembayaran */
        .pembayaran-section {
            margin: 15px 0;
        }

        .pembayaran-section h3 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 8px;
        }

        table.pembayaran {
            width: 100%;
            border-collapse: collapse;
        }

        table.pembayaran thead {
            background-color: #e0e0e0;
        }

        table.pembayaran th {
            border: 1px solid #000;
            padding: 6px 5px;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
        }

        table.pembayaran td {
            border: 1px solid #000;
            padding: 5px;
            font-size: 9pt;
        }

        table.pembayaran td.center {
            text-align: center;
        }

        table.pembayaran td.right {
            text-align: right;
        }

        table.pembayaran tfoot {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
            text-align: center;
            font-size: 8pt;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <div class="header-left">
            @php
                $identitas = \App\Models\Admin\Setting\IdentitasKoperasi::first();
            @endphp
            @if($identitas && $identitas->logo)
                <img src="{{ public_path($identitas->logo) }}" alt="Logo">
            @endif
        </div>
        <div class="header-right">
            <h2>{{ strtoupper($identitas->nama_lembaga ?? 'KOPERASI') }}</h2>
            <p>{{ strtoupper($identitas->badan_hukum ?? 'BINA TIRTA LESTARI') }}</p>
            <p>BADAN HUKUM : {{ $identitas->no_badan_hukum ?? 'NO.10455BH/KWK/10.20' }}</p>
            <p>{{ strtoupper($identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA') }}</p>
        </div>
    </div>

    <!-- Title -->
    <div class="title">
        <h1>Detail Transaksi Pembayaran Kredit</h1>
    </div>

    <!-- Info Anggota & Pinjaman -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-col">
                <table class="info-table">
                    <tr>
                        <td class="label">ID Anggota</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $pinjaman->anggota->id_anggota ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nama Anggota</td>
                        <td class="separator">:</td>
                        <td class="value"><strong>{{ $pinjaman->anggota->nama ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Dept</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $pinjaman->anggota->departement ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $pinjaman->anggota->alamat ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Nomor Pinjam</td>
                        <td class="separator">:</td>
                        <td class="value"><strong>{{ $pinjaman->kode_pinjaman }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Pinjam</td>
                        <td class="separator">:</td>
                        <td class="value">{{ \Carbon\Carbon::parse($pinjaman->tanggal_pinjam)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Tempo</td>
                        <td class="separator">:</td>
                        <td class="value">{{ \Carbon\Carbon::parse($pinjaman->tanggal_pinjam)->addMonths($pinjaman->lamaAngsuran->lama_angsuran ?? 1)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Lama Pinjam</td>
                        <td class="separator">:</td>
                        <td class="value">{{ $pinjaman->lamaAngsuran->lama_angsuran ?? '1' }} Bulan</td>
                    </tr>
                </table>
            </div>
            <div class="info-col">
                <table class="info-table">
                    <tr>
                        <td class="label">Pokok Pinjaman</td>
                        <td class="separator">:</td>
                        <td class="value">Rp. {{ number_format($pinjaman->pokok_pinjaman, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Angsuran Pokok</td>
                        <td class="separator">:</td>
                        <td class="value">Rp. {{ number_format($pinjaman->angsuran_pokok, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Biaya Admin</td>
                        <td class="separator">:</td>
                        <td class="value">Rp. {{ number_format($pinjaman->biaya_admin, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Angsuran Bunga</td>
                        <td class="separator">:</td>
                        <td class="value">Rp. {{ number_format($pinjaman->biaya_bunga, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jumlah Angsuran</td>
                        <td class="separator">:</td>
                        <td class="value"><strong>Rp. {{ number_format($pinjaman->jumlah_angsuran, 0, ',', '.') }}</strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Detail Pembayaran -->
    <div class="detail-box">
        <h3>Detail Pembayaran</h3>
        <div class="detail-grid">
            <div class="detail-col">
                <div class="detail-item">
                    <span class="label">Total Pinjaman</span>
                    <span class="value">{{ number_format($pinjaman->jumlah_angsuran, 0, ',', '.') }}</span>
                </div>
                <div class="detail-item">
                    <span class="label">Total Denda</span>
                    <span class="value">{{ number_format($pinjaman->total_denda ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="detail-item">
                    <span class="label">Total Tagihan</span>
                    <span class="value">{{ number_format($pinjaman->jumlah_angsuran + ($pinjaman->total_denda ?? 0), 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="detail-col">
                <div class="detail-item">
                    <span class="label">Status Lunas : <strong>{{ $pinjaman->status_lunas }}</strong></span>
                </div>
                <div class="detail-item">
                    <span class="label">Sudah Dibayar</span>
                    <span class="value">{{ number_format($pinjaman->total_bayar ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="detail-item">
                    <span class="label">Sisa Tagihan</span>
                    <span class="value">{{ number_format($pinjaman->sisa_tagihan ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Simulasi Tagihan -->
    <div class="simulasi-section">
        <h3>Simulasi Tagihan</h3>
        <table class="simulasi">
            <thead>
                <tr>
                    <th width="10%">Bln ke</th>
                    <th width="20%">Angsuran Pokok</th>
                    <th width="20%">Angsuran Bunga</th>
                    <th width="15%">Biaya Adm</th>
                    <th width="20%">Jumlah Angsuran</th>
                    <th width="15%">Tanggal Tempo</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $tanggalPinjam = \Carbon\Carbon::parse($pinjaman->tanggal_pinjam);
                    $lamaAngsuran = $pinjaman->lamaAngsuran->lama_angsuran ?? 1;
                @endphp
                @for($i = 1; $i <= $lamaAngsuran; $i++)
                    <tr>
                        <td class="center">{{ $i }}</td>
                        <td class="right">{{ number_format($pinjaman->angsuran_pokok, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($pinjaman->biaya_bunga, 0, ',', '.') }}</td>
                        <td class="right">{{ $i == 1 ? number_format($pinjaman->biaya_admin, 0, ',', '.') : '0' }}</td>
                        <td class="right">{{ number_format(($i == 1 ? $pinjaman->angsuran_pokok + $pinjaman->biaya_bunga + $pinjaman->biaya_admin : $pinjaman->angsuran_pokok + $pinjaman->biaya_bunga), 0, ',', '.') }}</td>
                        <td class="center">{{ $tanggalPinjam->copy()->addMonths($i)->format('d F Y') }}</td>
                    </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr>
                    <td class="center"><strong>Jumlah</strong></td>
                    <td class="right">{{ number_format($pinjaman->angsuran_pokok * $lamaAngsuran, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($pinjaman->biaya_bunga * $lamaAngsuran, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($pinjaman->biaya_admin, 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($pinjaman->jumlah_angsuran, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Data Pembayaran -->
    <div class="pembayaran-section">
        <h3>Data Pembayaran</h3>
        @if($transaksi->count() > 0)
        <table class="pembayaran">
            <thead>
                <tr>
                    <th width="5%">No.</th>
                    <th width="15%">Kode Bayar</th>
                    <th width="15%">Tanggal Bayar</th>
                    <th width="10%">Angsuran Ke</th>
                    <th width="20%">Jenis Pembayaran</th>
                    <th width="20%">Jumlah Bayar</th>
                    <th width="15%">Denda</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaksi as $index => $item)
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="center">{{ $item->kode_bayar }}</td>
                        <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_bayar)->format('d M Y') }}</td>
                        <td class="center">{{ $item->angsuran_ke }}</td>
                        <td>{{ $item->jenis_pembayaran }}</td>
                        <td class="right">{{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="right">{{ number_format($item->denda ?? 0, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="center"><strong>Jumlah</strong></td>
                    <td class="right">{{ number_format($transaksi->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                    <td class="right">{{ number_format($transaksi->sum('denda'), 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        @else
        <div class="no-data">
            Belum ada data pembayaran
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }} WIB</p>
        <p>{{ $identitas->alamat ?? 'AKENO MULTIMEDIA SOLUTION, VILA BANDUNG INDAH 40393 JAWA WEST JAVA' }}</p>
        <p>Email: {{ $identitas->email ?? 'admin@koperasi.id' }} | Website: {{ $identitas->web ?? 'www.koperasi-akeno.id' }}</p>
    </div>
</body>

</html>