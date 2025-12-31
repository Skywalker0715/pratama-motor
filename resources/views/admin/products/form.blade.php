<div class="row g-2">

    <div class="col-md-6">
        <label class="form-label">Kode Barang</label>
        <input type="text" name="kode_barang"
            class="form-control form-control-sm"
            value="{{ $barang->kode_barang ?? '' }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Nama Barang</label>
        <input type="text" name="nama_barang"
            class="form-control form-control-sm"
            value="{{ $barang->nama_barang ?? '' }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Kategori</label>
        <input type="text" name="kategori"
            class="form-control form-control-sm"
            value="{{ $barang->kategori ?? '' }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Harga</label>
        <input type="number" name="harga"
            class="form-control form-control-sm"
            value="{{ $barang->harga ?? '' }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Satuan</label>
        <input type="text" name="satuan"
            class="form-control form-control-sm"
            value="{{ $barang->satuan ?? '' }}" required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Lokasi Rak</label>
        <input type="text" name="lokasi_rak"
            class="form-control form-control-sm"
            value="{{ $barang->lokasi_rak ?? '' }}">
    </div>

</div>
