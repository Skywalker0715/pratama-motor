<!DOCTYPE html>
<html>
<head>
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #444; padding: 6px; }
        th { background-color: #f2f2f2; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .summary-table { width: 60%; margin-bottom: 30px; }
        .summary-table th { width: 40%; }
        .fw-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Laba Rugi</h2>
        <p>Periode: {{ $startDate ? date('d/m/Y', strtotime($startDate)) : 'Semua' }} - {{ $endDate ? date('d/m/Y', strtotime($endDate)) : 'Semua' }}</p>
    </div>

    <h3>Ringkasan</h3>
    <table class="summary-table">
        <tr>
            <th>Total Omzet</th>
            <td class="text-end fw-bold">Rp {{ number_format($summary->total_omzet, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total HPP</th>
            <td class="text-end fw-bold">Rp {{ number_format($summary->total_hpp, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Profit</th>
            <td class="text-end fw-bold">Rp {{ number_format($summary->total_profit, 0, ',', '.') }}</td>
        </tr>
    </table>

    <h3>Detail Transaksi</h3>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Transaksi</th>
                <th>User</th>
                <th class="text-end">Total Sales</th>
                <th class="text-end">Total HPP</th>
                <th class="text-end">Profit</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $transaksi)
                <tr>
                    <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaksi->kode_transaksi ?? '-' }}</td>
                    <td>{{ $transaksi->user->name ?? '-' }}</td>
                    <td class="text-end">Rp {{ number_format($transaksi->total_sales, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($transaksi->total_hpp, 0, ',', '.') }}</td>
                    <td class="text-end">Rp {{ number_format($transaksi->profit, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data transaksi.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>