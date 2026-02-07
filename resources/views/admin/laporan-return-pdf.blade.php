<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Return Barang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #333;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Return Barang</h2>
        @if(isset($from) && isset($to))
            <p>Periode: {{ \Carbon\Carbon::parse($from)->format('d F Y') }} - {{ \Carbon\Carbon::parse($to)->format('d F Y') }}</p>
        @else
            <p>Semua Data</p>
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 25%">Nama Barang</th>
                <th style="width: 10%" class="text-center">Jumlah</th>
                <th style="width: 25%">Catatan</th>
                <th style="width: 20%">Kasir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($returns as $index => $return)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($return->created_at)->format('d-m-Y H:i') }}</td>
                <td>{{ $return->barang->nama_barang ?? 'Barang Dihapus' }}</td>
                <td class="text-center">{{ $return->quantity }}</td>
                <td>{{ $return->notes ?? '-' }}</td>
                <td>{{ $return->user->name ?? 'User Dihapus' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="text-align: right; font-size: 10px; color: #777;">
        Dicetak pada: {{ now()->format('d F Y H:i') }}
    </div>
</body>
</html>