<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pengajuan Pinjaman</title>
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

        .content-grid > table {
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

        /* Keterangan Box */
        .keterangan-box {
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #666;
            background: #fff;
            font-size: 8pt;
        }

        .keterangan-box strong {
            display: block;
            margin-bottom: 3px;
            font-size: 9pt;
        }

        /* Signature Area */
        .signature-area {
            width: 100%;
            margin-top: 15px;
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

        /* Badge */
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border: 1px solid #000;
            font-size: 8pt;
            font-weight: bold;
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
                    <h2>BUKTI PENGAJUAN PINJAMAN</h2>
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
                                <td class="label">Tanggal Pengajuan</td>
                                <td class="value">: {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d F Y / H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Nomor Pengajuan</td>
                                <td class="value">: {{ $pengajuan->id_ajuan }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">ID Anggota</td>
                                <td class="value">: {{ $pengajuan->anggota->id_anggota ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Nama Anggota</td>
                                <td class="value">: {{ $pengajuan->anggota->nama ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Dept</td>
                                <td class="value">: {{ $pengajuan->anggota->departement ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Jabatan</td>
                                <td class="value">: {{ $pengajuan->anggota->jabatan ?? 'Anggota' }}</td>
                            </tr>
                        </table>
                    </div>
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
                                <td class="label">User Pemohon</td>
                                <td class="value">: {{ $pengajuan->user->name ?? 'User' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Status</td>
                                <td class="value">: 
                                    @if($pengajuan->status == 0)
                                        <span class="badge">MENUNGGU</span>
                                    @elseif($pengajuan->status == 1)
                                        <span class="badge">DISETUJUI</span>
                                    @elseif($pengajuan->status == 2)
                                        <span class="badge">DITOLAK</span>
                                    @elseif($pengajuan->status == 3)
                                        <span class="badge">TERLAKSANA</span>
                                    @else
                                        <span class="badge">DIBATALKAN</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="section-divider"></div>

                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Jenis Pinjaman</td>
                                <td class="value">: {{ $pengajuan->jenis_pinjaman }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Lama Angsuran</td>
                                <td class="value">: {{ $pengajuan->lamaAngsuran->lama_angsuran ?? 0 }} Bulan</td>
                            </tr>
                        </table>
                    </div>
                    @if($pengajuan->tanggal_cair)
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Tanggal Pencairan</td>
                                <td class="value">: {{ \Carbon\Carbon::parse($pengajuan->tanggal_cair)->format('d F Y') }}</td>
                            </tr>
                        </table>
                    </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <!-- Amount Box -->
    <div class="amount-box">
        <div class="amount-label">Jumlah Pengajuan</div>
        <div class="amount-value">Rp. {{ number_format($pengajuan->jumlah, 0, ',', '.') }}</div>
        <div class="amount-words">Terbilang : {{ strtoupper($terbilang) }} RUPIAH</div>
    </div>

    <!-- Keterangan -->
    @if($pengajuan->keterangan)
    <div class="keterangan-box">
        <strong>Keterangan / Tujuan:</strong>
        {{ $pengajuan->keterangan }}
    </div>
    @endif

    @if($pengajuan->alasan)
    <div class="keterangan-box">
        <strong>Alasan / Catatan Admin:</strong>
        {{ $pengajuan->alasan }}
    </div>
    @endif

    <!-- Signature Area -->
    <div class="signature-area">
        <table>
            <tr>
                <td class="sig-box">
                    <div class="sig-title">Pemohon,</div>
                    <div class="sig-line">
                        {{ $pengajuan->anggota->nama ?? '_______________' }}
                    </div>
                </td>

                <td class="sig-box">
                    <div class="sig-title">Mengetahui,</div>
                    <div class="sig-line">
                        _________________
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
            ** Dokumen ini adalah bukti pengajuan pinjaman yang sah **
        </div>
    </div>
</body>

</html>