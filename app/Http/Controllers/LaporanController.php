<?php

namespace App\Http\Controllers;

use App\Models\ClusteringHistory;
use App\Models\ClusterResult;
use App\Exports\ClusteringExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    /**
     * Menampilkan Halaman Riwayat Laporan Klasterisasi.
     * Fungsi ini mengambil daftar riwayat proses K-Means yang pernah dilakukan sebelumnya.
     * Tujuannya agar guru bisa melihat kembali hasil kelompok siswa di masa lalu.
     *
     * @param  \Illuminate\Http\Request  $request  Data pencarian dan pengurutan (sorting) dari pengguna.
     */
    public function indexLaporan(Request $request)
    {
        $query = ClusteringHistory::withCount('results');

        // Pencarian nama laporan
        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('nama_klastering', 'like', '%' . $request->query('search') . '%');
        });

        // Sorting
        $order = $request->query('order', 'desc');
        $query->orderBy('created_at', $order === 'asc' ? 'asc' : 'desc');

        $laporanList = $query->paginate(10);

        return view('admin.laporan-hasil', compact('laporanList'));
    }

    /**
     * Mengunduh Laporan Hasil Klasterisasi ke Excel.
     * Fungsi ini mengubah data riwayat pengelompokan yang dipilih menjadi file Excel,
     * sehingga guru bisa mencetaknya atau membagikannya dengan mudah.
     *
     * @param  \App\Models\ClusteringHistory  $history  Data riwayat spesifik yang akan diunduh.
     */
    public function exportExcel(ClusteringHistory $history)
    {
        $filename = 'Laporan_' . str_replace(' ', '_', $history->nama_klastering) . '.xlsx';
        return Excel::download(new ClusteringExport($history), $filename);
    }

    /**
     * Menghapus Satu Riwayat Laporan Klasterisasi.
     * Jika ada laporan yang sudah tidak dibutuhkan, fungsi ini akan menghapus riwayat
     * beserta seluruh detail anggota kelompok di dalamnya.
     *
     * @param  \App\Models\ClusteringHistory  $history  Data riwayat spesifik yang akan dihapus.
     */
    public function destroyHistory(ClusteringHistory $history)
    {
        $nama = $history->nama_klastering;
        $history->results()->delete();
        $history->delete();

        return redirect()->route('admin.laporan')
            ->with('success', "Laporan \"{$nama}\" berhasil dihapus.");
    }

    /**
     * Menghapus Beberapa Riwayat Laporan Sekaligus.
     * Fungsi ini berguna jika guru ingin membersihkan (menghapus) banyak laporan
     * sekaligus hanya dengan mencentangnya di tabel.
     *
     * @param  \Illuminate\Http\Request  $request  Daftar ID riwayat laporan yang dicentang.
     */
    public function bulkDestroyHistory(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.laporan')
                ->with('error', 'Tidak ada laporan yang dipilih.');
        }

        // Hapus semua detail hasil terlebih dahulu
        ClusterResult::whereIn('clustering_history_id', $ids)->delete();
        $count = ClusteringHistory::whereIn('id', $ids)->delete();

        return redirect()->route('admin.laporan')
            ->with('success', "{$count} laporan berhasil dihapus.");
    }
}
