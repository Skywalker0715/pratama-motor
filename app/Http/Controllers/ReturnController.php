<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReturnService;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\ReturnModel;


Class ReturnController extends Controller
{
    protected ReturnService $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function index(Request $request)
    {
        $query = ReturnModel::where('user_id', auth()->id());

        // Filter Search (ID Transaksi)
        if ($request->filled('search')) {
            $query->where('transaksi_id', 'like', '%' . $request->search . '%');
        }

        // Filter Tanggal Mulai
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_mulai);
        }

        // Filter Tanggal Akhir
        if ($request->filled('tanggal_akhir')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_akhir);
        }

        // Get data dengan pagination
        $returns = $query->latest('tanggal')
                        ->paginate(10)
                        ->withQueryString(); // Penting! Agar filter tetap ada saat pindah halaman

        return view('user.return.index', compact('returns'));
    }

    public function create()
    {
        $transaksis = Transaksi::where('user_id', auth()->id())->latest()->get();

        return view('user.return.create', compact('transaksis'));
    }

    public function store(Request $request)
    {
    $request->validate([
        'transaksi_id' => 'required|exists:transaksis,id',
        'items' => 'required|array|min:1',
        'items.*.barang_id' => 'required|exists:barangs,id',
        'items.*.quantity' => 'required|integer|min:1',
        'alasan' => 'nullable|string|max:255',
    ]);

    $this->returnService->handle(
        $request->transaksi_id,
        $request->items,
        Auth::id(),
        $request->alasan
    );

    return redirect()
        ->route('user.return.index')
        ->with('success', 'Return berhasil diproses');
    }

     public function show($id)
    {
        $return = ReturnModel::with('items')->findOrFail($id);
        return view('user.return.show', compact('return'));
    }
}