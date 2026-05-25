<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClusterResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'clustering_history_id',
        'skor_sdq_id',
        'cluster_number',
    ];

    // Relasi Balik ke Master Riwayat Klastering
    public function clusteringHistory()
    {
        return $this->belongsTo(ClusteringHistory::class, 'clustering_history_id');
    }

    // Relasi Balik ke Skor SDQ
    public function skorSdq()
    {
        return $this->belongsTo(SkorSdq::class, 'skor_sdq_id');
    }
}