<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Bersihkan tabel events sebelum diisi agar tidak duplikat
        DB::table('events')->truncate();

        DB::table('events')->insert([
            [
                'cabang_id' => 1, // Darmo
                'nama_acara' => 'Masa Lalu: KKR Pemuda Bulan Lalu',
                'deskripsi_acara' => 'Ini contoh acara yang sudah lewat jauh dari H-1 minggu, harusnya otomatis tidak muncul di halaman depan.',
                'waktu_mulai' => Carbon::now()->subDays(14)->setTime(18, 0, 0), // Lewat 14 hari yang lalu
                'waktu_selesai' => Carbon::now()->subDays(14)->setTime(21, 0, 0),
                'lokasi_spesifik' => 'Gedung Serbaguna Lt.1',
                'gambar_banner' => null,
                'isActive' => true,
                'isDelete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cabang_id' => 1, // Darmo
                'nama_acara' => 'Batas Toleransi: Persekutuan Doa Keluarga',
                'deskripsi_acara' => 'Acara ini pas H-7 hari yang lalu. Berdasarkan logika filter kita, ini batas terbawah yang masih diizinkan muncul.',
                'waktu_mulai' => Carbon::now()->subDays(7)->setTime(17, 0, 0), // Pas H-7 hari yang lalu
                'waktu_selesai' => Carbon::now()->subDays(7)->setTime(19, 0, 0),
                'lokasi_spesifik' => 'Ruang Konsistori Pusat',
                'gambar_banner' => null,
                'isActive' => true,
                'isDelete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cabang_id' => 2, // Bromo
                'nama_acara' => 'Hari Ini: Kebaktian Kebangunan Rohani (KKR)',
                'deskripsi_acara' => 'Mari hadiri KKR spesial yang diadakan hari ini dengan penuh sukacita dan berkat rohani.',
                'waktu_mulai' => Carbon::now()->setTime(18, 30, 0), // Hari ini Jam 18:30
                'waktu_selesai' => Carbon::now()->setTime(21, 30, 0),
                'lokasi_spesifik' => 'Gedung Utama Aula Barat',
                'gambar_banner' => null,
                'isActive' => true,
                'isDelete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cabang_id' => 3, // Koblen
                'nama_acara' => 'Mendatang: Seminar Pra-Nikah Edisi 2026',
                'deskripsi_acara' => 'Mempersiapkan masa depan keluarga Kristen yang harmonis berlandaskan kebenaran firman Tuhan.',
                'waktu_mulai' => Carbon::now()->addDays(5)->setTime(9, 0, 0), // 5 Hari lagi
                'waktu_selesai' => Carbon::now()->addDays(5)->setTime(13, 0, 0),
                'lokasi_spesifik' => 'Ruang Teater Lt. 2',
                'gambar_banner' => null,
                'isActive' => true,
                'isDelete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'cabang_id' => 4, // Gateway
                'nama_acara' => 'Futuristik: Camp Sekolah Minggu & Remaja',
                'deskripsi_acara' => 'Kegiatan tahunan seru untuk mempererat keakraban dan pertumbuhan iman anak-anak remaja gereja.',
                'waktu_mulai' => Carbon::now()->addDays(20)->setTime(8, 0, 0), // 20 Hari lagi
                'waktu_selesai' => Carbon::now()->addDays(23)->setTime(17, 0, 0), // Selesai 3 hari kemudian
                'lokasi_spesifik' => 'Area Outbound Ruko Gateway',
                'gambar_banner' => null,
                'isActive' => true,
                'isDelete' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}