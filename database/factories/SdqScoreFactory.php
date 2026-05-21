<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SdqScoreFactory extends Factory
{
    public function definition(): array
    {
        // Acak skor 0-10 sesuai standar kuesioner SDQ
        $e = $this->faker->numberBetween(0, 10);
        $c = $this->faker->numberBetween(0, 10);
        $h = $this->faker->numberBetween(0, 10);
        $p = $this->faker->numberBetween(0, 10);

        return [
            // user_id akan ditimpa oleh Seeder nanti
            'user_id' => User::factory(), 
            'e_score' => $e,
            'c_score' => $c,
            'h_score' => $h,
            'p_score' => $p,
            'pro_score' => $this->faker->numberBetween(0, 10),
            
            // Kalkulasi otomatis agar data valid secara matematis
            'skor_kesulitan' => $e + $c + $h + $p, 
            
            // Umur acak anak SMA
            'umur_saat_tes' => $this->faker->numberBetween(15, 18),
        ];
    }
}