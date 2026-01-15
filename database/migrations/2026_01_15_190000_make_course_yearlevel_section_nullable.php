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
        $driver = DB::getDriverName();
        
        // Make course, yearlevel, and section nullable to allow empty imports
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE students ALTER COLUMN course DROP NOT NULL');
            DB::statement('ALTER TABLE students ALTER COLUMN yearlevel DROP NOT NULL');
            DB::statement('ALTER TABLE students ALTER COLUMN section DROP NOT NULL');
        } elseif ($driver === 'mysql' || $driver === 'mariadb') {
            Schema::table('students', function (Blueprint $table) {
                $table->string('course')->nullable()->change();
                $table->string('yearlevel')->nullable()->change();
                $table->string('section')->nullable()->change();
            });
        } else {
            Schema::table('students', function (Blueprint $table) {
                $table->string('course')->nullable()->change();
                $table->string('yearlevel')->nullable()->change();
                $table->string('section')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();
        
        if ($driver === 'pgsql') {
            // Set default empty string for existing nulls before making NOT NULL
            DB::statement("UPDATE students SET course = '' WHERE course IS NULL");
            DB::statement("UPDATE students SET yearlevel = '' WHERE yearlevel IS NULL");
            DB::statement("UPDATE students SET section = '' WHERE section IS NULL");
            DB::statement('ALTER TABLE students ALTER COLUMN course SET NOT NULL');
            DB::statement('ALTER TABLE students ALTER COLUMN yearlevel SET NOT NULL');
            DB::statement('ALTER TABLE students ALTER COLUMN section SET NOT NULL');
        } else {
            Schema::table('students', function (Blueprint $table) {
                $table->string('course')->nullable(false)->change();
                $table->string('yearlevel')->nullable(false)->change();
                $table->string('section')->nullable(false)->change();
            });
        }
    }
};
