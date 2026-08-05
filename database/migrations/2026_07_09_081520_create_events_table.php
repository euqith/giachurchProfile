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
    Schema::create('events', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cabang_id')->constrained('cabangs');
        $table->string('nama_acara'); 
        $table->text('deskripsi_acara')->nullable();
        $table->dateTime('waktu_mulai');
        $table->string('lokasi_spesifik'); 
        $table->string('gambar_banner')->nullable(); 
        
        // 🛡️ Standar Kolom Audit Seragam
        $table->string('createdBy')->nullable();
        $table->string('updatedBy')->nullable();
        $table->boolean('isActive')->default(true); 
        $table->boolean('isDelete')->default(false);
        $table->timestamps(); 
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
