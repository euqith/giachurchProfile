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
        if (Schema::hasTable('church_profiles')) {
            Schema::table('church_profiles', function (Blueprint $table) {
                if (! Schema::hasColumn('church_profiles', 'youtube_api_key')) {
                    $table->string('youtube_api_key')->nullable()->after('youtube_channel_url');
                }
            });
        }

        if (Schema::hasTable('streams')) {
            Schema::table('streams', function (Blueprint $table) {
                if (! Schema::hasColumn('streams', 'channel_id')) {
                    $table->string('channel_id')->nullable()->after('youtube_id');
                }
                if (! Schema::hasColumn('streams', 'auto_detect')) {
                    $table->boolean('auto_detect')->default(true)->after('is_live');
                }
                if (! Schema::hasColumn('streams', 'last_checked_at')) {
                    $table->timestamp('last_checked_at')->nullable()->after('auto_detect');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('church_profiles')) {
            Schema::table('church_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('church_profiles', 'youtube_api_key')) {
                    $table->dropColumn('youtube_api_key');
                }
            });
        }

        if (Schema::hasTable('streams')) {
            Schema::table('streams', function (Blueprint $table) {
                $columns = ['channel_id', 'auto_detect', 'last_checked_at'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('streams', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
