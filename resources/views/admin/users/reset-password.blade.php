@extends('layouts.admin')

@section('title', 'Reset Password User')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Reset Password User: <strong>{{ $user->name }}</strong></div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.reset-password', $user->id) }}">
                        @csrf

                        <div class="form-group mb-3">
                            <label class="form-label" for="password">Password Baru</label>
                            <input name="password" required autocomplete="new-password" class="form-control @error('password') is-invalid @enderror" id="password" type="password">
                            @error('password')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="password-confirm">Konfirmasi Password</label>
                            <input name="password_confirmation" required autocomplete="new-password" class="form-control" id="password-confirm" type="password">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Reset Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
