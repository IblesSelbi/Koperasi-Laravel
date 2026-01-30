<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pembayaran Angsuran - {{ $data['kode_bayar'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A5 landscape;
            margin: 8mm;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            color: #000;
            line-height: 1.35;
        }

        /* Header dengan Logo */
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }

        .header-left {
            display: table-cell;
            width: 50px;
            vertical-align: top;
        }

        .header-left img {
            width: 45px;
            height: auto;
        }

        .header-right {
            display: table-cell;
            vertical-align: top;
            padding-left: 8px;
            text-align: center;
        }

        .header-right h2 {
            font-size: 11pt;
            font-weight: bold;
            margin-bottom: 1px;
        }

        .header-right p {
            font-size: 7pt;
            margin: 0;
        }

        .header-right .ref {
            font-size: 8pt;
            margin-top: 2px;
            font-weight: bold;
        }

        /* Content Table */
        .content {
            margin: 8px 0;
        }

        .info-table {
            width: 100%;
            font-size: 7.5pt;
            margin-bottom: 5px;
        }

        .info-table td {
            padding: 1px 4px;
            vertical-align: top;
        }

        .info-table td.label {
            width: 130px;
        }

        .info-table td.separator {
            width: 8px;
        }

        /* Detail Box */
        .detail-box {
            border: 1px solid #000;
            padding: 5px;
            margin: 5px 0;
            background: #f9f9f9;
        }

        .detail-box table {
            width: 100%;
            font-size: 7.5pt;
        }

        .detail-box td {
            padding: 1px 0;
        }

        .detail-box td.label {
            width: 140px;
        }

        .detail-box .total-row {
            border-top: 1px solid #000;
            font-weight: bold;
            padding-top: 2px !important;
        }

        /* Amount Box */
        .amount-box {
            text-align: center;
            border: 2px solid #0d6efd;
            padding: 4px;
            margin: 5px 0;
            background: #cfe2ff;
        }

        .amount-box .label {
            font-size: 7pt;
            font-weight: bold;
            color: #084298;
        }

        .amount-box .value {
            font-size: 11pt;
            font-weight: bold;
            margin: 2px 0;
            color: #084298;
        }

        .amount-box .words {
            font-size: 6.5pt;
            font-style: italic;
            color: #084298;
        }

        /* Status Badge */
        .status-paid {
            display: inline-block;
            background: #0d6efd;
            color: #fff;
            padding: 2px 8px;
            border-radius: 3px;
            font-weight: bold;
            font-size: 8pt;
        }

        /* Signature */
        .signature {
            margin-top: 6px;
            border-top: 1px solid #000;
            padding-top: 4px;
        }

        .signature table {
            width: 100%;
            text-align: center;
            font-size: 7.5pt;
        }

        .signature .sig-title {
            font-weight: bold;
            padding-bottom: 60px;
        }

        .signature .sig-name {
            border-top: 1px solid #000;
            display: inline-block;
            min-width: 100px;
            padding-top: 2px;
        }

        /* Footer */
        .footer {
            margin-top: 13px;
            border-top: 1px solid #000;
            padding-top: 4px;
            text-align: center;
            font-size: 6pt;
        }

        .footer p {
            margin: 3px 0;
        }

        /* Paid Stamp */
        .paid-stamp {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            border: 4px solid #0d6efd;
            color: #0d6efd;
            font-size: 36pt;
            font-weight: bold;
            padding: 10px 30px;
            opacity: 0.15;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <!-- Watermark PAID -->
    <div class="paid-stamp">PAID</div>

    <!-- Header dengan Logo -->
    <div class="header">
        <div class="header-left">
            @if($identitas && $identitas->logo)
                <img src="{{ public_path($identitas->logo) }}" alt="Logo">
            @endif
        </div>
        <div class="header-right">
            <h2>NOTA PEMBAYARAN ANGSURAN</h2>
            <p>{{ $identitas->nama_lembaga ?? 'KOPERASI' }}</p>
            <p>{{ $identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA' }} Tel. {{ $identitas->telepon ?? '0231-36387985' }}</p>
            <p class="ref">Ref. {{ $data['kode_bayar'] }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <table class="info-table">
            <tr>
                <td class="label">Kode Pembayaran</td>
                <td class="separator">:</td>
                <td><strong>{{ $data['kode_bayar'] }}</strong></td>
                <td class="label">Tanggal Bayar</td>
                <td class="separator">:</td>
                <td><strong>{{ \Carbon\Carbon::parse($data['tanggal_bayar'])->translatedFormat('d F Y H:i') }}</strong></td>
            </tr>
            <tr>
                <td class="label">Kode Pinjaman</td>
                <td class="separator">:</td>
                <td>{{ $data['kode_pinjaman'] }}</td>
                <td class="label">Angsuran Ke</td>
                <td class="separator">:</td>
                <td>
                    <strong>{{ $data['angsuran_ke'] }} dari {{ $data['lama_pinjaman'] }}</strong>
                </td>
            </tr>
            <tr>
                <td class="label">ID Anggota</td>
                <td class="separator">:</td>
                <td>{{ $data['id_anggota'] }}</td>
                <td class="label">Status</td>
                <td class="separator">:</td>
                <td>
                    <span class="status-paid">✓ LUNAS</span>
                </td>
            </tr>
            <tr>
                <td class="label">Nama Anggota</td>
                <td class="separator">:</td>
                <td><strong>{{ $data['nama_anggota'] }}</strong></td>
                <td class="label">User Kasir</td>
                <td class="separator">:</td>
                <td>{{ $data['user'] }}</td>
            </tr>
            <tr>
                <td class="label">Dept.</td>
                <td class="separator">:</td>
                <td>{{ $data['departemen'] }}</td>
                <td class="label">Kas</td>
                <td class="separator">:</td>
                <td>{{ $data['nama_kas'] }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pinjam</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($data['tanggal_pinjam'])->translatedFormat('d F Y') }}</td>
                <td class="label">Pokok Pinjaman</td>
                <td class="separator">:</td>
                <td>Rp {{ number_format($data['pokok_pinjaman'], 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- Amount Box -->
        <div class="amount-box">
            <div class="label">Total Pembayaran Angsuran Ke-{{ $data['angsuran_ke'] }}</div>
            <div class="value">Rp. {{ number_format($data['total_bayar'], 0, ',', '.') }}</div>
            <div class="words">TERBILANG : {{ strtoupper($terbilang) }} RUPIAH</div>
        </div>

        <!-- Detail Perhitungan -->
        <div class="detail-box">
            <table>
                <tr>
                    <td class="label">Angsuran Pokok</td>
                    <td>: Rp {{ number_format($data['angsuran_pokok'], 0, ',', '.') }}</td>
                    <td class="label">Angsuran Bunga</td>
                    <td>: Rp {{ number_format($data['angsuran_bunga'], 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Biaya Admin</td>
                    <td>: Rp {{ number_format($data['biaya_admin'], 0, ',', '.') }}</td>
                    <td class="label">Denda Keterlambatan</td>
                    <td>: Rp {{ number_format($data['denda'], 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">Total yang Dibayar</td>
                    <td colspan="3">: <strong>Rp {{ number_format($data['total_bayar'], 0, ',', '.') }}</strong></td>
                </tr>
                <tr style="border-top: 2px solid #0d6efd; background: #cfe2ff;">
                    <td colspan="2" class="label"><strong style="color: #084298;">STATUS PEMBAYARAN</strong></td>
                    <td colspan="2"><strong style="color: #084298; font-size: 9pt;">✓ LUNAS</strong></td>
                </tr>
            </table>
        </div>

        <!-- Signature -->
        <div class="signature">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <div class="sig-title">Peminjam,</div>
                        <div class="sig-name">{{ $data['nama_anggota'] }}</div>
                    </td>
                    <td style="width: 50%;">
                        <div class="sig-title">Kasir,</div>
                        <div class="sig-name">{{ $data['user'] }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>{{ $identitas->alamat ?? 'AKENO MULTIMEDIA SOLUTION, VILA BANDUNG INDAH 40393 JAWA WEST JAVA' }}, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Email: {{ $identitas->email ?? 'admin@koperasi.id' }} | Website: {{ $identitas->web ?? 'www.koperasi-akeno.id' }}</p>
        <p style="font-style: italic; margin-top: 1px; color: #084298;">** TERIMA KASIH ATAS PEMBAYARAN ANGSURAN ANDA **</p>
    </div>
</body>

</html>