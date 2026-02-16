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
        $tables = [
            'organizations',
            'candidates',
            'positions',
            'elections',
            'partylists',
            'students',
            'votes'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'school_id')) {
                    $table->unsignedBigInteger('school_id')->nullable()->after('id');
                    $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                }
            });
        }

        // 2. Data Migration: Populate school_id
        // Find or create the "Main School" record
        $mainOrg = DB::table('organizations')->where('slug', 'main-school')->first();
        $mainSchool = DB::table('schools')->where('slug', 'main-school')->first();
        
        if (!$mainSchool && $mainOrg) {
            $schoolId = DB::table('schools')->insertGetId([
                'name' => $mainOrg->name,
                'slug' => $mainOrg->slug,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $mainSchool = (object)['id' => $schoolId];
        }
        
        if ($mainSchool) {
            // For organizations, link them to the main school if they don't have one
            DB::table('organizations')->whereNull('school_id')->update(['school_id' => $mainSchool->id]);

            // For other tables, we can now trace via organization_id
            foreach (['candidates', 'positions', 'elections', 'partylists', 'students', 'votes'] as $tableName) {
                // Postgres-compatible update via subquery
                DB::table($tableName)->whereNull('school_id')->update([
                    'school_id' => DB::table('organizations')
                        ->whereColumn('organizations.id', "{$tableName}.organization_id")
                        ->select('school_id')
                        ->limit(1)
                ]);
                
                // Fallback for any records still null (shouldn't happen if they have valid org_id)
                DB::table($tableName)->whereNull('school_id')->update(['school_id' => $mainSchool->id]);
            }

            // Also ensure users associated with the Main Org get the School ID
            if ($mainOrg) {
                DB::table('users')
                    ->where('organization_id', $mainOrg->id)
                    ->whereNull('school_id')
                    ->update(['school_id' => $mainSchool->id]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'votes',
            'students',
            'partylists',
            'elections',
            'positions',
            'candidates',
            'organizations'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'school_id')) {
                    $table->dropForeign([$tableName . '_school_id_foreign']);
                    $table->dropColumn('school_id');
                }
            });
        }
    }
};
