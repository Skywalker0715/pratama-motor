<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReturnService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Transaksi;
use App\Models\ReturnModel;
use App\Models\Barang;


Class ReturnController extends Controller
{
    protected ReturnService $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function index(Request $request)
    {
        $query = ReturnModel::with('items')->where('user_id', auth()->id());

        // Filter Search (ID Transaksi)
        if ($request->filled('search')) {
            $query->where('transaksi_id', 'like', '%' . $request->search . '%');
        }

        // Filter Tanggal Mulai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        // Filter Tanggal Akhir
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        // Get data dengan pagination
        $returns = $query->latest('tanggal')
                        ->withCount('items')
                        ->paginate(10)
                        ->withQueryString(); // Penting! Agar filter tetap ada saat pindah halaman

        return view('user.return.index', compact('returns'));
    }

    public function create()
    {
        // Ambil transaksi user (hanya penjualan) dengan relasi barang untuk hitung harga
        $transaksisGrouped = Transaksi::with('barang')
            ->where('user_id', auth()->id())
            ->where('jenis', 'penjualan')
            ->latest()
            ->get()
            ->groupBy(function ($item) {
                // Grouping berdasarkan timestamp untuk menyatukan item dalam satu "Order"
                return $item->created_at->format('Y-m-d H:i:s');
            });

        $transaksis = $transaksisGrouped->map(function ($group) {
                // Ambil satu item sebagai representasi (header) transaksi
                $trx = $group->first();

                // Hitung total manual: Sum(jumlah * harga barang)
                $trx->total = $group->sum(function ($t) {
                    return $t->jumlah * (optional($t->barang)->harga ?? 0);
                });

                // Lampirkan semua item dari grup ini agar bisa diakses di Blade
                $trx->items_in_group = $group->map(function ($item) {
                    // Pastikan barang ada sebelum mengakses propertinya
                    if ($item->barang) {
                        return [
                            'barang_id'     => $item->barang->id,
                            'nama_barang'   => $item->barang->nama_barang,
                            'jumlah_dibeli' => $item->jumlah,
                        ];
                    }
                    return null;
                })->filter(); // Hapus item yang barangnya tidak ditemukan

                return $trx;
            })->values(); // Reset index array

        // Ambil semua barang untuk dropdown (opsional fallback)
        $barangs = Barang::select('id', 'nama_barang', 'kode_barang')->get();

        return view('user.return.create', compact('transaksis', 'barangs'));
    }

    /**
     * Endpoint AJAX untuk mengambil item berdasarkan ID transaksi (perwakilan grup).
     * Hanya item dari transaksi yang dipilih (group by created_at & user_id)
     */
    public function getItems($id)
    {
        try {
            $trx = Transaksi::find($id);

            if (!$trx) {
                return response()->json(['error' => 'Transaksi tidak ditemukan'], 404);
            }

            // Ambil semua item dalam satu "group" transaksi (berdasarkan created_at dan user_id)
            $itemsData = Transaksi::with('barang')
                ->where('user_id', $trx->user_id)
                ->whereDate('created_at', $trx->created_at->format('Y-m-d'))
                ->whereTime('created_at', '>=', $trx->created_at->format('H:i:s'))
                ->where('jenis', 'penjualan')
                ->get()
                ->map(function ($item) {
                    return [
                        'barang_id'     => $item->barang_id, 
                        'nama_barang'   => $item->barang->nama_barang ?? 'Item Terhapus',
                        'kode_barang'   => $item->barang->kode_barang ?? '-',
                        'jumlah_dibeli' => $item->jumlah,
                        'harga_satuan'  => $item->barang->harga ?? 0,
                    ];
                });

            // Detail info untuk tampilan
            $totalPrice = $itemsData->sum(function ($item) {
                return $item['jumlah_dibeli'] * $item['harga_satuan'];
            });

            return response()->json([
                'success' => true,
                'items' => $itemsData,
                'tanggal' => $trx->created_at->format('d M Y H:i'),
                'total' => $totalPrice,
                'total_formatted' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'transaksi_id' => 'required|exists:transaksi,id',
                'items' => 'required|array|min:1',
                'items.*.barang_id' => 'required|exists:barang,id',
                'items.*.jumlah' => 'required|integer|min:1', // PERBAIKAN: sesuai field name di form
                'items.*.alasan' => 'nullable|string|max:255',
                'catatan' => 'nullable|string|max:500',
            ]);

            // Ambil transaksi reference
            $referenceTrx = Transaksi::findOrFail($request->transaksi_id);
            
            // Ambil original items dari transaksi (qty yang dibeli)
            $originalItems = Transaksi::where('user_id', $referenceTrx->user_id)
                ->whereDate('created_at', $referenceTrx->created_at->format('Y-m-d'))
                ->whereTime('created_at', '>=', $referenceTrx->created_at->format('H:i:s'))
                ->pluck('jumlah', 'barang_id');

            // Validasi Backend: Pastikan jumlah return tidak melebihi jumlah beli
            foreach ($request->items as $item) {
                $qtyBeli = $originalItems[$item['barang_id']] ?? 0;
                if ($item['jumlah'] > $qtyBeli) {
                    return back()
                        ->withErrors(['items' => "Jumlah return melebihi pembelian untuk barang ini (Maks: {$qtyBeli})"])
                        ->withInput();
                }
            }

            // Transform items untuk service (ubah 'jumlah' menjadi 'quantity')
            $items = collect($request->items)->map(function ($item) {
                return [
                    'barang_id' => $item['barang_id'],
                    'quantity' => $item['jumlah'],
                ];
            })->toArray();

            // Jalankan service
            $this->returnService->handle(
                $request->transaksi_id,
                $items,
                Auth::id(),
                $request->catatan ?? null
            );

            return redirect()
                ->route('user.return.index')
                ->with('success', 'Return berhasil diproses! Stok barang telah diperbarui.');
                
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Return store error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'transaksi_id' => $request->transaksi_id,
                'exception' => $e
            ]);
            
            return back()
                ->with('error', 'Gagal memproses return: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $return = ReturnModel::with(['items.barang', 'transaksi.barang'])->findOrFail($id);
        return view('user.return.show', compact('return'));
    }
}