<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nomor',
        'name',
        'email',
        'no_hp',
        'password',
        'kelas',
        'jenis_kelamin',
        'tanggal_lahir',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi: Satu Siswa bisa punya banyak riwayat tes SDQ
    public function sdqScores()
    {
        return $this->hasMany(SdqScore::class);
    }

    // Relasi: Satu Siswa bisa masuk ke dalam banyak hasil klastering di masa depan
    public function clusterResults()
    {
        return $this->hasMany(ClusterResult::class);
    }
}