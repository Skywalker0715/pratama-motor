@extends('layouts.user')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Riwayat Return Barang</h1>
        <a href="{{ route('user.return.create') }}" 
           class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <span class="material-icons text-sm">add_circle</span>
            Buat Return
        </a>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <form method="GET" action="{{ route('user.return.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="material-icons text-sm align-middle">search</span>
                        Cari Transaksi
                    </label>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Cari ID transaksi..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Filter Tanggal Mulai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="material-icons text-sm align-middle">date_range</span>
                        Dari Tanggal
                    </label>
                    <input type="date" 
                           name="tanggal_mulai" 
                           value="{{ request('tanggal_mulai') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Filter Tanggal Akhir -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span class="material-icons text-sm align-middle">date_range</span>
                        Sampai Tanggal
                    </label>
                    <input type="date" 
                           name="tanggal_akhir" 
                           value="{{ request('tanggal_akhir') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <button type="submit" 
                        class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <span class="material-icons text-sm">filter_alt</span>
                    Filter
                </button>
                <a href="{{ route('user.return.index') }}" 
                   class="flex items-center gap-2 px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                    <span class="material-icons text-sm">refresh</span>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Info Badge (jika ada filter aktif) -->
    @if(request()->hasAny(['search', 'tanggal_mulai', 'tanggal_akhir']))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 flex items-center justify-between">
        <div class="flex items-center gap-2 text-blue-700">
            <span class="material-icons text-sm">info</span>
            <span class="text-sm">
                Menampilkan hasil filter
                @if(request('search'))
                    untuk "<strong>{{ request('search') }}</strong>"
                @endif
                @if(request('tanggal_mulai') || request('tanggal_akhir'))
                    dari <strong>{{ request('tanggal_mulai') ?? '...' }}</strong> 
                    sampai <strong>{{ request('tanggal_akhir') ?? '...' }}</strong>
                @endif
            </span>
        </div>
        <a href="{{ route('user.return.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
            Hapus Filter
        </a>
    </div>
    @endif

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Transaksi
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total Item
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($returns as $return)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        {{ \Carbon\Carbon::parse($return->tanggal)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        #{{ $return->transaksi_id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $return->items_count }} Item
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('user.return.show', $return->id) }}" 
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                            <span class="material-icons text-sm">visibility</span>
                            Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center gap-3">
                            <span class="material-icons text-6xl text-gray-300">inbox</span>
                            <p class="text-lg font-medium">
                                @if(request()->hasAny(['search', 'tanggal_mulai', 'tanggal_akhir']))
                                    Tidak ada data return yang sesuai dengan filter
                                @else
                                    Belum ada data return
                                @endif
                            </p>
                            @if(!request()->hasAny(['search', 'tanggal_mulai', 'tanggal_akhir']))
                            <a href="{{ route('user.return.create') }}" 
                               class="text-blue-600 hover:underline">
                                Buat return pertama kamu
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($returns->hasPages())
    <div class="mt-6">
        {{ $returns->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection