<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    protected $table = 'price_histories';

    protected $fillable = [
        'barang_id',
        'old_price',
        'new_price',
        'source',
        'user_id',
    ];
}
