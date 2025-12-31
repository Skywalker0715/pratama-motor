<div>
    <h1 class="text-2xl font-bold mb-6">Transaksi Penjualan</h1>

    {{-- GRID LAYOUT: FORM + INFO --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- FORM TRANSAKSI --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg p-6">
                
                @if (session()->has('success'))
                    <div class="bg-green-100 text-green-700 p-3 rounded-lg mb-4 border border-green-300 flex items-center gap-2">
                        <span class="material-icons text-green-600">check_circle</span>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-100 text-red-700 p-3 rounded-lg mb-4 border border-red-300 flex items-center gap-2">
                        <span class="material-icons text-red-600">error</span>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                <form wire:submit.prevent="simpanTransaksi" class="space-y-5">

                    {{-- SEARCH BARANG --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="material-icons text-sm align-middle mr-1">search</span>
                            Cari Barang
                        </label>
                        <input 
                            type="text"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Ketik nama barang atau kategori..."
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="material-icons text-xs align-middle">info</span>
                            Ditemukan {{ count($barangList) }} barang
                        </p>
                    </div>

                    {{-- PILIH BARANG --}}
                    <div>
                        <label for="barang_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Pilih Barang <span class="text-red-500">*</span>
                        </label>
                        <select 
                            wire:model.live="barang_id"
                            id="barang_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('barang_id') border-red-500 @enderror">
                            <option value="">-- Pilih Barang --</option>
                            @foreach ($barangList as $barang)
                                <option value="{{ $barang->id }}">
                                    {{ $barang->nama_barang }} - {{ $barang->kategori }} | Rp {{ number_format($barang->harga, 0, ',', '.') }} (Stok: {{ $barang->stok }})
                                </option>
                            @endforeach
                        </select>
                        @error('barang_id')
                            <span class="text-red-500 text-sm mt-1 block flex items-center gap-1">
                                <span class="material-icons text-xs">error</span>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- JUMLAH --}}
                    <div>
                        <label for="jumlah" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jumlah <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number"
                                wire:model.live="jumlah"
                                id="jumlah"
                                min="1"
                                placeholder="Masukkan jumlah"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all @error('jumlah') border-red-500 @enderror">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">item</span>
                        </div>
                        @error('jumlah')
                            <span class="text-red-500 text-sm mt-1 block flex items-center gap-1">
                                <span class="material-icons text-xs">error</span>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    {{-- SUBMIT BUTTON --}}
                    <button 
                        type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all font-semibold shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <span class="material-icons">shopping_cart</span>
                        Simpan Transaksi
                    </button>

                </form>
            </div>
        </div>

        {{-- INFO PANEL KANAN --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- PREVIEW BARANG TERPILIH --}}
            @if($selectedBarang)
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-lg p-5 border border-blue-200">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-icons text-blue-600">shopping_bag</span>
                        <h3 class="font-bold text-gray-800">Barang Dipilih</h3>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Nama Barang</p>
                            <p class="font-bold text-gray-900">{{ $selectedBarang->nama_barang }}</p>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Kategori</p>
                                <p class="text-sm font-semibold text-gray-700">{{ $selectedBarang->kategori }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Stok Tersedia</p>
                                <p class="text-sm font-semibold text-green-600">{{ $selectedBarang->stok }} item</p>
                            </div>
                        </div>
                        
                        <div class="pt-3 border-t border-gray-200">
                            <p class="text-xs text-gray-500 mb-1">Harga Satuan</p>
                            <p class="text-lg font-bold text-blue-600">Rp {{ number_format($selectedBarang->harga, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- SUBTOTAL --}}
            @if($barang_id && $jumlah)
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-lg p-5 border border-green-200">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="material-icons text-green-600">calculate</span>
                        <h3 class="font-bold text-gray-800">Ringkasan</h3>
                    </div>
                    
                    <div class="bg-white rounded-lg p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Jumlah</span>
                            <span class="font-semibold text-gray-900">{{ $jumlah }} item</span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Harga Satuan</span>
                            <span class="font-semibold text-gray-900">Rp {{ number_format($selectedBarang->harga, 0, ',', '.') }}</span>
                        </div>
                        
                        <div class="pt-3 border-t-2 border-green-200">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-bold text-gray-700">SUBTOTAL</span>
                                <span class="text-xl font-bold text-green-600">
                                    Rp {{ number_format($subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 rounded-xl shadow-lg p-5 border border-gray-200">
                    <div class="text-center py-8">
                        <span class="material-icons text-6xl text-gray-300 mb-3">shopping_cart</span>
                        <p class="text-gray-500 text-sm">Pilih barang dan jumlah untuk melihat subtotal</p>
                    </div>
                </div>
            @endif

            {{-- QUICK STATS --}}
            <div class="bg-white rounded-xl shadow-lg p-5 border border-gray-200">
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-icons text-gray-600">inventory_2</span>
                    <h3 class="font-bold text-gray-800">Info Stok</h3>
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Total Barang</span>
                        <span class="font-semibold text-gray-900">{{ $totalBarang }} item</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600">Barang Tersedia</span>
                        <span class="font-semibold text-green-600">{{ count($barangList) }} item</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>