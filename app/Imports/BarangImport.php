<?php

namespace App\Imports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BarangImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Barang([
            'kode_barang' => $row['kode'],
            'nama_barang' => $row['nama'],
            'kategori'    => $row['kategori'],
            'harga'       => $row['harga'],
            'satuan'      => $row['satuan'],
            'lokasi_rak'  => $row['lokasi_rak'] ?? null,
            'stok'        => 0,
        ]);
    }
}
