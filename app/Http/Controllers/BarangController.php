<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BarangImport;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{

  public function index(Request $request)
  {
    $query = Barang::query();

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('nama_barang', 'like', "%{$search}%")
              ->orWhere('kode_barang', 'like', "%{$search}%")
              ->orWhere('kategori', 'like', "%{$search}%");
    }

    if ($request->filled('kategori')) {
    $query->where('kategori', $request->kategori);
   }

    $barangs = $query->latest()->paginate(20);
    return view('admin.products', compact('barangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barang,kode_barang',
            'nama_barang' => 'required|string|max:255',
            'kategori'    => 'required|string|max:100',
            'harga'       => 'required|numeric|min:0',
            'satuan'      => 'required|string|max:20',
            'lokasi_rak'  => 'nullable|string|max:50',
        ]);

      Barang::create([
       ...$request->except('stok'),
       'stok' => 0
      ]);
        return redirect()->back()->with('success', 'Produk berhasil ditambahkan');
    }

    
    public function update(Request $request, Barang $barang)
    {
        $request->validate([
            'kode_barang' => 'required|string|max:50|unique:barang,kode_barang,' . $barang->id,
            'nama_barang' => 'required|string|max:255',
            'kategori'    => 'required|string|max:100',
            'satuan'      => 'required|string|max:20',
            'lokasi_rak'  => 'nullable|string|max:50',
        ]);

        $barang->update($request->except('stok','harga'));

        return redirect()->back()->with('success', 'Produk berhasil diupdate');
    }

    public function destroy(Barang $barang)
    {
        $barang->delete();
        return redirect()->back()->with('success', 'Produk berhasil dihapus');
    }

     
   public function stock()
   {
    $barangs = Barang::orderBy('nama_barang')->get();
    
    // 1. Transactions (IN, SOLD, DAMAGED, LOST, ADJUSTMENT)
    $transactions = DB::table('transaksi')
        ->join('barang', 'transaksi.barang_id', '=', 'barang.id')
        ->join('users', 'transaksi.user_id', '=', 'users.id')
        ->select(
            'transaksi.id',
            'transaksi.tanggal as date',
            'barang.kode_barang',
            'barang.nama_barang as product_name',
            'barang.stok as current_stock',
            'transaksi.jenis as type',
            'transaksi.jumlah as quantity',
            'users.name as user_name',
            DB::raw("'transaction' as source")
        );

    // 2. Stock Movements (RETURN only)
    $movements = DB::table('stock_movements')
        ->join('barang', 'stock_movements.barang_id', '=', 'barang.id')
        ->join('users', 'stock_movements.user_id', '=', 'users.id')
        ->whereIn('stock_movements.type', ['return', 'RETURN'])
        ->select(
            'stock_movements.id',
            'stock_movements.created_at as date',
            'barang.kode_barang',
            'barang.nama_barang as product_name',
            'barang.stok as current_stock',
            'stock_movements.type',
            'stock_movements.quantity',
            'users.name as user_name',
            DB::raw("'movement' as source")
        );

    // Union and Paginate
    $transaksis = $transactions->union($movements)
        ->orderBy('date', 'desc')
        ->paginate(20);

    return view('admin.stock', compact('barangs', 'transaksis'));
 }

    public function updateStock(Request $request)
    {
    $request->validate([
    'barang_id' => 'required|exists:barang,id',
    'jumlah'    => 'required|integer|min:1',
    'jenis'     => 'required|in:masuk,rusak,hilang,koreksi',
    ]);

     $barang = Barang::findOrFail($request->barang_id);

     if ($request->jenis === 'masuk') {
     $barang->stok += $request->jumlah;
    } else {
    if ($barang->stok < $request->jumlah) {
        return back()->with('error', 'Stok tidak cukup');
    }
    $barang->stok -= $request->jumlah;
    }

    $barang->save();

    \App\Models\Transaksi::create([
    'barang_id' => $barang->id,
    'user_id'   => auth()->id(),
    'jumlah'    => $request->jumlah,
    'jenis'     => $request->jenis,
    'tanggal'   => now(),
   ]);

    return redirect()->back()->with('success', 'Stok berhasil diperbarui');
    }

    public function import(Request $request)
    {
    $request->validate([
        'file' => 'required|mimes:xlsx,xls'
    ]);

    Excel::import(new BarangImport, $request->file('file'));

    return redirect()
        ->route('admin.products')
        ->with('success', 'Produk berhasil diimport');
    }

}
