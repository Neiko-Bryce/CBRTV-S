<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensures students table has all required columns.
     */
    public function up(): void
    {
        // Skip if table doesn't exist (will be created by create_students_table)
        if (!Schema::hasTable('students')) {
            return;
        }

        // Ensure fname exists
        if (!Schema::hasColumn('students', 'fname')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('fname')->nullable();
            });
        }

        // Change gender from enum to string if needed (for PostgreSQL compatibility)
        if (Schema::hasColumn('students', 'gender')) {
            try {
                $driver = DB::getDriverName();
                if ($driver === 'pgsql') {
                    DB::statement('ALTER TABLE students ALTER COLUMN gender TYPE VARCHAR(255)');
                }
            } catch (\Exception $e) {
                // Column might already be string, ignore error
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse
    }
};
