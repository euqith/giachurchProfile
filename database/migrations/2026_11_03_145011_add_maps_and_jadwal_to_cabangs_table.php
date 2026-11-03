<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            $table->text('maps_url')->nullable()->after('lokasi');
            $table->text('maps_embed')->nullable()->after('maps_url');
            $table->text('jadwal_ibadah')->nullable()->after('maps_embed');
        });

        // Seed data awal maps dan jadwal untuk 5 cabang yang sudah ada
        $defaults = [
            'Darmo' => [
                'jadwal' => "Ibadah 1: Minggu 06.00 WIB\nIbadah 2: Minggu 08.00 WIB\nIbadah 3: Minggu 10.00 WIB\nDoa Malam: Rabu 19.00 WIB",
                'maps_url' => 'https://maps.google.com/?q=GIA+Surabaya+Darmo+Harapan',
            ],
            'Bromo' => [
                'jadwal' => "Ibadah Pagi: Minggu 07.00 WIB\nIbadah Siang: Minggu 09.30 WIB\nIbadah Sore: Minggu 17.00 WIB\nDoa Syafaat: Kamis 18.30 WIB",
                'maps_url' => 'https://maps.google.com/?q=Gereja+Isa+Almasih+Bromo+Surabaya',
            ],
            'Koblen' => [
                'jadwal' => "Ibadah Pagi: Minggu 06.30 WIB\nIbadah Siang: Minggu 09.00 WIB\nIbadah Remaja: Minggu 16.00 WIB\nDoa Tengah Minggu: Rabu 18.30 WIB",
                'maps_url' => 'https://maps.google.com/?q=Gereja+Isa+Almasih+Koblen+Surabaya',
            ],
            'Gateway' => [
                'jadwal' => "Ibadah 1: Minggu 08.00 WIB\nIbadah 2: Minggu 10.30 WIB\nIbadah Pemuda: Sabtu 18.00 WIB\nDoa Bersama: Rabu 19.00 WIB",
                'maps_url' => 'https://maps.google.com/?q=GIA+Gateway+Waru+Sidoarjo',
            ],
            'Tengger' => [
                'jadwal' => "Ibadah Pagi: Minggu 07.30 WIB\nIbadah Siang: Minggu 10.00 WIB\nIbadah Sore: Minggu 16.30 WIB\nDoa Semalam: Jumat 19.00 WIB",
                'maps_url' => 'https://maps.google.com/?q=Gereja+Isa+Almasih+Tengger+Surabaya',
            ],
        ];

        foreach ($defaults as $nama => $data) {
            DB::table('cabangs')
                ->where('nama_cabang', 'like', "%{$nama}%")
                ->update([
                    'jadwal_ibadah' => $data['jadwal'],
                    'maps_url' => $data['maps_url'],
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            $table->dropColumn(['maps_url', 'maps_embed', 'jadwal_ibadah']);
        });
    }
};
