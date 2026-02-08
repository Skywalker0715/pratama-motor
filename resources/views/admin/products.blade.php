@extends('layouts.admin')

@section('title', 'Data Produk')

@section('content')
<div class="card shadow-sm">
    {{-- HEADER --}}
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold">Data Produk</h5>
        <button class="btn btn-primary btn-icon"
            data-bs-toggle="modal"
            data-bs-target="#addModal"
            title="Tambah Produk">
            <i class="fas fa-plus"></i>
        </button>
    </div>

    <div class="card-body">

        {{-- SEARCH / FILTER / IMPORT --}}
        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <form method="GET" action="{{ route('admin.products') }}" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm"
                        placeholder="Cari produk..." value="{{ request('search') }}">

                    <select name="kategori" class="form-select form-select-sm">
                        <option value="">Semua Kategori</option>
                        @foreach($barangs->pluck('kategori')->unique() as $kat)
                            <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                                {{ $kat }}
                            </option>
                        @endforeach
                    </select>

                    <button class="btn btn-secondary btn-icon" type="submit" title="Cari">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <div class="col-md-4 ms-auto text-end">
                <form id="importForm"
                    action="{{ route('admin.products.import') }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="d-inline-block">
                    @csrf
                    <input type="file" id="importFile" name="file" hidden
                        onchange="document.getElementById('importForm').submit()">

                    <button type="button"
                        class="btn btn-success btn-icon"
                        onclick="document.getElementById('importFile').click()"
                        title="Import Excel">
                        <i class="fas fa-file-excel"></i>
                    </button>
                </form>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle small">
                <thead class="table-light text-center">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Satuan</th>
                        <th>Rak</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($barangs as $index => $barang)
                    <tr>
                        <td class="text-center">{{ $barangs->firstItem() + $index }}</td>
                        <td class="fw-semibold text-center">{{ $barang->kode_barang }}</td>
                        <td class="text-center">{{ $barang->nama_barang }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary">{{ $barang->kategori }}</span>
                        </td>
                        <td class="text-center">Rp {{ number_format($barang->harga, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $barang->stok <= 5 ? 'bg-danger' : 'bg-success' }}">
                                {{ $barang->stok }}
                            </span>
                        </td>
                        <td class="text-center">{{ $barang->satuan }}</td>
                        <td class="text-center">{{ $barang->lokasi_rak }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button class="btn btn-icon btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal{{ $barang->id }}"
                                    title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>

                              <button class="btn btn-icon btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#priceModal{{ $barang->id }}"
                                    title="Ubah Harga">
                                    <i class="fas fa-dollar-sign"></i>
                                </button>

                                <form action="{{ route('admin.products.destroy', $barang->id) }}"
                                    method="POST"
                                    class="delete-form">
                                    @csrf
                                    @method('DELETE')
                                   <button type="button"
                                    class="btn btn-icon btn-danger delete-btn"
                                    data-action="{{ route('admin.products.destroy', $barang->id) }}"
                                    title="Hapus">
                                   <i class="fas fa-trash-alt"></i>
                               </button>
                                </form>
                            </div>
                        </td>
                    </tr>

                    {{-- MODAL EDIT --}}
                    <div class="modal fade" id="editModal{{ $barang->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <form action="{{ route('admin.products.update', $barang->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title">Edit Produk</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        @include('admin.products.form', ['barang' => $barang])
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- MODAL PRICE --}}
                    <div class="modal fade" id="priceModal{{ $barang->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <form action="{{ route('admin.products.price.update', $barang->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h6 class="modal-title">Ubah Harga</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-start">
                                        <div class="mb-3">
                                            <label class="form-label">Harga Baru</label>
                                            <input type="number" name="harga" class="form-control"
                                                value="{{ $barang->harga }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Sumber</label>
                                            <select name="source" class="form-select" required>
                                                <option value="pratama_motor">Pratama Motor</option>
                                                <option value="mandala">Mandala</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-3x mb-3 d-block text-secondary"></i>
                            Data produk kosong
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>


        {{-- PAGINATION --}}
        <div class="d-flex justify-content-end mt-3">
            {{ $barangs->withQueryString()->links() }}
        </div>
    </div>
</div>

{{-- MODAL ADD --}}
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.products.store') }}" method="POST" id="addProductForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Tambah Produk</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @include('admin.products.form-add')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MODAL CONFIRM DELETE --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title text-white">
                    <i class="fas fa-trash-alt"></i> Konfirmasi Hapus
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <p class="mb-0">Yakin ingin menghapus produk ini?</p>
            </div>

            <div class="modal-footer justify-content-center">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button class="btn btn-sm btn-danger" id="confirmDeleteBtn">
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>

    let deleteAction = null;

document.addEventListener('click', function (e) {
    const btn = e.target.closest('.delete-btn');
    if (!btn) return;

    deleteAction = btn.getAttribute('data-action');

    const modal = new bootstrap.Modal(
        document.getElementById('confirmDeleteModal')
    );
    modal.show();
});

document.getElementById('confirmDeleteBtn')
    .addEventListener('click', function () {

        if (!deleteAction) return;

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = deleteAction;

        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;

        document.body.appendChild(form);
        form.submit();
});

</script>
@endpush
@endsection