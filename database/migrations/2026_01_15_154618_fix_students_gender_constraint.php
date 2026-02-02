<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix PostgreSQL check constraint on gender column that blocks inserts.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // Drop the check constraint if it exists (PostgreSQL creates this for enum columns)
            try {
                DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_gender_check');
                DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_gender_check1');
                Log::info('Dropped gender check constraint(s) from students table');
            } catch (\Exception $e) {
                // Constraint might not exist or already dropped, ignore
                Log::info('No gender check constraint to drop (or already removed): '.$e->getMessage());
            }

            // Ensure gender column is VARCHAR and nullable
            try {
                DB::statement('ALTER TABLE students ALTER COLUMN gender TYPE VARCHAR(255)');
                DB::statement('ALTER TABLE students ALTER COLUMN gender DROP NOT NULL');
            } catch (\Exception $e) {
                // Column might already be correct type, ignore
                Log::info('Gender column type already correct or error: '.$e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't recreate the constraint - let it remain as VARCHAR
    }
};
