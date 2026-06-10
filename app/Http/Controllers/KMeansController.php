<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\SkorSdq;
use App\Models\ClusteringHistory;
use App\Models\ClusterResult;
use App\Services\ForwardChainingService;
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
     * Helper to build filtered query for Siswa.
     */
    private function buildSiswaQuery(Request $request)
    {
        $query = Siswa::with('skorSdq');

        // Filter: Pencarian nama, id, email, atau no hp
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->query('search');
            $q->where(function ($uq) use ($search) {
                $uq->where('nama_siswa', 'like', '%' . $search . '%')
                   ->orWhere('id', 'like', '%' . $search . '%')
                   ->orWhere('email', 'like', '%' . $search . '%')
                   ->orWhere('no_hp', 'like', '%' . $search . '%');
            });
        });

        // Filter: Kelas
        $query->when($request->filled('kelas'), function ($q) use ($request) {
            $q->where('kelas', $request->query('kelas'));
        });

        // Filter: Jenis Kelamin
        $query->when($request->filled('jenis_kelamin'), function ($q) use ($request) {
            $q->where('jenis_kelamin', $request->query('jenis_kelamin'));
        });

        // Filter: Umur
        $query->when($request->filled('umur'), function ($q) use ($request) {
            $q->where('umur', $request->query('umur'));
        });

        // Filter: Tanggal Screening (Exact Date Match)
        $query->when($request->filled('date'), function ($q) use ($request) {
            $q->whereHas('skorSdq', function ($sq) use ($request) {
                $sq->whereDate('tanggal_pemeriksaan', $request->query('date'));
            });
        });

        // Filter: Kategori
        $query->when($request->filled('kategori'), function ($q) use ($request) {
            $q->whereHas('skorSdq', function ($sq) use ($request) {
                $sq->where('kategori', $request->query('kategori'));
            });
        });

        // Sorting Kolom
        $sortBy = $request->query('sort_by');
        $order = strtolower($request->query('order')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'id_siswa' || $sortBy === 'nomor') {
            $query->orderBy('no_hp', $order);
        } elseif ($sortBy === 'nama_siswa') {
            $query->orderBy('nama_siswa', $order);
        } elseif (in_array($sortBy, ['skor_diff', 'skor_e', 'skor_c', 'skor_h', 'skor_p', 'skor_pr'])) {
            // Kita join untuk sorting, tapi pastikan select('siswas.*') agar hasil paginasi tetap model Siswa
            $query->join('skor_sdqs', 'siswas.id', '=', 'skor_sdqs.siswa_id')
                  ->select('siswas.*')
                  ->orderBy('skor_sdqs.' . $sortBy, $order);
        } else {
            // Default sorting: Terbaru (created_at desc)
            $query->orderBy('created_at', 'desc');
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
        // Menggunakan SkorSdq untuk ML agar bisa per riwayat tes
        $query = SkorSdq::with('siswa');

        // Terapkan filter demografi melalui relasi
        $query->when($request->filled('kelas'), function ($q) use ($request) {
            $q->whereHas('siswa', function ($sq) use ($request) {
                $sq->where('kelas', $request->query('kelas'));
            });
        });

        $query->when($request->filled('jenis_kelamin'), function ($q) use ($request) {
            $q->whereHas('siswa', function ($sq) use ($request) {
                $sq->where('jenis_kelamin', $request->query('jenis_kelamin'));
            });
        });

        $query->when($request->filled('umur'), function ($q) use ($request) {
            $q->whereHas('siswa', function ($sq) use ($request) {
                $sq->where('umur', $request->query('umur'));
            });
        });

        $query->when($request->filled('date'), function ($q) use ($request) {
            $q->whereDate('tanggal_pemeriksaan', $request->query('date'));
        });

        $query->when($request->filled('kategori'), function ($q) use ($request) {
            $q->where('kategori', $request->query('kategori'));
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
            return ['skor_e', 'skor_c', 'skor_h', 'skor_p', 'skor_pr'];
        }

        // Jika ada parameter, cek satu-satu (Checkbox akan bernilai '1' jika dicentang)
        if ($request->query('cb_e') == '1')    $columns[] = 'skor_e';
        if ($request->query('cb_c') == '1')    $columns[] = 'skor_c';
        if ($request->query('cb_h') == '1')    $columns[] = 'skor_h';
        if ($request->query('cb_p') == '1')    $columns[] = 'skor_p';
        if ($request->query('cb_pr') == '1')   $columns[] = 'skor_pr';
        if ($request->query('cb_diff') == '1') $columns[] = 'skor_diff';

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

        $dataSiswa = $this->buildSiswaQuery($request)->paginate($perPage);

        // Ambil data filter untuk dropdown dinamis agar selalu sinkron dengan DB riil
        $listKelas = Siswa::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $listUmur = Siswa::distinct()
            ->orderBy('umur')
            ->pluck('umur');

        // Mengambil daftar tanggal pemeriksaan yang unik untuk filter dropdown
        $daftarTanggal = SkorSdq::selectRaw('DATE(tanggal_pemeriksaan) as tanggal_pemeriksaan')
            ->distinct()
            ->whereNotNull('tanggal_pemeriksaan')
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->pluck('tanggal_pemeriksaan');

        // Ambil semua data user siswa untuk keperluan CRUD (Edit/Hapus)
        $listSiswa = Siswa::orderBy('nama_siswa')->get();

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
            ? $this->buildSiswaQuery($request)->paginate(5)
            : null;

        // Tentukan kolom aktif berdasarkan checkbox
        $activeColumns = $this->getActiveColumns($request);

        // Cek apakah Diff diaktifkan (untuk tampilan tabel saja, bukan ML)
        $showDiff = $request->query('cb_diff') == '1';

        // Ambil data filter untuk dropdown dinamis
        $listKelas = Siswa::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $listUmur = Siswa::distinct()
            ->orderBy('umur')
            ->pluck('umur');

        $daftarTanggal = SkorSdq::selectRaw('DATE(tanggal_pemeriksaan) as tanggal_pemeriksaan')
            ->distinct()
            ->whereNotNull('tanggal_pemeriksaan')
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->pluck('tanggal_pemeriksaan');

        return view('admin.analisis-k', compact(
            'dataSiswa', 'listKelas', 'listUmur', 'daftarTanggal',
            'loaded', 'activeColumns', 'showDiff'
        ));
    }

    /**
     * Tampilkan halaman Klasterisasi K-Means.
     */
    public function indexKlasterisasi(Request $request)
    {
        $loaded = $request->has('load');
        $dataSiswa = $loaded
            ? $this->buildSiswaQuery($request)->paginate(10)
            : null;

        $activeColumns = $this->getActiveColumns($request);
        $showDiff = $request->query('cb_diff') == '1';

        $listKelas = Siswa::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $listUmur = Siswa::distinct()
            ->orderBy('umur')
            ->pluck('umur');

        $daftarTanggal = SkorSdq::selectRaw('DATE(tanggal_pemeriksaan) as tanggal_pemeriksaan')
            ->distinct()
            ->whereNotNull('tanggal_pemeriksaan')
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->pluck('tanggal_pemeriksaan');

        return view('admin.klasterisasi', compact(
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
                        'id'      => $item->siswa_id,
                        'no_hp'   => $item->siswa->no_hp ?? '-',
                        'nama'    => $item->siswa->nama_siswa ?? '-',
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
                        'skor_sdq_id'    => $item->id,
                        'siswa_id'       => $item->siswa_id,
                        'no_hp'          => $item->siswa->no_hp ?? '-',
                        'nama'           => $item->siswa->nama_siswa ?? '-',
                        'kelas'          => $item->siswa->kelas ?? '-',
                        'jenis_kelamin'  => $item->siswa->jenis_kelamin ?? '-',
                        'skor_e'         => $item->skor_e,
                        'skor_c'         => $item->skor_c,
                        'skor_h'         => $item->skor_h,
                        'skor_p'         => $item->skor_p,
                        'skor_diff'      => $item->skor_diff,
                        'skor_pr'        => $item->skor_pr,
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

                // Simpan detail hasil per skor
                foreach ($hasilKlaster as $item) {
                    ClusterResult::create([
                        'clustering_history_id' => $history->id,
                        'skor_sdq_id'           => $item['skor_sdq_id'],
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

    // =============================================================
    //  FORWARD CHAINING: Re-klasifikasi seluruh data
    // =============================================================

    /**
     * Jalankan ulang Forward Chaining pada semua data skor SDQ.
     * Digunakan untuk mengisi kolom kategori pada data lama
     * yang belum memiliki label klasifikasi.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recalculateKategori()
    {
        try {
            $fc = new ForwardChainingService();
            $count = $fc->classifyAll();

            return response()->json([
                'status'  => 'success',
                'message' => "Forward Chaining berhasil dijalankan pada {$count} data.",
                'total_updated' => $count,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menjalankan Forward Chaining', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menjalankan Forward Chaining: ' . $e->getMessage(),
            ], 500);
        }
    }
}
