<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaksi;
use Carbon\Carbon;

class UserHistory extends Component
{
    use WithPagination;

    public $showDetailModal = false;
    public $selectedDate;
    public $detailTransaksi = [];
    public $viewMode = 'continuous';
    
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

    public function setViewMode(string $mode)
    {
        $this->viewMode = $mode;
    }

    public function showDetail($identifier)
    {
        // Identifier bisa berupa transaksi_kode (string TRX-...) atau ID (integer) untuk data lama
        $this->selectedDate = null; 
        $this->viewMode = 'continuous';
        
        $query = Transaksi::with('barang')
            ->where('user_id', Auth::id());

        if (str_starts_with((string)$identifier, 'TRX-')) {
            $query->where('transaksi_kode', $identifier);
        } else {
            // Fallback untuk data lama (legacy)
            $query->where('id', $identifier);
        }
            
        $data = $query->orderBy('created_at', 'asc')->get();

        if ($data->isNotEmpty()) {
            $this->selectedDate = $data->first()->created_at;
        }

        $this->detailTransaksi = $data
            ->map(function ($transaksi) {
                return [
                    'id' => $transaksi->id,
                    'waktu' => $transaksi->created_at->timezone('Asia/Jakarta')->format('H:i'),                    
                    'nama_barang' => $transaksi->barang->nama_barang,
                    'kategori' => $transaksi->barang->kategori,
                    'harga_satuan' => $transaksi->barang->harga,
                    'jumlah' => $transaksi->jumlah,
                    'subtotal' => $transaksi->barang->harga * $transaksi->jumlah,
                    'jenis' => $transaksi->jenis,
                    'transaksi_kode' => $transaksi->transaksi_kode,
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
                // Gunakan COALESCE untuk mengelompokkan: Jika ada kode, pakai kode. Jika tidak (lama), pakai ID.
                DB::raw('COALESCE(transaksi_kode, transaksi.id) as kode_referensi'),
                DB::raw('MAX(transaksi_kode) as display_kode'),
                DB::raw('MAX(transaksi.created_at) as tanggal'),
                DB::raw('COUNT(*) as total_item_rows'),
                DB::raw('SUM(transaksi.jumlah) as total_item'),
                DB::raw('SUM(transaksi.jumlah * barang.harga) as total_harga')
            )
            ->join('barang', 'barang.id', '=', 'transaksi.barang_id')
            ->where('transaksi.user_id', Auth::id())
            ->where('jenis', 'penjualan'); // Fokus hanya pada penjualan kasir

        // Apply filter bulan
        if ($this->filterBulan !== '') {
            $query->whereMonth('transaksi.created_at', $this->filterBulan);
        }

        // Apply filter tahun
        if ($this->filterTahun !== '') {
            $query->whereYear('transaksi.created_at', $this->filterTahun);
        }

        $histories = $query->groupBy(DB::raw('COALESCE(transaksi_kode, transaksi.id)'))
            ->orderByDesc('tanggal')
            ->paginate(10);

        return view('livewire.user-history', [
            'histories' => $histories
        ]);
    }

    public function printDetail()
    {
        $pdf = Pdf::loadView('user.print-history.print-detail-continuous', [
            'selectedDate' => $this->selectedDate,
            'detailTransaksi' => $this->detailTransaksi
        ])->setPaper('a4');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'detail-transaksi-' . ($this->selectedDate ? \Carbon\Carbon::parse($this->selectedDate)->format('d-m-Y') : 'unknown') . '.pdf'
        );
    }
}