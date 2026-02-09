@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold text-dark mb-1">Laporan Penjualan</h2>
        <p class="text-muted mb-0">Analisis pendapatan, HPP, dan profit berdasarkan periode</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.accounting.export.pdf', ['from' => $startDate, 'to' => $endDate]) }}"
           class="btn btn-danger">
            <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
        </a>
        <a href="{{ route('admin.accounting.export.excel', ['from' => $startDate, 'to' => $endDate]) }}"
           class="btn btn-success">
            <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
        </a>
    </div>
</div>

{{-- FILTER --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-funnel me-2"></i>Filter Laporan
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.accounting.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-calendar-event me-1 text-primary"></i>Dari Tanggal
                </label>
                <input type="date" name="from" class="form-control" value="{{ $startDate }}">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    <i class="bi bi-calendar-check me-1 text-primary"></i>Sampai Tanggal
                </label>
                <input type="date" name="to" class="form-control" value="{{ $endDate }}">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-funnel me-2"></i>Filter
                </button>
            </div>

            <div class="col-md-2">
                <a href="{{ route('admin.accounting.index') }}" class="btn btn-secondary w-100">
                    <i class="bi bi-arrow-clockwise me-2"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

{{-- SUMMARY CARDS --}}
<div class="row g-3 mb-4">
    {{-- TOTAL OMZET --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-primary bg-opacity-10 p-2">
                            <i class="bi bi-cash-stack text-primary fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase mb-2 small fw-semibold">Total Omzet</p>
                        <h4 class="fw-bold mb-0">Rp 20.960.000</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOTAL HPP --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-warning bg-opacity-10 p-2">
                            <i class="bi bi-box-seam text-warning fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase mb-2 small fw-semibold">Total HPP</p>
                        <h4 class="fw-bold mb-1">Rp 0</h4>
                        <small class="text-muted">Belum tersedia</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TOTAL PROFIT --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0 me-3">
                        <div class="rounded-3 bg-success bg-opacity-10 p-2">
                            <i class="bi bi-graph-up-arrow text-success fs-5"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <p class="text-muted text-uppercase mb-2 small fw-semibold">Total Profit</p>
                        <h4 class="fw-bold mb-1">Rp 20.960.000</h4>
                        <small class="text-muted">Profit = Omzet (HPP belum ada)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABEL DETAIL TRANSAKSI --}}
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-table me-2"></i>Data Laporan Penjualan
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle small mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th>TANGGAL</th>
                        <th>KODE TRANSAKSI</th>
                        <th>USER</th>
                        <th>TOTAL SALES</th>
                        <th>TOTAL HPP</th>
                        <th>PROFIT</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaksi)
                        <tr>
                            <td class="text-center">{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                            <td class="text-center">{{ $transaksi->transaksi_kode }}</td>
                            <td class="text-center">{{ $transaksi->user_name }}</td>
                            <td class="text-end">Rp {{ number_format($transaksi->total_sales, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($transaksi->total_hpp, 0, ',', '.') }}</td>
                            <td class="text-end fw-semibold text-success">Rp {{ number_format($transaksi->profit, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem; color: #6c757d;"></i>
                                Tidak ada data transaksi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- PAGINATION --}}
@if($transactions->hasPages())
<div class="d-flex justify-content-end mt-3">
    {{ $transactions->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection