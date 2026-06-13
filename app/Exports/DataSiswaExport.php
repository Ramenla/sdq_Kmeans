<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataSiswaExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles
{
    protected array $filters;
    protected array $columns;

    public function __construct(array $filters, array $columns)
    {
        $this->filters = $filters;
        $this->columns = $columns;
    }

    public function collection()
    {
        $query = Siswa::with('skorSdq');

        // Apply filters (Ruang Lingkup)
        $query->when(!empty($this->filters['kelas']), function ($q) {
            $q->where('kelas', $this->filters['kelas']);
        });
        $query->when(!empty($this->filters['jenis_kelamin']), function ($q) {
            $q->where('jenis_kelamin', $this->filters['jenis_kelamin']);
        });
        $query->when(!empty($this->filters['umur']), function ($q) {
            $q->where('umur', $this->filters['umur']);
        });
        $query->when(!empty($this->filters['kategori']), function ($q) {
            $q->whereHas('skorSdq', function ($sq) {
                $sq->where('kategori', $this->filters['kategori']);
            });
        });
        $query->when(!empty($this->filters['date']), function ($q) {
            $q->whereHas('skorSdq', function ($sq) {
                $sq->whereDate('tanggal_pemeriksaan', $this->filters['date']);
            });
        });

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        $headings = ['No'];
        
        $map = [
            'id_siswa' => 'ID Siswa',
            'nama_siswa' => 'Nama Siswa',
            'kelas' => 'Kelas',
            'jenis_kelamin' => 'Jenis Kelamin',
            'umur' => 'Umur',
            'email' => 'Email',
            'no_hp' => 'No HP',
            'tanggal_pemeriksaan' => 'Tanggal Pemeriksaan',
            'skor_e' => 'Skor E',
            'skor_c' => 'Skor C',
            'skor_h' => 'Skor H',
            'skor_p' => 'Skor P',
            'skor_diff' => 'Total Kesulitan',
            'skor_pr' => 'Skor Pr',
            'kategori' => 'Kategori',
        ];

        foreach ($this->columns as $col) {
            if (isset($map[$col])) {
                $headings[] = $map[$col];
            }
        }

        return $headings;
    }

    public function map($siswa): array
    {
        static $no = 0;
        $no++;

        $row = [$no];
        $skor = $siswa->skorSdq->first();

        foreach ($this->columns as $col) {
            switch ($col) {
                case 'id_siswa':
                    $row[] = $siswa->id;
                    break;
                case 'nama_siswa':
                    $row[] = $siswa->nama_siswa;
                    break;
                case 'kelas':
                    $row[] = $siswa->kelas ?? '-';
                    break;
                case 'jenis_kelamin':
                    $row[] = $siswa->jenis_kelamin ?? '-';
                    break;
                case 'umur':
                    $row[] = $siswa->umur ?? '-';
                    break;
                case 'email':
                    $row[] = $siswa->email ?? '-';
                    break;
                case 'no_hp':
                    $row[] = $siswa->no_hp ?? '-';
                    break;
                case 'tanggal_pemeriksaan':
                    $row[] = $skor && $skor->tanggal_pemeriksaan ? \Carbon\Carbon::parse($skor->tanggal_pemeriksaan)->isoFormat('D MMMM YYYY') : '-';
                    break;
                case 'skor_e':
                    $row[] = $skor->skor_e ?? '-';
                    break;
                case 'skor_c':
                    $row[] = $skor->skor_c ?? '-';
                    break;
                case 'skor_h':
                    $row[] = $skor->skor_h ?? '-';
                    break;
                case 'skor_p':
                    $row[] = $skor->skor_p ?? '-';
                    break;
                case 'skor_diff':
                    $row[] = $skor->skor_diff ?? '-';
                    break;
                case 'skor_pr':
                    $row[] = $skor->skor_pr ?? '-';
                    break;
                case 'kategori':
                    $row[] = $skor->kategori ?? '-';
                    break;
            }
        }

        return $row;
    }

    public function title(): string
    {
        return 'Data Siswa';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
