<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JenisIbadah;

class JenisIbadahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisIbadahs = [
            [
                'nama_ibadah' => 'Umum Pagi',
                'deskripsi'   => 'Ibadah Raya Umum sesi pagi',
                'isActive'    => 1,
                'isDelete'    => 0,
            ],
            [
                'nama_ibadah' => 'Umum Sore',
                'deskripsi'   => 'Ibadah Raya Umum sesi sore',
                'isActive'    => 1,
                'isDelete'    => 0,
            ],
            [
                'nama_ibadah' => 'Pemuda',
                'deskripsi'   => 'Ibadah persekutuan pemuda / youth',
                'isActive'    => 1,
                'isDelete'    => 0,
            ],
            [
                'nama_ibadah' => 'Remaja',
                'deskripsi'   => 'Ibadah persekutuan remaja / teens',
                'isActive'    => 1,
                'isDelete'    => 0,
            ],
            [
                'nama_ibadah' => 'Pendalaman Alkitab',
                'deskripsi'   => 'Sesi pengajaran dan pendalaman Alkitab (PA)',
                'isActive'    => 1,
                'isDelete'    => 0,
            ],
        ];

        foreach ($jenisIbadahs as $ibadah) {
            JenisIbadah::updateOrCreate(
                ['nama_ibadah' => $ibadah['nama_ibadah']],
                $ibadah
            );
        }
    }
}