<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    public function index()
    {
        return view('admin.price-history');
    }
}
