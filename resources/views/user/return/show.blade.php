@extends('layouts.user')

@section('content')
<style>
    .no-print {
        print-color-adjust: none;
        -webkit-print-color-adjust: none;
    }
    
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<div class="p-6">
    <!-- Header with Back Button -->
    <div class="flex items-center gap-4 mb-6" id="header-section">
        <a href="{{ route('user.return.index') }}" 
           class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-200 hover:bg-gray-300 transition no-print">
            <span class="material-icons">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Detail Return Barang</h1>
            <p class="text-sm text-gray-500">ID Return: #{{ $return->id }}</p>
        </div>
    </div>

    <!-- Info Return Card -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Tanggal Return -->
                <div class="flex items-start gap-3">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <span class="material-icons text-blue-600">calendar_today</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Return</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ \Carbon\Carbon::parse($return->tanggal)->format('d M Y') }}
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($return->tanggal)->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <!-- ID Transaksi -->
                <div class="flex items-start gap-3">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <span class="material-icons text-green-600">receipt</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">ID Transaksi</p>
                        <p class="text-lg font-semibold text-gray-800">
                            #{{ $return->transaksi_id }}
                        </p>
                        <a href="{{ route('user.history') }}" class="text-xs text-blue-600 hover:underline">
                            Lihat Transaksi
                        </a>
                    </div>
                </div>

                <!-- Total Item -->
                <div class="flex items-start gap-3">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <span class="material-icons text-purple-600">inventory</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Item</p>
                        <p class="text-lg font-semibold text-gray-800">
                            {{ $return->items->count() }} Item
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ $return->items->sum('quantity') }} Unit
                        </p>
                    </div>
                </div>
            </div>

            <!-- Catatan Return (jika ada) -->
            @if($return->alasan)
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start gap-2">
                    <span class="material-icons text-yellow-600 text-sm">note</span>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Catatan Return:</p>
                        <p class="text-sm text-yellow-700 mt-1">{{ $return->alasan }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Detail Item Return -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-800">Item yang Dikembalikan</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            No
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Barang
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Jumlah
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Harga Satuan
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Subtotal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Alasan
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($return->items as $index => $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                            {{ $index + 1 }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            <div class="font-medium">{{ $item->barang->nama_barang ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">SKU: {{ $item->barang->kode_barang ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $item->quantity }} Unit
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">
                            Rp {{ number_format($item->barang->harga ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                            Rp {{ number_format(($item->barang->harga ?? 0) * $item->quantity, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-gray-100">
                                <span class="material-icons text-xs">check_circle</span>
                                Dikembalikan
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                            Total Return:
                        </td>
                        <td class="px-6 py-4 text-right text-lg font-bold text-red-600">
                            Rp {{ number_format($return->items->sum(function($item) {
                                return ($item->barang->harga ?? 0) * $item->quantity;
                            }), 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex gap-3 no-print">
        <a href="{{ route('user.return.index') }}" 
           class="flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
            <span class="material-icons text-sm">arrow_back</span>
            Kembali
        </a>
        
        <button onclick="window.print()" 
                class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <span class="material-icons text-sm">print</span>
            Cetak
        </button>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    /* Reset default styles */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    html, body {
        width: 100%;
        height: 100%;
        padding: 0;
        margin: 0;
    }
    
    /* Main content container */
    .p-6 {
        padding: 1cm !important;
        width: 100% !important;
        display: block !important;
    }
    
    /* Show content, hide everything else */
    body > * {
        display: none !important;
    }
    
    body > .p-6 {
        display: block !important;
        visibility: visible !important;
    }
    
    /* Hide buttons and interactive elements */
    button, a.flex, .mt-6 {
        display: none !important;
    }
    
    /* Optimize typography for print */
    body {
        font-family: Arial, sans-serif;
        font-size: 11pt;
        color: #000;
        line-height: 1.4;
    }
    
    h1 {
        font-size: 16pt !important;
        margin-bottom: 0.3cm !important;
    }
    
    h2 {
        font-size: 13pt !important;
        margin-top: 0.3cm !important;
        margin-bottom: 0.2cm !important;
    }
    
    /* Card styling for print */
    .bg-white {
        border: 1px solid #ccc !important;
        page-break-inside: avoid !important;
        margin-bottom: 0.5cm !important;
    }
    
    /* Grid optimization */
    .grid {
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 0.5cm !important;
        page-break-inside: avoid !important;
    }
    
    /* Table styling */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 10pt !important;
        margin: 0.3cm 0 !important;
    }
    
    th, td {
        border: 1px solid #999 !important;
        padding: 0.2cm !important;
        text-align: left !important;
    }
    
    th {
        background-color: #e0e0e0 !important;
        font-weight: bold !important;
        color: #333 !important;
    }
    
    tfoot {
        font-weight: bold !important;
        background-color: #f5f5f5 !important;
    }
    
    /* Alert styling */
    .bg-yellow-50 {
        background-color: #fef3c7 !important;
        border: 1px solid #fbbf24 !important;
        padding: 0.3cm !important;
        page-break-inside: avoid !important;
    }
    
    /* Badges */
    .inline-flex {
        display: inline-block !important;
        padding: 0.1cm 0.2cm !important;
        font-size: 9pt !important;
    }
    
    /* Icon styling for print */
    .material-icons {
        display: none !important;
    }
    
    /* Page break rules */
    @page {
        size: A4;
        margin: 0.8cm;
    }
    
    /* Prevent orphans and widows */
    p, h1, h2, h3 {
        orphans: 2;
        widows: 2;
    }
    
    /* Ensure proper spacing */
    .flex {
        display: flex !important;
    }
    
    .gap-3 {
        gap: 0.3cm !important;
    }
    
    /* Info boxes */
    .p-3 {
        padding: 0.2cm !important;
        font-size: 10pt !important;
    }
}

@media print and (color) {
    /* Color-aware printing */
    .text-red-600 {
        color: #cc0000 !important;
    }
    
    .text-green-600 {
        color: #008000 !important;
    }
}
</style>
@endsection