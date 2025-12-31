<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalProduk'   => Barang::count(),
            'totalStok'     => Barang::sum('stok'),
            'totalTransaksi'=> Transaksi::count(),
            'userAktif'     => User::where('role', '!=', 'owner')->count(),
        ]);
    }
}
