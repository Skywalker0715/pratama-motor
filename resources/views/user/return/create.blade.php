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
            <h1 class="text-2xl font-bold text-gray-800">Return Barang</h1>
            <p class="text-sm text-gray-500">Kembalikan barang dari transaksi sebelumnya</p>
        </div>
    </div>

    <form method="POST" action="{{ route('user.return.store') }}" class="space-y-6">
        @csrf

        <!-- Card Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="space-y-6">
                <!-- Pilih Transaksi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="material-icons text-sm align-middle">receipt</span>
                        Pilih Transaksi <span class="text-red-500">*</span>
                    </label>
                    <select name="transaksi_id" 
                            id="transaksi_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('transaksi_id') border-red-500 @enderror"
                            required>
                        <option value="">-- Pilih Transaksi --</option>
                        @foreach ($transaksis as $trx)
                            <option value="{{ $trx->id }}" {{ old('transaksi_id') == $trx->id ? 'selected' : '' }}>
                                #{{ $trx->id }} - {{ \Carbon\Carbon::parse($trx->created_at)->format('d M Y, H:i') }} 
                                - Total: Rp {{ number_format($trx->total ?? 0, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </select>
                    @error('transaksi_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        <span class="material-icons text-xs align-middle">info</span>
                        Pilih transaksi yang ingin dikembalikan barangnya
                    </p>
                </div>

                <!-- Detail Transaksi (akan muncul setelah pilih transaksi) -->
                <div id="transaksi-detail" class="hidden p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex items-start gap-2">
                        <span class="material-icons text-blue-600 text-sm">info</span>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium">Detail Transaksi:</p>
                            <ul class="mt-2 space-y-1 list-disc list-inside" id="detail-items">
                                <!-- Will be filled by JavaScript -->
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pilih Item yang Dikembalikan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        <span class="material-icons text-sm align-middle">inventory</span>
                        Pilih Barang yang Dikembalikan <span class="text-red-500">*</span>
                    </label>
                    
                    <div id="items-container" class="space-y-3">
                        <!-- Item pertama -->
                        <div class="item-row p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Pilih Barang -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Barang</label>
                                    <select name="items[0][barang_id]" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required>
                                        <option value="">-- Pilih Barang --</option>
                                        @foreach($barangs ?? [] as $barang)
                                            <option value="{{ $barang->id }}">
                                                {{ $barang->nama }} - {{ $barang->kode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Jumlah -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Jumlah</label>
                                    <input type="number" 
                                           name="items[0][jumlah]" 
                                           min="1"
                                           placeholder="0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           required>
                                </div>

                                <!-- Alasan per item -->
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Alasan</label>
                                    <input type="text" 
                                           name="items[0][alasan]" 
                                           placeholder="Rusak, salah kirim, dll"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                           required>
                                </div>
                            </div>
                            <button type="button" 
                                    class="remove-item mt-3 text-red-600 hover:text-red-700 text-sm hidden">
                                <span class="material-icons text-sm align-middle">delete</span>
                                Hapus Item
                            </button>
                        </div>
                    </div>

                    <!-- Tombol Tambah Item -->
                    <button type="button" 
                            id="add-item"
                            class="mt-3 flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                        <span class="material-icons text-sm">add_circle</span>
                        Tambah Item
                    </button>
                </div>

                <!-- Catatan Return -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="material-icons text-sm align-middle">note</span>
                        Catatan Return
                    </label>
                    <textarea name="catatan" 
                              rows="4"
                              placeholder="Tambahkan catatan atau penjelasan tambahan..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition @error('catatan') border-red-500 @enderror">{{ old('catatan') }}</textarea>
                    @error('catatan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-500">
                        <span class="material-icons text-xs align-middle">info</span>
                        Opsional: Jelaskan alasan return secara keseluruhan
                    </p>
                </div>
            </div>
        </div>

        <!-- Info Warning -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start gap-3">
                <span class="material-icons text-yellow-600">warning</span>
                <div class="text-sm text-yellow-800">
                    <p class="font-medium">Perhatian:</p>
                    <ul class="mt-2 space-y-1 list-disc list-inside">
                        <li>Pastikan barang yang dikembalikan masih dalam kondisi baik</li>
                        <li>Return akan menambah stok barang kembali ke sistem</li>
                        <li>Proses return tidak dapat dibatalkan setelah disimpan</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <button type="submit" 
                    class="flex items-center gap-2 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                <span class="material-icons text-sm">keyboard_return</span>
                Proses Return
            </button>
            
            <a href="{{ route('user.return.index') }}" 
               class="flex items-center gap-2 px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                <span class="material-icons text-sm">close</span>
                Batal
            </a>
        </div>
    </form>
</div>

<!-- JavaScript untuk Dynamic Items -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');

    // Tambah item baru
    addButton.addEventListener('click', function() {
        const newItem = document.querySelector('.item-row').cloneNode(true);
        
        // Update index
        newItem.querySelectorAll('input, select').forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                input.setAttribute('name', name.replace('[0]', `[${itemIndex}]`));
                input.value = '';
            }
        });

        // Show remove button
        newItem.querySelector('.remove-item').classList.remove('hidden');
        
        container.appendChild(newItem);
        itemIndex++;

        // Update event listener untuk remove button
        updateRemoveButtons();
    });

    // Fungsi untuk update remove buttons
    function updateRemoveButtons() {
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                if (document.querySelectorAll('.item-row').length > 1) {
                    this.closest('.item-row').remove();
                }
            });
        });
    }

    updateRemoveButtons();

    // Show detail transaksi saat pilih
    document.getElementById('transaksi_id').addEventListener('change', function() {
        const detailDiv = document.getElementById('transaksi-detail');
        if (this.value) {
            // Simulasi - dalam real case, fetch via AJAX
            detailDiv.classList.remove('hidden');
            document.getElementById('detail-items').innerHTML = `
                <li>Loading detail transaksi...</li>
            `;

            // TODO: Fetch real data via AJAX
            // fetch('/api/transaksi/' + this.value)
            //     .then(response => response.json())
            //     .then(data => {
            //         // Update detail items
            //     });
        } else {
            detailDiv.classList.add('hidden');
        }
    });
});
</script>
@endsection