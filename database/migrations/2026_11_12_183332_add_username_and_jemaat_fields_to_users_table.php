<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username', 60)->nullable()->unique()->after('name');
            }
            if (! Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'google_id')) {
                $table->string('google_id')->nullable()->unique()->after('avatar');
            }
            if (! Schema::hasColumn('users', 'telepon')) {
                $table->string('telepon', 30)->nullable()->after('google_id');
            }
            if (! Schema::hasColumn('users', 'bio')) {
                $table->text('bio')->nullable()->after('telepon');
            }
            $table->string('email')->nullable()->change();
        });

        // Berikan username default untuk user yang sudah ada
        try {
            $users = DB::table('users')->whereNull('username')->get();
            foreach ($users as $u) {
                $prefix = strtolower(explode('@', $u->email ?? 'user'.$u->id)[0]);
                $clean = preg_replace('/[^a-z0-9_]/', '', $prefix);
                if (empty($clean)) {
                    $clean = 'user'.$u->id;
                }
                $candidate = $clean;
                $counter = 1;
                while (DB::table('users')->where('username', $candidate)->exists()) {
                    $candidate = $clean.$counter;
                    $counter++;
                }
                DB::table('users')->where('id', $u->id)->update(['username' => $candidate]);
            }
        } catch (Throwable $e) {
            // Abaikan jika error saat seeding username
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $cols = ['username', 'avatar', 'google_id', 'telepon', 'bio'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('users', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
    }
};
