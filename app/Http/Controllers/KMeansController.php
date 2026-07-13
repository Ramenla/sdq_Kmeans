<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\SkorSdq;
use App\Models\ClusteringHistory;
use App\Models\ClusterResult;
use App\Services\ForwardChainingService;
use App\Services\PythonMlService;
use App\Exports\ClusteringExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class KMeansController extends Controller
{
    protected PythonMlService $pythonMlService;

    public function __construct(PythonMlService $pythonMlService)
    {
        $this->pythonMlService = $pythonMlService;
    }



    // HELPER: Ambil data skor SDQ mentah dari DB (DENGAN FILTER)


    /**
     * Mengambil Data Kuesioner Mentah dari Database.
     * Fungsi ini bertugas mencari data skor kuesioner SDQ milik siswa, 
     * lengkap dengan filter (pencarian nama, jenis kelamin, kelas, umur, dll)
     * agar data yang diproses sesuai dengan keinginan guru.
     *
     * @param  \Illuminate\Http\Request  $request  Data filter pencarian dari form web.
     * @return \Illuminate\Support\Collection  Kumpulan data skor siswa dari database.
     */
    private function getRawSdqData(Request $request)
    {
        // Menggunakan SkorSdq untuk ML agar bisa per riwayat tes
        $query = SkorSdq::with('siswa')
            ->join('siswas', 'skor_sdqs.siswa_id', '=', 'siswas.id')
            ->select('skor_sdqs.*'); // Pastikan hanya ambil kolom skor

        // Filter: Pencarian nama, id, email, atau no hp
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->query('search');
            $q->where(function ($uq) use ($search) {
                $uq->where('siswas.nama_siswa', 'like', '%' . $search . '%')
                   ->orWhere('siswas.id', 'like', '%' . $search . '%')
                   ->orWhere('siswas.email', 'like', '%' . $search . '%')
                   ->orWhere('siswas.no_hp', 'like', '%' . $search . '%');
            });
        });

        // Filter demografi melalui join siswas
        $query->when($request->filled('kelas'), function ($q) use ($request) {
            $q->where('siswas.kelas', $request->query('kelas'));
        });

        $query->when($request->filled('jenis_kelamin'), function ($q) use ($request) {
            $q->where('siswas.jenis_kelamin', $request->query('jenis_kelamin'));
        });

        $query->when($request->filled('umur'), function ($q) use ($request) {
            $q->where('siswas.umur', $request->query('umur'));
        });

        // Filter data SkorSdq
        $query->when($request->filled('date'), function ($q) use ($request) {
            $q->whereDate('skor_sdqs.tanggal_pemeriksaan', $request->query('date'));
        });

        $query->when($request->filled('kategori'), function ($q) use ($request) {
            $q->where('skor_sdqs.kategori', $request->query('kategori'));
        });

        // Sorting Kolom agar sesuai tabel preview
        $sortBy = $request->query('sort_by');
        $order = strtolower($request->query('order')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'id_siswa' || $sortBy === 'nomor') {
            $query->orderBy('siswas.id', $order);
        } elseif ($sortBy === 'nama_siswa') {
            $query->orderBy('siswas.nama_siswa', $order);
        } elseif (in_array($sortBy, ['skor_diff', 'skor_e', 'skor_c', 'skor_h', 'skor_p', 'skor_pr'])) {
            $query->orderBy('skor_sdqs.' . $sortBy, $order);
        } else {
            // Default sorting untuk siswa adalah created_at desc (dari buildSiswaQuery)
            // Jadi kita samakan dengan siswas.created_at
            $query->orderBy('siswas.created_at', 'desc');
        }

        // Tie-breaker: Pastikan urutan selalu deterministik, persis dengan tabel preview
        $query->orderBy('siswas.id', 'desc');

        return $query->get();
    }

    /**
     * Menentukan Variabel SDQ yang Akan Dihitung.
     * Kuesioner SDQ punya 5 aspek (Emosi, Perilaku, dll) ditambah 1 Total Kesulitan (Diff).
     * Fungsi ini mengecek kotak centang (checkbox) mana saja yang diaktifkan oleh guru,
     * sehingga hanya variabel tersebut yang akan dikirim ke mesin Python.
     *
     * @param  \Illuminate\Http\Request  $request  Status kotak centang dari web.
     * @return array  Daftar nama kolom yang aktif (misal: ['skor_e', 'skor_h']).
     */
    private function getActiveColumns(Request $request): array
    {
        $columns = [];

        // Jika ini adalah load pertama (tidak ada parameter 'load'),
        // default: semua 5 subskala aktif (tanpa Diff)
        if (!$request->has('load')) {
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
     * Memformat Data Siswa untuk Dikirim ke Mesin Pintar (Python).
     * Mesin Python butuh data dalam bentuk daftar rapi (array) agar bisa diproses.
     * Fungsi ini menyaring data siswa dan hanya memasukkan kolom skor yang aktif saja.
     *
     * @param  \Illuminate\Support\Collection  $rawData  Data asli dari database.
     * @param  array  $activeColumns  Kolom yang aktif/dicentang.
     * @return array  Data rapi yang siap dikirim.
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
    //  HALAMAN: Analisis K Terbaik (View + Elbow Data)
    // =============================================================

    /**
     * Menampilkan Halaman "Analisis K Terbaik" (Elbow Method).
     * Fungsi ini menyiapkan data dan pengaturan awal saat guru membuka halaman
     * untuk mencari tahu jumlah kelompok (K) yang paling pas.
     */
    public function indexAnalisis(Request $request)
    {
        // Hanya query data jika user sudah klik "Load Data Siswa"
        $loaded = $request->has('load');
        $dataSiswa = $loaded
            ? Siswa::filterAndSort($request)->paginate(5)
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
     * Menampilkan Halaman "Klasterisasi K-Means".
     * Fungsi ini menyiapkan halaman tempat guru bisa memasukkan jumlah kelompok (K)
     * dan mengeksekusi proses pembagian kelompok siswa (K-Means Clustering).
     */
    public function indexKlasterisasi(Request $request)
    {
        $loaded = $request->has('load');
        $dataSiswa = $loaded 
            ? Siswa::filterAndSort($request)->paginate(10)
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
     * Mengirim Data ke Mesin Python untuk Tahap Awal (Preprocessing).
     * Sebelum dibagi kelompok, data harus "distandarkan" (Z-Score) agar perhitungannya adil.
     * Fungsi ini mengirim data mentah ke Python, lalu Python mengembalikannya
     * dalam bentuk angka yang sudah terstandar.
     *
     * @param  \Illuminate\Http\Request  $request  Data filter dan kotak centang.
     * @return \Illuminate\Http\JsonResponse  Data hasil standarisasi dari Python.
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

        $response = $this->pythonMlService->preprocess($payload);

        if (!$response['success']) {
            return response()->json([
                'status'  => 'error',
                'message' => $response['message'] ?? 'Gagal memproses data di server Python.',
                'detail'  => $response['detail'] ?? null,
            ], $response['status'] ?? 500);
        }

        $result = $response['data'];

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

    // =============================================================
    //  API 2: Elbow Method (WCSS / Inertia K=1..10)
    // =============================================================

    /**
     * Meminta Rekomendasi Jumlah Kelompok ke Mesin Python (Elbow Method).
     * Fungsi ini mengirim data ke Python, lalu Python akan menguji perhitungan dari 1 kelompok
     * sampai 10 kelompok, dan membuat grafik untuk menunjukkan jumlah kelompok yang paling ideal.
     *
     * @param  \Illuminate\Http\Request  $request  Data filter dan kotak centang.
     * @return \Illuminate\Http\JsonResponse  Data koordinat untuk membuat grafik Elbow.
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

        $response = $this->pythonMlService->elbow($payload);

        if (!$response['success']) {
            return response()->json([
                'status'  => 'error',
                'message' => $response['message'] ?? 'Gagal menghitung Elbow Method di server Python.',
                'detail'  => $response['detail'] ?? null,
            ], $response['status'] ?? 500);
        }

        $result = $response['data'];

        return response()->json([
            'status'     => 'success',
            'message'    => $result['message'] ?? 'Elbow Method berhasil.',
            'max_k'      => $result['max_k'] ?? 10,
            'inertia'    => $result['inertia'] ?? [],
            'silhouette' => $result['silhouette'] ?? [],
        ]);
    }

    /**
     * Menyimpan Hasil Klasterisasi (Kelompok Siswa) ke Database.
     * Setelah melihat hasil pembagian kelompok, jika guru menekan tombol "Simpan Laporan",
     * fungsi ini akan memindahkan hasil sementara tersebut ke dalam database secara permanen.
     */
    public function simpanKlasterisasi()
    {
        $pending = session('pending_clustering');

        if (!$pending) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada data klasterisasi yang perlu disimpan.'
            ], 400);
        }

        try {
            $history = ClusteringHistory::create([
                'nama_klastering' => $pending['nama_klastering'],
                'jumlah_k'        => $pending['jumlah_k'],
                'filter_kelas'    => $pending['filter_kelas'],
                'filter_jk'       => $pending['filter_jk'],
            ]);

            foreach ($pending['hasil_klaster'] as $item) {
                ClusterResult::create([
                    'clustering_history_id' => $history->id,
                    'skor_sdq_id'           => $item['skor_sdq_id'],
                    'cluster_number'        => $item['cluster_number'],
                ]);
            }

            // Hapus dari session setelah berhasil disimpan
            session()->forget('pending_clustering');

            return response()->json([
                'status' => 'success',
                'message' => 'Data klasterisasi berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan klasterisasi', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan ke database: ' . $e->getMessage()
            ], 500);
        }
    }

    // =============================================================
    //  API 3: K-Means Clustering
    // =============================================================

    /**
     * Mengeksekusi Algoritma K-Means melalui Mesin Python.
     * Ini adalah inti dari aplikasinya!
     * Fungsi ini mengirimkan jumlah kelompok (K) yang diminta guru beserta datanya ke Python.
     * Python akan menghitung dan mengelompokkan siswa, lalu mengembalikan hasilnya
     * (siapa masuk kelompok mana) beserta koordinat untuk grafik penyebaran (PCA).
     *
     * @param  \Illuminate\Http\Request  $request  Jumlah kelompok (K) yang dimasukkan guru.
     * @return \Illuminate\Http\JsonResponse  Hasil pengelompokan lengkap dari Python.
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

        $response = $this->pythonMlService->kmeans($payload);

        if (!$response['success']) {
            return response()->json([
                'status'  => 'error',
                'message' => $response['message'] ?? 'Gagal memproses K-Means di server Python.',
                'detail'  => $response['detail'] ?? null,
            ], $response['status'] ?? 500);
        }

        $result = $response['data'];
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

        $namaKlastering = $request->input('nama_klastering');
        if (empty($namaKlastering)) {
            $namaKlastering = 'Klasterisasi K=' . $jumlahK . ' — ' . now()->format('d M Y H:i');
        }

        // Simpan sementara di session (web route) untuk nanti disimpan jika diklik
        session()->put('pending_clustering', [
            'nama_klastering' => $namaKlastering,
            'jumlah_k'        => $jumlahK,
            'filter_kelas'    => $request->input('kelas'),
            'filter_jk'       => $request->input('jenis_kelamin'),
            'hasil_klaster'   => $hasilKlaster,
        ]);

        return response()->json([
            'status'                 => 'success',
            'message'                => $result['message'] ?? 'Klasterisasi berhasil. Silakan klik Simpan untuk menyimpan ke database.',
            'jumlah_klaster'         => $jumlahK,
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

    // =============================================================
    //  FORWARD CHAINING: Re-klasifikasi seluruh data
    // =============================================================

    /**
     * Mengecek dan Memperbarui Kategori Manual (Forward Chaining).
     * Fungsi ini berguna jika ada data siswa lama yang kategori mentalnya belum terisi,
     * sistem akan membaca aturan manual SDQ dan mengisi kategorinya secara otomatis.
     *
     * @return \Illuminate\Http\JsonResponse  Jumlah data yang berhasil diperbarui.
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
