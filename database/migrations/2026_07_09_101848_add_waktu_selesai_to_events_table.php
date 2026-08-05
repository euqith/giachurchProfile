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
        Schema::table('events', function (Blueprint $table) {
            // Cek aman agar tidak duplikat, diletakkan tepat setelah waktu_mulai
            if (!Schema::hasColumn('events', 'waktu_selesai')) {
                $table->dateTime('waktu_selesai')->nullable()->after('waktu_mulai');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'waktu_selesai')) {
                $table->dropColumn('waktu_selesai');
            }
        });
    }
};