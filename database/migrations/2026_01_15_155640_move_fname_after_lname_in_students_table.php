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
        // For PostgreSQL, we need to use ALTER TABLE to move the column
        if (DB::getDriverName() === 'pgsql') {
            // Drop and recreate the column in the correct position
            DB::statement('ALTER TABLE students DROP COLUMN IF EXISTS fname');
            DB::statement('ALTER TABLE students ADD COLUMN fname VARCHAR(255) NULL');
            // Note: PostgreSQL doesn't support AFTER clause, so we'll just ensure it exists
            // The position doesn't affect functionality
        } else {
            // For MySQL/MariaDB
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('fname');
            });
            Schema::table('students', function (Blueprint $table) {
                $table->string('fname')->nullable()->after('lname');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Just drop the column if rolling back
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('fname');
        });
    }
};
