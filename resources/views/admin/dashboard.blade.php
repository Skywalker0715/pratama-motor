@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row g-4">

    <!-- Total Produk -->
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('admin.products') }}" class="text-decoration-none">
            <div class="stat-card card-primary">
                <div class="card-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="card-body">
                    <div class="card-title">Total Produk</div>
                    <div class="card-value">{{ $totalProduk }}</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Total Stok -->
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('admin.stock') }}" class="text-decoration-none">
            <div class="stat-card card-success">
                <div class="card-icon">
                    <i class="bi bi-boxes"></i>
                </div>
                <div class="card-body">
                    <div class="card-title">Total Stok</div>
                    <div class="card-value">{{ number_format($totalStok) }}</div>
                </div>
            </div>
        </a>
    </div>

    <!-- Transaksi -->
    <div class="col-lg-3 col-md-6">
        <a href="{{ route('admin.stock') }}" class="text-decoration-none">
            <div class="stat-card card-warning">
                <div class="card-icon">
                    <i class="bi bi-arrow-down-up"></i>
                </div>
                <div class="card-body">
                    <div class="card-title">Transaksi</div>
                    <div class="card-value">{{ $totalTransaksi }}</div>
                </div>
            </div>
        </a>
    </div>

    <!-- User Aktif -->
    <div class="col-lg-3 col-md-6">
        <a href="{{ url('/admin/users') }}" class="text-decoration-none">
            <div class="stat-card card-info">
                <div class="card-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div class="card-body">
                    <div class="card-title">User Aktif</div>
                    <div class="card-value">{{ $userAktif }}</div>
                </div>
            </div>
        </a>
    </div>

</div>
@endsection



