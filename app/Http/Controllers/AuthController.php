<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. Menampilkan Halaman Form (Kita buat tampilannya nanti)
    public function showRegister() { return view('auth.register'); }
    public function showLoginSiswa() { return view('auth.login-siswa'); }
    public function showLoginGuru() { return view('auth.login-guru'); }

    // 2. Proses Registrasi (Khusus Siswa)
    public function processRegister(Request $request)
    {
        // Validasi input: Nama, Email (harus unik), dan Password
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed', // Harus ada input 'password_confirmation' di form nanti
        ]);

        // Simpan ke database MySQL
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Password diacak (hashing)
            'role' => 'siswa', // Otomatis diset sebagai siswa
        ]);

        return redirect('/login-siswa')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // 3. Proses Login (Digunakan oleh Siswa & Guru BK)
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

            // Cek Role (Hak Akses) untuk menentukan arah Dashboard
            if (Auth::user()->role === 'siswa') {
                return redirect()->route('dashboard.siswa');
            } elseif (Auth::user()->role === 'guru_bk') {
                return redirect()->route('dashboard.guru');
            }
        }

        // Jika email/password salah, kembalikan ke halaman sebelumnya dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    // 4. Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login-siswa');
    }
}