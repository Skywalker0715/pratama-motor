<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\PriceHistory;
use Illuminate\Support\Facades\DB;

class PriceHistoryService
{
    public function changePrice(
        Barang $barang,
        float $newPrice,
        string $source,
        int $userId
    ): void {
        DB::transaction(function () use ($barang, $newPrice, $source, $userId) {

            PriceHistory::create([
                'barang_id' => $barang->id,
                'old_price' => $barang->harga,
                'new_price' => $newPrice,
                'source'    => $source,
                'user_id'   => $userId,
            ]);

            $barang->update([
                'harga' => $newPrice,
            ]);
        });
    }
}
