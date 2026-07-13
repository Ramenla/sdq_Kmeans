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
            'name' => 'Guru BK',
            'email' => 'admin@email.com',
            'password' => bcrypt('admin123'), // Password untuk login 
        ]);
    }
}