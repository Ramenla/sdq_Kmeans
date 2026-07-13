<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan Halaman Form Login.
     * Fungsi ini bertugas memanggil tampilan (view) form login untuk guru.
     */
    public function showLoginGuru() { return view('auth.login-guru'); }

    /**
     * Memproses Data Login.
     * Fungsi ini dipanggil ketika guru menekan tombol "Login".
     * Menerima kiriman email dan password, memvalidasinya, lalu mengecek ke database.
     * Jika cocok, pengguna diizinkan masuk ke halaman dashboard.
     *
     * @param  \Illuminate\Http\Request  $request  Berisi data inputan form dari pengguna.
     */
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

            // Langsung arahkan ke dashboard (intended route)
            return redirect()->intended(route('admin.dashboard'));
        }

        // Jika email/password salah, kembalikan ke halaman sebelumnya dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Memproses Logout (Keluar).
     * Fungsi ini dipanggil ketika guru menekan tombol logout.
     * Akan menghapus sesi yang aktif dan mengembalikan pengguna ke halaman login.
     *
     * @param  \Illuminate\Http\Request  $request  Data sesi pengguna saat ini.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login-guru');
    }
}