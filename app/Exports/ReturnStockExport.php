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

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $query = StockMovement::with(['barang', 'user'])
            ->where('type', 'RETURN');

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [
                $this->from . ' 00:00:00',
                $this->to . ' 23:59:59'
            ]);
        }

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