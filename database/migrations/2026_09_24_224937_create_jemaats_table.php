<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jemaats', function (Blueprint $table) {
            $table->id();
            $table->string('nama_asli');
            $table->string('status')->default('Anggota'); // Anggota / Tamu
            $table->string('badge_tag')->nullable(); // Contoh: 'Perlu Dikunjungi'
            $table->string('alias_1')->nullable();
            $table->string('alias_2')->nullable();
            $table->string('keluarga')->nullable(); // Kel. Gunawan, dsb
            $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->onDelete('set null'); // Wilayah Ibadah
            $table->text('alamat')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('telepon')->nullable();
            $table->string('createdBy')->nullable();
            $table->string('updatedBy')->nullable();
            $table->boolean('isActive')->default(1);
            $table->boolean('isDelete')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jemaats');
    }
};