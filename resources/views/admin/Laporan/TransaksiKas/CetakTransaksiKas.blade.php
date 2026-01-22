<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi Kas - {{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d M Y') }} s/d
        {{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d M Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
        }

        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .header h2 {
            font-size: 16px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 11px;
            color: #666;
        }

        .periode-info {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th {
            background-color: #4a90e2;
            color: white;
            padding: 10px 5px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #333;
        }

        table td {
            padding: 8px 5px;
            border: 1px solid #ddd;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tbody tr:hover {
            background-color: #f0f0f0;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .fw-bold {
            font-weight: bold;
        }

        .saldo-sebelumnya {
            background-color: #fff3cd !important;
            font-weight: bold;
        }

        .total-row {
            background-color: #e3f2fd !important;
            font-weight: bold;
            font-size: 13px;
        }

        .summary-box {
            margin-top: 30px;
            padding: 15px;
            border: 2px solid #4a90e2;
            background-color: #f8f9fa;
        }

        .summary-box h3 {
            font-size: 14px;
            margin-bottom: 10px;
            color: #4a90e2;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dotted #ccc;
        }

        .summary-item:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 14px;
            padding-top: 10px;
            border-top: 2px solid #4a90e2;
        }

        .footer {
            margin-top: 40px;
            text-align: right;
        }

        .signature {
            margin-top: 80px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }

            @page {
                margin: 1cm;
            }
        }

        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            z-index: 1000;
        }

        .print-button:hover {
            background-color: #357abd;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-primary {
            background-color: #cce5ff;
            color: #004085;
        }
    </style>
</head>

<body>
    <!-- Print Button -->
    <button class="print-button no-print" onclick="window.print()">
        🖨️ Cetak Laporan
    </button>

    <!-- Header -->
    <div class="header">
        <h1>KOPERASI SIMPAN PINJAM</h1>
        <h2>LAPORAN TRANSAKSI KAS</h2>
        <p>Alamat: Jl. Contoh No. 123, Kota, Provinsi | Telp: (021) 12345678</p>
    </div>

    <!-- Periode Info -->
    <div class="periode-info">
        Periode: {{ \Carbon\Carbon::parse($startDate)->locale('id')->translatedFormat('d F Y') }} s/d
        {{ \Carbon\Carbon::parse($endDate)->locale('id')->translatedFormat('d F Y') }}
    </div>

    <!-- Table -->
    @if($format == 'ringkas')
        <!-- Format Ringkas -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 33%;">Keterangan</th>
                    <th style="width: 16%;">Debet</th>
                    <th style="width: 16%;">Kredit</th>
                    <th style="width: 18%;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <!-- Saldo Sebelumnya -->
                <tr class="saldo-sebelumnya">
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="fw-bold">SALDO SEBELUMNYA</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">Rp {{ number_format($saldoSebelumnya, 0, ',', '.') }}</td>
                </tr>

                @foreach($transaksiKas as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->locale('id')->translatedFormat('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $item->akun_transaksi }}</strong><br>
                            <small>{{ $item->keterangan }}</small>
                        </td>
                        <td class="text-right">
                            @if($item->debet > 0)
                                Rp {{ number_format($item->debet, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($item->kredit > 0)
                                Rp {{ number_format($item->kredit, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right fw-bold">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="3" class="text-center">TOTAL TRANSAKSI</td>
                    <td class="text-right">Rp {{ number_format($totalDebet, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

    @elseif($format == 'summary')
        <!-- Format Summary -->
        <div class="summary-box">
            <h3>RINGKASAN TRANSAKSI KAS</h3>
            <div class="summary-item">
                <span>Saldo Awal Periode:</span>
                <span>Rp {{ number_format($saldoSebelumnya, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span>Total Pemasukan (Debet):</span>
                <span>Rp {{ number_format($totalDebet, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span>Total Pengeluaran (Kredit):</span>
                <span>Rp {{ number_format($totalKredit, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span>Mutasi Bersih:</span>
                <span>Rp {{ number_format($totalDebet - $totalKredit, 0, ',', '.') }}</span>
            </div>
            <div class="summary-item">
                <span>Saldo Akhir Periode:</span>
                <span>Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</span>
            </div>
        </div>

        <table style="margin-top: 20px;">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 12%;">Tanggal</th>
                    <th style="width: 33%;">Keterangan</th>
                    <th style="width: 16%;">Debet</th>
                    <th style="width: 16%;">Kredit</th>
                    <th style="width: 18%;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <tr class="saldo-sebelumnya">
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="fw-bold">SALDO SEBELUMNYA</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">Rp {{ number_format($saldoSebelumnya, 0, ',', '.') }}</td>
                </tr>

                @foreach($transaksiKas as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->locale('id')->translatedFormat('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $item->akun_transaksi }}</strong><br>
                            <small>{{ $item->keterangan }}</small>
                        </td>
                        <td class="text-right">
                            @if($item->debet > 0)
                                Rp {{ number_format($item->debet, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($item->kredit > 0)
                                Rp {{ number_format($item->kredit, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right fw-bold">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="3" class="text-center">TOTAL TRANSAKSI</td>
                    <td class="text-right">Rp {{ number_format($totalDebet, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

    @else
        <!-- Format Lengkap (Default) -->
        <table>
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 10%;">Kode</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 20%;">Akun</th>
                    <th style="width: 10%;">Dari Kas</th>
                    <th style="width: 10%;">Untuk Kas</th>
                    <th style="width: 12%;">Debet</th>
                    <th style="width: 12%;">Kredit</th>
                    <th style="width: 13%;">Saldo</th>
                </tr>
            </thead>
            <tbody>
                <!-- Saldo Sebelumnya -->
                <tr class="saldo-sebelumnya">
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="fw-bold">SALDO SEBELUMNYA</td>
                    <td class="text-center">-</td>
                    <td class="text-center">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">Rp {{ number_format($saldoSebelumnya, 0, ',', '.') }}</td>
                </tr>

                @foreach($transaksiKas as $index => $item)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">
                            <span class="badge badge-primary">{{ $item->kode_transaksi }}</span>
                        </td>
                        <td class="text-center">
                            {{ \Carbon\Carbon::parse($item->tanggal_transaksi)->locale('id')->translatedFormat('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $item->akun_transaksi }}</strong><br>
                            <small>{{ $item->keterangan }}</small>
                        </td>
                        <td class="text-center">
                            @if($item->dari_kas && $item->dari_kas != '-')
                                <span class="badge badge-success">{{ $item->dari_kas }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            @if($item->untuk_kas && $item->untuk_kas != '-')
                                <span class="badge badge-danger">{{ $item->untuk_kas }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($item->debet > 0)
                                Rp {{ number_format($item->debet, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">
                            @if($item->kredit > 0)
                                Rp {{ number_format($item->kredit, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right fw-bold">Rp {{ number_format($item->saldo, 0, ',', '.') }}</td>
                    </tr>
                @endforeach

                <!-- Total Row -->
                <tr class="total-row">
                    <td colspan="6" class="text-center">TOTAL TRANSAKSI</td>
                    <td class="text-right">Rp {{ number_format($totalDebet, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($totalKredit, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($saldoAkhir, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('d F Y, H:i:s') }}</p>
        <div class="signature">
            <p>Mengetahui,</p>
            <br><br><br>
            <p><strong>_______________________</strong></p>
            <p>Kepala Koperasi</p>
        </div>
    </div>

    <script>
        // Auto print on load (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>