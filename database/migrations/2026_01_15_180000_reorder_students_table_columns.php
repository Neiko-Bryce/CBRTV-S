<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ensures students table has the correct structure with columns in the proper order:
     * student_id_number, campus, lname, fname, mname, ext, gender, course, yearlevel, section
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        // Check if table exists
        if (! Schema::hasTable('students')) {
            // Create table with correct structure
            Schema::create('students', function (Blueprint $table) {
                $table->id();
                $table->string('student_id_number')->unique();
                $table->string('campus');
                $table->string('lname');
                $table->string('fname')->nullable();
                $table->string('mname')->nullable();
                $table->string('ext')->nullable();
                $table->string('gender')->nullable();
                $table->string('course');
                $table->string('yearlevel');
                $table->string('section');
                $table->timestamps();
            });

            return;
        }

        // Table exists - ensure all columns are correct
        // Rename studentid to student_id_number if it exists
        if (Schema::hasColumn('students', 'studentid') && ! Schema::hasColumn('students', 'student_id_number')) {
            if ($driver === 'pgsql') {
                DB::statement('ALTER TABLE students RENAME COLUMN studentid TO student_id_number');
            } elseif ($driver === 'mysql' || $driver === 'mariadb') {
                DB::statement('ALTER TABLE students CHANGE studentid student_id_number VARCHAR(255)');
            } else {
                Schema::table('students', function (Blueprint $table) {
                    $table->renameColumn('studentid', 'student_id_number');
                });
            }
        }

        // Ensure fname exists after lname
        if (! Schema::hasColumn('students', 'fname')) {
            if ($driver === 'mysql' || $driver === 'mariadb') {
                Schema::table('students', function (Blueprint $table) {
                    $table->string('fname')->nullable()->after('lname');
                });
            } else {
                Schema::table('students', function (Blueprint $table) {
                    $table->string('fname')->nullable();
                });
            }
        }

        // Ensure all required columns exist
        $requiredColumns = [
            'campus' => 'string',
            'lname' => 'string',
            'mname' => 'string',
            'ext' => 'string',
            'gender' => 'string',
            'course' => 'string',
            'yearlevel' => 'string',
            'section' => 'string',
        ];

        foreach ($requiredColumns as $column => $type) {
            if (! Schema::hasColumn('students', $column)) {
                Schema::table('students', function (Blueprint $table) use ($column, $type) {
                    if ($type === 'string') {
                        $nullable = in_array($column, ['mname', 'ext', 'gender']);
                        $table->string($column)->nullable($nullable);
                    }
                });
            }
        }

        // Change gender from enum to string if it's enum
        if (Schema::hasColumn('students', 'gender')) {
            try {
                if ($driver === 'pgsql') {
                    DB::statement('ALTER TABLE students ALTER COLUMN gender TYPE VARCHAR(255)');
                } elseif ($driver === 'mysql' || $driver === 'mariadb') {
                    DB::statement('ALTER TABLE students MODIFY COLUMN gender VARCHAR(255) NULL');
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
        // Don't drop columns on rollback to preserve data
        // If you need to rollback, manually adjust the table structure
    }
};
