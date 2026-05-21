<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClusteringHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_klastering',
        'jumlah_k',
        'filter_kelas',
        'filter_jk',
    ];

    // Relasi: Satu riwayat (batch) klastering memiliki banyak detail hasil siswa
    public function results()
    {
        return $this->hasMany(ClusterResult::class, 'clustering_history_id');
    }
}