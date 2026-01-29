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
            margin: 6mm;
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

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 9pt;
            font-weight: bold;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-approved {
            background-color: #28a745;
            color: #fff;
        }

        .status-rejected {
            background-color: #dc3545;
            color: #fff;
        }

        .status-done {
            background-color: #17a2b8;
            color: #fff;
        }

        .status-cancelled {
            background-color: #6c757d;
            color: #fff;
        }

        /* Keterangan Box */
        .keterangan-box {
            margin: 10px 0;
            padding: 8px;
            border: 1px solid #999;
            background: #f5f5f5;
            font-size: 9pt;
        }

        .keterangan-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Signature Area */
        .signature-area {
            width: 100%;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid #000;
        }


        .signature-area table {
            width: 100%;
            border-collapse: collapse;
        }

        .sig-box {
            text-align: center;
            font-size: 10pt;
            width: 33.33%;
            padding: 0 20px;
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
            width: 150px;
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
                                <td class="value">:
                                    {{ \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d F Y / H:i') }}
                                </td>
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
                                <td class="value">: {{ $pengajuan->jumlah_angsuran }} Bulan</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Angsuran/Bulan</td>
                                <td class="value">: Rp
                                    {{ number_format($pengajuan->jumlah / $pengajuan->jumlah_angsuran, 0, ',', '.') }}
                                </td>
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
                                <td class="label">User Pengaju</td>
                                <td class="value">: {{ $pengajuan->user->name ?? 'admin' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="info-line">
                        <table>
                            <tr>
                                <td class="label">Status</td>
                                <td class="value">:
                                    @if($pengajuan->status == 0)
                                        <span class="status-badge status-pending">MENUNGGU</span>
                                    @elseif($pengajuan->status == 1)
                                        <span class="status-badge status-approved">DISETUJUI</span>
                                    @elseif($pengajuan->status == 2)
                                        <span class="status-badge status-rejected">DITOLAK</span>
                                    @elseif($pengajuan->status == 3)
                                        <span class="status-badge status-done">TERLAKSANA</span>
                                    @else
                                        <span class="status-badge status-cancelled">BATAL</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>

                    @if($pengajuan->tanggal_cair)
                        <div class="info-line">
                            <table>
                                <tr>
                                    <td class="label">Tanggal Cair</td>
                                    <td class="value">:
                                        {{ \Carbon\Carbon::parse($pengajuan->tanggal_cair)->format('d F Y') }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    @endif

                    @if($pengajuan->approvedBy)
                        <div class="info-line">
                            <table>
                                <tr>
                                    <td class="label">Disetujui Oleh</td>
                                    <td class="value">: {{ $pengajuan->approvedBy->name ?? '-' }}</td>
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
        <div class="amount-label">Jumlah Pinjaman</div>
        <div class="amount-value">Rp. {{ number_format($pengajuan->jumlah, 0, ',', '.') }}</div>
        <div class="amount-words">Terbilang : {{ strtoupper($terbilang) }} RUPIAH</div>
    </div>

    <!-- Keterangan -->
    @if($pengajuan->keterangan)
        <div class="keterangan-box">
            <div class="keterangan-label">Keterangan / Tujuan Pinjaman:</div>
            <div>{{ $pengajuan->keterangan }}</div>
        </div>
    @endif

    @if($pengajuan->alasan)
        <div class="keterangan-box">
            <div class="keterangan-label">Alasan / Catatan:</div>
            <div>{{ $pengajuan->alasan }}</div>
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
                        {{ $pengajuan->approvedBy->name ?? '_______________' }}
                    </div>
                </td>

                <td class="sig-box">
                    <div class="sig-title">Petugas,</div>
                    <div class="sig-line">
                        {{ $pengajuan->user->name ?? 'admin' }}
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
            ** Dokumen ini sah jika telah dibubuhi cap dan tanda tangan oleh pihak yang berwenang **
        </div>
    </div>
</body>

</html>