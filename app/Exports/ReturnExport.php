<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class ReturnExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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

        return $query->latest()->get();
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

    public function map($return): array
    {
        return [
            Carbon::parse($return->created_at)->format('d-m-Y H:i'),
            $return->barang->nama_barang ?? 'Barang Dihapus',
            $return->quantity,
            $return->notes ?? '-',
            $return->user->name ?? 'User Dihapus',
        ];
    }
}