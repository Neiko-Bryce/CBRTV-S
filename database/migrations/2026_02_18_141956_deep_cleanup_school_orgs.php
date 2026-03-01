<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration uses ILIKE which is PostgreSQL-specific — skip on SQLite
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // Use ILIKE for Postgres case-insensitive matching
        $schoolOrgs = DB::table('organizations')
            ->where(function ($q) {
                $q->where('name', 'ILIKE', '%Sipalay%')
                    ->orWhere('name', 'ILIKE', '%Campus%')
                    ->orWhere('name', 'ILIKE', '%Main School%')
                    ->orWhere('name', 'ILIKE', '%Main Campus%')
                    ->orWhere('slug', 'ILIKE', '%sipalay%');
            })
            ->get();

        foreach ($schoolOrgs as $org) {
            // Find a matching School record to move any orphaned data to
            $school = DB::table('schools')
                ->where('name', 'ILIKE', '%'.explode(' ', $org->name)[0].'%')
                ->first();

            if ($school) {
                // Find or create SSG for this school to receive the data
                $ssg = DB::table('organizations')
                    ->where('school_id', $school->id)
                    ->where('code', 'SSG')
                    ->first();

                if ($ssg) {
                    $tables = ['users', 'students', 'candidates', 'positions', 'elections', 'partylists', 'votes'];
                    foreach ($tables as $table) {
                        if (Schema::hasTable($table)) {
                            DB::table($table)->where('organization_id', $org->id)->update([
                                'organization_id' => $ssg->id,
                                'school_id' => $school->id,
                            ]);
                        }
                    }
                }
            }

            // Finally remove the redundant organizational record
            DB::table('organizations')->where('id', $org->id)->delete();
        }

        // 5. Final Precision Pass: Separate the 3 "President" positions correctly
        // We find the global SSG where all positions are currently lumped
        $ssg = DB::table('organizations')->where('code', 'SSG')->first();
        if ($ssg) {
            $presidents = DB::table('positions')
                ->where('organization_id', $ssg->id)
                ->where('name', 'President')
                ->orderBy('id')
                ->get();

            if ($presidents->count() >= 3) {
                // Move 2nd President to CCS
                $ccs = DB::table('organizations')->where('code', 'CCS')->first();
                if ($ccs) {
                    DB::table('positions')->where('id', $presidents[1]->id)->update(['organization_id' => $ccs->id]);
                }

                // Move 3rd President to FLP
                $flp = DB::table('organizations')->where('code', 'FLP')->first();
                if ($flp) {
                    DB::table('positions')->where('id', $presidents[2]->id)->update(['organization_id' => $flp->id]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
