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
        // 1. Get all organizations
        $organizations = DB::table('organizations')->get();

        foreach ($organizations as $org) {
            // 2. Find position names that are duplicated within this organization
            // Using ILIKE/lower for exact name matching regardless of case
            $duplicateNames = DB::table('positions')
                ->where('organization_id', $org->id)
                ->select('name')
                ->groupBy('name')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('name');

            foreach ($duplicateNames as $name) {
                // 3. Get all positions with this name for this org, ordered by ID
                $positions = DB::table('positions')
                    ->where('organization_id', $org->id)
                    ->where('name', $name)
                    ->orderBy('id', 'asc')
                    ->get();

                // The first one is the "Survivor" (the one we keep)
                $survivor = $positions->first();
                $duplicates = $positions->slice(1);

                foreach ($duplicates as $duplicate) {
                    // 4. Move all candidates from the duplicate to the survivor
                    // This fixes the "Cannot delete" error
                    DB::table('candidates')
                        ->where('position_id', $duplicate->id)
                        ->update([
                            'position_id' => $survivor->id,
                            'organization_id' => $org->id, // Ensure org_id is correct
                        ]);

                    // 5. Move any associated votes for these candidates (Double Safety)
                    $movedCandidateIds = DB::table('candidates')
                        ->where('position_id', $survivor->id)
                        ->pluck('id');

                    if ($movedCandidateIds->isNotEmpty()) {
                        DB::table('votes')
                            ->whereIn('candidate_id', $movedCandidateIds)
                            ->update(['organization_id' => $org->id]);
                    }

                    // 6. Finally delete the redundant position row
                    DB::table('positions')->where('id', $duplicate->id)->delete();
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
