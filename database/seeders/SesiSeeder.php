<?php

namespace Database\Seeders;

use App\Models\Sesi;
use Illuminate\Database\Seeder;

class SesiSeeder extends Seeder
{
    public function run(): void
    {
        $sesis = ['Pagi', 'Siang', 'Sore'];

        foreach ($sesis as $nama) {
            Sesi::updateOrCreate(
                ['nama_sesi' => $nama],
                ['isActive' => 1, 'isDelete' => 0]
            );
        }
    }
}
