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

        $this->barangList = $query->orderBy('nama_barang')->get();
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

    public function simpanTransaksi()
    {
        try {
            $this->validate();

            $barang = Barang::findOrFail($this->barang_id);

            if ($this->jumlah > $barang->stok) {
                $this->addError('jumlah', 'Stok tidak mencukupi. Sisa stok: ' . $barang->stok);
                return;
            }

            DB::beginTransaction();

            Transaksi::create([
                'user_id' => Auth::id(),
                'barang_id' => $barang->id,
                'jumlah' => $this->jumlah,
                'jenis' => 'penjualan',
                'tanggal' => now(),
            ]);

            $barang->decrement('stok', $this->jumlah);

            DB::commit();

            $this->dispatch('show-notification', [
                'type' => 'success', 
                'message' => 'Transaksi berhasil disimpan! Subtotal: Rp ' . number_format($this->subtotal, 0, ',', '.')
            ]);

            $this->reset(['barang_id', 'jumlah', 'selectedBarang', 'subtotal', 'search']);
            
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