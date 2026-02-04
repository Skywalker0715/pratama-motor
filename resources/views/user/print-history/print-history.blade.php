<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Transaksi</title>

    {{-- CSS KHUSUS PRINT --}}
    <link rel="stylesheet" href="{{ public_path('css/print-continuous.css') }}">
</head>
<body>

    <h2 class="title">Riwayat Transaksi</h2>
    <p class="subtitle">Dicetak pada: {{ now()->format('d-m-Y H:i') }}</p>

    <table class="table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kode</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($histories as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->created_at->format('d-m-Y') }}</td>
                    <td>{{ $item->kode_transaksi }}</td>
                    <td>Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($item->status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
