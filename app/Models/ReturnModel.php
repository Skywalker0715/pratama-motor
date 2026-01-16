<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnModel extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'transaksi_id',
        'user_id',
        'tanggal',
        'alasan',
    ];

     // Relasi ke transaksi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    // Relasi ke item return
    public function items()
    {
        return $this->hasMany(ReturnItem::class, 'return_id');
    }
}
