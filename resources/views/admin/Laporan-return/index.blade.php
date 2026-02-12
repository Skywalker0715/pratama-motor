@extends('layouts.admin')

@section('title', 'Laporan Return')

@section('content')

<div class="mb-4">
    <h2 class="fw-bold text-dark">Laporan Return</h2>
    <p class="text-muted mb-0">Filter dan export laporan return barang</p>
</div>

{{-- FILTER & EXPORT --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-funnel me-2"></i>Filter Laporan
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.laporan-return') }}">
            <div class="row g-3 align-items-end">

                {{-- Tanggal Dari --}}
                <div class="col-md-2">
                    <label for="from" class="form-label fw-semibold small">
                        <i class="bi bi-calendar-event me-1 text-primary"></i>Tanggal Dari
                    </label>
                    <input type="date" class="form-control form-control-sm" id="from" name="from" value="{{ request('from') }}">
                </div>

                {{-- Tanggal Sampai --}}
                <div class="col-md-2">
                    <label for="to" class="form-label fw-semibold small">
                        <i class="bi bi-calendar-check me-1 text-primary"></i>Tanggal Sampai
                    </label>
                    <input type="date" class="form-control form-control-sm" id="to" name="to" value="{{ request('to') }}">
                </div>

                {{-- Keyword --}}
                <div class="col-md-3">
                    <label for="keyword" class="form-label fw-semibold small">
                        <i class="bi bi-search me-1 text-primary"></i>Keyword
                    </label>
                    <input type="text" class="form-control form-control-sm" id="keyword" name="keyword"
                           value="{{ request('keyword') }}" placeholder="Search product or user">
                </div>

                {{-- Tombol Filter & Reset --}}
                <div class="col-md-2">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        <a href="{{ route('admin.laporan-return') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </a>
                    </div>
                </div>

                {{-- Tombol Export + Cleanup --}}
                <div class="col-md-3">
                    <div class="d-flex gap-2 justify-content-end">
                        <a href="{{ route('admin.laporan-return.excel', request()->query()) }}"
                           class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-excel me-1"></i>Excel
                        </a>
                        <a href="{{ route('admin.laporan-return.pdf', request()->query()) }}"
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

{{-- TABEL DATA RETURN --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-table me-2"></i>Data Return Barang
        </h5>
        <span class="badge bg-light text-dark">Total: {{ $returns->total() }} data</span>
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

<script>
function confirmCleanup(years) {
    if (confirm(`Are you sure you want to delete data older than ${years} years?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("laporan-return.cleanup") }}';
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