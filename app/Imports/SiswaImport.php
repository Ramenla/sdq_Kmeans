<?php

namespace App\Imports;

use App\Models\Siswa;
use App\Models\SkorSdq;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class SiswaImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        if ($rows->isEmpty()) {
            throw new \Exception('File Excel terbaca kosong atau format baris header tidak ditemukan.');
        }

        foreach ($rows as $row) {
            $noHp  = $row['no_hp'] ?? $row['nomor_hp'] ?? $row['hp'] ?? $row['no_whatsapp'] ?? null;
            
            // Lewati jika no_hp kosong karena kita butuh identifier
            if (empty($noHp)) {
                throw new \Exception('Kolom no_hp kosong atau tidak ditemukan! Kolom yang tersedia: ' . implode(', ', array_keys($row->toArray())));
            }

            $email = $row['email'] ?? null;
            
            // Format Tanggal Pemeriksaan
            $rawDate = $row['tanggal_pemeriksaan'] ?? $row['tanggal'] ?? $row['waktu'] ?? $row['waktu_pengisian'] ?? $row['timestamp'] ?? null;

            $tanggalTes = null;
            if (!empty($rawDate)) {
                try {
                    if (is_numeric($rawDate)) {
                        $tanggalTes = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawDate)->format('Y-m-d');
                    } else {
                        // Terjemahkan nama bulan Indonesia ke Inggris
                        $indoMonths = [
                            'Januari' => 'January', 'Februari' => 'February', 'Maret' => 'March',
                            'April' => 'April', 'Mei' => 'May', 'Juni' => 'June', 'Juli' => 'July',
                            'Agustus' => 'August', 'September' => 'September', 'Oktober' => 'October',
                            'November' => 'November', 'Desember' => 'December'
                        ];
                        $translatedDate = str_ireplace(array_keys($indoMonths), array_values($indoMonths), $rawDate);
                        
                        // Buang nama hari jika ada (contoh: "Senin, 16 December 2024" -> "16 December 2024")
                        if (strpos($translatedDate, ',') !== false) {
                            $parts = explode(',', $translatedDate);
                            $translatedDate = trim(end($parts));
                        }
                        
                        $tanggalTes = Carbon::parse($translatedDate)->format('Y-m-d');
                    }
                } catch (\Throwable $e) {
                    $tanggalTes = now()->format('Y-m-d');
                }
            } else {
                $tanggalTes = now()->format('Y-m-d');
            }

            // Bersihkan data umur (misal "17 Tahun" -> 17)
            $rawUmur = $row['usia'] ?? $row['umur'] ?? null;
            $umur = $rawUmur ? (int) preg_replace('/[^0-9]/', '', $rawUmur) : null;

            // Bersihkan data jenis kelamin (menjadi 'L' atau 'P')
            $rawJk = strtoupper(trim($row['jenis_kelamin'] ?? 'L'));
            $jenisKelamin = 'L';
            if (in_array($rawJk, ['P', 'PEREMPUAN', 'WOMAN', 'FEMALE', 'PR', 'WANITA'])) {
                $jenisKelamin = 'P';
            }

            // Cari atau buat Siswa berdasarkan no_hp
            $siswa = Siswa::updateOrCreate(
                ['no_hp' => $noHp],
                [
                    'nama_siswa'    => $row['nama'] ?? $row['nama_siswa'] ?? null, 
                    'email'         => $email,
                    'kelas'         => $row['kelas'] ?? '-',
                    'jenis_kelamin' => $jenisKelamin,
                    'umur'          => $umur,
                ]
            );

            // Simpan skor SDQ ke tabel skor_sdqs
            SkorSdq::create([
                'siswa_id'            => $siswa->id,
                'tanggal_pemeriksaan' => $tanggalTes,
                'skor_e'              => $row['skor_gejala_emosional_e'] ?? 0,
                'skor_c'              => $row['skor_masalah_perilaku_c'] ?? 0,
                'skor_h'              => $row['skor_masalah_hiperaktifitas_h'] ?? 0,
                'skor_p'              => $row['skor_masalah_teman_sebaya_p'] ?? 0,
                'skor_pr'             => $row['total_skor_kekuatan'] ?? 0,
                'skor_diff'           => $row['total_skor_kesulitan'] ?? 0,
            ]);
        }
    }
}
