<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceHistory extends Model
{
    use HasFactory;
    protected $table = 'price_histories';

    protected $fillable = [
        'barang_id',
        'old_price',
        'new_price',
        'source',
        'user_id',
    ];

     public function barang()
    {
        return $this->belongsTo(Barang::class)->withoutGlobalScope('active');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
