@extends('layouts.admin')

@section('title', 'Laporan Penjualan')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark">Laporan Penjualan</h2>
    <p class="text-muted mb-0">Analisis pendapatan, HPP, dan profit berdasarkan periode</p>
</div>

{{-- FILTER & EXPORT --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-funnel me-2"></i>Filter Laporan
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.accounting.index') }}">
            <div class="row g-3 align-items-end">

                {{-- Tanggal Dari --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">
                        <i class="bi bi-calendar-event me-1 text-primary"></i>Dari Tanggal
                    </label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>

                {{-- Tanggal Sampai --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">
                        <i class="bi bi-calendar-check me-1 text-primary"></i>Sampai Tanggal
                    </label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>

                {{-- Spacer --}}
                <div class="col-md-3"></div>

                {{-- Tombol Filter & Reset --}}
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.accounting.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    </div>
                </div>

                {{-- Tombol Export + Cleanup --}}
                <div class="col-md-3">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.accounting.export.excel', ['from' => $startDate, 'to' => $endDate]) }}"
                           class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel me-1"></i>Excel
                        </a>
                        <a href="{{ route('admin.accounting.export.pdf', ['from' => $startDate, 'to' => $endDate]) }}"
                           class="btn btn-danger btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-outline-danger btn-sm dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-trash me-1"></i>Cleanup
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                                <li><button class="dropdown-item" onclick="confirmCleanup(1)">Delete data older than 1 year</button></li>
                                <li><button class="dropdown-item" onclick="confirmCleanup(2)">Delete data older than 2 years</button></li>
                                <li><button class="dropdown-item" onclick="confirmCleanup(3)">Delete data older than 3 years</button></li>
                                <li><button class="dropdown-item" onclick="confirmCleanup(4)">Delete data older than 4 years</button></li>
                                <li><button class="dropdown-item" onclick="confirmCleanup(5)">Delete data older than 5 years</button></li>
                                <li><button class="dropdown-item" onclick="confirmCleanup(6)">Delete data older than 6 years</button></li>
                                <li><button class="dropdown-item" onclick="confirmCleanup(7)">Delete data older than 7 years</button></li>
                            </ul>
                        </div>
                    </div>
                </div>

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
                        <h4 class="fw-bold mb-0">Rp {{ number_format($totalOmzet ?? 0, 0, ',', '.') }}</h4>
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
                        <h4 class="fw-bold mb-1">Rp {{ number_format($totalHpp ?? 0, 0, ',', '.') }}</h4>
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
                        <h4 class="fw-bold mb-1">Rp {{ number_format($totalProfit ?? 0, 0, ',', '.') }}</h4>
                        <small class="text-muted">Profit = Omzet (HPP belum ada)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABEL DETAIL TRANSAKSI --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-table me-2"></i>Data Laporan Penjualan
        </h5>
        <span class="badge bg-light text-dark">Total: {{ $transactions->total() }} transaksi</span>
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
                        <th>AKSI</th>
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
                            <td class="text-center">
                                <a href="{{ route('admin.laporan-penjualan.show', $transaksi->transaksi_kode) }}"
                                   class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
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

<script>
function confirmCleanup(years) {
    if (confirm(`Are you sure you want to delete data older than ${years} years?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("accounting.cleanup") }}';
        form.innerHTML = `
            @csrf
            <input type="hidden" name="years" value="${years}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

@endsection