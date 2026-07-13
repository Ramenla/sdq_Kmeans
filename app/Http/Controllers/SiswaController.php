<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\SkorSdq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\SiswaImport;

class SiswaController extends Controller
{
    /**
     * Menampilkan Halaman Data Siswa.
     * Fungsi ini bertugas mengambil data siswa dari database (termasuk pencarian dan filter),
     * lalu menampilkannya di halaman web dalam bentuk tabel yang rapi (dengan paginasi).
     *
     * @param  \Illuminate\Http\Request  $request  Data request seperti halaman ke berapa, pencarian, dan filter.
     */
    public function index(Request $request)
    {
        // Tangkap per_page limit (pilihan: 10, 50, 100. Default: 10)
        $perPage = $request->query('per_page', 10);
        if (!in_array($perPage, [10, 50, 100])) {
            $perPage = 10;
        }

        $dataSiswa = Siswa::filterAndSort($request)->paginate($perPage);

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

    /**
     * Menyimpan Data Siswa Baru ke Database.
     * Fungsi ini dipanggil ketika guru menekan tombol "Simpan" pada form tambah siswa.
     * Ia akan memeriksa (validasi) apakah data sudah lengkap dan benar,
     * lalu menyimpannya ke tabel `siswas` dan membuat data kosong/awal di tabel `skor_sdqs`.
     *
     * @param  \Illuminate\Http\Request  $request  Data form yang dikirimkan oleh pengguna.
     */
    public function store(Request $request)
    {
        $request->validate([
            'no_hp'         => 'nullable|string|max:20',
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|email|unique:siswas,email',
            'kelas'         => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'umur'          => 'nullable|integer',
            'tanggal_pemeriksaan' => 'nullable|date',
            'skor_e'        => 'nullable|integer|min:0|max:10',
            'skor_c'        => 'nullable|integer|min:0|max:10',
            'skor_h'        => 'nullable|integer|min:0|max:10',
            'skor_p'        => 'nullable|integer|min:0|max:10',
            'skor_pr'       => 'nullable|integer|min:0|max:10',
        ], [
            // 'no_hp.unique' dihilangkan
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah terdaftar di sistem.',
            'kelas.required'         => 'Kelas wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin harus L atau P.',
        ]);

        $siswa = Siswa::create([
            'no_hp'               => $request->no_hp,
            'nama_siswa'          => $request->name,
            'email'               => $request->email,
            'kelas'               => $request->kelas,
            'jenis_kelamin'       => $request->jenis_kelamin,
            'umur'                => $request->umur,
        ]);

        SkorSdq::create([
            'siswa_id'            => $siswa->id,
            'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan ?? now()->format('Y-m-d'),
            'skor_e'              => $request->skor_e ?? 0,
            'skor_c'              => $request->skor_c ?? 0,
            'skor_h'              => $request->skor_h ?? 0,
            'skor_p'              => $request->skor_p ?? 0,
            'skor_pr'             => $request->skor_pr ?? 0,
            'skor_diff'           => 0, // Akan dikalkulasi otomatis oleh Model SkorSdq
        ]);

        return redirect()->route('dashboard.guru')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    /**
     * Memperbarui (Update) Data Siswa yang Sudah Ada.
     * Fungsi ini dijalankan ketika guru mengedit data siswa dan menekan "Simpan Perubahan".
     * Sama seperti store, fungsi ini akan mengecek kecocokan data,
     * lalu menyimpan perubahan terbaru ke tabel `siswas` dan `skor_sdqs`.
     *
     * @param  \Illuminate\Http\Request  $request  Data perubahan terbaru dari form.
     * @param  \App\Models\Siswa  $siswa  Data siswa spesifik yang sedang diedit.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'no_hp'         => 'nullable|string|max:20',
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|email|unique:siswas,email,' . $siswa->id,
            'kelas'         => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'umur'          => 'nullable|integer',
            'tanggal_pemeriksaan' => 'nullable|date',
            'skor_e'        => 'nullable|integer|min:0|max:10',
            'skor_c'        => 'nullable|integer|min:0|max:10',
            'skor_h'        => 'nullable|integer|min:0|max:10',
            'skor_p'        => 'nullable|integer|min:0|max:10',
            'skor_pr'       => 'nullable|integer|min:0|max:10',
        ], [
            // 'no_hp.unique' dihilangkan
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah terdaftar di sistem.',
            'kelas.required'         => 'Kelas wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin harus L atau P.',
        ]);

        $siswa->update([
            'no_hp'               => $request->no_hp,
            'nama_siswa'          => $request->name,
            'email'               => $request->email,
            'kelas'               => $request->kelas,
            'jenis_kelamin'       => $request->jenis_kelamin,
            'umur'                => $request->umur,
        ]);

        $skorData = [
            'tanggal_pemeriksaan' => $request->tanggal_pemeriksaan ?? now()->format('Y-m-d'),
            'skor_e'              => $request->skor_e ?? 0,
            'skor_c'              => $request->skor_c ?? 0,
            'skor_h'              => $request->skor_h ?? 0,
            'skor_p'              => $request->skor_p ?? 0,
            'skor_pr'             => $request->skor_pr ?? 0,
        ];

        $skor = $siswa->skorSdq()->latest('tanggal_pemeriksaan')->first();
        if ($skor) {
            $skor->update($skorData);
        } else {
            $skorData['siswa_id'] = $siswa->id;
            SkorSdq::create($skorData);
        }

        return redirect()->route('dashboard.guru')->with('success', 'Data siswa berhasil diperbarui!');
    }

    /**
     * Menghapus Satu Data Siswa dari Database.
     * Fungsi ini dipanggil saat tombol "Hapus" ditekan pada satu baris siswa.
     * Seluruh data yang berkaitan dengan siswa tersebut akan terhapus.
     *
     * @param  \App\Models\Siswa  $siswa  Data siswa spesifik yang akan dihapus.
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('dashboard.guru')->with('success', 'Data siswa berhasil dihapus!');
    }

    /**
     * Menghapus Beberapa Data Siswa Sekaligus (Bulk Delete).
     * Fungsi ini dijalankan jika guru mencentang beberapa siswa sekaligus lalu menekan "Hapus Terpilih".
     *
     * @param  \Illuminate\Http\Request  $request  Berisi daftar ID siswa yang dicentang.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:siswas,id'
        ]);

        Siswa::whereIn('id', $request->ids)->delete();

        return redirect()->route('dashboard.guru')->with('success', count($request->ids) . ' data siswa berhasil dihapus!');
    }

    /**
     * Mengimpor Data Siswa dari File CSV/Excel.
     * Fungsi ini mempermudah guru jika ingin memasukkan banyak data siswa
     * sekaligus dari file Excel tanpa harus mengetik satu per satu.
     *
     * @param  \Illuminate\Http\Request  $request  File Excel yang diunggah.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file_import' => 'required|mimes:csv,xls,xlsx|max:10240',
        ], [
            'file_import.required' => 'Silakan pilih file untuk diimport.',
            'file_import.mimes'    => 'Format file harus berupa CSV, XLS, atau XLSX.',
            'file_import.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        try {
            Excel::import(new SiswaImport, $request->file('file_import'));
            return redirect()->route('dashboard.guru')->with('success', 'Data siswa berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.guru')->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Mengunduh (Ekspor) Data Siswa ke File Excel.
     * Fungsi ini menghasilkan file `.xlsx` berisi data siswa (beserta hasil kuesionernya)
     * yang bisa didownload oleh guru untuk keperluan laporan *offline*.
     *
     * @param  \Illuminate\Http\Request  $request  Filter yang dipilih sebelum mengunduh data.
     */
    public function export(Request $request)
    {
        $filters = $request->only(['kelas', 'jenis_kelamin', 'umur', 'kategori', 'date']);
        $columns = $request->input('columns', []);
        
        if (empty($columns)) {
            return redirect()->back()->with('error', 'Silakan pilih setidaknya satu variabel untuk diunduh.');
        }

        return Excel::download(new \App\Exports\DataSiswaExport($filters, $columns), 'Data_Siswa_' . date('Ymd_His') . '.xlsx');
    }
}
