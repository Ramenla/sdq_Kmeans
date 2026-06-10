<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ForwardChainingService;

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
        'skor_diff',
        'kategori',
        'kategori_e',
        'kategori_c',
        'kategori_h',
        'kategori_p',
        'kategori_pr',
    ];

    /**
     * Boot model events.
     * Forward Chaining dijalankan otomatis setiap kali skor disimpan.
     */
    protected static function booted(): void
    {
        static::saving(function (SkorSdq $skor) {
            // Hitung Total Skor Kesulitan (TK = E + C + H + P)
            $skor->skor_diff = (int) $skor->skor_e
                             + (int) $skor->skor_c
                             + (int) $skor->skor_h
                             + (int) $skor->skor_p;

            // Jalankan Forward Chaining
            $fc = new ForwardChainingService();
            $labels = $fc->classify($skor);

            $skor->kategori    = $labels['kategori'];
            $skor->kategori_e  = $labels['kategori_e'];
            $skor->kategori_c  = $labels['kategori_c'];
            $skor->kategori_h  = $labels['kategori_h'];
            $skor->kategori_p  = $labels['kategori_p'];
            $skor->kategori_pr = $labels['kategori_pr'];
        });
    }

    /**
     * Accessor: Total Skor Kesulitan (computed)
     */
    public function getTotalSkorKesulitanAttribute(): int
    {
        return (int) $this->skor_e
             + (int) $this->skor_c
             + (int) $this->skor_h
             + (int) $this->skor_p;
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
    
    public function clusterResults()
    {
        return $this->hasMany(ClusterResult::class);
    }
}
