@extends('layouts.user')

@section('content')
<!-- Print Stylesheet -->
<link rel="stylesheet" href="{{ asset('css/print-return.css') }}" media="print">

<div id="a4-content">
<div class="w-full p-6">
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
    <div class="flex gap-3 no-print mt-6">
        {{-- Cetak A4 --}}
        <button onclick="window.print()" 
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
            <span class="material-icons">print</span>
            Cetak A4
        </button>

        {{-- Cetak Struk Thermal --}}
        <button onclick="printThermal()" 
                class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition">
            <span class="material-icons">receipt</span>
            Cetak Struk
        </button>
    </div>

    <script>
    function printThermal() {
        // Add class to body to trigger thermal styles
        document.body.classList.add('thermal-mode');
        
        // Show thermal receipt
        document.getElementById('thermal-receipt').style.display = 'block';
        
        // Print
        window.print();
        
        // Hide thermal receipt again after print dialog closes
        setTimeout(() => {
            document.getElementById('thermal-receipt').style.display = 'none';
            document.body.classList.remove('thermal-mode');
        }, 500);
    }
    </script>
</div>
</div>

{{-- THERMAL RECEIPT SECTION (HIDDEN BY DEFAULT) --}}
<div id="thermal-receipt" style="display: none;">
    <style>
        @media print {
            body.thermal-mode #a4-content { display: none !important; }
            body.thermal-mode #thermal-receipt { display: block !important; }
            
            @page { 
                size: 80mm auto; 
                margin: 0; 
            }
            
            #thermal-receipt {
                width: 80mm !important;
                font-family: 'Courier New', monospace !important;
                font-size: 11px !important;
                line-height: 1.3 !important;
                padding: 5mm !important;
            }
        }
    </style>

    <div style="text-align: center; margin-bottom: 3mm;">
        <div style="font-size: 16px; font-weight: bold;">PRATAMA MOTOR</div>
        <div style="font-size: 12px; font-weight: bold; margin-top: 1mm;">BUKTI RETURN BARANG</div>
    </div>

    <div style="border-top: 2px solid #000; margin: 2mm 0;"></div>

    <div style="margin: 1mm 0; font-size: 10px;">
        <div style="display: flex; justify-content: space-between;">
            <span>ID Return</span>
            <span style="font-weight: bold;">#{{ $return->id }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 0.5mm;">
            <span>Tanggal</span>
            <span>{{ \Carbon\Carbon::parse($return->tanggal)->format('d M Y H:i') }}</span>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 0.5mm;">
            <span>ID Transaksi</span>
            <span style="font-weight: bold;">#{{ $return->transaksi_id }}</span>
        </div>
    </div>

    <div style="border-top: 1px dashed #000; margin: 1mm 0;"></div>

    @foreach($return->items as $item)
    <div style="margin: 2mm 0; font-size: 10px;">
        <div style="font-weight: bold;">{{ $item->barang->nama_barang ?? 'N/A' }}</div>
        <div style="font-size: 9px; color: #666;">SKU: {{ $item->barang->kode_barang ?? '-' }}</div>
        <div style="display: flex; justify-content: space-between; margin-top: 0.5mm;">
            <span>{{ $item->quantity }} x Rp {{ number_format($item->barang->harga ?? 0, 0, ',', '.') }}</span>
            <span style="font-weight: bold;">Rp {{ number_format($item->quantity * ($item->barang->harga ?? 0), 0, ',', '.') }}</span>
        </div>
    </div>
    @endforeach

    <div style="border-top: 2px solid #000; margin: 1mm 0;"></div>

    <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: bold; margin: 1mm 0;">
        <span>TOTAL RETURN</span>
        <span>Rp {{ number_format($return->items->sum(fn($item) => $item->quantity * ($item->barang->harga ?? 0)), 0, ',', '.') }}</span>
    </div>

    <div style="border-top: 2px solid #000; margin: 1mm 0;"></div>

    @if($return->alasan)
    <div style="margin: 1mm -5mm 1mm -5mm; padding: 1mm 5mm; border-left: 1px dashed #000; border-right: 1px dashed #000; font-size: 9px;">
        <strong>Alasan:</strong> {{ $return->alasan }}
    </div>
    @endif

    <div style="text-align: center; margin-top: 2mm; font-size: 9px;">
        <p style="margin: 1mm 0;">Terima kasih</p>
        <p style="margin: 0;">Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
</div>

@endsection