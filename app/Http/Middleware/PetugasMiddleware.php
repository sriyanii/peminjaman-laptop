<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PetugasMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Petugas bisa mengakses route petugas
        if ($user->role === 'petugas' || $user->role === 'admin') {
            return $next($request);
        }

        abort(403, 'Unauthorized access. Petugas only.');
    }
}