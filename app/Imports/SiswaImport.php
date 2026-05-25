<?php

namespace App\Imports;

use App\Models\User;
use App\Models\SdqScore;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Jika "No" (nomor) kosong, lewati baris ini
            if (empty($row['no']) && empty($row['nomor'])) {
                continue;
            }

            $nomor = $row['no'] ?? $row['nomor'] ?? null;
            $email = $row['email'] ?? null;
            $noHp  = $row['no_hp'] ?? null;
            
            // Format Tanggal Pemeriksaan
            $tanggalTes = null;
            if (!empty($row['tanggal_pemeriksaan'])) {
                try {
                    $tanggalTes = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['tanggal_pemeriksaan'])->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $tanggalTes = Carbon::parse($row['tanggal_pemeriksaan'])->format('Y-m-d H:i:s');
                }
            } else {
                $tanggalTes = now();
            }

            // Cari atau buat User berdasarkan nomor
            $user = User::firstOrCreate(
                ['nomor' => $nomor],
                [
                    'name'          => $row['nama'] ?? $row['nama_siswa'] ?? null, 
                    'email'         => $email,
                    'no_hp'         => $noHp,
                    'kelas'         => $row['kelas'] ?? '-',
                    'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? 'L'),
                    'password'      => Hash::make('password123'), // Default password
                    'role'          => 'siswa',
                ]
            );

            // Jika user sudah ada tapi data mau diperbarui, bisa diubah di sini:
            if (!$user->wasRecentlyCreated) {
                $user->update([
                    'email'         => $email ?? $user->email,
                    'no_hp'         => $noHp ?? $user->no_hp,
                    'kelas'         => $row['kelas'] ?? $user->kelas,
                    'jenis_kelamin' => strtoupper($row['jenis_kelamin'] ?? $user->jenis_kelamin),
                ]);
            }

            // Simpan Skor SDQ
            SdqScore::create([
                'user_id'        => $user->id,
                'e_score'        => $row['skor_gejala_emosional_e'] ?? 0,
                'c_score'        => $row['skor_masalah_perilaku_c'] ?? 0,
                'h_score'        => $row['skor_masalah_hiperaktifitas_h'] ?? 0,
                'p_score'        => $row['skor_masalah_teman_sebaya_p'] ?? 0,
                'pro_score'      => $row['total_skor_kekuatan'] ?? 0,
                'skor_kesulitan' => $row['total_skor_kesulitan'] ?? 0,
                'umur_saat_tes'  => $row['usia'] ?? $row['umur'] ?? 0,
                'created_at'     => $tanggalTes,
                'updated_at'     => $tanggalTes,
            ]);
        }
    }
}
