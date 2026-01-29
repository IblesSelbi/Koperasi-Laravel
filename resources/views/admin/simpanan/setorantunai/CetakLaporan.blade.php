<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Data Setoran Tunai</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            color: #000;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
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
            line-height: 1.3;
        }

        .title {
            text-align: center;
            margin: 15px 0;
        }

        .title h1 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .title p {
            font-size: 10pt;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table thead tr {
            background-color: #f0f0f0;
        }

        table th {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
        }

        table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 9pt;
        }

        table td.center {
            text-align: center;
        }

        table td.right {
            text-align: right;
        }

        table tfoot tr {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        table tfoot td {
            padding: 10px 8px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
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

    <div class="title">
        <h1>Laporan Data Setoran Tunai</h1>
        <p>{{ $periode }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="40">No.</th>
                <th width="100">No Transaksi</th>
                <th width="90">Tanggal</th>
                <th width="90">ID Anggota</th>
                <th>Nama Anggota</th>
                <th width="100">Departemen</th>
                <th width="100">Jenis Simpanan</th>
                <th width="100">Jumlah</th>
                <th width="12%">User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($setoran as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center">{{ $item->kode_transaksi }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($item->tanggal_transaksi)->format('d M Y') }}</td>
                <td class="center">{{ $item->anggota->id_anggota ?? '-' }}</td>
                <td>{{ $item->anggota->nama ?? '-' }}</td>
                <td>{{ $item->anggota->departement ?? '-' }}</td>
                <td>{{ $item->jenisSimpanan->jenis_simpanan ?? '-' }}</td>
                <td class="right">{{ number_format($item->jumlah, 0, ',', '.') }}</td>
                <td class="center">{{ $item->user->name ?? 'admin' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="center">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7" class="right">Jumlah Total</td>
                <td class="right">{{ number_format($total_setoran, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>