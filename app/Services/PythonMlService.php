<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PythonMlService
{
    /**
     * BASE URL Server Python ML
     */
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('PYTHON_API_URL', 'http://127.0.0.1:5000');
    }

    /**
     * Helper untuk memproses request ke Python ML Server
     */
    private function sendRequest(string $endpoint, array $payload, int $timeout = 60)
    {
        try {
            $response = Http::timeout($timeout)->post("{$this->baseUrl}{$endpoint}", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data'    => $response->json(),
                ];
            }

            Log::error("Python ML Server Error ({$endpoint})", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'status'  => $response->status(),
                'message' => 'Gagal memproses data di server Python.',
                'detail'  => $response->json() ?? $response->body(),
            ];

        } catch (\Exception $e) {
            Log::error("Koneksi ke Python server gagal ({$endpoint})", [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'status'  => 503,
                'message' => 'Tidak dapat terhubung ke server Python. Pastikan api.py berjalan di ' . $this->baseUrl,
                'detail'  => $e->getMessage(),
            ];
        }
    }

    /**
     * Memanggil API Preprocess (Z-Score)
     */
    public function preprocess(array $payload)
    {
        return $this->sendRequest('/api/preprocess', $payload, 30);
    }

    /**
     * Memanggil API Elbow Method
     */
    public function elbow(array $payload)
    {
        return $this->sendRequest('/api/elbow', $payload, 60);
    }

    /**
     * Memanggil API K-Means Clustering
     */
    public function kmeans(array $payload)
    {
        return $this->sendRequest('/api/kmeans', $payload, 120);
    }
}
