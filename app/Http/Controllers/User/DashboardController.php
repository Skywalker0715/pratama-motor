<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
     public function index()
{
    // TOTAL STOK (GLOBAL)
    $totalStok = Barang::sum('stok');

    // JUMLAH JENIS BARANG (stok > 0)
    $jumlahBarang = Barang::where('stok', '>', 0)->count();

    // TOTAL TRANSAKSI (PER USER LOGIN)
    $totalTransaksi = Transaksi::where('user_id', Auth::id())->count();

    return view('user.dashboard', compact(
        'totalStok',
        'jumlahBarang',
        'totalTransaksi'
    ));
}

}
