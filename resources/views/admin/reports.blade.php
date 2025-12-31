@extends('layouts.admin')

@section('title', 'Laporan Stok')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark">Laporan Penjualan</h2>
    <p class="text-muted mb-0">Filter dan export laporan transaksi stok barang</p>
</div>

{{-- FILTER & EXPORT --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-filter me-2"></i>Filter Laporan
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-calendar-alt me-1 text-primary"></i>Tanggal Dari
                </label>
                <input type="date" name="from" class="form-control" value="{{ request('from') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-calendar-check me-1 text-primary"></i>Tanggal Sampai
                </label>
                <input type="date" name="to" class="form-control" value="{{ request('to') }}">
            </div>

            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-2"></i>Filter
                </button>
            </div>

            <div class="col-md-4 text-end">
                <div class="btn-group" role="group">
                   <a href="{{ route('admin.reports.excel', request()->query()) }}"
                            class="btn btn-success">
                               <i class="fas fa-file-excel me-2"></i>Export Excel
                             </a>
                            <a href="{{ route('admin.reports.pdf', request()->query()) }}"
                                     class="btn btn-danger">
                             <i class="fas fa-file-pdf me-2"></i>Export PDF
                      </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- TABEL LAPORAN --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-chart-line me-2"></i>Data Laporan
        </h5>
        <span class="badge bg-light text-dark">Total: {{ $transaksis->total() }} transaksi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle small mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th width="180">TANGGAL</th>
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
                            <td class="text-center">{{ $t->barang->nama_barang }}</td>
                            <td class="text-center">
                                @if($t->jenis == 'masuk')
                                    <span class="badge bg-success">
                                        <i class="fas fa-arrow-down me-1"></i>Masuk
                                    </span>
                                @elseif($t->jenis == 'rusak')
                                    <span class="badge bg-warning text-dark">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Rusak
                                    </span>
                                @elseif($t->jenis == 'hilang')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Hilang
                                    </span>
                                @elseif($t->jenis == 'penjualan')
                                    <span class="badge bg-primary">
                                        <i class="fas fa-cash-register me-1"></i>Terjual
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="fas fa-edit me-1"></i>Koreksi
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
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block text-secondary"></i>
                                Tidak ada data laporan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- STATISTIK FOOTER (FIX: 4 CARD SEJAJAR) --}}
    @if($transaksis->count() > 0)
    <div class="card-footer bg-light">
        <div class="row text-center g-0">
            {{-- TOTAL MASUK --}}
            <div class="col-md-3 col-6">
                <div class="p-3 border-end">
                    <i class="fas fa-arrow-down text-success fa-2x mb-2"></i>
                    <h6 class="mb-1 text-muted small">Total Masuk</h6>
                    <h4 class="fw-bold text-success mb-0">
                        {{ $transaksis->where('jenis', 'masuk')->sum('jumlah') }}
                    </h4>
                </div>
            </div>

            {{-- TOTAL RUSAK --}}
            <div class="col-md-3 col-6">
                <div class="p-3 border-end">
                    <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                    <h6 class="mb-1 text-muted small">Total Rusak</h6>
                    <h4 class="fw-bold text-warning mb-0">
                        {{ $transaksis->where('jenis', 'rusak')->sum('jumlah') }}
                    </h4>
                </div>
            </div>

            {{-- TOTAL HILANG --}}
            <div class="col-md-3 col-6">
                <div class="p-3 border-end">
                    <i class="fas fa-times-circle text-danger fa-2x mb-2"></i>
                    <h6 class="mb-1 text-muted small">Total Hilang</h6>
                    <h4 class="fw-bold text-danger mb-0">
                        {{ $transaksis->where('jenis', 'hilang')->sum('jumlah') }}
                    </h4>
                </div>
            </div>

            {{-- TOTAL TERJUAL --}}
            <div class="col-md-3 col-6">
                <div class="p-3">
                    <i class="fas fa-cash-register text-primary fa-2x mb-2"></i>
                    <h6 class="mb-1 text-muted small">Total Terjual</h6>
                    <h4 class="fw-bold text-primary mb-0">
                        {{ $transaksis->where('jenis', 'penjualan')->sum('jumlah') }}
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

@endsection