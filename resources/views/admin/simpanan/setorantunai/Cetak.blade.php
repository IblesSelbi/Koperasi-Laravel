<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Setoran Tunai</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: 210mm 148mm;
            margin: 10mm;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 10pt;
            color: #000;
            line-height: 1.4;
        }

        /* Header */
        .header {
            width: 100%;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 2px solid #000;
        }

        .header table {
            width: 100%;
            border-collapse: collapse;
        }

        .header td {
            vertical-align: top;
            padding: 0;
        }

        .logo-section {
            width: 60px;
            text-align: left;
        }

        .logo-section img {
            width: 50px;
            height: auto;
        }

        .title-section {
            text-align: left;
            padding-left: 15px;
        }

        .title-section h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }

        .company-section {
            text-align: right;
            font-size: 9pt;
            width: 300px;
        }

        .company-section strong {
            font-size: 10pt;
            display: block;
            margin-bottom: 2px;
        }

        /* Content Grid */
        .content-grid {
            width: 100%;
            margin: 10px 0;
        }

        .content-grid>table {
            width: 100%;
            border-collapse: collapse;
        }

        .content-grid td {
            vertical-align: top;
            padding: 0 10px;
        }

        .left-col,
        .right-col {
            width: 50%;
        }

        .info-line {
            margin-bottom: 3px;
            font-size: 9pt;
        }

        .info-line table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-line td.label {
            width: 130px;
            padding-right: 5px;
        }

        .info-line td.value {
            text-align: left;
        }

        .section-divider {
            border-bottom: 1px dashed #666;
            margin: 8px 0;
        }

        /* Amount Box */
        .amount-box {
            margin: 10px 0;
            padding: 8px;
            border: 2px solid #000;
            text-align: center;
            background: #f9f9f9;
        }

        .amount-label {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .amount-value {
            font-size: 14pt;
            font-weight: bold;
            margin: 5px 0;
        }

        .amount-words {
            font-size: 7pt;
            font-style: italic;
            margin-top: 3px;
        }

        /* Signature Area */
        .signature-area {
            width: 100%;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #000;
        }

        .signature-area table {
            width: 100%;
            border-collapse: collapse;
        }

        .sig-box {
            text-align: center;
            font-size: 10pt;
            width: 50%;
            padding: 0 40px;
        }

        .sig-title {
            font-weight: bold;
            margin-bottom: 50px;
            font-size: 10pt;
        }

        .sig-line {
            border-top: 1px solid #000;
            padding-top: 5px;
            margin: 0 auto;
            width: 180px;
            font-size: 10pt;
        }

        /* Footer */
        .footer {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 7pt;
        }

        .footer-line {
            margin: 2px 0;
        }

        .disclaimer {
            margin-top: 5px;
            font-style: italic;
            font-size: 6.5pt;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="header">
        <table>
            <tr>
                @if($identitas && $identitas->logo)
                    <td class="logo-section">
                        <img src="{{ public_path($identitas->logo) }}" alt="Logo">
                    </td>
                @endif

                <td class="title-section">
                    <h2>BUKTI SETORAN TUNAI</h2>
                </td>

                <td class="company-section">
                    <strong>{{ $identitas->nama_lembaga ?? 'KOPERASI' }}</strong>
                    {{ $identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA' }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <table>
            <tr>
                <!-- Left Column -->
                <td class="left-col">
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Tanggal Transaksi</td>
                                <td class="value">:
                                    {{ \Carbon\Carbon::parse($setoran->tanggal_transaksi)->format('d F Y / H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Nomor Transaksi</td>
                                <td class="value">: {{ $setoran->kode_transaksi }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">ID Anggota</td>
                                <td class="value">: {{ $setoran->anggota->id_anggota ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Nama Anggota</td>
                                <td class="value">: {{ $setoran->anggota->nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Dept</td>
                                <td class="value">: {{ $setoran->anggota->departement ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    @if($setoran->nama_penyetor)
                        <div class="info-line">
                            <table>
                                <tr>
                                    <td class="label">Nama Penyetor</td>
                                    <td class="value">: {{ $setoran->nama_penyetor }}</td>
                                </tr>
                            </table>
                        </div>
                    @endif
                    @if($setoran->alamat)
                        <div class="info-line">
                            <table>
                                <tr>
                                    <td class="label">Alamat</td>
                                    <td class="value">: {{ $setoran->alamat }}</td>
                                </tr>
                            </table>
                        </div>
                    @endif
                </td>

                <!-- Right Column -->
                <td class="right-col">
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Tanggal Cetak</td>
                                <td class="value">: {{ now()->format('d F Y / H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">User Akun</td>
                                <td class="value">: {{ $setoran->user->name ?? 'admin' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Status</td>
                                <td class="value">: SUKSES</td>
                            </tr>
                        </table>
                    </div>

                    <div class="section-divider"></div>

                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Jenis Akun</td>
                                <td class="value">: {{ $setoran->jenisSimpanan->jenis_simpanan ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Masuk Kas</td>
                                <td class="value">: {{ $setoran->untukKas->nama_kas ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Amount Box -->
    <div class="amount-box">
        <div class="amount-label">Jumlah Setoran</div>
        <div class="amount-value">Rp. {{ number_format($setoran->jumlah, 0, ',', '.') }}</div>
        <div class="amount-words">Terbilang : {{ strtoupper($terbilang) }} RUPIAH</div>
    </div>

    <!-- Signature Area -->
    <div class="signature-area">
        <table>
            <tr>
                <td class="sig-box">
                    <div class="sig-title">Penyetor,</div>
                    <div class="sig-line">
                        {{ $setoran->nama_penyetor ?? $setoran->anggota->nama ?? '_______________' }}
                    </div>
                </td>

                <td class="sig-box">
                    <div class="sig-title">Petugas,</div>
                    <div class="sig-line">
                        {{ $setoran->user->name ?? 'admin' }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div class="footer-line">Ref. {{ date('Ymd_His') }}</div>
        <div class="footer-line">Informasi Hubungi Call Center : {{ $identitas->telepon ?? '0231-36387985' }}</div>
        <div class="footer-line">atau dapat diakses melalui : {{ $identitas->web ?? 'www.kingnet.id' }}</div>
        <div class="disclaimer">
            ** Tanda terima ini sah jika telah dibubuhi cap dan tanda tangan oleh Admin **
        </div>
    </div>
</body>

</html>