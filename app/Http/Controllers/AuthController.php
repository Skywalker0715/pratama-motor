<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
   
    public function showLogin()
    {
         if (Auth::check()) {
        return Auth::user()->role === 'admin'
            ? redirect('/admin/dashboard')
            : redirect('/user/dashboard');
    }

       return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $message = 'Login berhasil! Selamat datang kembali.';

            return Auth::user()->role === 'admin'
                ? redirect('/admin/dashboard')->with('success', $message)
                : redirect('/user/dashboard')->with('success', $message);
        }

        return back()->with('error', 'Email atau password salah');
    }

   
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        try {
            User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => 'user', // default kasir
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Maaf, registrasi gagal. Silakan coba lagi.');
        }

        return redirect('/login')->with('success', 'Registrasi berhasil, silakan login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user && $user->role !== 'admin') {
            return back()->withErrors(['email' => 'Password reset is only available for administrators.']);
        }

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('success', 'Link reset password telah dikirim ke email Anda.')
            : back()->with('error', 'Kami tidak dapat menemukan pengguna dengan alamat email tersebut.');
    }

   
    public function showResetPassword($token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request()->email
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6|confirmed',
            'token'    => 'required'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect('/login')->with('success', 'Password berhasil direset, silakan login kembali.')
            : back()->with('error', 'Token reset password tidak valid atau telah kedaluwarsa.');
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Berhasil logout');
    }
}
