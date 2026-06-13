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
     * Simpan data siswa baru ke database.
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
     * Update data siswa yang sudah ada.
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
     * Hapus data siswa dari database.
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('dashboard.guru')->with('success', 'Data siswa berhasil dihapus!');
    }

    /**
     * Hapus beberapa data siswa sekaligus.
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
     * Import data dari CSV/Excel.
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
     * Export data siswa ke Excel dengan filter.
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
