<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class JemaatExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected bool $isTemplate;

    public function __construct(bool $isTemplate = false)
    {
        $this->isTemplate = $isTemplate;
    }

    public function collection(): Collection
    {
        // Jika dipanggil untuk template, berikan 2 baris data contoh
        if ($this->isTemplate) {
            return collect([
                (object) [
                    'nama_asli'     => 'Albertus Suryadi Chandra',
                    'status'        => 'Anggota',
                    'alias_1'       => 'Adi',
                    'alias_2'       => '',
                    'wilayah'       => 'Gateway',
                    'alamat'        => 'Jl. Mawar No. 47',
                    'tempat_lahir'  => 'Surabaya',
                    'tanggal_lahir' => '1993-08-10',
                    'telepon'       => '081234567890',
                    'keluarga'      => 'Kel. Albertus Adi',
                ],
                (object) [
                    'nama_asli'     => 'Anabel Sarah Maheswari',
                    'status'        => 'Anggota',
                    'alias_1'       => 'Sarah',
                    'alias_2'       => '',
                    'wilayah'       => 'Gateway',
                    'alamat'        => 'Jl. Pepelegi Indah Blok A-6 Waru',
                    'tempat_lahir'  => 'Jakarta',
                    'tanggal_lahir' => '2011-10-18',
                    'telepon'       => '089876543210',
                    'keluarga'      => 'Kel. Johanes',
                ],
            ]);
        }

        // Jika dipanggil untuk Export Data Jemaat dari Database
        return \App\Models\Jemaat::with('cabang')->where('isDelete', 0)->get();
    }

    public function headings(): array
    {
        return [
            'Nama Asli',
            'Status',
            'Alias 1',
            'Alias 2',
            'Wilayah Ibadah',
            'Alamat',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Telepon',
            'Keluarga'
        ];
    }

    public function map($jemaat): array
    {
        if ($this->isTemplate) {
            return [
                $jemaat->nama_asli,
                $jemaat->status,
                $jemaat->alias_1,
                $jemaat->alias_2,
                $jemaat->wilayah,
                $jemaat->alamat,
                $jemaat->tempat_lahir,
                $jemaat->tanggal_lahir,
                $jemaat->telepon,
                $jemaat->keluarga,
            ];
        }

        return [
            $jemaat->nama_asli,
            $jemaat->status ?? 'Anggota',
            $jemaat->alias_1,
            $jemaat->alias_2,
            $jemaat->cabang->nama_cabang ?? $jemaat->wilayah_ibadah ?? 'Darmo',
            $jemaat->alamat,
            $jemaat->tempat_lahir,
            $jemaat->tanggal_lahir ? date('Y-m-d', strtotime($jemaat->tanggal_lahir)) : '',
            $jemaat->telepon,
            $jemaat->keluarga,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E40AF'] // Header Biru Navy
                ],
            ],
        ];
    }
}