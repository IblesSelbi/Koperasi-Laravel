<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pinjaman Anggota</title>
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
            border: 2px solid #000;
            padding: 4px;
            margin: 5px 0;
            background: #f5f5f5;
        }

        .amount-box .label {
            font-size: 7pt;
            font-weight: bold;
        }

        .amount-box .value {
            font-size: 11pt;
            font-weight: bold;
            margin: 2px 0;
        }

        .amount-box .words {
            font-size: 6.5pt;
            font-style: italic;
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

        /* Status Badge */
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

        .badge-belum {
            background: #dc3545;
            color: #fff;
        }
    </style>
</head>

<body>
    <!-- Header dengan Logo -->
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
            <h2>BUKTI PINJAMAN ANGGOTA</h2>
            <p>{{ $identitas->nama_lembaga ?? 'KOPERASI' }}</p>
            <p>{{ $identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA' }} Tel. {{ $identitas->telepon ?? '0231-36387985' }}</p>
            <p class="ref">Ref. {{ $pinjaman->kode_pinjaman }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <table class="info-table">
            <tr>
                <td class="label">Tanggal Pinjam</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($pinjaman->tanggal_pinjam)->format('d F Y / H:i') }}</td>
                <td class="label">Tanggal Cetak</td>
                <td class="separator">:</td>
                <td>{{ now()->format('d F Y / H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Kode Pinjaman</td>
                <td class="separator">:</td>
                <td><strong>{{ $pinjaman->kode_pinjaman }}</strong></td>
                <td class="label">User Input</td>
                <td class="separator">:</td>
                <td>{{ $pinjaman->user->name ?? 'Admin Koperasi' }}</td>
            </tr>
            <tr>
                <td class="label">ID Anggota</td>
                <td class="separator">:</td>
                <td>{{ $pinjaman->anggota->id_anggota ?? 'member1' }}</td>
                <td class="label">Status Lunas</td>
                <td class="separator">:</td>
                <td>
                    @if($pinjaman->status_lunas == 'Lunas')
                        <span class="badge badge-lunas">LUNAS</span>
                    @else
                        <span class="badge badge-belum">BELUM LUNAS</span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Nama Anggota</td>
                <td class="separator">:</td>
                <td><strong>{{ $pinjaman->anggota->nama ?? 'User Koperasi' }}</strong></td>
                <td class="label">Dari Kas</td>
                <td class="separator">:</td>
                <td>{{ $pinjaman->dariKas->nama_kas ?? $pinjaman->kas->nama_kas ?? 'Kas Tunai' }}</td>
            </tr>
            <tr>
                <td class="label">Dept.</td>
                <td class="separator">:</td>
                <td>{{ $pinjaman->anggota->departement ?? 'Produksi Siliting' }}</td>
                <td class="label">Jenis Pinjaman</td>
                <td class="separator">:</td>
                <td>{{ $pinjaman->jenis_pinjaman }}</td>
            </tr>
            <tr>
                <td class="label">Lama Pinjam</td>
                <td class="separator">:</td>
                <td>{{ $pinjaman->lamaAngsuran->lama_angsuran ?? '1' }} Bulan</td>
                <td class="label">Tanggal Tempo</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($pinjaman->tanggal_pinjam)->addMonths($pinjaman->lamaAngsuran->lama_angsuran ?? 1)->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Bunga</td>
                <td class="separator">:</td>
                <td>{{ $pinjaman->bunga_persen ?? '5.00' }}%</td>
                <td class="label"></td>
                <td class="separator"></td>
                <td></td>
            </tr>
        </table>

        <!-- Amount Box -->
        <div class="amount-box">
            <div class="label">Pokok Pinjaman</div>
            <div class="value">Rp. {{ number_format($pinjaman->pokok_pinjaman, 0, ',', '.') }}</div>
            <div class="words">TERBILANG : {{ strtoupper($terbilang) }} RUPIAH</div>
        </div>

        <!-- Detail Perhitungan -->
        <div class="detail-box">
            <table>
                <tr>
                    <td class="label">Pokok Pinjaman</td>
                    <td>: Rp {{ number_format($pinjaman->pokok_pinjaman, 0, ',', '.') }}</td>
                    <td class="label">Angsuran Pokok</td>
                    <td>: Rp {{ number_format($pinjaman->angsuran_pokok, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Biaya Bunga per Bulan</td>
                    <td>: Rp {{ number_format($pinjaman->biaya_bunga, 0, ',', '.') }}</td>
                    <td class="label">Total Bunga ({{ $pinjaman->lamaAngsuran->lama_angsuran ?? 0 }} bln)</td>
                    <td>: Rp {{ number_format($pinjaman->biaya_bunga * ($pinjaman->lamaAngsuran->lama_angsuran ?? 0), 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Angsuran Bunga</td>
                    <td>: Rp {{ number_format($pinjaman->biaya_bunga, 0, ',', '.') }}</td>
                    <td class="label">Biaya Admin</td>
                    <td>: Rp {{ number_format($pinjaman->biaya_admin, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">Total Angsuran</td>
                    <td>: Rp {{ number_format($pinjaman->jumlah_angsuran, 0, ',', '.') }}</td>
                    <td class="label">Angsuran per Bulan</td>
                    <td>: Rp {{ number_format($pinjaman->angsuran_pokok + $pinjaman->biaya_bunga, 0, ',', '.') }}</td>
                </tr>
                <tr style="border-top: 1px dashed #666;">
                    <td class="label">Sudah Dibayar</td>
                    <td>: Rp {{ number_format($pinjaman->total_bayar ?? 0, 0, ',', '.') }}</td>
                    <td class="label">Sisa Angsuran</td>
                    <td>: {{ $pinjaman->sisa_angsuran ?? 0 }} kali</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td class="label"><strong>Sisa Tagihan</strong></td>
                    <td><strong>: Rp {{ number_format($pinjaman->sisa_tagihan ?? 0, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

        <!-- Signature -->
        <div class="signature">
            <table>
                <tr>
                    <td style="width: 33%;">
                        <div class="sig-title">Peminjam,</div>
                        <div class="sig-name">{{ $pinjaman->anggota->nama ?? 'User Koperasi' }}</div>
                    </td>
                    <td style="width: 33%;">
                        <div class="sig-title">Petugas,</div>
                        <div class="sig-name">{{ $pinjaman->user->name ?? 'Admin Koperasi' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>{{ $identitas->alamat ?? 'AKENO MULTIMEDIA SOLUTION, VILA BANDUNG INDAH 40393 JAWA WEST JAVA' }}, {{ now()->format('d F Y') }}</p>
        <p>Email: {{ $identitas->email ?? 'admin@koperasi.id' }} | Website: {{ $identitas->web ?? 'www.koperasi-akeno.id' }}</p>
        <p style="font-style: italic; margin-top: 1px;">** Tanda terima ini sah jika telah dibubuhi cap dan tanda tangan oleh pihak yang berwenang **</p>
    </div>
</body>

</html>