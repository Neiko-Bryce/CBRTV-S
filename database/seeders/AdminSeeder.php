<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin Account (Full Privileges)
        // updateOrCreate ensures the account exists and has the correct role
        // without creating duplicates or deleting other users.
        User::updateOrCreate(
            ['email' => 'superadmin@vosewisly.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('neiko@superadmin12345'),
                'usertype' => 'admin',
                'is_super_admin' => true,
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('Super Admin account synced.');

        // 2. Regular Admin Account (Scoped to a specific School)
        // Prioritize "Main Campus" for the default admin
        $school = \App\Models\School::where('slug', 'main-campus')->first() ?: \App\Models\School::first();
        $schoolId = $school ? $school->id : null;

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Neiko Bryce',
                'password' => Hash::make('neikobryce@admin12345'),
                'usertype' => 'admin',
                'is_super_admin' => false,
                'school_id' => $schoolId,
                'email_verified_at' => now(),
            ]
        );
        $this->command->info('Regular Admin account synced (School ID: '.($schoolId ?? 'NONE').').');
    }
}
