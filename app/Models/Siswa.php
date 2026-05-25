<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_siswa',
        'kelas',
        'jenis_kelamin',
        'umur',
        'email',
        'no_hp'
    ];

    public function skorSdq()
    {
        return $this->hasMany(SkorSdq::class);
    }
}
