<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $isInactive = $status === 'inactive';

        $users = User::where('is_active', $isInactive ? 0 : 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users', compact('users', 'status'));
    }

    public function destroy(User $user)
    {
        // admin tidak boleh hapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri');
        }

        // Delete related data manually
        $user->returns()->delete();
        $user->transaksi()->delete();

        // Delete user
        $user->delete();

        return back()->with('success', 'User berhasil dihapus');
    }

    public function activate(User $user)
    {
        $user->update(['is_active' => true]);
        return back()->with('success', 'User berhasil diaktifkan');
    }
}
