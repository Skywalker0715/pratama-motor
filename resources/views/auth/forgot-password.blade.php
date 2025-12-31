<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lupa Password | Pratama Motor</title>
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
            <h1>Lupa Password</h1>
            <p>Masukkan email untuk menerima link reset password</p>
        </div>

        <form method="POST" action="{{ route('forgot-password') }}">
            @csrf

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" required placeholder="email@pratamamotor.com">
            </div>

            <button type="submit" class="btn-auth">
                Kirim Link Reset
            </button>
        </form>

        <div class="auth-footer">
            <a href="{{ route('login') }}">Kembali ke Login</a>
        </div>

    </div>
</div>

    @include('partials.notyf')
</body>
</html>