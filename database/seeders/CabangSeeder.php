<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Matikan cek foreign key lalu bersihkan tabel agar aman dari duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('cabangs')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Suntik data 5 cabang sesuai nama kolom aslimu ('nama_cabang' & 'lokasi')
        DB::table('cabangs')->insert([
            [
                'id' => 1,
                'nama_cabang' => 'Darmo',
                'lokasi' => 'Jl. Raya Darmo Harapan No.PF-1 · (031) 7317208',
                'isActive' => true,
                'isDelete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nama_cabang' => 'Bromo',
                'lokasi' => 'Jalan Raya Arjuna (No. 75A)',
                'isActive' => true,
                'isDelete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nama_cabang' => 'Koblen',
                'lokasi' => 'Jalan Koblen Tengah No. 22A',
                'isActive' => true,
                'isDelete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'nama_cabang' => 'Gateway',
                'lokasi' => 'Ruko Gateway F-22',
                'isActive' => true,
                'isDelete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'nama_cabang' => 'Tengger',
                'lokasi' => 'Jalan Raya Tengger Kandangan No.137',
                'isActive' => true,
                'isDelete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}