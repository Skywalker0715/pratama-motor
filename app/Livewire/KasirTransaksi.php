<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KasirTransaksi extends Component
{
    public $barang_id;
    public $jumlah;
    public $search = '';
    public $barangList = [];
    public $selectedBarang = null;
    public $subtotal = 0;
    public $totalBarang = 0;
    public $cart = [];
    public $grandTotal = 0;

    protected $rules = [
        'barang_id' => 'required|exists:barang,id',
        'jumlah' => 'required|integer|min:1',
    ];

    protected $messages = [
        'barang_id.required' => 'Pilih barang terlebih dahulu',
        'barang_id.exists' => 'Barang tidak ditemukan',
        'jumlah.required' => 'Jumlah harus diisi',
        'jumlah.min' => 'Jumlah minimal 1',
    ];

    public function mount()
    {
        $this->loadBarang();
        $this->totalBarang = Barang::count();
    }

    public function loadBarang()
    {
        $query = Barang::where('stok', '>', 0);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_barang', 'like', '%' . $this->search . '%')
                  ->orWhere('kategori', 'like', '%' . $this->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $this->search . '%');
            });
        }

        $this->barangList = $query->orderBy('nama_barang')->take(50)->get();
    }

    public function updatedSearch()
    {
        $this->loadBarang();
    }

    public function updatedBarangId($value)
    {
        if ($value) {
            $this->selectedBarang = Barang::find($value);
            $this->calculateSubtotal();
        } else {
            $this->selectedBarang = null;
            $this->subtotal = 0;
        }
    }

    public function updatedJumlah()
    {
        $this->calculateSubtotal();
    }

    public function calculateSubtotal()
    {
        if ($this->selectedBarang && $this->jumlah) {
            $this->subtotal = $this->selectedBarang->harga * $this->jumlah;
        } else {
            $this->subtotal = 0;
        }
    }

    public function tambahKeKeranjang()
    {
        $this->validate();

        $barang = Barang::find($this->barang_id);

        // Cek stok dengan memperhitungkan item yang sudah ada di keranjang
        $qtyInCart = 0;
        foreach ($this->cart as $item) {
            if ($item['barang_id'] == $this->barang_id) {
                $qtyInCart += $item['jumlah'];
            }
        }

        if (($this->jumlah + $qtyInCart) > $barang->stok) {
            $this->addError('jumlah', 'Stok tidak mencukupi. Sisa: ' . $barang->stok . ', Di keranjang: ' . $qtyInCart);
            return;
        }

        // Tambah atau update item di keranjang
        $found = false;
        foreach ($this->cart as &$item) {
            if ($item['barang_id'] == $this->barang_id) {
                $item['jumlah'] += $this->jumlah;
                $item['subtotal'] = $item['jumlah'] * $item['harga'];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $this->cart[] = [
                'barang_id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'harga' => $barang->harga,
                'jumlah' => $this->jumlah,
                'subtotal' => $this->jumlah * $barang->harga,
            ];
        }

        $this->calculateGrandTotal();
        $this->reset(['barang_id', 'jumlah', 'selectedBarang', 'subtotal', 'search']);
        $this->dispatch('show-notification', ['type' => 'success', 'message' => 'Item ditambahkan ke keranjang']);
    }

    public function hapusDariKeranjang($index)
    {
        if (isset($this->cart[$index])) {
            unset($this->cart[$index]);
            $this->cart = array_values($this->cart);
            $this->calculateGrandTotal();
        }
    }

    public function calculateGrandTotal()
    {
        $this->grandTotal = array_sum(array_column($this->cart, 'subtotal'));
    }

    public function simpanTransaksi()
    {
        // Validasi: Keranjang tidak boleh kosong
        if (empty($this->cart)) {
            $this->dispatch('show-notification', ['type' => 'error', 'message' => 'Keranjang belanja kosong. Tambahkan barang terlebih dahulu.']);
            return;
        }

        try {
            DB::beginTransaction();

            // Generate Kode Transaksi: TRX-{user_id}-{YmdHis}
            $transaksiKode = 'TRX-' . Auth::id() . '-' . now()->format('YmdHis');

            foreach ($this->cart as $item) {
                $barang = Barang::lockForUpdate()->find($item['barang_id']);
                
                if (!$barang || $barang->stok < $item['jumlah']) {
                    throw new \Exception("Stok {$item['nama_barang']} tidak mencukupi saat proses checkout.");
                }

                Transaksi::create([
                    'user_id' => Auth::id(),
                    'barang_id' => $item['barang_id'],
                    'jumlah' => $item['jumlah'],
                    'jenis' => 'penjualan',
                    'tanggal' => now(),
                    'transaksi_kode' => $transaksiKode,
                    'keterangan' => 'Penjualan Kasir',
                ]);

                $barang->decrement('stok', $item['jumlah']);
            }

            DB::commit();

            $this->dispatch('show-notification', [
                'type' => 'success', 
                'message' => 'Transaksi berhasil! Kode: ' . $transaksiKode . '. Total: Rp ' . number_format($this->grandTotal, 0, ',', '.')
            ]);

            $this->reset(['barang_id', 'jumlah', 'selectedBarang', 'subtotal', 'search', 'cart', 'grandTotal']);
            
            $this->loadBarang();
            $this->totalBarang = Barang::count();

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            $this->dispatch('show-notification', [
                'type' => 'error', 
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.kasir-transaksi');
    }
}