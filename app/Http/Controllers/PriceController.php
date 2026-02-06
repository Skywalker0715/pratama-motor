<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\PriceHistory;
use App\Services\PriceHistoryService;
use Illuminate\Http\Request;

class PriceController extends Controller
{
    protected PriceHistoryService $priceHistoryService;

    public function __construct(PriceHistoryService $priceHistoryService)
    {
        $this->priceHistoryService = $priceHistoryService;
    }

    // UPDATE HARGA BARANG
    public function update(Request $request, Barang $barang)
{
    $request->validate([
        'harga'  => 'required|numeric|min:0',
        'source' => 'required|in:mandala,pratama_motor',
    ]);

    $this->priceHistoryService->changePrice(
        $barang,
        (float) $request->harga,
        $request->source,
        auth()->id()
    );

    return back()->with('success', 'Harga berhasil diperbarui');
}

    // LIST HISTORI HARGA
    public function history()
    {
        $histories = PriceHistory::with(['barang', 'user'])
            ->latest()
            ->paginate(20);

        return view('admin.price-history.index', compact('histories'));
    }
}
