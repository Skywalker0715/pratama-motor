<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | Pratama Motor</title>
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
            <h1>Reset Password</h1>
            <p>Masukkan password baru untuk akun Anda</p>
        </div>

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $email ?? '') }}" required>
            </div>

            <div class="form-group">
                <label>Password Baru</label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter">
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required placeholder="Ulangi password">
            </div>

            <button type="submit" class="btn-auth">
                Reset Password
            </button>
        </form>

    </div>
</div>

    @include('partials.notyf')
</body>
</html>
