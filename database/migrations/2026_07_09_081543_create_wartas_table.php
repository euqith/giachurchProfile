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
    // A. Tabel Induk Edisi Warta
    Schema::create('wartas', function (Blueprint $table) {
        $table->id();
        $table->string('judul_edisi'); 
        $table->date('tanggal_rilis');
        
        // 🛡️ Standar Kolom Audit Seragam
        $table->string('createdBy')->nullable();
        $table->string('updatedBy')->nullable();
        $table->boolean('isActive')->default(true); 
        $table->boolean('isDelete')->default(false);
        $table->timestamps(); 
    });

    // B. Tabel Anak berupa Slide Gambar Warta
    Schema::create('warta_slides', function (Blueprint $table) {
        $table->id();
        $table->foreignId('warta_id')->constrained('wartas')->onDelete('cascade');
        $table->string('file_gambar'); 
        $table->integer('urutan_halaman')->default(1); 
        
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
        Schema::dropIfExists('wartas');
    }
};
