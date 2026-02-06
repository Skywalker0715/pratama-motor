<?php

namespace App\Models;

use App\Models\Barang;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;
    
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

    public function barang() {

    return $this->belongsTo(Barang::class);
}

    public function user() {

    return $this->belongsTo(User::class);
}
}
