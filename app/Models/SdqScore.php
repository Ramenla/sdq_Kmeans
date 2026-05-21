<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SdqScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'e_score',
        'c_score',
        'h_score',
        'p_score',
        'pro_score',
        'skor_kesulitan',
        'umur_saat_tes',
    ];

    // Relasi: Skor ini milik satu User (Siswa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}