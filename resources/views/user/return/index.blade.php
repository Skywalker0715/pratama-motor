@extends('layouts.user')

@section('content')
<h4>Riwayat Return</h4>

<a href="{{ route('user.return.create') }}" class="btn btn-primary mb-3">
    Buat Return
</a>

<table class="table">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Transaksi</th>
            <th>Total Item</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($returns as $return)
        <tr>
            <td>{{ $return->tanggal }}</td>
            <td>#{{ $return->transaksi_id }}</td>
            <td>{{ $return->items_count }}</td>
            <td>
                <a href="{{ route('user.return.show', $return->id) }}" class="btn btn-sm btn-info">
                    Detail
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
