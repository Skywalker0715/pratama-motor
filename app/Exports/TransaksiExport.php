<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;    
use App\Models\Transaksi;
use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;

class TransaksiExport implements FromCollection, WithHeadings
{
    protected $from;
    protected $to;
    protected $keyword;

    public function __construct($from = null, $to = null, $keyword = null)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->keyword = $keyword;
    }

   public function collection()
    {
        // 1. Transactions
        $transactions = Transaksi::with(['barang', 'user'])
            ->when($this->from && $this->to, function ($q) {
                $q->whereBetween('tanggal', [
                    Carbon::parse($this->from)->startOfDay(),
                    Carbon::parse($this->to)->endOfDay()
                ]);
            })
            ->when($this->keyword, function ($q) {
                $keyword = $this->keyword;
                $q->where(function ($sub) use ($keyword) {
                    $sub->whereHas('barang', function ($b) use ($keyword) {
                            $b->where('nama_barang', 'like', "%{$keyword}%")
                              ->orWhere('kode_barang', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$keyword}%"))
                        ->orWhere('transaksi_kode', 'like', "%{$keyword}%")
                        ->orWhere('jenis', 'like', "%{$keyword}%");
                });
            })
            ->get();

        // 2. Returns (StockMovements)
        $returns = StockMovement::with(['barang', 'user'])
            ->where('type', 'return')
            ->when($this->from && $this->to, function ($q) {
                $q->whereBetween('created_at', [
                    Carbon::parse($this->from)->startOfDay(),
                    Carbon::parse($this->to)->endOfDay()
                ]);
            })
            ->when($this->keyword, function ($q) {
                $keyword = $this->keyword;
                $q->where(function ($sub) use ($keyword) {
                    $sub->whereHas('barang', function ($b) use ($keyword) {
                            $b->where('nama_barang', 'like', "%{$keyword}%")
                              ->orWhere('kode_barang', 'like', "%{$keyword}%");
                        })
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$keyword}%"))
                        ->orWhere('type', 'like', "%{$keyword}%");
                });
            })
            ->get()
            ->map(function ($item) {
                $item->jenis = 'return';
                $item->jumlah = $item->quantity;
                return $item;
            });

        // 3. Merge and Sort
        $merged = $transactions->concat($returns)->sortByDesc('created_at');

        return $merged->map(function ($t) {
            $date = $t->created_at instanceof Carbon
                ? $t->created_at->setTimezone('Asia/Jakarta')
                : Carbon::parse($t->created_at)->setTimezone('Asia/Jakarta');

            return [
                'Tanggal'        => $date->format('d-m-Y H:i'),
                'Kode Barang'    => $t->barang->kode_barang ?? '-',
                'Barang'         => $t->barang->nama_barang ?? '-',
                'Jenis'          => ucfirst($t->jenis),
                'Jumlah'         => $t->jumlah,
                'Stok Saat Ini'  => $t->barang->stok ?? 0,
                'User'           => $t->user->name ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kode Barang',
            'Barang',
            'Jenis',
            'Jumlah',
            'Stok Saat Ini',
            'User'
        ];
    }
}
