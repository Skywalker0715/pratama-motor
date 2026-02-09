<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header p { margin: 5px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #333; padding: 6px; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .summary-box { margin-bottom: 20px; border: 1px solid #333; padding: 10px; width: 50%; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Penjualan</h2>
        @if($startDate && $endDate)
            <p>Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
        @else
            <p>Semua Periode</p>
        @endif
    </div>

    <div class="summary-box">
        <strong>Total Summary</strong><br>
        <table style="border: none; margin: 5px 0 0 0; width: 100%;">
            <tr>
                <td style="border: none; padding: 2px;">Total Omzet</td>
                <td style="border: none; padding: 2px;" class="text-right">Rp {{ number_format($summary->total_omzet, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;">Total HPP</td>
                <td style="border: none; padding: 2px;" class="text-right">Rp 0 (Belum tersedia)</td>
            </tr>
            <tr>
                <td style="border: none; padding: 2px;"><strong>Profit</strong></td>
                <td style="border: none; padding: 2px;" class="text-right"><strong>Rp {{ number_format($summary->total_profit, 0, ',', '.') }} *</strong></td>
            </tr>
        </table>
        <div style="font-size: 10px; font-style: italic; margin-bottom: 10px;">* Profit sama dengan omzet karena HPP belum tersedia</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kode Transaksi</th>
                <th>User</th>
                <th class="text-right">Total Sales</th>
                <th class="text-right">Total HPP</th>
                <th class="text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaksi)
            <tr>
                <td>{{ $transaksi->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $transaksi->transaksi_kode }}</td>
                <td>{{ $transaksi->user_name }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->total_sales, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->total_hpp, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($transaksi->profit, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            
            <!-- Total Row -->
            <tr style="font-weight: bold; background-color: #f9f9f9;">
                <td colspan="3" class="text-center">TOTAL</td>
                <td class="text-right">Rp {{ number_format($summary->total_omzet, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($summary->total_hpp, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($summary->total_profit, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div style="font-size: 10px; color: #777; text-align: right;">
        Dicetak pada: {{ now()->format('d F Y H:i') }}
    </div>
</body>
</html>