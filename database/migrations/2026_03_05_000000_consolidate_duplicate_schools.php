<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * "Main School" (id=1) and "Main Campus" (id=3) are the same campus.
 * The admin account and new elections use school_id=3 (Main Campus).
 * Move all records from school_id=1 to school_id=3 so students see elections.
 */
return new class extends Migration
{
    public function up(): void
    {
        $mainSchool = DB::table('schools')->where('slug', 'main-school')->first();
        $mainCampus = DB::table('schools')->where('slug', 'main-campus')->first();

        if (! $mainSchool || ! $mainCampus || $mainSchool->id === $mainCampus->id) {
            Log::info('ConsolidateDuplicateSchools: No duplicate to fix.');

            return;
        }

        $oldId = $mainSchool->id;
        $newId = $mainCampus->id;

        Log::info("ConsolidateDuplicateSchools: Merging school #{$oldId} (Main School) → #{$newId} (Main Campus)");

        $tables = ['organizations', 'elections', 'candidates', 'positions', 'partylists', 'students', 'users', 'votes', 'landing_page_settings'];

        foreach ($tables as $table) {
            try {
                $count = DB::table($table)->where('school_id', $oldId)->count();
                if ($count > 0) {
                    DB::table($table)->where('school_id', $oldId)->update(['school_id' => $newId]);
                    Log::info("  {$table}: moved {$count} record(s) from school #{$oldId} → #{$newId}");
                }
            } catch (\Exception $e) {
                Log::warning("  {$table}: skipped — ".$e->getMessage());
            }
        }

        // Don't delete the old school record — just deactivate or leave it
        // so we don't break any foreign key references elsewhere
        Log::info('ConsolidateDuplicateSchools: Done.');
    }

    public function down(): void
    {
        // Data consolidation — no rollback
    }
};
