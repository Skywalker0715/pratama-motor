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

    public function __construct($from = null, $to = null)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function collection()
    {
        $query = StockMovement::with(['barang', 'user'])
            ->where('type', 'RETURN')
            ->latest();

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->from)->startOfDay(),
                Carbon::parse($this->to)->endOfDay()
            ]);
        }

        return $query->get();
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