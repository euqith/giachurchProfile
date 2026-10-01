<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sesi;

class SesiSeeder extends Seeder
{
    public function run(): void
    {
        $sesis = ['Pagi', 'Siang', 'Sore', 'Malam'];

        foreach ($sesis as $nama) {
            Sesi::updateOrCreate(
                ['nama_sesi' => $nama],
                ['isActive' => 1, 'isDelete' => 0]
            );
        }
    }
}