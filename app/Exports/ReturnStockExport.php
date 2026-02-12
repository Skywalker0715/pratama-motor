<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ReturnStockExport implements FromCollection, WithHeadings
{
    protected $from;
    protected $to;
    protected $keyword;

    public function __construct($from = null, $to = null, $keyword = null)
    {
        $this->from = $from;
        $this->to = $to;
        $this->keyword = $keyword;
    }

    public function collection()
    {
        $query = StockMovement::with(['barang', 'user'])
            ->where('type', 'return');

        $query->when($this->from && $this->to, function ($q) {
            $q->whereBetween('created_at', [
                Carbon::parse($this->from)->startOfDay(),
                Carbon::parse($this->to)->endOfDay()
            ]);
        });

        $query->when($this->keyword, function ($q) {
            $keyword = $this->keyword;
            $q->where(function ($sub) use ($keyword) {
                $sub->whereHas('barang', function ($b) use ($keyword) {
                        $b->where('nama_barang', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$keyword}%"));
            });
        });

        return $query->latest()
            ->get()
            ->map(function ($item) {
                return [
                    'Tanggal'       => Carbon::parse($item->created_at)->setTimezone('Asia/Jakarta')->format('d-m-Y H:i'),
                    'Nama Barang'   => $item->barang->nama_barang ?? 'Barang Dihapus',
                    'Jumlah Return' => $item->quantity,
                    'Catatan'       => $item->notes ?? '-',
                    'Kasir'         => $item->user->name ?? 'User Dihapus',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Barang',
            'Jumlah Return',
            'Catatan',
            'Kasir',
        ];
    }
}