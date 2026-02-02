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
        // The create_elections_table migration already creates id column
        // This migration is only needed if upgrading from an older version
        // Skip for fresh installs
        if (!Schema::hasTable('elections')) {
            return;
        }

        // Check if id column already exists
        if (Schema::hasColumn('elections', 'id')) {
            return;
        }

        $driver = DB::connection()->getDriverName();

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE elections ADD COLUMN id BIGSERIAL PRIMARY KEY');
        } else {
            Schema::table('elections', function (Blueprint $table) {
                $table->id()->first();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't drop id column on rollback
    }
};
