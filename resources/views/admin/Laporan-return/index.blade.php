@extends('layouts.admin')

@section('title', 'Laporan Return')

@section('content')
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Laporan Return</h1>
        <div>
            <a href="{{ route('admin.laporan-return.pdf', request()->query()) }}" class="btn btn-sm btn-danger shadow-sm">
                <i class="fas fa-file-pdf fa-sm text-white-50"></i> Export PDF
            </a>
            <a href="{{ route('admin.laporan-return.excel', request()->query()) }}" class="btn btn-sm btn-success shadow-sm ml-2">
                <i class="fas fa-file-excel fa-sm text-white-50"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.laporan-return') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="from">Dari Tanggal</label>
                        <input type="date" class="form-control" id="from" name="from" value="{{ request('from') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="to">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="to" name="to" value="{{ request('to') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.laporan-return') }}" class="btn btn-secondary">
                            <i class="fas fa-sync"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Content Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary">
            <h6 class="m-0 font-weight-bold text-white">Data Return Barang</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th width="5%" class="text-center">No</th>
                            <th width="15%">Tanggal</th>
                            <th width="20%">Nama Barang</th>
                            <th width="10%" class="text-center">Jumlah Return</th>
                            <th width="30%">Alasan / Catatan</th>
                            <th width="15%">Kasir</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($returns as $index => $return)
                        <tr>
                            <td class="text-center">{{ $returns->firstItem() + $index }}</td>
                            <td>{{ \Carbon\Carbon::parse($return->created_at)->format('d M Y H:i') }}</td>
                            <td>{{ $return->barang->nama_barang ?? 'Barang Dihapus' }}</td>
                            <td class="text-center">
                                <span class="badge badge-pill" style="background-color: #212529 !important; color: #ffffff !important; font-weight: bold; padding: 6px 12px; min-width: 40px;">{{ $return->quantity }}</span>
                            </td>
                            <td>{{ $return->notes ?? '-' }}</td>
                            <td>{{ $return->user->name ?? 'User Dihapus' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i>
                                <p class="mb-0">Tidak ada data return ditemukan.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($returns->count() > 0)
                    <tfoot>
                        <tr class="table-secondary font-weight-bold">
                            <td colspan="3" class="text-right">Total (Halaman Ini)</td>
                            <td class="text-center">
                                <span class="badge badge-pill" style="background-color: #007bff !important; color: #ffffff !important; font-weight: bold; padding: 6px 12px; min-width: 40px;">{{ $returns->sum('quantity') }}</span>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            
            <!-- Pagination -->
            @if($returns->hasPages())
            <div class="row mt-3 align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Menampilkan {{ $returns->firstItem() ?? 0 }} sampai {{ $returns->lastItem() ?? 0 }} dari {{ $returns->total() }} data
                    </p>
                </div>
                <div class="col-md-6">
                    <div class="float-right">
                        {{ $returns->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

@push('styles')
<style>
    .table th {
        vertical-align: middle;
        white-space: nowrap;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .badge-pill {
        min-width: 40px;
    }
    
    /* Custom badge untuk jumlah yang keliatan jelas */
    .badge-custom-warning {
        background-color: #212529 !important; /* Hitam */
        color: #ffffff !important; /* Putih */
        font-weight: bold !important;
        padding: 6px 12px !important;
    }
    
    .badge-custom-total {
        background-color: #007bff !important;
        color: #fff !important;
        font-weight: bold !important;
        padding: 6px 12px !important;
    }
</style>
@endpush
@endsection