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
        Schema::create('church_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_gereja')->default('Gereja Isa Almasih Surabaya');
            $table->string('tagline')->default('Bertumbuh dalam Kristus, Berbuah bagi Sesama');
            $table->string('foto_gereja')->nullable();
            $table->text('tentang_gereja')->nullable();
            $table->text('sejarah_singkat')->nullable();
            $table->text('visi')->nullable();
            $table->text('misi')->nullable();
            $table->string('alamat_pusat')->default('Jl. Raya Darmo Harapan No.PF-1, Surabaya');
            $table->string('telepon')->default('+62-31-7317208 / +62-812-3456-789');
            $table->string('email')->default('sekretariat@giasurabaya.org');
            $table->string('youtube_channel_url')->nullable()->default('https://www.youtube.com/@giasurabaya');
            $table->string('instagram_url')->nullable()->default('https://www.instagram.com/giasurabaya');
            $table->timestamps();
        });

        // Insert initial default profile
        DB::table('church_profiles')->insert([
            'nama_gereja' => 'Gereja Isa Almasih Surabaya',
            'tagline' => 'Bertumbuh dalam Kristus, Berakar dalam Firman, Berbuah dalam Kasih',
            'foto_gereja' => null,
            'tentang_gereja' => 'Gereja Isa Almasih (GIA) Jemaat Surabaya adalah komunitas persekutuan orang-orang percaya yang dipanggil untuk memuliakan Allah Bapa, Tuhan Yesus Kristus, dan Roh Kudus. Berdiri sejak puluhan tahun melayani jemaat di Surabaya dan sekitarnya melalui lima cabang ibadah: Darmo, Bromo, Koblen, Gateway, dan Tengger. Kami berkomitmen menyediakan wadah persekutuan yang hangat, pengajaran firman yang berlandaskan Alkitab, serta pelayanan kasih holistik bagi masyarakat.',
            'sejarah_singkat' => 'Bermula dari persekutuan doa keluarga, GIA Surabaya kini berkembang menjadi keluarga rohani yang menaungi ribuan jemaat lintas generasi dari anak-anak hingga lansia di berbagai wilayah kota Surabaya.',
            'visi' => 'Menjadi jemaat yang serupa dengan Kristus, berdampak nyata bagi transformasi kota Surabaya, dan setia menjalankan Amanat Agung hingga ke ujung bumi.',
            'misi' => "1. Menyelenggarakan ibadah yang berpusat pada Kristus dan memperdalam pengenalan akan Firman Allah.\n2. Membangun persekutuan jemaat yang saling mengasihi, menopang, dan membina kedewasaan rohani.\n3. Memperlengkapi setiap anggota jemaat untuk menjadi saksi Kristus yang berintegritas di lingkungan kerja, keluarga, dan masyarakat.\n4. Menjalankan pelayanan kasih dan kepedulian sosial bagi mereka yang membutuhkan.",
            'alamat_pusat' => 'Jl. Raya Darmo Harapan No.PF-1, Surabaya, Jawa Timur 60187',
            'telepon' => '+62 31 7317208',
            'email' => 'sekretariat@giasurabaya.org',
            'youtube_channel_url' => 'https://www.youtube.com/@giasurabaya',
            'instagram_url' => 'https://www.instagram.com/giasurabaya',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('church_profiles');
    }
};
