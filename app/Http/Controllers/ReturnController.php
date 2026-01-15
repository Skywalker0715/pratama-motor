<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;

Class ReturnController extends Controller
{
    public function index()
    {
        return view('user.return.index');
    }
}