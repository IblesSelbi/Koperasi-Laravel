<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pinjaman Lunas</title>
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
            background: #f0fff0;
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
            border: 2px solid #28a745;
            padding: 4px;
            margin: 5px 0;
            background: #d4edda;
        }

        .amount-box .label {
            font-size: 7pt;
            font-weight: bold;
            color: #155724;
        }

        .amount-box .value {
            font-size: 11pt;
            font-weight: bold;
            margin: 2px 0;
            color: #155724;
        }

        .amount-box .words {
            font-size: 6.5pt;
            font-style: italic;
            color: #155724;
        }

        /* Status Badge */
        .status-lunas {
            display: inline-block;
            background: #28a745;
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

        /* Lunas Stamp */
        .lunas-stamp {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            border: 4px solid #28a745;
            color: #28a745;
            font-size: 36pt;
            font-weight: bold;
            padding: 10px 30px;
            opacity: 0.3;
            pointer-events: none;
        }
    </style>
</head>

<body>
    <!-- Watermark LUNAS -->
    <div class="lunas-stamp">LUNAS</div>

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
            <h2>BUKTI PINJAMAN LUNAS</h2>
            <p>{{ $identitas->nama_lembaga ?? 'KOPERASI' }}</p>
            <p>{{ $identitas->alamat ?? 'JL LASWI 2 TONJONG MAJALENGKA' }} Tel. {{ $identitas->telepon ?? '0231-36387985' }}</p>
            <p class="ref">Ref. {{ $pinjamanLunas->kode_lunas }}</p>
        </div>
    </div>

    <!-- Content -->
    <div class="content">
        <table class="info-table">
            <tr>
                <td class="label">Kode Lunas</td>
                <td class="separator">:</td>
                <td><strong>{{ $pinjamanLunas->kode_lunas }}</strong></td>
                <td class="label">Tanggal Lunas</td>
                <td class="separator">:</td>
                <td><strong>{{ \Carbon\Carbon::parse($pinjamanLunas->tanggal_lunas)->translatedFormat('d F Y H:i') }}</strong></td>
            </tr>
            <tr>
                <td class="label">Kode Pinjaman</td>
                <td class="separator">:</td>
                <td>{{ $pinjamanLunas->pinjaman->kode_pinjaman ?? '-' }}</td>
                <td class="label">Tanggal Cetak</td>
                <td class="separator">:</td>
                <td>{{ now()->translatedFormat('d F Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">ID Anggota</td>
                <td class="separator">:</td>
                <td>{{ $pinjamanLunas->pinjaman->anggota->id_anggota ?? '-' }}</td>
                <td class="label">Status Pelunasan</td>
                <td class="separator">:</td>
                <td>
                    <span class="status-lunas">✓ LUNAS</span>
                </td>
            </tr>
            <tr>
                <td class="label">Nama Anggota</td>
                <td class="separator">:</td>
                <td><strong>{{ $pinjamanLunas->pinjaman->anggota->nama ?? 'Unknown' }}</strong></td>
                <td class="label">User Validasi</td>
                <td class="separator">:</td>
                <td>{{ $pinjamanLunas->user->name ?? 'System' }}</td>
            </tr>
            <tr>
                <td class="label">Dept.</td>
                <td class="separator">:</td>
                <td>{{ $pinjamanLunas->pinjaman->anggota->departement ?? '-' }}</td>
                <td class="label">Jenis Pinjaman</td>
                <td class="separator">:</td>
                <td>{{ $pinjamanLunas->pinjaman->jenis_pinjaman ?? 'Biasa' }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pinjam</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($pinjamanLunas->pinjaman->tanggal_pinjam)->translatedFormat('d F Y') }}</td>
                <td class="label">Lama Pinjam</td>
                <td class="separator">:</td>
                <td>{{ $pinjamanLunas->lama_cicilan }} Bulan</td>
            </tr>
            <tr>
                <td class="label">Tanggal Tempo</td>
                <td class="separator">:</td>
                <td>{{ \Carbon\Carbon::parse($pinjamanLunas->pinjaman->tanggal_pinjam)->addMonths($pinjamanLunas->lama_cicilan)->translatedFormat('d F Y') }}</td>
                <td class="label">Bunga</td>
                <td class="separator">:</td>
                <td>{{ $pinjamanLunas->pinjaman->bunga_persen ?? '0' }}%</td>
            </tr>
        </table>

        <!-- Amount Box -->
        <div class="amount-box">
            <div class="label">Pokok Pinjaman</div>
            <div class="value">Rp. {{ number_format($pinjamanLunas->pinjaman->pokok_pinjaman ?? 0, 0, ',', '.') }}</div>
            <div class="words">TERBILANG : {{ strtoupper($terbilang) }} RUPIAH</div>
        </div>

        <!-- Detail Perhitungan -->
        <div class="detail-box">
            <table>
                <tr>
                    <td class="label">Pokok Pinjaman</td>
                    <td>: Rp {{ number_format($pinjamanLunas->pinjaman->pokok_pinjaman ?? 0, 0, ',', '.') }}</td>
                    <td class="label">Angsuran Pokok</td>
                    <td>: Rp {{ number_format($pinjamanLunas->pinjaman->angsuran_pokok ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Biaya Bunga per Bulan</td>
                    <td>: Rp {{ number_format($pinjamanLunas->pinjaman->biaya_bunga ?? 0, 0, ',', '.') }}</td>
                    <td class="label">Total Bunga ({{ $pinjamanLunas->lama_cicilan }} bln)</td>
                    <td>: Rp {{ number_format(($pinjamanLunas->pinjaman->biaya_bunga ?? 0) * $pinjamanLunas->lama_cicilan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="label">Biaya Admin</td>
                    <td>: Rp {{ number_format($pinjamanLunas->pinjaman->biaya_admin ?? 0, 0, ',', '.') }}</td>
                    <td class="label">Total Denda</td>
                    <td>: Rp {{ number_format($pinjamanLunas->total_denda ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td class="label">Total Tagihan</td>
                    <td>: Rp {{ number_format($pinjamanLunas->pinjaman->jumlah_angsuran ?? 0, 0, ',', '.') }}</td>
                    <td class="label">Total Dibayar</td>
                    <td>: Rp {{ number_format($pinjamanLunas->total_dibayar ?? 0, 0, ',', '.') }}</td>
                </tr>
                <tr style="border-top: 2px solid #28a745; background: #d4edda;">
                    <td colspan="2" class="label"><strong style="color: #155724;">STATUS PEMBAYARAN</strong></td>
                    <td colspan="2"><strong style="color: #155724; font-size: 9pt;"> LUNAS (SISA: Rp 0)</strong></td>
                </tr>
            </table>
        </div>

        <!-- Signature -->
        <div class="signature">
            <table>
                <tr>
                    <td style="width: 33%;">
                        <div class="sig-title">Peminjam,</div>
                        <div class="sig-name">{{ $pinjamanLunas->pinjaman->anggota->nama ?? 'Anggota' }}</div>
                    </td>
                    <td style="width: 33%;">
                        <div class="sig-title">Petugas,</div>
                        <div class="sig-name">{{ $pinjamanLunas->user->name ?? 'Admin' }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>{{ $identitas->alamat ?? 'AKENO MULTIMEDIA SOLUTION, VILA BANDUNG INDAH 40393 JAWA WEST JAVA' }}, {{ now()->translatedFormat('d F Y') }}</p>
        <p>Email: {{ $identitas->email ?? 'admin@koperasi.id' }} | Website: {{ $identitas->web ?? 'www.koperasi-akeno.id' }}</p>
        <p style="font-style: italic; margin-top: 1px; color: #155724;">** PINJAMAN INI TELAH LUNAS DAN TIDAK ADA KEWAJIBAN TERSISA **</p>
    </div>
</body>

</html>