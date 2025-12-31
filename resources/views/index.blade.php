<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pratama Motor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <!-- Notyf -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
</head>
<body class="no-dropdown">

<section class="hero">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-md-6 hero-left">
                <h1 class="fw-bold mb-3">
                    Sistem Penjualan & Stok Barang
                </h1>

                <p class="text-muted mb-4">
                    Kelola stok, transaksi, dan laporan Pratama Motor
                    secara cepat, rapi, dan terintegrasi.
                </p>

                <a href="/login" class="btn btn-primary btn-lg">
                    Mulai Sekarang
                </a>
            </div>

            <div class="col-md-6 text-center">
                <img
                    src="{{ asset('images/hero.jpeg') }}"
                    class="img-fluid rounded hero-img"
                    alt="Hero Image"
                >
            </div>

        </div>
    </div>
</section>

@include('partials.notyf')
</body>
</html>
