@extends('layouts.user')

@section('content')
<div class="p-6">
    <!-- Header with Back Button -->
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('user.return.index') }}" 
           class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-200 hover:bg-gray-300 transition">
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
                            {{ $return->items->sum('jumlah') }} Unit
                        </p>
                    </div>
                </div>
            </div>

            <!-- Catatan Return (jika ada) -->
            @if($return->catatan)
            <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-start gap-2">
                    <span class="material-icons text-yellow-600 text-sm">note</span>
                    <div>
                        <p class="text-sm font-medium text-yellow-800">Catatan:</p>
                        <p class="text-sm text-yellow-700 mt-1">{{ $return->catatan }}</p>
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
                            <div class="font-medium">{{ $item->barang->nama ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">SKU: {{ $item->barang->kode ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $item->jumlah }} Unit
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">
                            Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                            Rp {{ number_format($item->harga_satuan * $item->jumlah, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded text-xs bg-gray-100">
                                <span class="material-icons text-xs">info</span>
                                {{ $item->alasan ?? 'Tidak ada alasan' }}
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
                                return $item->harga_satuan * $item->jumlah;
                            }), 0, ',', '.') }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-6 flex gap-3">
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

        <!-- Kalau mau ada tombol hapus return -->
        @if(auth()->user()->role === 'admin')
        <form action="{{ route('user.return.destroy', $return->id) }}" method="POST" 
              onsubmit="return confirm('Yakin ingin menghapus return ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" 
                    class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                <span class="material-icons text-sm">delete</span>
                Hapus
            </button>
        </form>
        @endif
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body * {
        visibility: hidden;
    }
    .p-6, .p-6 * {
        visibility: visible;
    }
    .p-6 {
        position: absolute;
        left: 0;
        top: 0;
    }
    button, a[href*="index"] {
        display: none !important;
    }
}
</style>
@endsection