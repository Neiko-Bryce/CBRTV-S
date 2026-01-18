<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop PostgreSQL check constraint on gender column that blocks inserts.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // Drop the check constraint if it exists (PostgreSQL creates this for enum columns)
            try {
                DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_gender_check');
                DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_gender_check1');
            } catch (\Exception $e) {
                // Constraint might not exist, ignore
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't recreate the constraint
    }
};
