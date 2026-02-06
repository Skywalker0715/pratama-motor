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

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to   = $to;
    }

   public function collection()
    {
        // 1. Transactions
        $transactions = Transaksi::with(['barang', 'user'])
            ->when($this->from && $this->to, function ($q) {
                $q->whereBetween('created_at', [
                    $this->from . ' 00:00:00',
                    $this->to . ' 23:59:59'
                ]);
            })
            ->get();

        // 2. Returns (StockMovements)
        $returns = StockMovement::with(['barang', 'user'])
            ->where('type', 'return')
            ->when($this->from && $this->to, function ($q) {
                $q->whereBetween('created_at', [
                    $this->from . ' 00:00:00',
                    $this->to . ' 23:59:59'
                ]);
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
            'Barang',
            'Jenis',
            'Jumlah',
            'Stok Saat Ini',
            'User'
        ];
    }
}
