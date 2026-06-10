<?php

namespace App\Services;

use App\Models\SkorSdq;

class ForwardChainingService
{
    // =================================================================
    //  ATURAN PRODUKSI UTAMA — Total Skor Kesulitan (TK = E + C + H + P)
    //  Standar: SDQ Self-Completed (4-17 tahun)
    // =================================================================

    /**
     * RULE 1: IF TK >= 0  AND TK <= 15 THEN 'Normal'
     * RULE 2: IF TK >= 16 AND TK <= 19 THEN 'Borderline'
     * RULE 3: IF TK >= 20 AND TK <= 40 THEN 'Abnormal'
     */
    public function classifyTotalKesulitan(int $tk): string
    {
        if ($tk >= 0 && $tk <= 15) {
            return 'Normal';
        }
        if ($tk >= 16 && $tk <= 19) {
            return 'Borderline';
        }
        // $tk >= 20
        return 'Abnormal';
    }

    // =================================================================
    //  ATURAN PRODUKSI SUB-INDIKATOR
    // =================================================================

    /**
     * Emosional (E): Normal(0-5), Borderline(6), Abnormal(7-10)
     */
    public function classifyEmosional(int $skor): string
    {
        if ($skor >= 0 && $skor <= 5) {
            return 'Normal';
        }
        if ($skor === 6) {
            return 'Borderline';
        }
        return 'Abnormal';
    }

    /**
     * Perilaku / Conduct (C): Normal(0-3), Borderline(4), Abnormal(5-10)
     */
    public function classifyPerilaku(int $skor): string
    {
        if ($skor >= 0 && $skor <= 3) {
            return 'Normal';
        }
        if ($skor === 4) {
            return 'Borderline';
        }
        return 'Abnormal';
    }

    /**
     * Hiperaktivitas (H): Normal(0-5), Borderline(6), Abnormal(7-10)
     */
    public function classifyHiperaktivitas(int $skor): string
    {
        if ($skor >= 0 && $skor <= 5) {
            return 'Normal';
        }
        if ($skor === 6) {
            return 'Borderline';
        }
        return 'Abnormal';
    }

    /**
     * Teman Sebaya / Peer (P): Normal(0-3), Borderline(4-5), Abnormal(6-10)
     */
    public function classifyTemanSebaya(int $skor): string
    {
        if ($skor >= 0 && $skor <= 3) {
            return 'Normal';
        }
        if ($skor >= 4 && $skor <= 5) {
            return 'Borderline';
        }
        return 'Abnormal';
    }

    /**
     * Prososial (Pr): Normal(6-10), Borderline(5), Abnormal(0-4)
     * CATATAN: Logika TERBALIK — skor rendah = buruk
     */
    public function classifyPrososial(int $skor): string
    {
        if ($skor >= 6 && $skor <= 10) {
            return 'Normal';
        }
        if ($skor === 5) {
            return 'Borderline';
        }
        return 'Abnormal';
    }

    // =================================================================
    //  METODE UTAMA: Jalankan Forward Chaining pada satu record
    // =================================================================

    /**
     * Jalankan semua aturan produksi pada satu record SkorSdq.
     * Mengembalikan array label tanpa menyimpan ke DB.
     *
     * @param  SkorSdq  $skor
     * @return array
     */
    public function classify(SkorSdq $skor): array
    {
        $totalKesulitan = (int) $skor->skor_e
                        + (int) $skor->skor_c
                        + (int) $skor->skor_h
                        + (int) $skor->skor_p;

        return [
            'kategori'    => $this->classifyTotalKesulitan($totalKesulitan),
            'kategori_e'  => $this->classifyEmosional((int) $skor->skor_e),
            'kategori_c'  => $this->classifyPerilaku((int) $skor->skor_c),
            'kategori_h'  => $this->classifyHiperaktivitas((int) $skor->skor_h),
            'kategori_p'  => $this->classifyTemanSebaya((int) $skor->skor_p),
            'kategori_pr' => $this->classifyPrososial((int) $skor->skor_pr),
        ];
    }

    /**
     * Jalankan Forward Chaining dan simpan hasilnya ke database.
     *
     * @param  SkorSdq  $skor
     * @return SkorSdq
     */
    public function classifyAndSave(SkorSdq $skor): SkorSdq
    {
        $labels = $this->classify($skor);

        $skor->kategori    = $labels['kategori'];
        $skor->kategori_e  = $labels['kategori_e'];
        $skor->kategori_c  = $labels['kategori_c'];
        $skor->kategori_h  = $labels['kategori_h'];
        $skor->kategori_p  = $labels['kategori_p'];
        $skor->kategori_pr = $labels['kategori_pr'];

        // Gunakan saveQuietly untuk menghindari infinite loop dari model event
        $skor->saveQuietly();

        return $skor;
    }

    /**
     * Re-klasifikasi seluruh data di tabel skor_sdqs.
     * Berguna untuk mengisi data lama yang belum memiliki label.
     *
     * @return int  Jumlah record yang di-update
     */
    public function classifyAll(): int
    {
        $records = SkorSdq::all();
        $count = 0;

        foreach ($records as $skor) {
            $this->classifyAndSave($skor);
            $count++;
        }

        return $count;
    }
}
