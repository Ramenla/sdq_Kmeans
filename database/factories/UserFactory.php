<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Generate NIS unik dengan 6 digit angka
            'nis' => $this->faker->unique()->numerify('######'), 
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            
            // Password default untuk semua akun dummy adalah 'password'
            'password' => bcrypt('password'), 
            
            // Acak demografi siswa
            'kelas' => $this->faker->randomElement(['10 MIPA 1', '10 MIPA 2', '11 MIPA 1', '11 MIPA 2']),
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-18 years', '-15 years')->format('Y-m-d'),
            
            // Default role untuk factory adalah siswa
            'role' => 'siswa',
            'remember_token' => Str::random(10),
        ];
    }
}