<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\ReturnStockExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanReturnController extends Controller
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

        $returns = StockMovement::with(['barang', 'user'])
            ->where('type', 'RETURN')
            ->when($from && $to, function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.laporan-return.index', compact('returns'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ReturnStockExport($request->from, $request->to), 'laporan-return.xlsx');
    }

    public function exportPdf(Request $request)
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

        $returns = StockMovement::with(['barang', 'user'])
            ->where('type', 'RETURN')
            ->when($from && $to, function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to]);
            })
            ->latest()
            ->get();

        $pdf = Pdf::loadView('admin.laporan-return.pdf', [
            'returns' => $returns,
            'from' => $request->from,
            'to' => $request->to
        ]);

        return $pdf->download('laporan-return.pdf');
    }
}