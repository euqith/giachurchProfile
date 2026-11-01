<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jemaats', function (Blueprint $table) {
            // Menggunakan json agar mudah menyimpan multiple selection ['Pemuda', 'Umum']
            $table->json('fokus_ibadah')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('jemaats', function (Blueprint $table) {
            $table->dropColumn('fokus_ibadah');
        });
    }
};
