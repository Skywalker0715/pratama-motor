<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Exports\TransaksiExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;



class LaporanController extends Controller
{
   public function index(Request $request)
{
    $baseQuery = Transaksi::with(['barang','user'])
        ->orderBy('created_at','desc');

    if ($request->filled('from') && $request->filled('to')) {
        $baseQuery->whereBetween('created_at', [
            $request->from.' 00:00:00',
            $request->to.' 23:59:59'
        ]);
    }

    $transaksis = (clone $baseQuery)
        ->paginate(10)
        ->withQueryString();

    $rekap = (clone $baseQuery)->selectRaw("
    SUM(CASE WHEN jenis = 'masuk' THEN jumlah ELSE 0 END) as total_masuk,
    SUM(CASE WHEN jenis = 'penjualan' THEN jumlah ELSE 0 END) as total_penjualan,
    SUM(CASE WHEN jenis = 'rusak' THEN jumlah ELSE 0 END) as total_rusak,
    SUM(CASE WHEN jenis = 'hilang' THEN jumlah ELSE 0 END) as total_hilang
")->first();


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
    $query = Transaksi::with(['barang','user'])
        ->orderBy('created_at','desc');

    if ($request->filled('from') && $request->filled('to')) {
        $query->whereBetween('created_at', [
            $request->from . ' 00:00:00',
            $request->to . ' 23:59:59'
        ]);
    }

    $transaksis = $query->get();

    $pdf = Pdf::loadView('admin.reports-pdf', compact('transaksis'));

    return $pdf->download('laporan-transaksi.pdf');
    }

}
