<!DOCTYPE html>
<html>
<head>
    <title>Laporan Transaksi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width:100%; border-collapse: collapse; }
        th, td { border:1px solid #000; padding:5px; }
        th { background:#eee; }
    </style>
</head>
<body>

<h3>Laporan Transaksi</h3>

<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Barang</th>
            <th>Jenis</th>
            <th>Jumlah</th>
            <th>Stok Saat Ini</th>
            <th>User</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transaksis as $t)
        <tr>
            <td>{{ $t->created_at->format('d-m-Y H:i') }}</td>
            <td>{{ $t->barang->nama_barang }}</td>
            <td>{{ ucfirst($t->jenis) }}</td>
            <td>{{ $t->jumlah }}</td>
            <td>{{ $t->barang->stok }}</td>
            <td>{{ $t->user->name ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
