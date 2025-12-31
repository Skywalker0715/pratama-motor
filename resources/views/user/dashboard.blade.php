@extends('layouts.user')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- TOTAL STOK -->
    <a href="{{ route('user.transaksi') }}"
       class="bg-white p-6 rounded-xl shadow flex items-center gap-4 hover:scale-[1.02] transition">

        <span class="material-icons text-blue-600 text-4xl">inventory</span>

        <div>
            <p class="text-sm text-gray-500 font-medium">
                Total Stok Tersedia
            </p>
            <h3 class="text-3xl font-bold text-gray-800">
                {{ $totalStok }}
            </h3>
        </div>
    </a>

    <!-- JUMLAH JENIS BARANG -->
    <a href="{{ route('user.transaksi') }}"
       class="bg-white p-6 rounded-xl shadow flex items-center gap-4 hover:scale-[1.02] transition">

        <span class="material-icons text-green-600 text-4xl">category</span>

        <div>
            <p class="text-sm text-gray-500 font-medium">
                Jumlah Jenis Barang
            </p>
            <h3 class="text-3xl font-bold text-gray-800">
                {{ $jumlahBarang }}
            </h3>
        </div>
    </a>

    <!-- TOTAL TRANSAKSI -->
    <a href="{{ route('user.history') }}"
       class="bg-white p-6 rounded-xl shadow flex items-center gap-4 hover:scale-[1.02] transition">

        <span class="material-icons text-purple-600 text-4xl">point_of_sale</span>

        <div>
            <p class="text-sm text-gray-500 font-medium">
                Total Transaksi
            </p>
            <h3 class="text-3xl font-bold text-gray-800">
                {{ $totalTransaksi }}
            </h3>
        </div>
    </a>

</div>

@endsection
