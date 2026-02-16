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
        // Check if admin already exists
        if (User::where('email', 'superadmin@vosewisly.com')->exists()) {
            $this->command->info('Super Admin user already exists, skipping...');

            return;
        }

        User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@vosewisly.com',
            'password' => Hash::make('neiko@superadmin12345'),
            'usertype' => 'admin',
            'is_super_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin user created successfully!');
    }
}
