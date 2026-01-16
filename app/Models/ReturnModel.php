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
}
