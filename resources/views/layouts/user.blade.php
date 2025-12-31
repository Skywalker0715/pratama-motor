<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir - Pratama Motor')</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">

    <style>
        body { font-family: 'Poppins', sans-serif; }
        .notyf { z-index: 9999 !important; }

        [x-cloak] { display: none !important; }

    </style>

    @livewireStyles
</head>

<body class="bg-gray-100">

<div
    x-data="{
        sidebarOpen: localStorage.getItem('sidebar') === 'open',
        settingsOpen: false,
        logoutOpen: false
    }"
    x-init="$watch('sidebarOpen', v => localStorage.setItem('sidebar', v ? 'open' : 'closed'))"
>

    <header class="fixed top-0 inset-x-0 h-16 bg-white shadow z-20 flex items-center justify-between px-4">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-600">
                <span class="material-icons">menu</span>
            </button>
            <span class="font-bold text-gray-800">Pratama Motor</span>
        </div>

        <div class="relative">
            <button @click="settingsOpen = !settingsOpen">
                <span class="material-icons text-gray-600">settings</span>
            </button>

            <div x-cloak x-show="settingsOpen" x-transition @click.away="settingsOpen = false""
                 class="absolute right-0 mt-2 w-44 bg-white rounded shadow">
                <button @click="settingsOpen = false; logoutOpen = true"
                        class="w-full flex items-center gap-2 px-4 py-2 text-red-600 hover:bg-gray-100">
                    <span class="material-icons text-sm">logout</span>
                    Logout
                </button>
            </div>
        </div>
    </header>

    <aside class="fixed top-16 left-0 h-full w-64 bg-white shadow transition-transform duration-300 z-10"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <nav class="p-4 space-y-2">
            <a href="{{ route('user.dashboard') }}"
               class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('user.dashboard') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100' }}">
                <span class="material-icons text-gray-600">dashboard</span>
                Dashboard
            </a>

            <a href="{{ route('user.transaksi') }}"
               class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('user.transaksi') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100' }}">
                <span class="material-icons text-gray-600">point_of_sale</span>
                Transaksi
            </a>

            <a href="{{ route('user.history') }}"
               class="flex items-center gap-3 px-4 py-2 rounded {{ request()->routeIs('user.history') ? 'bg-gray-200 font-semibold' : 'hover:bg-gray-100' }}">
                <span class="material-icons text-gray-600">history</span>
                Riwayat
            </a>
        </nav>
    </aside>

    <main class="pt-20 p-4 transition-all duration-300" :class="sidebarOpen ? 'ml-64' : 'ml-0'">
        @yield('content')
    </main>

    <div x-cloak x-show="logoutOpen" x-transition.opacity class="fixed inset-0 bg-black/50 z-[9999] flex items-center justify-center">
        <div class="bg-white rounded-xl p-6 w-full max-w-sm shadow-lg">
            <h3 class="font-semibold text-lg mb-2">Konfirmasi Logout</h3>
            <p class="text-gray-600 mb-4">Yakin ingin keluar dari sistem?</p>

            <div class="flex justify-end gap-2">
                <button @click="logoutOpen = false" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">
                    Batal
                </button>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded bg-red-600 text-white hover:bg-red-700">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

{{-- SCRIPTS - URUTAN PENTING! --}}

{{-- 1. LIVEWIRE SCRIPTS PERTAMA (HARUS!) --}}
@livewireScripts

{{-- 2. ALPINE.JS - HANYA 1 KALI! --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/alpine.js"></script>

{{-- 3. NOTYF --}}
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.notyf = new Notyf({
            duration: 3000,
            position: { x: 'right', y: 'top' },
            dismissible: true
        });

        // Event listener untuk Livewire
        window.addEventListener('show-notification', event => {
            const detail = event.detail[0] || event.detail;
            if (detail.type === 'success') {
                window.notyf.success(detail.message);
            } else if (detail.type === 'error') {
                window.notyf.error(detail.message);
            }
        });
    });
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.notyf.success(@json(session('success')));
    });
</script>
@endif

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        window.notyf.error(@json(session('error')));
    });
</script>
@endif

</body>
</html>