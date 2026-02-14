<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login | Pratama Motor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
     <!-- Notyf -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-header">
            <h1>Pratama Motor</h1>
            <p>Masuk ke sistem penjualan & stok Pratama Motor</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required autofocus placeholder="email@pratamamotor.com">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>

            <div class="form-extra">
                <a href="{{ route('forgot-password') }}" class="link">
                    Lupa password?
                </a>
            </div>

            <button type="submit" class="btn-auth">
                Masuk
            </button>
        </form>

        <div class="auth-footer">
            <span>Belum punya akun?</span>
            <a href="{{ route('register') }}">Daftar</a>
        </div>

    </div>
</div>

    @include('partials.notyf')
</body>
</html>
