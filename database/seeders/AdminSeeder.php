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
        // 1. Super Admin Account
        $saEmail = env('SUPER_ADMIN_EMAIL', 'superadmin@vosewisly.com');
        $saPassword = env('SUPER_ADMIN_PASSWORD', 'neiko@superadmin12345');

        if (!User::where('email', $saEmail)->exists()) {
            User::create([
                'name' => 'Super Administrator',
                'email' => $saEmail,
                'password' => Hash::make($saPassword),
                'usertype' => 'admin',
                'is_super_admin' => true,
                'email_verified_at' => now(),
            ]);
            $this->command->info("Super Admin account ({$saEmail}) created.");
        }

        // 2. Regular Admin Account
        $adminEmail = 'admin@gmail.com';
        $adminPassword = 'neiko@admin12345';

        if (!User::where('email', $adminEmail)->exists()) {
            User::create([
                'name' => 'Neiko Bryce',
                'email' => $adminEmail,
                'password' => Hash::make($adminPassword),
                'usertype' => 'admin',
                'is_super_admin' => false,
                'email_verified_at' => now(),
            ]);
            $this->command->info("Regular Admin account ({$adminEmail}) created.");
        }
    }
}
