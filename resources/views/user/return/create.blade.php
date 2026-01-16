@extends('layouts.user')

@section('content')
<h4>Return Barang</h4>

<form method="POST" action="{{ route('user.return.store') }}">
@csrf

<div class="mb-3">
    <label>Transaksi</label>
    <select name="transaksi_id" class="form-control" required>
        @foreach ($transaksis as $trx)
            <option value="{{ $trx->id }}">
                #{{ $trx->id }} - {{ $trx->created_at }}
            </option>
        @endforeach
    </select>
</div>

<div class="mb-3">
    <label>Alasan Return</label>
    <textarea name="alasan" class="form-control" required></textarea>
</div>

<button class="btn btn-danger">Proses Return</button>
</form>
@endsection
