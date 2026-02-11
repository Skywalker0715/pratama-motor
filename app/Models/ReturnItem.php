<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    protected $table = 'return_items';

    protected $fillable = [
        'return_id',
        'barang_id',
        'quantity',
    ];

     public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id')->withoutGlobalScope('active');
    }

    public function return()
    {
        return $this->belongsTo(ReturnModel::class, 'return_id');
    }
}
