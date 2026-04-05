<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * TAMPILAN FORM REGISTER
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * PROSES REGISTER
     */
    public function register(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Buat user baru (default role = 'user')
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role untuk user baru
        ]);

        // 3. Login otomatis setelah register
        Auth::login($user);

        // 4. Redirect ke dashboard sesuai role
        return redirect()->route('user.dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang.');
    }
}