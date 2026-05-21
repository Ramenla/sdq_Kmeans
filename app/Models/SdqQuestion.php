<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SdqQuestion extends Model
{
    use HasFactory;

    // Sesuaikan nama-nama di dalam array ini dengan kolom yang ada di file migration Anda
    protected $fillable = [
        'pertanyaan', // Teks soal kuesioner
        'kategori',   // Kategori gejala (Misal: 'E', 'C', 'H', 'P', 'Pr')
        'jenis',      
    ];
}