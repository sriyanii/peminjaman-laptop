<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * TAMPILAN FORM LOGIN
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * PROSES LOGIN
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        // 2. Cek remember me
        $remember = $request->has('remember');

        // 3. Coba login
        if (Auth::attempt($credentials, $remember)) {
            // Regenerate session untuk keamanan
            $request->session()->regenerate();
            
            // 4. Redirect ke dashboard sesuai role
            $user = Auth::user();
            
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard')
                        ->with('success', 'Login berhasil! Selamat datang Admin.');
                case 'petugas':
                    return redirect()->route('petugas.dashboard')
                        ->with('success', 'Login berhasil! Selamat datang Petugas.');
                case 'user':
                    return redirect()->route('user.dashboard')
                        ->with('success', 'Login berhasil! Selamat datang.');
                default:
                    // Jika role tidak dikenali, redirect ke home
                    return redirect()->route('home')
                        ->with('success', 'Login berhasil!');
            }
        }

        // 5. Jika gagal, kembali ke login dengan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * PROSES LOGOUT
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')
            ->with('success', 'Logout berhasil!');
    }
}