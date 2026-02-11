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
        // 1. Restructure organizations table
        if (Schema::hasTable('organizations')) {
            Schema::table('organizations', function (Blueprint $table) {
                if (!Schema::hasColumn('organizations', 'slug')) {
                    $table->string('slug')->unique()->after('name')->nullable();
                }
                if (!Schema::hasColumn('organizations', 'logo_path')) {
                    $table->string('logo_path')->after('description')->nullable();
                }
            });
        }

        // 2. Add is_super_admin to users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_super_admin')) {
                $table->boolean('is_super_admin')->default(false)->after('usertype');
            }
        });

        // 3. Add organization_id to all relevant tables
        // Note: landing_page_settings organization_id is NULLABLE for global content (About, Team)
        $tables = ['users', 'students', 'candidates', 'positions', 'partylists', 'votes', 'landing_page_settings'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'organization_id')) {
                    $table->unsignedBigInteger('organization_id')->nullable()->after('id');
                    $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
                }
            });
        }

        // 4. Initial Setup: Create "Main School" and "Grandfather" existing data
        $mainOrgId = DB::table('organizations')->insertGetId([
            'name' => 'Main School',
            'slug' => 'main-school',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Link all existing data to this organization EXCLUDING global landing page settings
        $tablesToLink = ['users', 'students', 'candidates', 'positions', 'partylists', 'votes'];
        foreach ($tablesToLink as $tableName) {
            DB::table($tableName)->update(['organization_id' => $mainOrgId]);
        }
        
        // Only link landing page settings that ARE NOT 'about', 'team', or 'features' (Global Content)
        DB::table('landing_page_settings')
            ->whereNotIn('section', ['about', 'team', 'features'])
            ->update(['organization_id' => $mainOrgId]);

        // Promote current admin(s) to Super Admin
        DB::table('users')->where('usertype', 'admin')->update([
            'is_super_admin' => true,
            'organization_id' => $mainOrgId
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_super_admin');
        });

        $tables = ['users', 'students', 'candidates', 'positions', 'partylists', 'votes', 'landing_page_settings'];
        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                // Drop foreign key first
                $foreignKey = $tableName . '_organization_id_foreign';
                // Check if index exists before dropping (DB specific, but safe in modern Laravel)
                $table->dropForeign($foreignKey);
                $table->dropColumn('organization_id');
            });
        }

        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['slug', 'logo_path']);
        });
    }
};
