<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use Carbon\Carbon;

class UserHistory extends Component
{
    use WithPagination;

    public $showDetailModal = false;
    public $selectedDate;
    public $detailTransaksi = [];
    
    // Filter properties
    public $filterBulan = '';
    public $filterTahun = '';
    public $availableYears = [];
    public $availableMonths = [];

    protected $paginationTheme = 'tailwind';

    public function mount()
    {
        // Set default filter ke bulan dan tahun sekarang
        $this->filterBulan = now()->month;
        $this->filterTahun = now()->year;
        
        // Ambil semua tahun yang ada transaksi user ini
        $this->availableYears = Transaksi::where('user_id', Auth::id())
            ->selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
            
        // Kalau tidak ada tahun, set tahun sekarang
        if (empty($this->availableYears)) {
            $this->availableYears = [now()->year];
        }

        // Generate array bulan dari Carbon (1-12)
        $this->availableMonths = collect(range(1, 12))->map(function ($month) {
            return [
                'value' => $month,
                'label' => Carbon::create(null, $month, 1)->translatedFormat('F') // Nama bulan full
            ];
        })->toArray();
    }

    public function resetFilter()
    {
        $this->filterBulan = '';
        $this->filterTahun = '';
        $this->resetPage();
    }

    public function updatedFilterBulan()
    {
        $this->resetPage();
    }

    public function updatedFilterTahun()
    {
        $this->resetPage();
    }

    public function showDetail($tanggal)
    {
        $this->selectedDate = $tanggal;
        
        $this->detailTransaksi = Transaksi::with('barang')
            ->where('user_id', Auth::id())
            ->whereDate('transaksi.created_at', $tanggal)
            ->select('transaksi.*')
            ->orderBy('transaksi.created_at', 'asc')
            ->get()
            ->map(function ($transaksi) {
                return [
                    'id' => $transaksi->id,
                    'waktu' => $transaksi->created_at->format('H:i:s'),
                    'nama_barang' => $transaksi->barang->nama_barang,
                    'kategori' => $transaksi->barang->kategori,
                    'harga_satuan' => $transaksi->barang->harga,
                    'jumlah' => $transaksi->jumlah,
                    'subtotal' => $transaksi->barang->harga * $transaksi->jumlah,
                    'jenis' => $transaksi->jenis,
                ];
            });

        $this->showDetailModal = true;
    }

    public function closeModal()
    {
        $this->showDetailModal = false;
        $this->selectedDate = null;
        $this->detailTransaksi = [];
    }

    public function render()
    {
        $query = Transaksi::select(
                DB::raw('DATE(transaksi.created_at) as tanggal'),
                DB::raw('COUNT(*) as total_transaksi'),
                DB::raw('SUM(transaksi.jumlah) as total_item'),
                DB::raw('SUM(transaksi.jumlah * barang.harga) as total_harga')
            )
            ->join('barang', 'barang.id', '=', 'transaksi.barang_id')
            ->where('transaksi.user_id', Auth::id());

        // Apply filter bulan
        if ($this->filterBulan !== '') {
            $query->whereMonth('transaksi.created_at', $this->filterBulan);
        }

        // Apply filter tahun
        if ($this->filterTahun !== '') {
            $query->whereYear('transaksi.created_at', $this->filterTahun);
        }

        $histories = $query->groupBy(DB::raw('DATE(transaksi.created_at)'))
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('livewire.user-history', [
            'histories' => $histories
        ]);
    }
}