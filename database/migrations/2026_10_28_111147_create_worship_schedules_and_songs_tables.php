<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('worship_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('jenis_ibadah_id')->nullable()->constrained('jenis_ibadahs')->onDelete('set null');
            $table->string('nama_ibadah');
            $table->foreignId('sesi_id')->nullable()->constrained('sesis')->onDelete('set null');
            $table->string('nama_sesi')->nullable();
            $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->onDelete('set null');
            $table->string('nama_cabang');
            $table->string('tema')->nullable();
            $table->string('ayat')->nullable();
            $table->text('bahan_khotbah')->nullable();
            $table->string('pengkhotbah')->nullable();
            $table->string('pemimpin_pujian')->nullable();
            $table->json('petugas_tambahan')->nullable();
            $table->string('createdBy')->nullable();
            $table->string('updatedBy')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('isDelete')->default(false);
            $table->timestamps();
        });

        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->string('judul_lagu');
            $table->string('artis_sumber')->nullable();
            $table->string('nada_dasar', 10)->nullable();
            $table->string('bagian_terakhir')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('chord_url')->nullable();
            $table->string('lirik_url')->nullable();
            $table->text('lirik_teks')->nullable();
            $table->string('createdBy')->nullable();
            $table->string('updatedBy')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('isDelete')->default(false);
            $table->timestamps();
        });

        Schema::create('worship_schedule_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('worship_schedule_id')->constrained('worship_schedules')->onDelete('cascade');
            $table->foreignId('song_id')->constrained('songs')->onDelete('cascade');
            $table->string('bagian_liturgi')->nullable();
            $table->string('nada_dasar', 10)->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worship_schedule_songs');
        Schema::dropIfExists('songs');
        Schema::dropIfExists('worship_schedules');
    }
};
