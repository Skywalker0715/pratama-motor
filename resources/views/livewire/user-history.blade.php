<div>
    <h1 class="text-2xl font-bold mb-6">Riwayat Transaksi</h1>

    {{-- FILTER SECTION --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <div class="flex flex-wrap items-end gap-4">
            
            {{-- FILTER BULAN (DINAMIS DARI CARBON) --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Bulan</label>
                <select wire:model.live="filterBulan" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Bulan</option>
                    @foreach($availableMonths as $month)
                        <option value="{{ $month['value'] }}">{{ $month['label'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- FILTER TAHUN (DINAMIS DARI DATABASE) --}}
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Tahun</label>
                <select wire:model.live="filterTahun" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Tahun</option>
                    @foreach($availableYears as $year)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <button 
                    wire:click="resetFilter"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                    <span class="material-icons text-sm align-middle mr-1">refresh</span>
                    Reset Filter
                </button>
            </div>
        </div>

        {{-- Filter Info (DINAMIS DARI CARBON) --}}
        @if($filterBulan !== '' || $filterTahun !== '')
            <div class="mt-3 flex items-center gap-2 text-sm text-gray-600">
                <span class="material-icons text-sm">filter_alt</span>
                <span>Menampilkan data:
                    @if($filterBulan !== '')
                        {{ Carbon\Carbon::create(null, $filterBulan, 1)->translatedFormat('F') }}
                    @endif
                    @if($filterTahun !== '')
                        {{ $filterBulan !== '' ? '' : 'Tahun' }} {{ $filterTahun }}
                    @endif
                </span>
            </div>
        @endif
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Transaksi</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Total Item</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Total Harga</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($histories as $history)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($history->tanggal)->translatedFormat('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                    {{ $history->total_transaksi }}x
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $history->total_item }} item
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                Rp {{ number_format($history->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <button 
                                    wire:click="showDetail('{{ $history->tanggal }}')"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-xs font-medium">
                                    <span class="material-icons text-sm mr-1">visibility</span>
                                    Lihat Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-icons text-6xl text-gray-300 mb-3">receipt_long</span>
                                    <p class="text-gray-500 font-medium mb-1">Tidak Ada Transaksi</p>
                                    <p class="text-gray-400 text-sm">Belum ada transaksi pada periode ini</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($histories->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $histories->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL DETAIL --}}
    @if($showDetailModal)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:click="closeModal">
            <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[85vh] flex flex-col" wire:click.stop>
                
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50 flex-shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Detail Transaksi</h3>
                        <p class="text-sm text-gray-600">
                            {{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d F Y') }}
                        </p>
                    </div>
                    <button 
                        wire:click="closeModal"
                        class="text-gray-400 hover:text-gray-600 transition-colors p-1">
                        <span class="material-icons">close</span>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto flex-1">
                    <div class="space-y-4">
                        @foreach($detailTransaksi as $index => $detail)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-bold">
                                            {{ $index + 1 }}
                                        </span>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $detail['nama_barang'] }}</h4>
                                            <span class="text-xs text-gray-500">{{ $detail['kategori'] }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">
                                        {{ ucfirst($detail['jenis']) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-3 gap-4 bg-gray-50 rounded-lg p-3">
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Waktu</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $detail['waktu'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Harga Satuan</p>
                                        <p class="text-sm font-medium text-gray-900">Rp {{ number_format($detail['harga_satuan'], 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Jumlah</p>
                                        <p class="text-sm font-medium text-gray-900">{{ $detail['jumlah'] }} item</p>
                                    </div>
                                </div>

                                <div class="mt-3 pt-3 border-t border-gray-200 flex items-center justify-between">
                                    <span class="text-sm text-gray-600">Subtotal:</span>
                                    <span class="text-lg font-bold text-blue-600">
                                        Rp {{ number_format($detail['subtotal'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 pt-4 border-t-2 border-gray-300">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Transaksi: <span class="font-semibold">{{ count($detailTransaksi) }}x</span></p>
                                <p class="text-sm text-gray-600">Total Item: <span class="font-semibold">{{ collect($detailTransaksi)->sum('jumlah') }} item</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600 mb-1">Grand Total</p>
                                <p class="text-2xl font-bold text-blue-600">
                                    Rp {{ number_format(collect($detailTransaksi)->sum('subtotal'), 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end flex-shrink-0">
                    <button 
                        wire:click="closeModal"
                        class="px-6 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>