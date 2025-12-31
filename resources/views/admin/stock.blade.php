@extends('layouts.admin')

@section('title', 'Stok Barang')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-dark">Pengelolaan Stok Barang</h2>
    <p class="text-muted mb-0">Kelola stok masuk, keluar, rusak, dan hilang</p>
</div>

{{-- FORM STOK --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-box-open me-2"></i>Form Transaksi Stok
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.stock.update') }}" method="POST" class="row g-3">
            @csrf

            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    <i class="fas fa-cube me-1 text-primary"></i>Produk
                </label>
                  <select name="barang_id" class="form-select select-barang" required>
                         <option value="">-- Cari & Pilih Produk --</option>
                           @foreach($barangs as $barang)
                           <option value="{{ $barang->id }}">
                          {{ $barang->nama_barang }} | Stok: {{ $barang->stok }}
                              </option>
                        @endforeach
                 </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-sort-numeric-up me-1 text-primary"></i>Jumlah
                </label>
                <input type="number" name="jumlah" min="1" class="form-control" placeholder="0" required>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-exchange-alt me-1 text-primary"></i>Jenis Transaksi
                </label>
                <select name="jenis" class="form-select" required>
                    <option value="masuk">Masuk</option>
                    <option value="rusak">Rusak</option>
                    <option value="hilang">Hilang</option>
                    <option value="koreksi">Koreksi</option>
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 btn-submit">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- RIWAYAT STOK --}}
<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-history me-2"></i>Riwayat Stok
        </h5>
        <span class="badge bg-light text-dark">Total: {{ $transaksis->count() }} transaksi</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle small mb-0">
                <thead class="table-light text-center">
                    <tr>
                        <th width="50">NO</th>
                        <th>NAMA BARANG</th>
                        <th width="120">JENIS</th>
                        <th width="100">JUMLAH</th>
                        <th width="150">PETUGAS</th>
                        <th width="180">TANGGAL</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transaksis as $index => $transaksi)
                        <tr>
                            <td class="text-center">
                                 {{ $transaksis->firstItem() + $index }}
                            </td>
                            <td class="text-center">{{ $transaksi->barang->nama_barang ?? '-' }}</td>
                            <td class="text-center">
                                @if($transaksi->jenis === 'masuk')
                                    <span class="badge bg-success">
                                        <i class="fas fa-arrow-down me-1"></i>Masuk
                                    </span>
                                @elseif($transaksi->jenis === 'rusak')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Rusak
                                    </span>
                                @elseif($transaksi->jenis === 'hilang')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Hilang
                                    </span>
                                 @elseif($transaksi->jenis === 'penjualan')
                                 <span class="badge bg-primary">
                                      <i class="fas fa-cash-register me-1"></i>Terjual
                                  </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="fas fa-edit me-1"></i>Koreksi
                                    </span>
                                @endif
                            </td>
                            <td class="text-center fw-semibold">{{ $transaksi->jumlah }}</td>
                            <td class="text-center">{{ $transaksi->user->name ?? '-' }}</td>
                            <td class="text-center">{{ $transaksi->created_at->format('d-m-Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block text-secondary"></i>
                                Belum ada riwayat stok
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($transaksis->hasPages())
              <div class="d-flex justify-content-end mt-3">
                    {{ $transaksis->onEachSide(1)->links('pagination::bootstrap-5') }}
              </div>
          @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('.select-barang').select2({
            placeholder: 'Cari nama barang...',
            width: '100%'
        });
    });
</script>
@endpush
