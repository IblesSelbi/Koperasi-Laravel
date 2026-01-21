<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jatuh Tempo - {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->isoFormat('MMMM YYYY') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 3px solid #333;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            color: #666;
            margin-bottom: 10px;
        }
        
        .periode-info {
            background: #e7f1ff;
            padding: 12px;
            margin-bottom: 15px;
            border-left: 4px solid #0d6efd;
            border-radius: 4px;
        }
        
        .periode-info h3 {
            font-size: 13px;
            color: #0d6efd;
            margin-bottom: 5px;
        }
        
        .periode-info p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 10px;
        }
        
        table thead {
            background: #0d6efd;
            color: white;
        }
        
        table th {
            padding: 10px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #0d6efd;
        }
        
        table td {
            padding: 8px;
            border: 1px solid #ddd;
            vertical-align: middle;
        }
        
        table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        table tfoot {
            background: #f8f9fa;
            font-weight: bold;
        }
        
        table tfoot td {
            padding: 10px 8px;
            border: 2px solid #333;
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
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .badge-primary {
            background: #0d6efd;
            color: white;
        }
        
        .badge-danger {
            background: #dc3545;
            color: white;
        }
        
        .badge-warning {
            background: #ffc107;
            color: #000;
        }
        
        .badge-info {
            background: #17a2b8;
            color: white;
        }
        
        .text-primary {
            color: #0d6efd;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        .text-success {
            color: #28a745;
        }
        
        .text-muted {
            color: #6c757d;
        }
        
        .summary-box {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .summary-item {
            background: #f8f9fa;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }
        
        .summary-item .label {
            font-size: 10px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }
        
        .footer .left {
            text-align: left;
        }
        
        .footer .right {
            text-align: right;
        }
        
        .signature {
            margin-top: 60px;
        }
        
        .signature p {
            margin: 3px 0;
        }
        
        .signature .line {
            border-top: 1px solid #333;
            width: 200px;
            margin-top: 50px;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        
        .status-lewat {
            background: #fee;
            color: #c00;
            border: 1px solid #c00;
        }
        
        .status-segera {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
        }
        
        .status-normal {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #17a2b8;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>Koperasi Simpan Pinjam</h1>
        <h2>Laporan Jatuh Tempo Pembayaran Kredit</h2>
        <p>Periode: {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->isoFormat('MMMM YYYY') }}</p>
    </div>

    <!-- Periode Info -->
    <div class="periode-info">
        <h3>Informasi Laporan</h3>
        <p><strong>Total Pinjaman:</strong> {{ $jatuhTempo->count() }} pinjaman</p>
        <p><strong>Tanggal Cetak:</strong> {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY HH:mm') }}</p>
        <p><strong>Dicetak oleh:</strong> {{ Auth::user()->name }}</p>
    </div>

    <!-- Summary Boxes -->
    <div class="summary-box">
        <div class="summary-item">
            <div class="label">Total Tagihan</div>
            <div class="value text-primary">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Dibayar</div>
            <div class="value text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Sisa Tagihan</div>
            <div class="value text-danger">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 4%;">No</th>
                <th class="text-center" style="width: 10%;">Kode Pinjaman</th>
                <th style="width: 15%;">Nama Anggota</th>
                <th class="text-center" style="width: 10%;">Tgl Pinjam</th>
                <th class="text-center" style="width: 10%;">Tgl Tempo</th>
                <th class="text-center" style="width: 8%;">Status</th>
                <th class="text-center" style="width: 8%;">Lama</th>
                <th class="text-right" style="width: 12%;">Jml Tagihan</th>
                <th class="text-right" style="width: 11%;">Dibayar</th>
                <th class="text-right" style="width: 12%;">Sisa Tagihan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($jatuhTempo as $index => $item)
                @php
                    $tempo = \Carbon\Carbon::parse($item->tanggal_tempo);
                    $now = \Carbon\Carbon::now();
                    $diff = $now->diffInDays($tempo, false);
                    
                    if ($diff < 0) {
                        $statusClass = 'status-lewat';
                        $statusText = 'Terlambat';
                    } elseif ($diff <= 7) {
                        $statusClass = 'status-segera';
                        $statusText = 'Segera';
                    } else {
                        $statusClass = 'status-normal';
                        $statusText = 'Normal';
                    }
                @endphp
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center">
                        <span class="badge badge-primary">{{ $item->kode_pinjam }}</span>
                    </td>
                    <td>
                        <strong>{{ $item->nama_anggota }}</strong><br>
                        <small class="text-muted">ID: {{ $item->id_anggota }}</small>
                    </td>
                    <td class="text-center">
                        {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') }}
                    </td>
                    <td class="text-center">
                        {{ $tempo->format('d/m/Y') }}
                    </td>
                    <td class="text-center">
                        <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                    </td>
                    <td class="text-center">
                        {{ $item->lama_pinjam }} Bulan
                    </td>
                    <td class="text-right">
                        <strong>Rp {{ number_format($item->jumlah_tagihan, 0, ',', '.') }}</strong>
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($item->dibayar, 0, ',', '.') }}
                    </td>
                    <td class="text-right">
                        <strong class="text-danger">Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}</strong>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center" style="padding: 30px;">
                        Tidak ada data pinjaman yang jatuh tempo pada periode ini
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="text-center"><strong>TOTAL</strong></td>
                <td class="text-right">
                    <strong class="text-primary">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</strong>
                </td>
                <td class="text-right">
                    <strong class="text-success">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</strong>
                </td>
                <td class="text-right">
                    <strong class="text-danger">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Footer with Signature -->
    <div class="footer">
        <div class="left">
            <p><strong>Catatan:</strong></p>
            <p>• Laporan ini menampilkan pinjaman yang jatuh tempo pada periode {{ \Carbon\Carbon::createFromFormat('Y-m', $periode)->isoFormat('MMMM YYYY') }}</p>
            <p>• Sisa tagihan merupakan total tagihan dikurangi pembayaran yang sudah dilakukan</p>
            <p>• Segera lakukan penagihan untuk pinjaman yang statusnya "Terlambat"</p>
        </div>
        <div class="right">
            <div class="signature">
                <p>{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY') }}</p>
                <p>Mengetahui,</p>
                <div class="line"></div>
                <p><strong>Kepala Koperasi</strong></p>
            </div>
        </div>
    </div>

    <!-- Print Script -->
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>