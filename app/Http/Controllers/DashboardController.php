<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\SkorSdq;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan Halaman Utama (Dashboard).
     * Fungsi ini menyiapkan semua data yang dibutuhkan di halaman dashboard,
     * seperti total siswa, jumlah masing-masing kategori mental, daftar siswa prioritas, 
     * serta data untuk grafik (Donut Chart dan Bar Chart).
     *
     * @param  \Illuminate\Http\Request  $request  Data filter dari pengguna (misal: filter jenis kelamin).
     */
    public function index(Request $request)
    {
        $jkMental = $request->input('jk_mental', 'Semua');
        $jkGejala = $request->input('jk_gejala', 'Semua');

        // Global stats for Summary Cards
        $totalSiswa = Siswa::count();
        $globalNormalCount = SkorSdq::where('kategori', 'Normal')->count();
        $globalBorderlineCount = SkorSdq::where('kategori', 'Borderline')->count();
        $globalAbnormalCount = SkorSdq::where('kategori', 'Abnormal')->count();

        // Get top 5 abnormal priority list
        $prioritas = SkorSdq::with('siswa')
            ->orderByRaw("FIELD(kategori, 'Abnormal', 'Borderline', 'Normal')")
            ->orderBy('skor_diff', 'desc')
            ->take(5)
            ->get()
            ->map(function ($skor) {
                // Determine gejala tertinggi (which subscores are Abnormal)
                $gejala = [];
                if ($skor->kategori_e == 'Abnormal') $gejala[] = 'Emosional';
                if ($skor->kategori_c == 'Abnormal') $gejala[] = 'Perilaku';
                if ($skor->kategori_h == 'Abnormal') $gejala[] = 'Hiperaktivitas';
                if ($skor->kategori_p == 'Abnormal') $gejala[] = 'Teman Sebaya';
                if ($skor->kategori_pr == 'Abnormal') $gejala[] = 'Prososial';
                
                $skor->gejala_tertinggi = count($gejala) > 0 ? implode(', ', $gejala) : '-';
                return $skor;
            });

        // 1. Chart Query for Distribusi Status Mental (Donut Chart)
        $queryMental = SkorSdq::query();
        if ($jkMental === 'L' || $jkMental === 'P') {
            $queryMental->whereHas('siswa', function($q) use ($jkMental) {
                $q->where('jenis_kelamin', $jkMental);
            });
        }

        $chartTotalSiswa = $queryMental->count();
        $normalCount = (clone $queryMental)->where('kategori', 'Normal')->count();
        $borderlineCount = (clone $queryMental)->where('kategori', 'Borderline')->count();
        $abnormalCount = (clone $queryMental)->where('kategori', 'Abnormal')->count();

        // 2. Chart Query for Rata-Rata Gejala Tertinggi (Bar Chart)
        $queryGejala = SkorSdq::query();
        if ($jkGejala === 'L' || $jkGejala === 'P') {
            $queryGejala->whereHas('siswa', function($q) use ($jkGejala) {
                $q->where('jenis_kelamin', $jkGejala);
            });
        }

        $avgE = round((clone $queryGejala)->avg('skor_e') ?? 0, 2);
        $avgC = round((clone $queryGejala)->avg('skor_c') ?? 0, 2);
        $avgH = round((clone $queryGejala)->avg('skor_h') ?? 0, 2);
        $avgP = round((clone $queryGejala)->avg('skor_p') ?? 0, 2);
        $avgPr = round((clone $queryGejala)->avg('skor_pr') ?? 0, 2);

        return view('admin.dashboard', compact(
            'totalSiswa', 'globalNormalCount', 'globalBorderlineCount', 'globalAbnormalCount', 
            'prioritas', 'jkMental', 'jkGejala', 'chartTotalSiswa', 'normalCount', 'borderlineCount', 'abnormalCount',
            'avgE', 'avgC', 'avgH', 'avgP', 'avgPr'
        ));
    }
}
