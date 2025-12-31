<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Notyf -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
     <!-- Google Font: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="d-flex">
    <!-- Sidebar -->
    <aside id="admin-sidebar" class="bg-dark text-white p-3">
        <h5 class="mb-4 sidebar-title">Pratama Motor</h5>
        <ul class="nav flex-column gap-2">
            <li class="nav-item"><a href="{{ url('/admin/dashboard') }}" class="nav-link text-white"><i class="bi bi-speedometer2 me-2"></i> <span>Dashboard</span></a></li>
            <li class="nav-item"><a href="{{ url('/admin/products') }}" class="nav-link text-white"><i class="bi bi-box me-2"></i> <span>Produk</span></a></li>
            <li class="nav-item"><a href="{{ url('/admin/stock') }}" class="nav-link text-white"><i class="bi bi-archive me-2"></i> <span>Stok</span></a></li>
            <li class="nav-item"><a href="{{ url('/admin/reports') }}" class="nav-link text-white"><i class="bi bi-file-earmark-text me-2"></i> <span>Laporan</span></a></li>
            <li class="nav-item"><a href="{{ url('/admin/users') }}" class="nav-link text-white"><i class="bi bi-people me-2"></i> <span>User</span></a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <div id="main-content" class="flex-grow-1">
        <!-- Navbar -->
        <nav class="navbar navbar-light bg-white shadow-sm px-4">
            <div class="d-flex align-items-center">
                <button id="sidebar-toggle" class="btn btn-sm me-2">
                    <i class="bi bi-list fs-5"></i>
                </button>
                <span class="navbar-text">Admin Panel</span>
            </div>

            <div class="dropdown">
                <button class="btn btn-sm" type="button" id="user-menu" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-gear fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="user-menu">
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="dropdown-item" id="logoutForm">
                            @csrf
                           <button type="button"
                            class="btn btn-link text-danger p-0 text-decoration-none"
                            data-bs-toggle="modal"
                            data-bs-target="#logoutModal">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>

        <main class="p-4">
            @yield('content')
        </main>
    </div>
</div>

<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body text-center">
        Yakin ingin logout?
      </div>

      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          Batal
        </button>
        <button type="button" class="btn btn-danger" onclick="submitLogout()">
          Logout
        </button>
      </div>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Notyf -->
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    const notyf = new Notyf({ duration: 3000, position: { x: 'right', y: 'top' } });
</script>

@if(session('success'))
<script>notyf.success("{{ session('success') }}");</script>
@endif
@if(session('error'))
<script>notyf.error("{{ session('error') }}");</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('admin-sidebar');
    const mainContent = document.getElementById('main-content');
    const toggleButton = document.getElementById('sidebar-toggle');

    // === LOAD STATE DARI localStorage ===
    const sidebarState = localStorage.getItem('sidebar-collapsed');

    if (sidebarState === 'true') {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('collapsed');
    }

    // === TOGGLE SIDEBAR ===
    toggleButton.addEventListener('click', function () {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');

        // SIMPAN STATE
        localStorage.setItem(
            'sidebar-collapsed',
            sidebar.classList.contains('collapsed')
        );
    });
});
</script>
<script>
function submitLogout() {
    document.getElementById('logoutForm').submit();
}
</script>
@stack('scripts')
</body>
</html>
