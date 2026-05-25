<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. Menampilkan Halaman Form (Kita buat tampilannya nanti)
    public function showLoginGuru() { return view('auth.login-guru'); }

    // 2. Proses Login (Digunakan oleh Guru BK)
    public function processLogin(Request $request)
    {
        // Validasi input form
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Coba login (Auth::attempt akan otomatis mengecek email & mencocokkan hash password)
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            
            // Jika berhasil, buat ulang sesi untuk keamanan (mencegah session fixation)
            $request->session()->regenerate();

            // Langsung arahkan ke dashboard guru bk (intended route)
            return redirect()->intended(route('dashboard.guru'));
        }

        // Jika email/password salah, kembalikan ke halaman sebelumnya dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // 3. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login-guru');
    }
}