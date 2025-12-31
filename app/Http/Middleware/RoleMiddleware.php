<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class RoleMiddleware
{
    
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Cek apakah user sudah login
         if (!Auth::check()) {  
            return redirect('/login');
        }

        // Kalau role tidak sesuai
        if (Auth::user()->role !== $role) {
            abort(403, 'ANDA TIDAK PUNYA AKSES');
        }

        return $next($request);
    }

}
