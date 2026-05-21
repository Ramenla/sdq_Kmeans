<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SdqScore;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun Khusus Guru BK (Sesuai role baru Anda)
        User::create([
            'nis' => null,
            'name' => 'Fauzan (Guru BK)',
            'email' => 'admin@sekolah.com',
            'password' => bcrypt('admin123'), // Password untuk login nanti
            'role' => 'guru_bk', 
        ]);

        // 2. Generate 100 Data Siswa dan 1 riwayat SDQ untuk setiap siswanya
        User::factory(100)->create()->each(function ($siswa) {
            SdqScore::factory()->create([
                'user_id' => $siswa->id, 
            ]);
        });
    }
}