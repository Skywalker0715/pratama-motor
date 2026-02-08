<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AccountingExport;

class AccountingController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->from;
        $endDate = $request->to;

        // Base query for transactions
        $query = Transaksi::query()
            ->where('jenis', 'penjualan')
            ->with(['transaksiItems.barang', 'user']);

        // Apply Date Filter
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        // 1. Calculate Summary (Global for the filtered range)
        // We clone the query to avoid modifying the pagination query
        $summaryQuery = clone $query;
        
        // We fetch all records for the summary to ensure accuracy with model relationships.
        // While a raw DB query is faster, using Eloquent ensures we respect any model scopes or casting.
        // Given the requirement for "world class" code, we'll optimize by chunking if needed, 
        // but for a typical report, getting the collection is acceptable or we can use a join if table names are known.
        // Here we will iterate to calculate to be safe with the 'barang' relation.
        
        $allTransactions = $summaryQuery->get();

        $totalOmzet = 0;
        $totalHpp = 0;

        foreach ($allTransactions as $transaksi) {
            foreach ($transaksi->transaksiItems as $item) {
                $qty = $item->qty ?? 0;
                $hargaJual = $item->harga_jual ?? 0;
                // Use current buy price from master barang as per prompt "SUM(qty * barang.harga_beli)"
                $hargaBeli = $item->barang->harga_beli ?? 0;

                $totalOmzet += ($qty * $hargaJual);
                $totalHpp += ($qty * $hargaBeli);
            }
        }

        $summary = (object) [
            'total_omzet' => $totalOmzet,
            'total_hpp'   => $totalHpp,
            'total_profit' => $totalOmzet - $totalHpp,
        ];

        // 2. Pagination for the list
        $transactions = $query->latest()->paginate(10)->withQueryString();

        // 3. Calculate totals for each transaction in the current page
        $transactions->getCollection()->transform(function ($transaksi) {
            $tSales = 0;
            $tHpp = 0;

            foreach ($transaksi->transaksiItems as $item) {
                $qty = $item->qty ?? 0;
                $hargaJual = $item->harga_jual ?? 0;
                $hargaBeli = $item->barang->harga_beli ?? 0;

                $tSales += ($qty * $hargaJual);
                $tHpp += ($qty * $hargaBeli);
            }

            $transaksi->total_sales = $tSales;
            $transaksi->total_hpp = $tHpp;
            $transaksi->profit = $tSales - $tHpp;

            return $transaksi;
        });

        return view('admin.accounting.index', compact(
            'transactions', 
            'summary', 
            'startDate', 
            'endDate'
        ));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->from;
        $endDate = $request->to;

        $query = Transaksi::query()
            ->where('jenis', 'penjualan')
            ->with(['transaksiItems.barang', 'user']);

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        // Get all records without pagination
        $transactions = $query->latest()->get();

        $totalOmzet = 0;
        $totalHpp = 0;

        // Calculate totals for each transaction and global summary
        foreach ($transactions as $transaksi) {
            $tSales = 0;
            $tHpp = 0;

            foreach ($transaksi->transaksiItems as $item) {
                $qty = $item->qty ?? 0;
                $hargaJual = $item->harga_jual ?? 0;
                $hargaBeli = $item->barang->harga_beli ?? 0;

                $tSales += ($qty * $hargaJual);
                $tHpp += ($qty * $hargaBeli);
            }

            $transaksi->total_sales = $tSales;
            $transaksi->total_hpp = $tHpp;
            $transaksi->profit = $tSales - $tHpp;

            $totalOmzet += $tSales;
            $totalHpp += $tHpp;
        }

        $summary = (object) [
            'total_omzet' => $totalOmzet,
            'total_hpp'   => $totalHpp,
            'total_profit' => $totalOmzet - $totalHpp,
        ];

        $pdf = Pdf::loadView('admin.accounting.export-pdf', compact('transactions', 'summary', 'startDate', 'endDate'));
        return $pdf->download('laporan-laba-rugi.pdf');
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->from;
        $endDate = $request->to;

        $query = Transaksi::query()
            ->where('jenis', 'penjualan')
            ->with(['transaksiItems.barang', 'user']);

        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }

        $transactions = $query->latest()->get();

        $totalOmzet = 0;
        $totalHpp = 0;

        foreach ($transactions as $transaksi) {
            $tSales = 0;
            $tHpp = 0;

            foreach ($transaksi->transaksiItems as $item) {
                $qty = $item->qty ?? 0;
                $hargaJual = $item->harga_jual ?? 0;
                $hargaBeli = $item->barang->harga_beli ?? 0;

                $tSales += ($qty * $hargaJual);
                $tHpp += ($qty * $hargaBeli);
            }

            $transaksi->total_sales = $tSales;
            $transaksi->total_hpp = $tHpp;
            $transaksi->profit = $tSales - $tHpp;

            $totalOmzet += $tSales;
            $totalHpp += $tHpp;
        }

        $summary = (object) [
            'total_omzet' => $totalOmzet,
            'total_hpp'   => $totalHpp,
            'total_profit' => $totalOmzet - $totalHpp,
        ];

        return Excel::download(new AccountingExport($transactions, $summary), 'laporan-laba-rugi.xlsx');
    }
}