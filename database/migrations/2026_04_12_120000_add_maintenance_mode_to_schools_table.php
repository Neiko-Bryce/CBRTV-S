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
        Schema::table('schools', function (Blueprint $table) {
            if (! Schema::hasColumn('schools', 'maintenance_mode')) {
                $table->boolean('maintenance_mode')->default(false)->after('is_active');
            }
        });

        // Migrate legacy global setting: if it was on, lock all schools (preserves prior behavior).
        if (Schema::hasTable('settings')) {
            $row = DB::table('settings')->where('key', 'maintenance_mode')->first();
            if ($row && $row->value === 'true') {
                DB::table('schools')->update(['maintenance_mode' => true]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'maintenance_mode')) {
                $table->dropColumn('maintenance_mode');
            }
        });
    }
};
