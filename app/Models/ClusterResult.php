<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClusterResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'clustering_history_id',
        'user_id',
        'sdq_score_id',
        'cluster_number',
    ];

    // Relasi Balik ke Master Riwayat Klastering
    public function clusteringHistory()
    {
        return $this->belongsTo(ClusteringHistory::class, 'clustering_history_id');
    }

    // Relasi Balik ke Siswa
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi Balik ke Skor SDQ yang dipakai saat klastering tersebut
    public function sdqScore()
    {
        return $this->belongsTo(SdqScore::class);
    }
}