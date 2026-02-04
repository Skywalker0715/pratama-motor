<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi</title>
    <link rel="stylesheet" href="{{ public_path('css/print-continuous.css') }}">
</head>
<body>

<h2>PRATAMA MOTOR</h2>
<p>
    Tanggal: {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
</p>

<hr>

<table width="100%" cellspacing="0" cellpadding="6">
    <thead>
        <tr>
            <th align="left">No</th>
            <th align="left">Barang</th>
            <th align="right">Harga</th>
            <th align="right">Qty</th>
            <th align="right">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($detailTransaksi as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>
                    {{ $item['nama_barang'] }}<br>
                    <small>{{ $item['kategori'] }}</small>
                </td>
                <td align="right">
                    Rp {{ number_format($item['harga_satuan'], 0, ',', '.') }}
                </td>
                <td align="right">{{ $item['jumlah'] }}</td>
                <td align="right">
                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<hr>

<h3 align="right">
    TOTAL:
    Rp {{ number_format(collect($detailTransaksi)->sum('subtotal'), 0, ',', '.') }}
</h3>
<p align="center">Terima Kasih</p>

</body>
</html>
