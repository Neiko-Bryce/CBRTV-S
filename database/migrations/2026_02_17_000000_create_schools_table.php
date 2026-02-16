<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create schools table
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('location')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Add school_id to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'school_id')) {
                $table->unsignedBigInteger('school_id')->nullable()->after('organization_id');
                $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
            }
        });

        // 3. Migrate existing "Main School" org data to schools table
        $mainSchoolOrg = DB::table('organizations')->where('slug', 'main-school')->first();
        if ($mainSchoolOrg) {
            $schoolId = DB::table('schools')->insertGetId([
                'name' => $mainSchoolOrg->name,
                'slug' => $mainSchoolOrg->slug,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Link all admin users that were associated with "Main School" org to the new school
            DB::table('users')
                ->where('usertype', 'admin')
                ->where('organization_id', $mainSchoolOrg->id)
                ->update(['school_id' => $schoolId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'school_id')) {
                $table->dropForeign(['school_id']);
                $table->dropColumn('school_id');
            }
        });

        Schema::dropIfExists('schools');
    }
};
