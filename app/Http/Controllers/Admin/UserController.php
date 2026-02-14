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

    public function deactivate(User $user)
    {
        // admin tidak boleh nonaktifkan dirinya sendiri
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update(['is_active' => false]);

        return back()->with('success', 'User berhasil dinonaktifkan.');
    }


    public function createAdmin()
    {
        return view('admin.users.create-admin');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => 'admin',
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Admin created successfully.');
    }

    /**
     * Show the form for resetting user password.
     */
    public function showResetForm($id)
    {
        $user = \App\Models\User::findOrFail($id);

        // Prevent admin from resetting their own password manually
        if ($user->id === auth()->id()) {
            abort(403, 'Anda tidak dapat mereset password Anda sendiri secara manual. Silakan gunakan fitur lupa password atau ubah password di profil.');
        }

        return view('admin.users.reset-password', compact('user'));
    }

    /**
     * Reset user password.
     */
    public function resetPassword(\App\Http\Requests\ResetUserPasswordRequest $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);

        // Prevent admin from resetting their own password manually
        if ($user->id === auth()->id()) {
            abort(403, 'Anda tidak dapat mereset password Anda sendiri secara manual. Silakan gunakan fitur lupa password atau ubah password di profil.');
        }

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        // Activity Log
        \Illuminate\Support\Facades\Log::info('Admin reset user password', [
            'admin_id' => auth()->id(),
            'target_user_id' => $user->id,
            'timestamp' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password user berhasil direset.');
    }
}
