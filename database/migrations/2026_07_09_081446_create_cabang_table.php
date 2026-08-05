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
    Schema::create('cabangs', function (Blueprint $table) {
        $table->id();
        $table->string('nama_cabang'); 
        $table->string('lokasi')->nullable();
        
        // 🛡️ Standar Kolom Audit Seragam
        $table->string('createdBy')->nullable();
        $table->string('updatedBy')->nullable();
        $table->boolean('isActive')->default(true); // Di MySQL otomatis dikonversi jadi BIT/TINYINT(1)
        $table->boolean('isDelete')->default(false);
        $table->timestamps(); // Menggantikan createdDate & updatedDate standar Laravel
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabang');
    }
};
