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
        // 1. Buat Tabel Master Kategori Jemaat (Kategori Usia)
        Schema::create('kategori_jemaats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori'); // Anak, Remaja, Pemuda, Dewasa, Lansia
            $table->integer('min_usia')->default(0);
            $table->integer('max_usia')->default(150);
            $table->string('rentang_label')->nullable(); // mis. 0-12 th, 13-20 th
            $table->string('warna_badge')->nullable()->default('blue');
            $table->integer('urutan')->default(1);
            $table->boolean('isActive')->default(1);
            $table->boolean('isDelete')->default(0);
            $table->timestamps();
        });

        // 2. Tambahkan kolom 'kategori' ke tabel jemaats jika belum ada
        if (! Schema::hasColumn('jemaats', 'kategori')) {
            Schema::table('jemaats', function (Blueprint $table) {
                $table->string('kategori')->nullable()->after('status');
            });
        }

        // 3. Masukkan Master Kategori Default (Anak, Remaja, Pemuda, Dewasa, Lansia)
        $defaultCategories = [
            [
                'nama_kategori' => 'Anak',
                'min_usia' => 0,
                'max_usia' => 12,
                'rentang_label' => '0 - 12 th',
                'warna_badge' => 'amber',
                'urutan' => 1,
                'isActive' => 1,
                'isDelete' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Remaja',
                'min_usia' => 13,
                'max_usia' => 20,
                'rentang_label' => '13 - 20 th',
                'warna_badge' => 'emerald',
                'urutan' => 2,
                'isActive' => 1,
                'isDelete' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Pemuda',
                'min_usia' => 21,
                'max_usia' => 30,
                'rentang_label' => '21 - 30 th',
                'warna_badge' => 'blue',
                'urutan' => 3,
                'isActive' => 1,
                'isDelete' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Dewasa',
                'min_usia' => 31,
                'max_usia' => 59,
                'rentang_label' => '31 - 59 th',
                'warna_badge' => 'indigo',
                'urutan' => 4,
                'isActive' => 1,
                'isDelete' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kategori' => 'Lansia',
                'min_usia' => 60,
                'max_usia' => 150,
                'rentang_label' => '>= 60 th',
                'warna_badge' => 'purple',
                'urutan' => 5,
                'isActive' => 1,
                'isDelete' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('kategori_jemaats')->insert($defaultCategories);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('jemaats', 'kategori')) {
            Schema::table('jemaats', function (Blueprint $table) {
                $table->dropColumn('kategori');
            });
        }
        Schema::dropIfExists('kategori_jemaats');
    }
};
