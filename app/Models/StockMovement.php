<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $table = 'stock_movements';

    protected $fillable = [
        'barang_id',
        'user_id',
        'type',
        'quantity',
        'source',
        'reference_id',
        'notes',
    ];
}
