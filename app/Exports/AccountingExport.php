<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AccountingExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles, ShouldAutoSize
{
    protected $transactions;
    protected $summary;

    public function __construct($transactions, $summary)
    {
        $this->transactions = $transactions;
        $this->summary = $summary;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Kode Transaksi',
            'Total Sales',
            'Total HPP',
            'Profit',
        ];
    }

    public function map($transaksi): array
    {
        return [
            $transaksi->created_at->format('d/m/Y H:i'),
            $transaksi->transaksi_kode,
            $transaksi->total_sales,
            $transaksi->total_hpp,
            $transaksi->profit,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => '#,##0',
            'D' => '#,##0',
            'E' => '#,##0',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $rowCount = $this->transactions->count() + 2; // +1 for heading, +1 for next row

        $sheet->setCellValue('A' . $rowCount, 'TOTAL SUMMARY');
        $sheet->mergeCells('A' . $rowCount . ':B' . $rowCount);
        
        $sheet->setCellValue('C' . $rowCount, $this->summary->total_omzet);
        $sheet->setCellValue('D' . $rowCount, $this->summary->total_hpp);
        $sheet->setCellValue('E' . $rowCount, $this->summary->total_profit);

        $sheet->getStyle('A' . $rowCount . ':E' . $rowCount)->getFont()->setBold(true);
        $sheet->getStyle('C' . $rowCount . ':E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0');
        
        // Header bold
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
    }
}