<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'barang_id',
        'jumlah',
        'jenis',
        'tanggal',
        'keterangan',
        'transaksi_kode',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tanggal'    => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class)->withoutGlobalScope('active');
    }


    public function getCreatedAtAttribute($value)
    {
    return Carbon::parse($value)->timezone('Asia/Jakarta');
    }

    public function getTanggalAttribute($value)
    {
    return Carbon::parse($value)->timezone('Asia/Jakarta');
    }
}
