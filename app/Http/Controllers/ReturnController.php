<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReturnService;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;


Class ReturnController extends Controller
{
    protected ReturnService $returnService;

    public function __construct(ReturnService $returnService)
    {
        $this->returnService = $returnService;
    }

    public function index()
    {
        $returns = $this->returnService->getUserReturns(auth()->id());

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

}