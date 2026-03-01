<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // EMERGENCY RESTORE: superadmin@vosewisly.com
        // This ensures the account is always available even if deleted or desupered.
        User::updateOrCreate(
            ['email' => 'superadmin@vosewisly.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('neiko@superadmin12345'),
                'usertype' => 'admin',
                'is_super_admin' => true,
                'school_id' => null, // Keep global
                'email_verified_at' => now(),
            ]
        );

        // Ensure any other accidentally created admin with this email is a Super Admin
        DB::table('users')
            ->where('email', 'superadmin@vosewisly.com')
            ->update([
                'is_super_admin' => true,
                'usertype' => 'admin',
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed for emergency restore
    }
};
