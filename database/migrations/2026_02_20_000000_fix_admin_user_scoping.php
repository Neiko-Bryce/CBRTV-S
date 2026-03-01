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
        // Find the first available school
        $firstSchool = DB::table('schools')->orderBy('id', 'asc')->first();

        if ($firstSchool) {
            // Assign this school to any Regular Admin (non-super-admin) who has a null school_id
            DB::table('users')
                ->where('usertype', 'admin')
                ->where('is_super_admin', false)
                ->whereNull('school_id')
                ->update(['school_id' => $firstSchool->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No easy way to reverse as we don't know who was null before,
        // but this is a data fix, so it's usually safe not to reverse.
    }
};
