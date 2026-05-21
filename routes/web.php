<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KMeansController;

// --- RUTE HALAMAN UTAMA (LANDING PAGE) ---
Route::get('/', function () {
    return view('welcome'); 
})->name('landing');

// --- RUTE UNTUK TAMPILAN HALAMAN (GET) ---
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::get('/login-siswa', [AuthController::class, 'showLoginSiswa'])->name('login.siswa');
Route::get('/login-guru', [AuthController::class, 'showLoginGuru'])->name('login.guru');

// --- RUTE UNTUK MEMPROSES DATA (POST) ---
Route::post('/register', [AuthController::class, 'processRegister']);
Route::post('/login', [AuthController::class, 'processLogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTE DASHBOARD SEMENTARA (Nanti kita rapikan) ---
Route::get('/dashboard-siswa', function () { return "Selamat datang di Dashboard Siswa"; })->name('dashboard.siswa');

Route::get('/dashboard-guru', [KMeansController::class, 'indexDataSiswa'])->name('dashboard.guru');

Route::get('/admin/analisis-k', [KMeansController::class, 'indexAnalisis'])->name('admin.analisis');

Route::get('/admin/klasterisasi', function () {
    return view('admin.klasterisasi');
})->name('admin.klasterisasi');

Route::get('/admin/laporan-hasil', function () {
    return view('admin.laporan-hasil');
})->name('admin.laporan');