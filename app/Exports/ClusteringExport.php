<?php

namespace App\Exports;

use App\Models\ClusteringHistory;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClusteringExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected ClusteringHistory $history;

    public function __construct(ClusteringHistory $history)
    {
        $this->history = $history;
    }

    public function collection()
    {
        return $this->history->results()
            ->with(['skorSdq.siswa'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Siswa',
            'Nama Siswa',
            'Kelas',
            'Jenis Kelamin',
            'Umur',
            'Email',
            'No HP',
            'Skor E',
            'Skor C',
            'Skor H',
            'Skor P',
            'Total Kesulitan',
            'Skor Pr',
            'Kategori',
            'Hasil Klaster',
        ];
    }

    public function map($result): array
    {
        static $no = 0;
        $no++;

        $skor  = $result->skorSdq;
        $siswa = $skor?->siswa;

        return [
            $no,
            $siswa?->id ?? '-',
            $siswa?->nama_siswa ?? '-',
            $siswa?->kelas ?? '-',
            $siswa?->jenis_kelamin ?? '-',
            $siswa?->umur ?? '-',
            $siswa?->email ?? '-',
            $siswa?->no_hp ?? '-',
            $skor?->skor_e ?? '-',
            $skor?->skor_c ?? '-',
            $skor?->skor_h ?? '-',
            $skor?->skor_p ?? '-',
            $skor?->skor_diff ?? '-',
            $skor?->skor_pr ?? '-',
            $skor?->kategori ?? '-',
            'Klaster ' . ($result->cluster_number + 1),
        ];
    }

    public function title(): string
    {
        return 'Hasil Klasterisasi';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
