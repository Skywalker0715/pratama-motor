@extends('layouts.admin')

@section('title', 'Laporan Stok')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark">Laporan transaksi stok dan barang</h2>
    <p class="text-muted mb-0">Filter dan export laporan transaksi stok barang</p>
</div>

{{-- FILTER & EXPORT --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-funnel me-2"></i>Filter Laporan
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports') }}">
            <div class="row g-3 align-items-end">

                {{-- Tanggal Dari --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">
                        <i class="bi bi-calendar-event me-1 text-primary"></i>Tanggal Dari
                    </label>
                    <input type="date" name="from" class="form-control form-control-sm" value="{{ request('from') }}">
                </div>

                {{-- Tanggal Sampai --}}
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">
                        <i class="bi bi-calendar-check me-1 text-primary"></i>Tanggal Sampai
                    </label>
                    <input type="date" name="to" class="form-control form-control-sm" value="{{ request('to') }}">
                </div>

                {{-- Keyword --}}
                <div class="col-md-3">
                    <label class="form-label fw-semibold small">
                        <i class="bi bi-search me-1 text-primary"></i>Keyword
                    </label>
                    <input type="text" name="keyword" class="form-control form-control-sm"
                           value="{{ request('keyword') }}"
                           placeholder="Search product, user, or transaction code">
                </div>

                {{-- Tombol Filter & Reset --}}
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    </div>
                </div>

                {{-- Tombol Export + Cleanup --}}
                <div class="col-md-3">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.reports.excel', request()->query()) }}"
                           class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel me-1"></i>Excel
                        </a>
                        <a href="{{ route('admin.reports.pdf', request()->query()) }}"
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

{{-- TABEL LAPORAN --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-graph-up me-2"></i>Data Laporan
        </h5>
        <span class="badge bg-light text-dark">Total: {{ $transaksis->total() }} transaksi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle small mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th width="180">TANGGAL</th>
                        <th>KODE BARANG</th>
                        <th>NAMA BARANG</th>
                        <th width="120">JENIS</th>
                        <th width="100">JUMLAH</th>
                        <th width="120">STOK SAAT INI</th>
                        <th width="150">USER</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $t)
                        <tr>
                            <td class="text-center">{{ $t->created_at->format('d-m-Y H:i') }}</td>
                            <td class="text-center">{{ $t->barang->kode_barang ?? '-' }}</td>
                            <td class="text-center">{{ $t->barang->nama_barang ?? '-' }}</td>
                            <td class="text-center">
                                @if($t->jenis == 'masuk')
                                    <span class="badge bg-success">
                                        <i class="bi bi-arrow-down-circle me-1"></i>Masuk
                                    </span>
                                @elseif($t->jenis == 'rusak')
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Rusak
                                    </span>
                                @elseif($t->jenis == 'hilang')
                                    <span class="badge bg-danger">
                                        <i class="bi bi-x-circle me-1"></i>Hilang
                                    </span>
                                @elseif($t->jenis == 'penjualan')
                                    <span class="badge bg-primary">
                                        <i class="bi bi-cash-coin me-1"></i>Terjual
                                    </span>
                                @elseif($t->jenis == 'return')
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-arrow-counterclockwise me-1"></i>Return
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="bi bi-pencil me-1"></i>Koreksi
                                    </span>
                                @endif
                            </td>
                            <td class="text-center fw-semibold">{{ $t->jumlah }}</td>
                            <td class="text-center">
                                <span class="badge {{ $t->barang->stok <= 5 ? 'bg-danger' : 'bg-success' }}">
                                    {{ $t->barang->stok ?? 0 }}
                                </span>
                            </td>
                            <td class="text-center">{{ $t->user->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem; color: #6c757d;"></i>
                                Tidak ada data laporan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- STATISTIK FOOTER --}}
    @if($transaksis->count() > 0)
    <div class="card-footer bg-light">
        <div class="row text-center g-0">
            <div class="col-md col-6">
                <div class="p-3 border-end">
                    <i class="bi bi-arrow-down-circle text-success" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    <h6 class="mb-1 text-muted small">Total Masuk</h6>
                    <h4 class="fw-bold text-success mb-0">
                        {{ $transaksis->where('jenis', 'masuk')->sum('jumlah') }}
                    </h4>
                </div>
            </div>
            <div class="col-md col-6">
                <div class="p-3 border-end">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    <h6 class="mb-1 text-muted small">Total Rusak</h6>
                    <h4 class="fw-bold text-warning mb-0">
                        {{ $transaksis->where('jenis', 'rusak')->sum('jumlah') }}
                    </h4>
                </div>
            </div>
            <div class="col-md col-6">
                <div class="p-3 border-end">
                    <i class="bi bi-x-circle text-danger" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    <h6 class="mb-1 text-muted small">Total Hilang</h6>
                    <h4 class="fw-bold text-danger mb-0">
                        {{ $transaksis->where('jenis', 'hilang')->sum('jumlah') }}
                    </h4>
                </div>
            </div>
            <div class="col-md col-6">
                <div class="p-3 border-end">
                    <i class="bi bi-cash-coin text-primary" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    <h6 class="mb-1 text-muted small">Total Terjual</h6>
                    <h4 class="fw-bold text-primary mb-0">
                        {{ $transaksis->where('jenis', 'penjualan')->sum('jumlah') }}
                    </h4>
                </div>
            </div>
            <div class="col-md col-6">
                <div class="p-3">
                    <i class="bi bi-arrow-counterclockwise text-secondary" style="font-size: 2rem; display: block; margin-bottom: 0.5rem;"></i>
                    <h6 class="mb-1 text-muted small">Total Return</h6>
                    <h4 class="fw-bold text-secondary mb-0">
                        {{ $transaksis->where('jenis', 'return')->sum('jumlah') }}
                    </h4>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- PAGINATION --}}
@if($transaksis->hasPages())
<div class="d-flex justify-content-end mt-3">
    {{ $transaksis->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endif

<script>
function confirmCleanup(years) {
    if (confirm(`Are you sure you want to delete data older than ${years} years?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("reports.cleanup") }}';
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