<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\AccountingExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Transaksi;

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

        $totalOmzet = $summary->total_omzet;
        $totalHpp = $summary->total_hpp;
        $totalProfit = $summary->total_profit;

        return view('admin.accounting.index', compact('summary', 'transactions', 'startDate', 'endDate', 'totalOmzet', 'totalHpp', 'totalProfit'));
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

    public function show($kode)
    {
        $transactions = DB::table('transaksi')
            ->join('barang', 'transaksi.barang_id', '=', 'barang.id')
            ->join('users', 'transaksi.user_id', '=', 'users.id')
            ->where('transaksi.transaksi_kode', $kode)
            ->where('transaksi.jenis', 'penjualan')
            ->select(
                'transaksi.transaksi_kode',
                'transaksi.created_at',
                'users.name as nama_kasir',
                'barang.nama_barang',
                'transaksi.jumlah as qty',
                'barang.harga'
            )
            ->orderBy('transaksi.created_at')
            ->get();

        if ($transactions->isEmpty()) {
            abort(404, 'Transaksi tidak ditemukan');
        }

        $header = (object) [
            'transaksi_kode' => $transactions->first()->transaksi_kode,
            'created_at' => $transactions->first()->created_at,
            'nama_kasir' => $transactions->first()->nama_kasir,
        ];

        $total = $transactions->sum(function ($item) {
            return $item->qty * $item->harga;
        });

        $transactions = $transactions->map(function ($item) {
            $item->subtotal = $item->qty * $item->harga;
            return $item;
        });

        return view('admin.accounting.detail', compact('header', 'transactions', 'total'));
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

    public function cleanup(Request $request)
    {
        $years = $request->years;

        if ($years < 1 || $years > 7) {
            return back()->with('error', 'Invalid year selection.');
        }

        $cutoff = now()->subYears($years);

        Transaksi::where('created_at', '<', $cutoff)->delete();

        return back()->with('success', 'Old transaction data deleted.');
    }
}
