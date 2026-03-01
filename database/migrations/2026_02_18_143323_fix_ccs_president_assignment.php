<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is a data-correction for PostgreSQL only — skip on SQLite
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        // 1. Find the SSG and CCS organizations robustly
        $ssg = DB::table('organizations')->where('code', 'SSG')->first();
        $ccs = DB::table('organizations')->where('code', 'CCS')->first();

        if ($ssg && $ccs) {
            // 2. Find all "President" positions currently in SSG
            $presidents = DB::table('positions')
                ->where('organization_id', $ssg->id)
                ->where('name', 'President')
                ->orderBy('id', 'asc')
                ->get();

            // Based on the screenshot, we have 2 Presidents in SSG.
            // We want to move the 2nd one back to CCS.
            if ($presidents->count() >= 2) {
                $ccsPresidentId = $presidents[1]->id;

                // 1. Move the Position record to CCS
                DB::table('positions')
                    ->where('id', $ccsPresidentId)
                    ->update(['organization_id' => $ccs->id]);

                // 2. Move all Candidates associated with this position to CCS
                DB::table('candidates')
                    ->where('position_id', $ccsPresidentId)
                    ->update(['organization_id' => $ccs->id]);

                // 3. Move all Votes associated with these candidates for maximum safety
                $movedCandidateIds = DB::table('candidates')
                    ->where('position_id', $ccsPresidentId)
                    ->pluck('id');

                if ($movedCandidateIds->isNotEmpty()) {
                    DB::table('votes')
                        ->whereIn('candidate_id', $movedCandidateIds)
                        ->update(['organization_id' => $ccs->id]);
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
