@extends('layouts.user')

@section('content')
<div class="p-6">
    <!-- Notification Alert -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-3">
                <span class="material-icons text-red-600 text-sm">error</span>
                <div>
                    <p class="font-medium text-red-800">Validasi Gagal</p>
                    <ul class="mt-2 text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            <div class="flex items-start gap-3">
                <span class="material-icons text-red-600">close_circle</span>
                <div>
                    <p class="font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

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
                                            class="barang-select w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                            required>
                                        <option value="">-- Pilih Barang --</option>
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

<!-- JavaScript untuk Dynamic Items & AJAX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 1;
    const container = document.getElementById('items-container');
    const addButton = document.getElementById('add-item');
    const transaksiSelect = document.getElementById('transaksi_id');
    const detailDiv = document.getElementById('transaksi-detail');
    const detailItems = document.getElementById('detail-items');

    // ==========================================
    // MAIN: Fetch & Populate Detail Transaksi + Items
    // ==========================================
    transaksiSelect.addEventListener('change', function() {
        const transaksiId = this.value;
        
        if (!transaksiId) {
            detailDiv.classList.add('hidden');
            clearAllItems();
            return;
        }

        // Show loading state
        detailDiv.classList.remove('hidden');
        detailItems.innerHTML = '<li class="text-blue-600">⏳ Memuat detail transaksi...</li>';

        // AJAX Fetch ke endpoint /user/return/items/{id}
        fetch(`{{ url('/user/return/items') }}/${transaksiId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success === false || data.error) {
                    throw new Error(data.error || 'Gagal memuat data');
                }

                // Update detail info
                detailItems.innerHTML = `
                    <li><strong>Tanggal:</strong> ${data.tanggal}</li>
                    <li><strong>Total Transaksi:</strong> ${data.total_formatted}</li>
                    <li><strong>Jumlah Item:</strong> ${data.items.length} item</li>
                `;

                // Update dropdown barang untuk semua row
                populateBarangDropdowns(data.items);
            })
            .catch(error => {
                console.error('Error:', error);
                detailItems.innerHTML = `<li class="text-red-600">❌ Error: ${error.message}</li>`;
                clearAllItems();
            });
    });

    // ==========================================
    // Populate Barang Dropdown dengan Items dari Transaksi
    // ==========================================
    function populateBarangDropdowns(items) {
        // Buat HTML options dari items yang diterima
        const options = items.map(item => 
            `<option value="${item.barang_id}" data-max="${item.jumlah_dibeli}" data-harga="${item.harga_satuan}">
                ${item.nama_barang} (${item.kode_barang}) - Max: ${item.jumlah_dibeli} unit
            </option>`
        ).join('');

        // Update semua select barang yang ada di form
        document.querySelectorAll('.barang-select').forEach(select => {
            const currentValue = select.value;
            select.innerHTML = '<option value="">-- Pilih Barang --</option>' + options;
            if (currentValue && select.querySelector(`option[value="${currentValue}"]`)) {
                select.value = currentValue;
            }
        });
    }

    // Clear items ketika transaksi belum dipilih
    function clearAllItems() {
        document.querySelectorAll('.barang-select').forEach(select => {
            select.innerHTML = '<option value="">-- Pilih Barang --</option>';
        });
    }

    // ==========================================
    // Tambah Item Baru (Dynamic Row)
    // ==========================================
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

        // Add class untuk tracking
        newItem.querySelector('select').classList.add('barang-select');

        // Show remove button
        newItem.querySelector('.remove-item').classList.remove('hidden');
        
        container.appendChild(newItem);
        itemIndex++;

        // Re-populate barang dropdown jika transaksi sudah dipilih
        if (transaksiSelect.value) {
            const event = new Event('change');
            transaksiSelect.dispatchEvent(event);
        }

        updateRemoveButtons();
    });

    // ==========================================
    // Hapus Item Baris
    // ==========================================
    function updateRemoveButtons() {
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                if (document.querySelectorAll('.item-row').length > 1) {
                    this.closest('.item-row').remove();
                }
            });
        });
    }

    // Initialize remove buttons
    updateRemoveButtons();

    // ==========================================
    // Validasi Jumlah Return saat Submit
    // ==========================================
    document.querySelector('form').addEventListener('submit', function(e) {
        let hasError = false;

        document.querySelectorAll('.item-row').forEach(row => {
            const barangSelect = row.querySelector('.barang-select');
            const jumlahInput = row.querySelector('input[name*="[jumlah]"]');

            if (!barangSelect.value) {
                barangSelect.classList.add('border-red-500');
                hasError = true;
            }

            if (!jumlahInput.value || parseInt(jumlahInput.value) <= 0) {
                jumlahInput.classList.add('border-red-500');
                hasError = true;
            }

            // Validasi jumlah tidak melebihi max
            const selectedOption = barangSelect.options[barangSelect.selectedIndex];
            if (selectedOption && selectedOption.dataset.max) {
                const maxJumlah = parseInt(selectedOption.dataset.max);
                const returnJumlah = parseInt(jumlahInput.value) || 0;

                if (returnJumlah > maxJumlah) {
                    alert(`Jumlah return tidak boleh melebihi ${maxJumlah} untuk barang ini`);
                    jumlahInput.classList.add('border-red-500');
                    hasError = true;
                }
            }
        });

        if (hasError) {
            e.preventDefault();
            alert('Silakan isi semua field dengan benar');
        }
    });

    // Clear error styling saat user mulai input
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('barang-select') || e.target.name.includes('[jumlah]')) {
            e.target.classList.remove('border-red-500');
        }
    });
});
</script>
@endsection