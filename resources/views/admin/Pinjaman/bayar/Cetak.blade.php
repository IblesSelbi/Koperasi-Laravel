<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran Angsuran</title>
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
            margin-bottom: 8px;
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

        /* Badge */
        .badge {
            display: inline-block;
            padding: 1px 6px;
            border-radius: 2px;
            font-size: 7pt;
            font-weight: bold;
        }

        .badge-lunas {
            background: #28a745;
            color: #fff;
        }

        .badge-pending {
            background: #ffc107;
            color: #000;
        }

        .badge-approved {
            background: #28a745;
            color: #fff;
        }

        /* Amount Box */
        .amount-box {
            text-align: center;
            border: 2px solid #000;
            padding: 6px;
            margin: 8px 0;
            background: #f5f5f5;
        }

        .amount-box .label {
            font-size: 7pt;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .amount-box .value {
            font-size: 12pt;
            font-weight: bold;
            margin: 3px 0;
        }

        .amount-box .words {
            font-size: 6.5pt;
            font-style: italic;
            margin-top: 2px;
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

        /* Signature */
        .signature {
            margin-top: 8px;
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
            padding-bottom: 50px;
        }

        .signature .sig-name {
            border-top: 1px solid #000;
            display: inline-block;
            min-width: 100px;
            padding-top: 2px;
        }

        /* Footer */
        .footer {
            margin-top: 8px;
            border-top: 1px solid #000;
            padding-top: 4px;
            text-align: center;
            font-size: 6pt;
        }

        .footer p {
            margin: 1px 0;
        }
    </style>
</head>
<body>
    <!-- Header dengan Logo -->
    <div class="header">
        <div class="header-left">
            @if($identitas && $identitas->logo && file_exists(public_path($identitas->logo)))
                <img src="{{ public_path($identitas->logo) }}" alt="Logo">
            @endif
        </div>
        <div class="header-right">
            <h2>BUKTI PEMBAYARAN ANGSURAN</h2>
            <p>{{ $identitas->nama_lembaga ?? 'KOPERASI SIMPAN PINJAM' }}</p>
            <p>{{ $identitas->alamat ?? '-' }} Tel. {{ $identitas->telepon ?? '-' }}</p>
            <p class="ref">Ref. {{ $data['kode_bayar'] }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <table class="info-table">
            <tr>
                <td class="label">Tanggal Bayar</td>
                <td class="separator">:</td>
                <td>{{ $data['tanggal_bayar']->format('d F Y / H:i') }}</td>
                <td class="label">Tanggal Cetak</td>
                <td class="separator">:</td>
                <td>{{ now()->format('d F Y / H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Kode Bayar</td>
                <td class="separator">:</td>
                <td><strong>{{ $data['kode_bayar'] }}</strong></td>
                <td class="label">Kasir</td>
                <td class="separator">:</td>
                <td>{{ $data['user'] }}</td>
            </tr>
            <tr>
                <td class="label">Kode Pinjaman</td>
                <td class="separator">:</td>
                <td>{{ $data['kode_pinjaman'] }}</td>
                <td class="label">Status</td>
                <td class="separator">:</td>
                <td>
                    @if($data['status_verifikasi'] == 'approved')
                        <span class="badge badge-approved">LUNAS</span>
                    @elseif($data['status_verifikasi'] == 'pending')
                        <span class="badge badge-pending">PENDING</span>
                    @else
                        <span class="badge badge-lunas">{{ strtoupper($data['status_verifikasi']) }}</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Angsuran Ke</td>
                <td class="separator">:</td>
                <td>{{ $data['angsuran_ke'] }} dari {{ $data['lama_pinjaman'] }}</td>
                <td class="label">Kas Tujuan</td>
                <td class="separator">:</td>
                <td>{{ $data['nama_kas'] }}</td>
            </tr>
            <tr>
                <td class="label">ID Anggota</td>
                <td class="separator">:</td>
                <td>{{ $data['id_anggota'] }}</td>
                <td class="label">Metode Bayar</td>
                <td class="separator">:</td>
                <td>
                    @if($data['bukti_transfer'])
                        Transfer Bank
                    @else
                        Tunai
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Nama Anggota</td>
                <td class="separator">:</td>
                <td><strong>{{ $data['nama_anggota'] }}</strong></td>
                <td class="label"></td>
                <td class="separator"></td>
                <td></td>
            </tr>
            <tr>
                <td class="label">Departemen</td>
                <td class="separator">:</td>
                <td>{{ $data['departemen'] }}</td>
                <td class="label"></td>
                <td class="separator"></td>
                <td></td>
            </tr>
        </table>

        <!-- Amount Box -->
        <div class="amount-box">
            <div class="label">TOTAL PEMBAYARAN</div>
            <div class="value">Rp. {{ number_format($data['total_bayar'], 0, ',', '.') }}</div>
            <div class="words">TERBILANG : {{ strtoupper($terbilang) }} RUPIAH</div>
        </div>

        <!-- Detail Pembayaran -->
        <div class="detail-box">
            <table>
                <tr>
                    <td class="label">Angsuran Pokok</td>
                    <td>: Rp {{ number_format($data['angsuran_pokok'], 0, ',', '.') }}</td>
                    <td class="label">Biaya & Bunga</td>
                    <td>: Rp {{ number_format($data['angsuran_bunga'], 0, ',', '.') }}</td>
                </tr>
                @if($data['denda'] > 0)
                <tr>
                    <td class="label">Denda Keterlambatan</td>
                    <td>: Rp {{ number_format($data['denda'], 0, ',', '.') }}</td>
                    <td class="label">Biaya Admin</td>
                    <td>: Rp {{ number_format($data['biaya_admin'], 0, ',', '.') }}</td>
                </tr>
                @else
                <tr>
                    <td class="label">Biaya Admin</td>
                    <td>: Rp {{ number_format($data['biaya_admin'], 0, ',', '.') }}</td>
                    <td class="label"></td>
                    <td></td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label"><strong>TOTAL DIBAYAR</strong></td>
                    <td><strong>: Rp {{ number_format($data['total_bayar'], 0, ',', '.') }}</strong></td>
                    <td class="label"></td>
                    <td></td>
                </tr>
            </table>
        </div>

        <!-- Info Pinjaman -->
        <div class="detail-box">
            <table>
                <tr>
                    <td class="label">Pokok Pinjaman</td>
                    <td>: Rp {{ number_format($data['pokok_pinjaman'], 0, ',', '.') }}</td>
                    <td class="label">Tanggal Pinjam</td>
                    <td>: {{ \Carbon\Carbon::parse($data['tanggal_pinjam'])->format('d F Y') }}</td>
                </tr>
                @if($data['keterangan'])
                <tr>
                    <td class="label">Keterangan</td>
                    <td colspan="3">: {{ $data['keterangan'] }}</td>
                </tr>
                @endif
            </table>
        </div>

        <!-- Signature -->
        <div class="signature">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <div class="sig-title">Peminjam / Anggota,</div>
                        <div class="sig-name">{{ $data['nama_anggota'] }}</div>
                    </td>
                    <td style="width: 50%;">
                        <div class="sig-title">Petugas Kasir,</div>
                        <div class="sig-name">{{ $data['user'] }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>{{ $identitas->alamat ?? '-' }}, {{ now()->format('d F Y') }}</p>
        <p>Email: {{ $identitas->email ?? '-' }} | Website: {{ $identitas->web ?? '-' }}</p>
        <p style="font-style: italic; margin-top: 2px;">** Tanda terima ini sah jika telah dibubuhi cap dan tanda tangan oleh pihak yang berwenang **</p>
    </div>
</body>
</html>