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

    /**
     * Scope for filtering and sorting students based on request parameters.
     */
    public function scopeFilterAndSort($query, \Illuminate\Http\Request $request)
    {
        $query->with('skorSdq');

        // Filter: Pencarian nama, id, email, atau no hp
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->query('search');
            $q->where(function ($uq) use ($search) {
                $uq->where('nama_siswa', 'like', '%' . $search . '%')
                   ->orWhere('id', 'like', '%' . $search . '%')
                   ->orWhere('email', 'like', '%' . $search . '%')
                   ->orWhere('no_hp', 'like', '%' . $search . '%');
            });
        });

        // Filter: Kelas
        $query->when($request->filled('kelas'), function ($q) use ($request) {
            $q->where('kelas', $request->query('kelas'));
        });

        // Filter: Jenis Kelamin
        $query->when($request->filled('jenis_kelamin'), function ($q) use ($request) {
            $q->where('jenis_kelamin', $request->query('jenis_kelamin'));
        });

        // Filter: Umur
        $query->when($request->filled('umur'), function ($q) use ($request) {
            $q->where('umur', $request->query('umur'));
        });

        // Filter: Tanggal Screening (Exact Date Match)
        $query->when($request->filled('date'), function ($q) use ($request) {
            $q->whereHas('skorSdq', function ($sq) use ($request) {
                $sq->whereDate('tanggal_pemeriksaan', $request->query('date'));
            });
        });

        // Filter: Kategori
        $query->when($request->filled('kategori'), function ($q) use ($request) {
            $q->whereHas('skorSdq', function ($sq) use ($request) {
                $sq->where('kategori', $request->query('kategori'));
            });
        });

        // Sorting Kolom
        $sortBy = $request->query('sort_by');
        $order = strtolower($request->query('order')) === 'desc' ? 'desc' : 'asc';

        if ($sortBy === 'id_siswa' || $sortBy === 'nomor') {
            $query->orderBy('siswas.id', $order);
        } elseif ($sortBy === 'nama_siswa') {
            $query->orderBy('nama_siswa', $order);
        } elseif (in_array($sortBy, ['skor_diff', 'skor_e', 'skor_c', 'skor_h', 'skor_p', 'skor_pr'])) {
            // Kita join untuk sorting, tapi pastikan select('siswas.*') agar hasil paginasi tetap model Siswa
            $query->join('skor_sdqs', 'siswas.id', '=', 'skor_sdqs.siswa_id')
                  ->select('siswas.*')
                  ->orderBy('skor_sdqs.' . $sortBy, $order);
        } else {
            // Default sorting: Terbaru (created_at desc)
            $query->orderBy('created_at', 'desc');
        }

        // Tie-breaker: Pastikan urutan selalu deterministik
        $query->orderBy('siswas.id', 'desc');

        return $query;
    }
}
