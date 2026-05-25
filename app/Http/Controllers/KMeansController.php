<?php

namespace App\Http\Controllers;

use App\Models\SdqScore;
use App\Models\User;
use App\Models\ClusteringHistory;
use App\Models\ClusterResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KMeansController extends Controller
{
    /**
     * ================================================================
     * BASE URL Server Python ML
     * ================================================================
     * Sesuaikan jika Python server jalan di host/port berbeda.
     */
    private string $pythonBaseUrl = 'http://127.0.0.1:5000';

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
                   ->orWhere('nomor', 'like', '%' . $search . '%');
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

        if ($sortBy === 'id_siswa' || $sortBy === 'nomor') {
            $query->join('users', 'sdq_scores.user_id', '=', 'users.id')
                  ->select('sdq_scores.*')
                  ->orderBy('users.nomor', $order);
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

    // =============================================================
    //  HELPER: Ambil data skor SDQ mentah dari DB (DENGAN FILTER)
    // =============================================================

    /**
     * Ambil data skor SDQ mentah dari database DENGAN menerapkan
     * filter demografi yang sama persis dengan tabel preview.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Support\Collection
     */
    private function getRawSdqData(Request $request)
    {
        $query = SdqScore::with('user');

        // Terapkan filter demografi yang sama persis
        $query->when($request->filled('kelas'), function ($q) use ($request) {
            $kelas = $request->query('kelas');
            $q->whereHas('user', function ($uq) use ($kelas) {
                $uq->where('kelas', $kelas);
            });
        });

        $query->when($request->filled('jenis_kelamin'), function ($q) use ($request) {
            $jk = $request->query('jenis_kelamin');
            $q->whereHas('user', function ($uq) use ($jk) {
                $uq->where('jenis_kelamin', $jk);
            });
        });

        $query->when($request->filled('umur'), function ($q) use ($request) {
            $umur = $request->query('umur');
            $q->where('umur_saat_tes', $umur);
        });

        $query->when($request->filled('date'), function ($q) use ($request) {
            $date = $request->query('date');
            $q->whereDate('sdq_scores.created_at', $date);
        });

        return $query->get();
    }

    /**
     * Tentukan kolom variabel aktif berdasarkan checkbox.
     * Hanya variabel yang di-centang user yang akan dikirim ke Python.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array  List kolom DB yang aktif (misal: ['e_score', 'h_score', 'pro_score'])
     */
    private function getActiveColumns(Request $request): array
    {
        $columns = [];

        // Jika tidak ada parameter checkbox sama sekali (akses pertama kali),
        // default: semua 5 subskala aktif (tanpa Diff)
        $hasAnyCheckbox = $request->hasAny(['cb_e', 'cb_c', 'cb_h', 'cb_p', 'cb_diff', 'cb_pr']);

        if (!$hasAnyCheckbox) {
            return ['e_score', 'c_score', 'h_score', 'p_score', 'pro_score'];
        }

        if ($request->query('cb_e') == '1')    $columns[] = 'e_score';
        if ($request->query('cb_c') == '1')    $columns[] = 'c_score';
        if ($request->query('cb_h') == '1')    $columns[] = 'h_score';
        if ($request->query('cb_p') == '1')    $columns[] = 'p_score';
        if ($request->query('cb_pr') == '1')   $columns[] = 'pro_score';
        if ($request->query('cb_diff') == '1') $columns[] = 'skor_kesulitan';

        return $columns;
    }

    /**
     * Format data SDQ mentah menjadi array siap kirim ke Python.
     * Hanya menyertakan kolom variabel yang aktif (sesuai checkbox).
     *
     * @param  \Illuminate\Support\Collection  $rawData
     * @param  array  $activeColumns  Kolom yang aktif
     * @return array
     */
    private function formatForPython($rawData, array $activeColumns)
    {
        return $rawData->map(function ($item) use ($activeColumns) {
            $row = [];
            foreach ($activeColumns as $col) {
                $row[$col] = $item->{$col};
            }
            return $row;
        })->values()->toArray();
    }

    // =============================================================
    //  HALAMAN: Data Siswa (View)
    // =============================================================

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

        // Mengambil daftar tanggal pemeriksaan yang unik untuk filter dropdown
        $daftarTanggal = SdqScore::selectRaw('DATE(created_at) as tanggal_pemeriksaan')
            ->distinct()
            ->whereNotNull('created_at')
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->pluck('tanggal_pemeriksaan');

        // Ambil semua data user siswa untuk keperluan CRUD (Edit/Hapus)
        $listSiswa = User::where('role', 'siswa')->orderBy('name')->get();

        return view('admin.data-siswa', compact('dataSiswa', 'listKelas', 'listUmur', 'daftarTanggal', 'listSiswa'));
    }

    // =============================================================
    //  HALAMAN: Analisis K Terbaik (View + Elbow Data)
    // =============================================================

    /**
     * Tampilkan halaman Analisis K Terbaik.
     */
    public function indexAnalisis(Request $request)
    {
        // Hanya query data jika user sudah klik "Load Data Siswa"
        $loaded = $request->has('load');
        $dataSiswa = $loaded
            ? $this->buildSdqScoreQuery($request)->paginate(5)
            : null;

        // Tentukan kolom aktif berdasarkan checkbox
        $activeColumns = $this->getActiveColumns($request);

        // Cek apakah Diff diaktifkan (untuk tampilan tabel saja, bukan ML)
        $showDiff = $request->query('cb_diff') == '1';

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

        $daftarTanggal = SdqScore::selectRaw('DATE(created_at) as tanggal_pemeriksaan')
            ->distinct()
            ->whereNotNull('created_at')
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->pluck('tanggal_pemeriksaan');

        return view('admin.analisis-k', compact(
            'dataSiswa', 'listKelas', 'listUmur', 'daftarTanggal',
            'loaded', 'activeColumns', 'showDiff'
        ));
    }

    // =============================================================
    //  API 1: Preprocessing (Z-Score / StandardScaler)
    // =============================================================

    /**
     * Kirim data mentah ke Python /api/preprocess
     * dan tangkap response berupa data Z-Score.
     * Filter demografi dan variabel checkbox diterapkan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPreprocessData(Request $request)
    {
        $rawData = $this->getRawSdqData($request);
        $activeColumns = $this->getActiveColumns($request);

        if ($rawData->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada data SDQ di database untuk filter ini.',
            ], 404);
        }

        if (empty($activeColumns)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada variabel yang dipilih untuk diproses.',
            ], 422);
        }

        $payload = ['data' => $this->formatForPython($rawData, $activeColumns)];

        try {
            $response = Http::timeout(30)
                ->post("{$this->pythonBaseUrl}/api/preprocess", $payload);

            if ($response->successful()) {
                $result = $response->json();

                // Gabungkan data asli (id, user_id) dengan hasil Z-Score
                $scaledData = $result['scaled_data'] ?? [];
                $merged = $rawData->values()->map(function ($item, $index) use ($scaledData, $activeColumns) {
                    $row = [
                        'id'      => $item->id,
                        'user_id' => $item->user_id,
                        'nis'     => $item->user->nis ?? '-',
                        'nama'    => $item->user->name ?? '-',
                    ];
                    foreach ($activeColumns as $col) {
                        $row[$col . '_zscore'] = $scaledData[$index][$col] ?? null;
                    }
                    return $row;
                });

                return response()->json([
                    'status'         => 'success',
                    'message'        => $result['message'] ?? 'Preprocessing berhasil.',
                    'active_columns' => $activeColumns,
                    'scaled_data'    => $merged,
                    'scaler_mean'    => $result['scaler_mean'] ?? [],
                    'scaler_std'     => $result['scaler_std'] ?? [],
                ]);
            }

            // Python server mengembalikan error
            Log::error('Python /api/preprocess error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses data di server Python.',
                'detail'  => $response->json() ?? $response->body(),
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Koneksi ke Python server gagal (preprocess)', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak dapat terhubung ke server Python. '
                           . 'Pastikan api.py berjalan di ' . $this->pythonBaseUrl,
                'detail'  => $e->getMessage(),
            ], 503);
        }
    }

    // =============================================================
    //  API 2: Elbow Method (WCSS / Inertia K=1..10)
    // =============================================================

    /**
     * Kirim data mentah ke Python /api/elbow
     * dan tangkap response berupa array Inertia.
     * Filter demografi dan variabel checkbox diterapkan.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getElbowData(Request $request)
    {
        $rawData = $this->getRawSdqData($request);
        $activeColumns = $this->getActiveColumns($request);

        if ($rawData->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada data SDQ di database untuk filter ini.',
            ], 404);
        }

        if (empty($activeColumns)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada variabel yang dipilih untuk diproses.',
            ], 422);
        }

        $payload = ['data' => $this->formatForPython($rawData, $activeColumns)];

        try {
            $response = Http::timeout(60)
                ->post("{$this->pythonBaseUrl}/api/elbow", $payload);

            if ($response->successful()) {
                $result = $response->json();

                return response()->json([
                    'status'     => 'success',
                    'message'    => $result['message'] ?? 'Elbow Method berhasil.',
                    'max_k'      => $result['max_k'] ?? 10,
                    'inertia'    => $result['inertia'] ?? [],
                    'silhouette' => $result['silhouette'] ?? [],
                ]);
            }

            Log::error('Python /api/elbow error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menghitung Elbow Method di server Python.',
                'detail'  => $response->json() ?? $response->body(),
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Koneksi ke Python server gagal (elbow)', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak dapat terhubung ke server Python. '
                           . 'Pastikan api.py berjalan di ' . $this->pythonBaseUrl,
                'detail'  => $e->getMessage(),
            ], 503);
        }
    }

    // =============================================================
    //  API 3: K-Means Clustering
    // =============================================================

    /**
     * Terima input K dari user, kirim data + jumlah_k ke
     * Python /api/kmeans, dan tangkap label klasternya.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function prosesKlasterisasi(Request $request)
    {
        // Validasi input K
        $request->validate([
            'jumlah_k' => 'required|integer|min:2|max:10',
        ]);

        $jumlahK = (int) $request->input('jumlah_k');

        $rawData = $this->getRawSdqData($request);
        $activeColumns = $this->getActiveColumns($request);

        if ($rawData->isEmpty()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada data SDQ di database.',
            ], 404);
        }

        if (empty($activeColumns)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tidak ada variabel yang dipilih untuk diproses.',
            ], 422);
        }

        if ($jumlahK > $rawData->count()) {
            return response()->json([
                'status'  => 'error',
                'message' => "Jumlah K ({$jumlahK}) melebihi jumlah data ({$rawData->count()}).",
            ], 422);
        }

        $payload = [
            'data'     => $this->formatForPython($rawData, $activeColumns),
            'jumlah_k' => $jumlahK,
        ];

        try {
            $response = Http::timeout(120)
                ->post("{$this->pythonBaseUrl}/api/kmeans", $payload);

            if ($response->successful()) {
                $result = $response->json();
                $labels = $result['labels'] ?? [];
                $pcaX   = $result['pca_x'] ?? [];
                $pcaY   = $result['pca_y'] ?? [];

                // Gabungkan label klaster + koordinat PCA + profil siswa
                $hasilKlaster = $rawData->values()->map(function ($item, $index) use ($labels, $pcaX, $pcaY) {
                    return [
                        'sdq_score_id'   => $item->id,
                        'user_id'        => $item->user_id,
                        'nis'            => $item->user->nis ?? '-',
                        'nama'           => $item->user->name ?? '-',
                        'kelas'          => $item->user->kelas ?? '-',
                        'jenis_kelamin'  => $item->user->jenis_kelamin ?? '-',
                        'e_score'        => $item->e_score,
                        'c_score'        => $item->c_score,
                        'h_score'        => $item->h_score,
                        'p_score'        => $item->p_score,
                        'skor_kesulitan' => $item->skor_kesulitan,
                        'pro_score'      => $item->pro_score,
                        'cluster_number' => $labels[$index] ?? null,
                        'pca_x'          => $pcaX[$index] ?? 0,
                        'pca_y'          => $pcaY[$index] ?? 0,
                    ];
                });

                // Hitung jumlah per klaster untuk card sebaran
                $clusterCounts = [];
                foreach ($labels as $label) {
                    $key = $label;
                    $clusterCounts[$key] = ($clusterCounts[$key] ?? 0) + 1;
                }
                ksort($clusterCounts);

                // Simpan riwayat klasterisasi ke database
                $namaKlastering = $request->input('nama_klastering');
                if (empty($namaKlastering)) {
                    $namaKlastering = 'Klasterisasi K=' . $jumlahK . ' — ' . now()->format('d M Y H:i');
                }

                $history = ClusteringHistory::create([
                    'nama_klastering' => $namaKlastering,
                    'jumlah_k'        => $jumlahK,
                    'filter_kelas'    => $request->input('filter_kelas'),
                    'filter_jk'       => $request->input('filter_jk'),
                ]);

                // Simpan detail hasil per siswa
                foreach ($hasilKlaster as $item) {
                    ClusterResult::create([
                        'clustering_history_id' => $history->id,
                        'user_id'               => $item['user_id'],
                        'sdq_score_id'          => $item['sdq_score_id'],
                        'cluster_number'        => $item['cluster_number'],
                    ]);
                }

                return response()->json([
                    'status'                 => 'success',
                    'message'                => $result['message'] ?? 'Klasterisasi berhasil.',
                    'jumlah_klaster'         => $jumlahK,
                    'clustering_history_id'  => $history->id,
                    'centroids'              => $result['centroids'] ?? [],
                    'inertia'                => $result['inertia'] ?? null,
                    'n_iter'                 => $result['n_iter'] ?? null,
                    'cluster_counts'         => $clusterCounts,
                    'total_data'             => $rawData->count(),
                    'data_siswa'             => $hasilKlaster,
                    'pca_explained_variance' => $result['pca_explained_variance'] ?? [],
                    'cluster_profiles'       => $result['cluster_profiles'] ?? [],
                ]);
            }

            Log::error('Python /api/kmeans error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal memproses K-Means di server Python.',
                'detail'  => $response->json() ?? $response->body(),
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Koneksi ke Python server gagal (kmeans)', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
