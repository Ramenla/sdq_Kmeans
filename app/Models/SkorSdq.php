<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkorSdq extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'tanggal_pemeriksaan',
        'skor_e',
        'skor_c',
        'skor_h',
        'skor_p',
        'skor_pr',
        'skor_diff'
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    
    public function clusterResults()
    {
        return $this->hasMany(ClusterResult::class);
    }
}
