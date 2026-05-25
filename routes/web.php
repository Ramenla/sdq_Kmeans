<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KMeansController;
use App\Http\Controllers\SiswaController;

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

// --- RUTE CRUD DATA SISWA ---
Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
Route::post('/data-siswa/import', [SiswaController::class, 'import'])->name('siswa.import');

Route::get('/admin/analisis-k', [KMeansController::class, 'indexAnalisis'])->name('admin.analisis');

Route::get('/admin/klasterisasi', function () {
    return view('admin.klasterisasi');
})->name('admin.klasterisasi');

Route::get('/admin/laporan-hasil', function () {
    return view('admin.laporan-hasil');
})->name('admin.laporan');

// --- RUTE API UNTUK KOMUNIKASI DENGAN SERVER PYTHON ML ---
Route::get('/api/preprocess', [KMeansController::class, 'getPreprocessData'])->name('api.preprocess');
Route::get('/api/elbow', [KMeansController::class, 'getElbowData'])->name('api.elbow');
Route::post('/api/klasterisasi', [KMeansController::class, 'prosesKlasterisasi'])->name('api.klasterisasi');