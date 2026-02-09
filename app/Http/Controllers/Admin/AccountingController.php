<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\AccountingExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('from');
        $endDate = $request->input('to');

        $data = $this->getAccountingData($startDate, $endDate);
        
        $summary = $data['summary'];
        $transactions = $data['query']->paginate(10);
        
        // Transformasi data untuk pagination
        $this->transformTransactions($transactions->getCollection());

        return view('admin.accounting.index', compact('summary', 'transactions', 'startDate', 'endDate'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->input('from');
        $endDate = $request->input('to');

        $data = $this->getAccountingData($startDate, $endDate);
        $transactions = $data['query']->get();
        $this->transformTransactions($transactions);

        return Excel::download(new AccountingExport($transactions, $data['summary']), 'laporan-penjualan.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('from');
        $endDate = $request->input('to');

        $data = $this->getAccountingData($startDate, $endDate);
        $transactions = $data['query']->get();
        $this->transformTransactions($transactions);

        $pdf = Pdf::loadView('admin.accounting.export-pdf', [
            'transactions' => $transactions,
            'summary' => $data['summary'],
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);

        return $pdf->download('laporan-penjualan.pdf');
    }

    private function getAccountingData($startDate, $endDate)
    {
        // 1. Hitung Summary (Omzet)
        $summaryQuery = DB::table('transaksi')
            ->join('barang', 'transaksi.barang_id', '=', 'barang.id')
            ->where('transaksi.jenis', 'penjualan');

        // 2. Query Detail Transaksi
        $query = DB::table('transaksi')
            ->join('barang', 'transaksi.barang_id', '=', 'barang.id')
            ->join('users', 'transaksi.user_id', '=', 'users.id')
            ->select(
                DB::raw('MAX(transaksi.transaksi_kode) as transaksi_kode'),
                DB::raw('MAX(transaksi.created_at) as created_at'),
                DB::raw('MAX(users.name) as user_name'),
                DB::raw('SUM(transaksi.jumlah * barang.harga) as total_sales')
            )
            ->where('transaksi.jenis', 'penjualan')
            ->groupBy(DB::raw('COALESCE(transaksi.transaksi_kode, transaksi.id)'))
            ->orderBy('created_at', 'desc');

        if ($startDate) {
            $summaryQuery->whereDate('transaksi.created_at', '>=', $startDate);
            $query->whereDate('transaksi.created_at', '>=', $startDate);
        }
        if ($endDate) {
            $summaryQuery->whereDate('transaksi.created_at', '<=', $endDate);
            $query->whereDate('transaksi.created_at', '<=', $endDate);
        }

        $totalOmzet = $summaryQuery->sum(DB::raw('transaksi.jumlah * barang.harga'));

        return [
            'summary' => (object) ['total_omzet' => $totalOmzet, 'total_hpp' => 0, 'total_profit' => $totalOmzet],
            'query' => $query
        ];
    }

    private function transformTransactions($collection)
    {
        $collection->transform(function ($item) {
            $item->created_at = Carbon::parse($item->created_at);
            $item->total_hpp = 0;
            $item->profit = $item->total_sales;
            $item->transaksi_kode = $item->transaksi_kode ?? '-';
            return $item;
        });
    }
}