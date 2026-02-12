@extends('layouts.admin')

@section('title', 'Detail Transaksi - ' . $header->transaksi_kode)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold text-dark mb-1">Detail Transaksi</h2>
        <p class="text-muted mb-0">Rincian lengkap transaksi penjualan</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.accounting.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>
</div>

{{-- HEADER INFO --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-primary bg-opacity-10 p-2">
                            <i class="bi bi-receipt text-primary fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase mb-2 small fw-semibold">Kode Transaksi</p>
                        <h5 class="fw-bold mb-0">{{ $header->transaksi_kode }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-info bg-opacity-10 p-2">
                            <i class="bi bi-calendar-event text-info fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase mb-2 small fw-semibold">Tanggal</p>
                        <h5 class="fw-bold mb-0">{{ \Carbon\Carbon::parse($header->created_at)->format('d-m-Y H:i') }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-success bg-opacity-10 p-2">
                            <i class="bi bi-person text-success fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase mb-2 small fw-semibold">Nama Kasir</p>
                        <h5 class="fw-bold mb-0">{{ $header->nama_kasir }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABEL DETAIL --}}
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-list-ul me-2"></i>Detail Barang
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $item)
                        <tr>
                            <td>{{ $item->nama_barang }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-end">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem; color: #6c757d;"></i>
                                Tidak ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Total Sales:</td>
                        <td class="text-end fw-bold text-success">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@endsection
