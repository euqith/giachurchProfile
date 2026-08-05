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
    Schema::create('streams', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cabang_id')->constrained('cabangs');
        $table->string('judul_streaming'); 
        $table->string('youtube_id'); 
        $table->string('waktu_tayang'); 
        $table->boolean('is_live')->default(false); 
        
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
        Schema::dropIfExists('streams');
    }
};
