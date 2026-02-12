<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Exports\ReturnExport;
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
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $keyword = $request->keyword;
                $q->where(function ($sub) use ($keyword) {
                    $sub->whereHas('barang', function ($b) use ($keyword) {
                        $b->where('nama_barang', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('user', function ($u) use ($keyword) {
                        $u->where('name', 'like', "%{$keyword}%");
                    });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.laporan-return.index', compact('returns'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new ReturnExport($request->from, $request->to, $request->keyword), 'laporan-return.xlsx');
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
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $keyword = $request->keyword;
                $q->where(function ($sub) use ($keyword) {
                    $sub->whereHas('barang', function ($b) use ($keyword) {
                        $b->where('nama_barang', 'like', "%{$keyword}%");
                    })
                    ->orWhereHas('user', function ($u) use ($keyword) {
                        $u->where('name', 'like', "%{$keyword}%");
                    });
                });
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

    public function cleanup(Request $request)
    {
        $years = $request->years;

        if ($years < 1 || $years > 7) {
            return back()->with('error', 'Invalid year selection.');
        }

        $cutoff = now()->subYears($years);

        // Delete return items first due to foreign key
        \App\Models\ReturnItem::whereHas('return', function ($q) use ($cutoff) {
            $q->where('created_at', '<', $cutoff);
        })->delete();

        // Then delete returns
        \App\Models\ReturnModel::where('created_at', '<', $cutoff)->delete();

        return back()->with('success', 'Old return data deleted.');
    }
}
