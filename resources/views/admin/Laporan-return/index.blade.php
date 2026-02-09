@extends('layouts.admin')

@section('title', 'Laporan Return')

@section('content')

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h2 class="fw-bold text-dark mb-1">Laporan Return</h2>
        <p class="text-muted mb-0">Filter dan export laporan return barang</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.laporan-return.pdf', request()->query()) }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i>PDF
        </a>
        <a href="{{ route('admin.laporan-return.excel', request()->query()) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i>Excel
        </a>
    </div>
</div>

<!-- Filter Section -->
<div class="card shadow-sm mb-3">
    <div class="card-header py-2">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-funnel me-2"></i>Filter Laporan
        </h6>
    </div>
    <div class="card-body p-2">
        <form method="GET" action="{{ route('admin.laporan-return') }}">
            <div class="row g-2 align-items-end">
                <div class="col-5">
                    <label for="from" class="form-label small mb-1">Dari</label>
                    <input type="date" class="form-control form-control-sm" id="from" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-5">
                    <label for="to" class="form-label small mb-1">Sampai</label>
                    <input type="date" class="form-control form-control-sm" id="to" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-2">
                    <div class="d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm px-2 w-100">
                            <i class="bi bi-search"></i>
                        </button>
                        <a href="{{ route('admin.laporan-return') }}" class="btn btn-secondary btn-sm px-2 w-100">
                            <i class="bi bi-x"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-table me-2"></i>Data Return Barang
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle small mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th width="50">NO</th>
                        <th width="130">TANGGAL</th>
                        <th>NAMA BARANG</th>
                        <th width="100">JUMLAH</th>
                        <th>ALASAN / CATATAN</th>
                        <th width="120">KASIR</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $index => $return)
                    <tr>
                        <td class="text-center">{{ $returns->firstItem() + $index }}</td>
                        <td class="text-center">{{ \Carbon\Carbon::parse($return->created_at)->format('d-m-Y H:i') }}</td>
                        <td class="text-center">{{ $return->barang->nama_barang ?? 'Barang Dihapus' }}</td>
                        <td class="text-center">
                            <span class="badge bg-dark">{{ $return->quantity }}</span>
                        </td>
                        <td class="text-center">{{ $return->notes ?? '-' }}</td>
                        <td class="text-center">{{ $return->user->name ?? 'User Dihapus' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="bi bi-inbox" style="font-size: 3rem; display: block; margin-bottom: 1rem; color: #6c757d;"></i>
                            Tidak ada data return
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($returns->count() > 0)
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td colspan="3" class="text-end">Total (Halaman Ini)</td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ $returns->sum('quantity') }}</span>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

{{-- PAGINATION --}}
@if($returns->hasPages())
<div class="d-flex justify-content-end mt-3">
    {{ $returns->withQueryString()->onEachSide(1)->links('pagination::bootstrap-5') }}
</div>
@endif

@endsection