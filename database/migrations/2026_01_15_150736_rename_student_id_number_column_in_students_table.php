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
        // If column already has the correct name (student_id_number), skip
        if (Schema::hasColumn('students', 'student_id_number')) {
            return;
        }

        // Check for Student_ID_num first (the actual column name in database)
        if (Schema::hasColumn('students', 'Student_ID_num')) {
            if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
                DB::statement('ALTER TABLE students CHANGE Student_ID_num student_id_number VARCHAR(255)');
            } elseif (DB::getDriverName() === 'pgsql') {
                DB::statement('ALTER TABLE students RENAME COLUMN "Student_ID_num" TO student_id_number');
            } else {
                Schema::table('students', function (Blueprint $table) {
                    $table->renameColumn('Student_ID_num', 'student_id_number');
                });
            }
        } elseif (Schema::hasColumn('students', 'Student_ID_number')) {
            // Also check for Student_ID_number (in case it exists)
            if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
                DB::statement('ALTER TABLE students CHANGE Student_ID_number student_id_number VARCHAR(255)');
            } elseif (DB::getDriverName() === 'pgsql') {
                DB::statement('ALTER TABLE students RENAME COLUMN "Student_ID_number" TO student_id_number');
            } else {
                Schema::table('students', function (Blueprint $table) {
                    $table->renameColumn('Student_ID_number', 'student_id_number');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Check if the new column exists and rename it back to Student_ID_num
        if (Schema::hasColumn('students', 'student_id_number')) {
            if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
                DB::statement('ALTER TABLE students CHANGE student_id_number Student_ID_num VARCHAR(255)');
            } elseif (DB::getDriverName() === 'pgsql') {
                DB::statement('ALTER TABLE students RENAME COLUMN student_id_number TO "Student_ID_num"');
            } else {
                Schema::table('students', function (Blueprint $table) {
                    $table->renameColumn('student_id_number', 'Student_ID_num');
                });
            }
        }
    }
};
