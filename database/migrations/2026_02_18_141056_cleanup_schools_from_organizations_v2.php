<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Find organizations that are actually schools
        // We look for name or slug matches to be 100% sure
        $schoolOrgs = DB::table('organizations')
            ->whereIn('slug', ['main-school', 'sipalay', 'sipalay-campus', 'main-campus'])
            ->orWhereIn('name', ['Main School', 'Sipalay Campus', 'Main Campus'])
            ->get();

        foreach ($schoolOrgs as $org) {
            // Find the corresponding School record
            $school = DB::table('schools')
                ->where('slug', $org->slug)
                ->orWhere('name', $org->name)
                ->first();

            if (! $school) {
                // If it's in the org list as a school name, it should exist in schools table
                // If not, we skip to avoid errors
                continue;
            }

            // Ensure standard organizations exist for this school
            $orgHierarchy = [
                'SSG' => ['name' => 'SUPREME STUDENT GOVERNMENT', 'code' => 'SSG'],
                'CCS' => ['name' => 'COLLEGE OF COMPUTER STUDIES', 'code' => 'CCS'],
                'FLP' => ['name' => 'FUTURE LEADERS OF THE PHILIPPINES', 'code' => 'FLP'],
            ];

            $targetOrgs = [];
            foreach ($orgHierarchy as $key => $data) {
                // Look for existing organization by code or name for this school
                $targetOrg = DB::table('organizations')
                    ->where('school_id', $school->id)
                    ->where(function ($q) use ($data) {
                        $q->where('code', $data['code'])
                            ->orWhere('name', $data['name']);
                    })
                    ->first();

                if (! $targetOrg) {
                    // Create it if it doesn't exist
                    $orgId = DB::table('organizations')->insertGetId([
                        'school_id' => $school->id,
                        'name' => $data['name'],
                        'code' => $data['code'],
                        'slug' => \Illuminate\Support\Str::slug($data['name'].'-'.$school->slug),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $targetOrg = (object) ['id' => $orgId, 'name' => $data['name'], 'code' => $data['code']];
                }
                $targetOrgs[$key] = $targetOrg;
            }

            // 2. Smart Reassignment of Positions
            // We reassign from the "School-Organization" to the proper "School-SubOrganization"
            $positions = DB::table('positions')->where('organization_id', $org->id)->get();
            foreach ($positions as $position) {
                $targetOrgId = $targetOrgs['SSG']->id; // Default to SSG

                // Check for keywords in name
                if (stripos($position->name, 'CCS') !== false) {
                    $targetOrgId = $targetOrgs['CCS']->id;
                } elseif (stripos($position->name, 'FLP') !== false) {
                    $targetOrgId = $targetOrgs['FLP']->id;
                }

                DB::table('positions')->where('id', $position->id)->update([
                    'organization_id' => $targetOrgId,
                    'school_id' => $school->id,
                ]);
            }

            // 3. Reassign other data (Elections, Students, etc.)
            // Most of these are better off in SSG if they were previously at the "School" level
            $tablesToReassign = ['users', 'students', 'candidates', 'elections', 'partylists', 'votes'];
            foreach ($tablesToReassign as $tableName) {
                DB::table($tableName)->where('organization_id', $org->id)->update([
                    'organization_id' => $targetOrgs['SSG']->id,
                    'school_id' => $school->id,
                ]);
            }

            // 4. Remove the redundant organizational record
            DB::table('organizations')->where('id', $org->id)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is data-corrective and difficult to reverse perfectly
        // without knowing exactly what was moved.
    }
};
