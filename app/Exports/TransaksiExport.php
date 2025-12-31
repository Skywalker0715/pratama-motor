<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;    
use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;

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
        $query = Transaksi::with(['barang','user'])
            ->orderBy('created_at','desc');

        if ($this->from && $this->to) {
            $query->whereBetween('created_at', [
                $this->from . ' 00:00:00',
                $this->to . ' 23:59:59'
            ]);
        }

        return $query->get()->map(function ($t) {
            return [
                'Tanggal'        => $t->created_at->format('d-m-Y H:i'),
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
