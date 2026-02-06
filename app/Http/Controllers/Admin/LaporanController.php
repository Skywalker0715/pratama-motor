<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use App\Exports\TransaksiExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;


class LaporanController extends Controller
{
   public function index(Request $request)
{
    $from = null;
    $to = null;

   if ($request->filled('from') && $request->filled('to')) {
    $from = Carbon::parse($request->from, 'Asia/Jakarta')
        ->startOfDay()
        ->timezone('UTC');

    $to = Carbon::parse($request->to, 'Asia/Jakarta')
        ->endOfDay()
        ->timezone('UTC');
}

    // 1. Fetch Transactions
    $transactions = Transaksi::with(['barang', 'user'])
        ->when($from && $to, function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from, $to]);
        })
        ->get();

    // 2. Fetch Returns (StockMovements)
    $returns = StockMovement::with(['barang', 'user'])
        ->where('type', 'return')
        ->when($from && $to, function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from, $to]);
        })
        ->get()
        ->map(function ($item) {
            $item->jenis = 'return';
            $item->jumlah = $item->quantity;
            return $item;
        });

    // 3. Merge and Sort
    $merged = $transactions->concat($returns)->sortByDesc('created_at');

    // 4. Calculate Rekap (Summary)
    $rekap = (object)[
        'total_masuk' => $merged->where('jenis', 'masuk')->sum('jumlah'),
        'total_penjualan' => $merged->where('jenis', 'penjualan')->sum('jumlah'),
        'total_rusak' => $merged->where('jenis', 'rusak')->sum('jumlah'),
        'total_hilang' => $merged->where('jenis', 'hilang')->sum('jumlah'),
    ];

    // 5. Pagination
    $page = Paginator::resolveCurrentPage() ?: 1;
    $perPage = 10;
    $items = $merged->forPage($page, $perPage);

    $transaksis = new LengthAwarePaginator(
        $items,
        $merged->count(),
        $perPage,
        $page,
        ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
    );


    return view('admin.reports', compact('transaksis','rekap'));
}


    public function exportExcel(Request $request)
    {
    return Excel::download(
        new TransaksiExport($request->from, $request->to),
        'laporan-transaksi.xlsx'
    ); 
    }

     public function exportPdf(Request $request)
    {
    $from = $request->from;
    $to = $request->to;

    // 1. Transactions
    $transactions = Transaksi::with(['barang', 'user'])
        ->when($from && $to, function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        })
        ->get();

    // 2. Returns
    $returns = StockMovement::with(['barang', 'user'])
        ->where('type', 'return')
        ->when($from && $to, function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        })
        ->get()
        ->map(function ($item) {
            $item->jenis = 'return';
            $item->jumlah = $item->quantity;
            return $item;
        });

    // 3. Merge
    $transaksis = $transactions->concat($returns)->sortByDesc('created_at');

    $pdf = Pdf::loadView('admin.reports-pdf', compact('transaksis'));

    return $pdf->download('laporan-transaksi.pdf');
    }

}
