<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop potential constraints using raw SQL for better control in Postgres
        // Postgres requires dropping the constraint, not just the index
        DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_student_id_num_unique');
        DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_student_id_number_unique');
        
        Schema::table('students', function (Blueprint $table) {
            // Add the new composite unique constraint
            $table->unique(['organization_id', 'student_id_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['organization_id', 'student_id_number']);
            $table->unique('student_id_number');
        });
    }
};
