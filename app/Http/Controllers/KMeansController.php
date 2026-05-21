<?php

namespace App\Http\Controllers;

use App\Models\SdqScore;
use App\Models\User;
use Illuminate\Http\Request;

class KMeansController extends Controller
{
    /**
     * Helper to build filtered query for SdqScore.
     */
    private function buildSdqScoreQuery(Request $request)
    {
        $query = SdqScore::with('user');

        // Filter: Pencarian nama / NIS siswa
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->query('search');
            $q->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', '%' . $search . '%')
                   ->orWhere('nis', 'like', '%' . $search . '%');
            });
        });

        // Filter: Kelas
        $query->when($request->filled('kelas'), function ($q) use ($request) {
            $kelas = $request->query('kelas');
            $q->whereHas('user', function ($uq) use ($kelas) {
                $uq->where('kelas', $kelas);
            });
        });

        // Filter: Jenis Kelamin
        $query->when($request->filled('jenis_kelamin'), function ($q) use ($request) {
            $jk = $request->query('jenis_kelamin');
            $q->whereHas('user', function ($uq) use ($jk) {
                $uq->where('jenis_kelamin', $jk);
            });
        });

        // Filter: Umur
        $query->when($request->filled('umur'), function ($q) use ($request) {
            $umur = $request->query('umur');
            $q->where('umur_saat_tes', $umur);
        });

        // Filter: Tanggal Screening (Exact Date Match)
        $query->when($request->filled('date'), function ($q) use ($request) {
            $date = $request->query('date');
            $q->whereDate('sdq_scores.created_at', $date);
        });

        // Sorting Kolom
        $sortBy = $request->query('sort_by');
        $order = strtolower($request->query('order')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'id_siswa') {
            $query->join('users', 'sdq_scores.user_id', '=', 'users.id')
                  ->select('sdq_scores.*')
                  ->orderBy('users.nis', $order);
        } elseif ($sortBy === 'nama_siswa') {
            $query->join('users', 'sdq_scores.user_id', '=', 'users.id')
                  ->select('sdq_scores.*')
                  ->orderBy('users.name', $order);
        } elseif ($sortBy === 'diff') {
            $query->orderBy('sdq_scores.skor_kesulitan', $order);
        } elseif (in_array($sortBy, ['e_score', 'c_score', 'h_score', 'p_score', 'pro_score'])) {
            $query->orderBy('sdq_scores.' . $sortBy, $order);
        } else {
            // Default sorting: Terbaru (created_at desc)
            $query->orderBy('sdq_scores.created_at', 'desc');
        }

        return $query;
    }

    /**
     * Tampilkan halaman Data Siswa.
     */
    public function indexDataSiswa(Request $request)
    {
        // Tangkap per_page limit (pilihan: 10, 50, 100. Default: 10)
        $perPage = $request->query('per_page', 10);
        if (!in_array($perPage, [10, 50, 100])) {
            $perPage = 10;
        }

        $dataSiswa = $this->buildSdqScoreQuery($request)->paginate($perPage);

        // Ambil data filter untuk dropdown dinamis agar selalu sinkron dengan DB riil
        $listKelas = User::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->where('role', 'siswa')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $listUmur = SdqScore::distinct()
            ->orderBy('umur_saat_tes')
            ->pluck('umur_saat_tes');

        return view('admin.data-siswa', compact('dataSiswa', 'listKelas', 'listUmur'));
    }

    /**
     * Tampilkan halaman Analisis K Terbaik.
     */
    public function indexAnalisis(Request $request)
    {
        $dataSiswa = $this->buildSdqScoreQuery($request)->paginate(5);

        // Ambil data filter untuk dropdown dinamis
        $listKelas = User::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->where('role', 'siswa')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $listUmur = SdqScore::distinct()
            ->orderBy('umur_saat_tes')
            ->pluck('umur_saat_tes');

        return view('admin.analisis-k', compact('dataSiswa', 'listKelas', 'listUmur'));
    }
}
