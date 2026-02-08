@extends('layouts.admin')

@section('title', 'Laporan Return')

@section('content')


<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
        <div>
            <h1 class="page-title mb-2">Laporan Return</h1>
            <p class="text-muted">Filter dan export laporan return barang</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.laporan-return.pdf', request()->query()) }}" class="btn btn-danger btn-lg px-4">
                <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
            </a>
            <a href="{{ route('admin.laporan-return.excel', request()->query()) }}" class="btn btn-success btn-lg px-4">
                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-gradient-primary text-white">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-funnel me-2"></i>Filter Laporan
            </h5>
        </div>
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.laporan-return') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="from" class="form-label fw-semibold text-dark">
                            <i class="bi bi-calendar-event text-primary me-1"></i>Dari Tanggal
                        </label>
                        <input type="date" class="form-control form-control-lg" id="from" name="from" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="to" class="form-label fw-semibold text-dark">
                            <i class="bi bi-calendar-check text-primary me-1"></i>Sampai Tanggal
                        </label>
                        <input type="date" class="form-control form-control-lg" id="to" name="to" value="{{ request('to') }}">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary btn-lg px-4">
                                <i class="bi bi-funnel me-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.laporan-return') }}" class="btn btn-secondary btn-lg px-4">
                                <i class="bi bi-arrow-counterclockwise me-2"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="bi bi-table me-2"></i>Data Return Barang
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-center" width="5%">No</th>
                            <th class="py-3" width="15%">Tanggal</th>
                            <th class="py-3" width="20%">Nama Barang</th>
                            <th class="py-3 text-center" width="12%">Jumlah Return</th>
                            <th class="py-3" width="30%">Alasan / Catatan</th>
                            <th class="py-3" width="15%">Kasir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $index => $return)
                        <tr>
                            <td class="px-4 text-center">{{ $returns->firstItem() + $index }}</td>
                            <td>
                                <span class="text-nowrap">{{ \Carbon\Carbon::parse($return->created_at)->format('d M Y H:i') }}</span>
                            </td>
                            <td class="fw-semibold">{{ $return->barang->nama_barang ?? 'Barang Dihapus' }}</td>
                            <td class="text-center">
                                <span class="badge-quantity">{{ $return->quantity }}</span>
                            </td>
                            <td>{{ $return->notes ?? '-' }}</td>
                            <td>{{ $return->user->name ?? 'User Dihapus' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Tidak ada data return ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($returns->count() > 0)
                    <tfoot>
                        <tr class="bg-light fw-bold">
                            <td colspan="3" class="text-end px-4 py-3">Total (Halaman Ini)</td>
                            <td class="text-center py-3">
                                <span class="badge-total">{{ $returns->sum('quantity') }}</span>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        @if($returns->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $returns->firstItem() }} to {{ $returns->lastItem() }} of {{ $returns->total() }} results
                </div>
                <nav aria-label="Page navigation">
                    <ul class="pagination mb-0">
                        {{-- Previous Button --}}
                        @if ($returns->onFirstPage())
                            <li class="page-item disabled">
                                <span class="page-link">&laquo;</span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $returns->previousPageUrl() }}" rel="prev">&laquo;</a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($returns->getUrlRange(1, $returns->lastPage()) as $page => $url)
                            @if ($page == $returns->currentPage())
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
                        @if ($returns->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $returns->nextPageUrl() }}" rel="next">&raquo;</a>
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
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
    
    .btn-secondary {
        background: #6c757d;
        box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
    }
    
    .btn-secondary:hover {
        background: #5a6268;
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
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
    
    .table tfoot tr {
        border-top: 2px solid #e2e8f0;
    }
    
    .badge-quantity {
        display: inline-block;
        background-color: #1e293b;
        color: white;
        font-weight: 700;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        min-width: 50px;
        text-align: center;
        font-size: 0.9375rem;
    }
    
    .badge-total {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 700;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        min-width: 50px;
        text-align: center;
        font-size: 0.9375rem;
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