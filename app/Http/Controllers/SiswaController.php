<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            'nomor'         => 'required|unique:users,nomor',
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|email|unique:users,email',
            'kelas'         => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
            'password'      => 'required|string|min:6',
        ], [
            'nomor.required'         => 'Nomor wajib diisi.',
            'nomor.unique'           => 'Nomor sudah terdaftar di sistem.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah terdaftar di sistem.',
            'kelas.required'         => 'Kelas wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin harus L atau P.',
            'password.required'      => 'Password wajib diisi.',
            'password.min'           => 'Password minimal 6 karakter.',
        ]);

        $user = User::create([
            'nomor'         => $request->nomor,
            'name'          => $request->name,
            'email'         => $request->email,
            'kelas'         => $request->kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
            'password'      => Hash::make($request->password),
            'role'          => 'siswa',
        ]);

        // Buat riwayat skor kosong agar siswa tampil di tabel
        \App\Models\SdqScore::create([
            'user_id'        => $user->id,
            'e_score'        => 0,
            'c_score'        => 0,
            'h_score'        => 0,
            'p_score'        => 0,
            'pro_score'      => 0,
            'skor_kesulitan' => 0,
            'umur_saat_tes'  => $request->tanggal_lahir ? \Carbon\Carbon::parse($request->tanggal_lahir)->age : 0,
        ]);

        return redirect()->route('dashboard.guru')->with('success', 'Data siswa berhasil ditambahkan!');
    }

    /**
     * Update data siswa yang sudah ada.
     */
    public function update(Request $request, User $siswa)
    {
        $request->validate([
            'nomor'         => 'required|unique:users,nomor,' . $siswa->id,
            'name'          => 'nullable|string|max:255',
            'email'         => 'nullable|email|unique:users,email,' . $siswa->id,
            'kelas'         => 'required|string|max:50',
            'jenis_kelamin' => 'required|in:L,P',
            'tanggal_lahir' => 'nullable|date',
        ], [
            'nomor.required'         => 'Nomor wajib diisi.',
            'nomor.unique'           => 'Nomor sudah terdaftar di sistem.',
            'email.email'            => 'Format email tidak valid.',
            'email.unique'           => 'Email sudah terdaftar di sistem.',
            'kelas.required'         => 'Kelas wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin harus L atau P.',
        ]);

        $siswa->update([
            'nomor'         => $request->nomor,
            'name'          => $request->name,
            'email'         => $request->email,
            'kelas'         => $request->kelas,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tanggal_lahir' => $request->tanggal_lahir,
        ]);

        return redirect()->route('dashboard.guru')->with('success', 'Data siswa berhasil diperbarui!');
    }

    /**
     * Hapus data siswa dari database.
     */
    public function destroy(User $siswa)
    {
        $siswa->delete();

        return redirect()->route('dashboard.guru')->with('success', 'Data siswa berhasil dihapus!');
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
}
