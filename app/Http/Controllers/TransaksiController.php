<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|exists:barang,id',
            'jumlah'    => 'required|integer|min:1',
        ]);

        $barang = Barang::findOrFail($request->barang_id);

        if ($barang->stok < $request->jumlah) {
            return back()->with('error', 'Stok tidak cukup');
        }

        // Kurangi stok (PENJUALAN)
        $barang->stok -= $request->jumlah;
        $barang->save();

        // Catat transaksi
        Transaksi::create([
            'barang_id' => $barang->id,
            'user_id'   => auth()->id(),
            'jumlah'    => $request->jumlah,
            'jenis'     => 'penjualan',
            'tanggal'   => now(),
        ]);

        return back()->with('success', 'Transaksi penjualan berhasil');
    }
}
