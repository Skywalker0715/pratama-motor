@extends('layouts.admin')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="container-fluid px-4">
    <div class="mt-4 mb-3">
        <h1 class="page-title mb-2">Laporan Laba Rugi</h1>
        <p class="text-muted">Filter dan export laporan laba rugi</p>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-funnel me-2"></i>Filter Laporan
            </h5>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.accounting.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="from" class="form-label fw-semibold text-dark">
                            <i class="bi bi-calendar-event text-primary me-1"></i>Tanggal Dari
                        </label>
                        <input type="date" class="form-control form-control-lg" id="from" name="from" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="to" class="form-label fw-semibold text-dark">
                            <i class="bi bi-calendar-check text-primary me-1"></i>Tanggal Sampai
                        </label>
                        <input type="date" class="form-control form-control-lg" id="to" name="to" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold text-dark d-block">&nbsp;</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-search me-2"></i>Filter
                            </button>
                            <button type="submit" formaction="{{ route('admin.accounting.export.excel') }}" class="btn btn-success btn-lg px-4">
                                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                            </button>
                            <button type="submit" formaction="{{ route('admin.accounting.export.pdf') }}" class="btn btn-danger btn-lg px-4">
                                <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm border-0 h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="card-label mb-2">Total Omzet</p>
                            <h2 class="card-value text-primary mb-0">Rp {{ number_format($summary->total_omzet, 0, ',', '.') }}</h2>
                        </div>
                        <div class="icon-wrapper bg-primary">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm border-0 h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="card-label mb-2">Total HPP</p>
                            <h2 class="card-value text-warning mb-0">Rp {{ number_format($summary->total_hpp, 0, ',', '.') }}</h2>
                        </div>
                        <div class="icon-wrapper bg-warning">
                            <i class="bi bi-calculator"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6">
            <div class="card shadow-sm border-0 h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <p class="card-label mb-2">Total Profit</p>
                            <h2 class="card-value {{ $summary->total_profit >= 0 ? 'text-success' : 'text-danger' }} mb-0">
                                Rp {{ number_format($summary->total_profit, 0, ',', '.') }}
                            </h2>
                        </div>
                        <div class="icon-wrapper {{ $summary->total_profit >= 0 ? 'bg-success' : 'bg-danger' }}">
                            <i class="bi bi-graph-up-arrow"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-table me-2"></i>Detail Transaksi
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="py-3">Kode Transaksi</th>
                            <th class="py-3">User</th>
                            <th class="text-end py-3">Total Sales</th>
                            <th class="text-end py-3">Total HPP</th>
                            <th class="text-end px-4 py-3">Profit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaksi)
                            <tr>
                                <td class="px-4">
                                    <span class="text-nowrap">{{ $transaksi->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        {{ $transaksi->kode_transaksi ?? '-' }}
                                    </span>
                                </td>
                                <td>{{ $transaksi->user->name ?? '-' }}</td>
                                <td class="text-end">Rp {{ number_format($transaksi->total_sales, 0, ',', '.') }}</td>
                                <td class="text-end">Rp {{ number_format($transaksi->total_hpp, 0, ',', '.') }}</td>
                                <td class="text-end px-4">
                                    <span class="fw-bold {{ $transaksi->profit >= 0 ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($transaksi->profit, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    Tidak ada data transaksi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($transactions->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $transactions->firstItem() }} to {{ $transactions->lastItem() }} of {{ $transactions->total() }} results
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0">
                        {{-- Previous Button --}}
                        @if ($transactions->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">&laquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $transactions->previousPageUrl() }}" rel="prev">&laquo;</a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                            @if ($page == $transactions->currentPage())
                                <li class="page-item active" aria-current="page">
                                    <span class="page-link">{{ $page }}</span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach

                        {{-- Next Button --}}
                        @if ($transactions->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $transactions->nextPageUrl() }}" rel="next">&raquo;</a>
                            </li>
                        @else
                            <li class="page-item disabled">
                                <span class="page-link">&raquo;</span>
                            </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
        @endif
    </div>
</div>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #1e293b;
        letter-spacing: -0.025em;
    }
    
    .card {
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    .card-hover {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1) !important;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .card-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    
    .card-value {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.025em;
    }
    
    .icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .icon-wrapper.bg-primary {
        background-color: #667eea;
    }
    
    .icon-wrapper.bg-warning {
        background-color: #f59e0b;
    }
    
    .icon-wrapper.bg-success {
        background-color: #10b981;
    }
    
    .icon-wrapper.bg-danger {
        background-color: #ef4444;
    }
    
    .icon-wrapper i {
        font-size: 1.75rem;
        color: white;
    }
    
    .form-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
    }
    
    .form-control-lg {
        border-radius: 0.5rem;
        border: 2px solid #e2e8f0;
        font-size: 0.9375rem;
        padding: 0.625rem 1rem;
        transition: all 0.2s ease;
    }
    
    .form-control-lg:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    
    .btn-lg {
        padding: 0.625rem 1.5rem;
        font-weight: 600;
        border-radius: 0.5rem;
        font-size: 0.9375rem;
        border: none;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:hover {
        background: #5568d3;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        transform: translateY(-2px);
    }
    
    .btn-success {
        background: #10b981;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }
    
    .btn-success:hover {
        background: #059669;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
        transform: translateY(-2px);
    }
    
    .btn-danger {
        background: #ef4444;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }
    
    .btn-danger:hover {
        background: #dc2626;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        transform: translateY(-2px);
    }
    
    .table > :not(caption) > * > * {
        padding: 1rem 0.75rem;
        vertical-align: middle;
    }
    
    .table thead th {
        font-weight: 700;
        font-size: 0.8125rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #000000;
        border-bottom: 2px solid #e2e8f0;
        background-color: #f8fafc;
    }
    
    .table tbody tr {
        transition: background-color 0.2s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .table tbody tr:hover {
        background-color: #f8fafc;
    }
    
    .table tbody td {
        font-size: 0.9375rem;
        color: #000000;
        font-weight: 500;
    }
    
    .badge {
        font-weight: 600;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.8125rem;
    }
    
    /* Modern Pagination Styling */
    .pagination {
        gap: 0.5rem;
        margin: 0;
    }
    
    .pagination .page-item .page-link {
        border: 2px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 0.5rem 0.875rem;
        color: #667eea;
        font-weight: 600;
        transition: all 0.2s ease;
        min-width: 44px;
        text-align: center;
        font-size: 0.9375rem;
    }
    
    .pagination .page-item .page-link:hover {
        background-color: #f1f5f9;
        border-color: #667eea;
        color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
    }
    
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .pagination .page-item.disabled .page-link {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #cbd5e1;
        cursor: not-allowed;
    }
    
    .card-footer {
        background-color: #f8fafc;
        border-top: 1px solid #e2e8f0;
    }
</style>
@endsection