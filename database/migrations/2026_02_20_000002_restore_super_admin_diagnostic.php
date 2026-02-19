<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Force Recovery of Super Admin
        // This ensures the account exists and has is_super_admin = true
        User::updateOrCreate(
            ['email' => 'superadmin@vosewisly.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('neiko@superadmin12345'),
                'usertype' => 'admin',
                'is_super_admin' => true,
                'school_id' => null, // Super admins are global
                'email_verified_at' => now(),
            ]
        );

        // 2. Ensure Regular Admin has a school_id if one exists
        $firstSchool = DB::table('schools')->orderBy('id', 'asc')->first();
        if ($firstSchool) {
            DB::table('users')
                ->where('email', 'admin@gmail.com')
                ->whereNull('school_id')
                ->update(['school_id' => $firstSchool->id]);
        }

        // 3. Keep existing NULL school_ids as Global
        // The scoping logic already handles OR WHERE NULL, but this 
        // ensures no data was accidentally semi-migrated into a 
        // broken state.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recovery migration, no down needed
    }
};
