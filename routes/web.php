<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KMeansController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KuesionerController;

// --- RUTE HALAMAN UTAMA (LANDING PAGE) ---
Route::get('/', function () {
    return view('welcome'); 
})->name('landing');

// --- RUTE KUESIONER PUBLIK (GUEST MODE) ---
Route::get('/kuesioner', [KuesionerController::class, 'showForm'])->name('kuesioner.form');
Route::post('/kuesioner', [KuesionerController::class, 'processForm'])->name('kuesioner.submit');
Route::get('/kuesioner/hasil/{id}', [KuesionerController::class, 'showHasil'])->name('kuesioner.hasil');

// --- RUTE UNTUK TAMPILAN HALAMAN (GET) ---
Route::get('/login', [AuthController::class, 'showLoginGuru'])->name('login');
Route::get('/login-guru', [AuthController::class, 'showLoginGuru'])->name('login.guru');

// --- RUTE UNTUK MEMPROSES DATA (POST) ---
Route::post('/login', [AuthController::class, 'processLogin']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- RUTE DASHBOARD ---

Route::get('/dashboard-guru', [KMeansController::class, 'indexDataSiswa'])->name('dashboard.guru');

// --- RUTE CRUD DATA SISWA ---
Route::delete('/siswa/bulk-delete', [SiswaController::class, 'bulkDestroy'])->name('siswa.bulkDestroy');
Route::post('/siswa', [SiswaController::class, 'store'])->name('siswa.store');
Route::put('/siswa/{siswa}', [SiswaController::class, 'update'])->name('siswa.update');
Route::delete('/siswa/{siswa}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
Route::post('/data-siswa/import', [SiswaController::class, 'import'])->name('siswa.import');
Route::get('/data-siswa/export', [SiswaController::class, 'export'])->name('siswa.export');

Route::get('/admin/analisis-k', [KMeansController::class, 'indexAnalisis'])->name('admin.analisis');

Route::get('/admin/klasterisasi', [KMeansController::class, 'indexKlasterisasi'])->name('admin.klasterisasi');

Route::get('/admin/laporan-hasil', [KMeansController::class, 'indexLaporan'])->name('admin.laporan');
Route::get('/admin/laporan-hasil/{history}/export-excel', [KMeansController::class, 'exportExcel'])->name('admin.laporan.export');
Route::delete('/admin/laporan-hasil/bulk-delete', [KMeansController::class, 'bulkDestroyHistory'])->name('admin.laporan.bulkDestroy');
Route::delete('/admin/laporan-hasil/{history}', [KMeansController::class, 'destroyHistory'])->name('admin.laporan.destroy');

// --- RUTE API UNTUK KOMUNIKASI DENGAN SERVER PYTHON ML ---
Route::get('/api/preprocess', [KMeansController::class, 'getPreprocessData'])->name('api.preprocess');
Route::get('/api/elbow', [KMeansController::class, 'getElbowData'])->name('api.elbow');
Route::post('/api/klasterisasi', [KMeansController::class, 'prosesKlasterisasi'])->name('api.klasterisasi');
Route::post('/api/simpan-klasterisasi', [KMeansController::class, 'simpanKlasterisasi'])->name('api.simpanKlasterisasi');

// --- RUTE API FORWARD CHAINING ---
Route::post('/api/recalculate-kategori', [KMeansController::class, 'recalculateKategori'])->name('api.recalculateKategori');