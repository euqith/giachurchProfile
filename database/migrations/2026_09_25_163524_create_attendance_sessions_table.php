<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('jenis_ibadah_id')->nullable()->constrained('jenis_ibadahs')->onDelete('set null');
            $table->string('nama_ibadah'); // Backup text jika master dihapus
            $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->onDelete('set null');
            $table->string('nama_cabang'); // Backup text
            $table->string('nama_petugas');
            $table->integer('total_anggota')->default(0);
            $table->integer('total_tamu')->default(0);
            $table->integer('total_hadir')->default(0);
            $table->boolean('isDelete')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_sessions');
    }
};