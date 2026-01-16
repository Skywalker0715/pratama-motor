<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\ReturnModel;
use App\Models\ReturnItem;
use App\Models\Barang;
use App\Models\StockMovement;

class ReturnService
{
    public function handle(
        int $transaksiId,
        array $items,
        int $userId,
        ?string $alasan = null
    ): void
    {
        DB::transaction(function () use ($transaksiId, $items, $userId, $alasan) {

            // 1. buat return
            $return = ReturnModel::create([
                'transaksi_id' => $transaksiId,
                'user_id'      => $userId,
                'tanggal'      => now(),
                'alasan'       => $alasan,
            ]);

            // 2. loop items dari form (BUKAN dari transaksi)
            foreach ($items as $item) {

                // 2a. simpan return_items
                ReturnItem::create([
                    'return_id' => $return->id,
                    'barang_id' => $item['barang_id'],
                    'quantity'  => $item['quantity'],
                ]);

                // 2b. update stok barang (+)
                $barang = Barang::lockForUpdate()->findOrFail($item['barang_id']);
                $barang->increment('stok', $item['quantity']);

                // 2c. stock movement
                StockMovement::create([
                    'barang_id'    => $barang->id,
                    'user_id'      => $userId,
                    'type'         => 'RETURN',
                    'quantity'     => $item['quantity'],
                    'source'       => 'RETURN',
                    'reference_id' => $return->id,
                    'notes'        => $alasan,
                ]);
            }
        });
    }
}
