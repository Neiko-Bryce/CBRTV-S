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
        // 1. Organizations: slug and code should be unique per school
        if (Schema::hasTable('organizations')) {
            // Drop both constraint and index to be absolutely sure
            try { DB::statement('ALTER TABLE organizations DROP CONSTRAINT IF EXISTS organizations_slug_unique CASCADE'); } catch (\Exception $e) {}
            try { DB::statement('ALTER TABLE organizations DROP CONSTRAINT IF EXISTS organizations_code_unique CASCADE'); } catch (\Exception $e) {}
            try { DB::statement('DROP INDEX IF EXISTS organizations_slug_unique CASCADE'); } catch (\Exception $e) {}
            try { DB::statement('DROP INDEX IF EXISTS organizations_code_unique CASCADE'); } catch (\Exception $e) {}

            Schema::table('organizations', function (Blueprint $table) {
                // Remove these columns if they were previously unique-indexed by Laravel's standard drop
                // (though we already tried with raw SQL)
                $table->unique(['slug', 'school_id'], 'organizations_slug_school_id_unique');
                $table->unique(['code', 'school_id'], 'organizations_code_school_id_unique');
            });
        }

        // 2. Elections: election_id should be unique per school
        if (Schema::hasTable('elections')) {
            try { DB::statement('ALTER TABLE elections DROP CONSTRAINT IF EXISTS elections_election_id_unique CASCADE'); } catch (\Exception $e) {}
            try { DB::statement('DROP INDEX IF EXISTS elections_election_id_unique CASCADE'); } catch (\Exception $e) {}

            Schema::table('elections', function (Blueprint $table) {
                $table->unique(['election_id', 'school_id'], 'elections_election_id_school_id_unique');
            });
        }

        // 3. Students: student_id_number should be unique per school
        if (Schema::hasTable('students')) {
            try { DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_student_id_number_unique CASCADE'); } catch (\Exception $e) {}
            try { DB::statement('ALTER TABLE students DROP CONSTRAINT IF EXISTS students_organization_id_student_id_number_unique CASCADE'); } catch (\Exception $e) {}
            try { DB::statement('DROP INDEX IF EXISTS students_student_id_number_unique CASCADE'); } catch (\Exception $e) {}
            try { DB::statement('DROP INDEX IF EXISTS students_organization_id_student_id_number_unique CASCADE'); } catch (\Exception $e) {}

            Schema::table('students', function (Blueprint $table) {
                $table->unique(['student_id_number', 'school_id'], 'students_student_id_number_school_id_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropUnique(['slug', 'school_id']);
            $table->dropUnique(['code', 'school_id']);
            $table->unique('slug');
            $table->unique('code');
        });

        Schema::table('elections', function (Blueprint $table) {
            $table->dropUnique(['election_id', 'school_id']);
            $table->unique('election_id');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique(['student_id_number', 'school_id']);
            $table->unique('student_id_number');
        });
    }
};
