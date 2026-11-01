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
        Schema::table('worship_schedules', function (Blueprint $table) {
            $table->text('catatan_singkat')->nullable()->after('bahan_khotbah');
            $table->json('bahan_media')->nullable()->after('petugas_tambahan');
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->string('spotify_url')->nullable()->after('youtube_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('worship_schedules', function (Blueprint $table) {
            $table->dropColumn(['catatan_singkat', 'bahan_media']);
        });

        Schema::table('songs', function (Blueprint $table) {
            $table->dropColumn(['spotify_url']);
        });
    }
};
