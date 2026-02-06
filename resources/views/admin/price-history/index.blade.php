@extends('layouts.admin')

@section('title', 'Histori Perubahan Harga')

@section('content')
<div class="card shadow-sm">
    <div class="card-header">
        <h5 class="mb-0 fw-semibold">Histori Perubahan Harga</h5>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle small">
                <thead class="table-light text-center">
                    <tr>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Harga Lama</th>
                        <th>Harga Baru</th>
                        <th>Sumber</th>
                        <th>Admin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($histories as $history)
                    <tr>
                        <td class="text-center">
                            {{ $history->created_at->format('d-m-Y') }}
                            <div class="text-muted" style="font-size: 0.85em;">{{ $history->created_at->format('H:i') }}</div>
                        </td>
                        <td class="text-center">
                            @if($history->barang)
                                <div class="fw-semibold">{{ $history->barang->nama_barang }}</div>
                                <div class="text-muted" style="font-size: 0.85em;">{{ $history->barang->kode_barang }}</div>
                            @else
                                <span class="text-danger fst-italic">Produk Terhapus</span>
                            @endif
                        </td>
                        <td class="text-center">Rp {{ number_format($history->old_price, 0, ',', '.') }}</td>
                        <td class="text-center">Rp {{ number_format($history->new_price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            @if($history->source === 'mandala')
                                <span class="badge bg-info text-dark">Mandala</span>
                            @elseif($history->source === 'pratama_motor')
                                <span class="badge bg-primary">Pratama Motor</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($history->source) }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $history->user->name ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="fas fa-history fa-2x mb-2 d-block text-secondary"></i>
                            Belum ada data histori perubahan harga
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end mt-3">
            {{ $histories->links() }}
        </div>
    </div>
</div>
@endsection