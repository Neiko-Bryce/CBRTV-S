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
        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@vosewisly.com');
        $password = env('SUPER_ADMIN_PASSWORD', 'neiko@superadmin12345');

        // Check if admin already exists
        if (User::where('email', $email)->exists()) {
            $this->command->info("Super Admin user ({$email}) already exists, skipping...");

            return;
        }

        User::create([
            'name' => 'Super Administrator',
            'email' => $email,
            'password' => Hash::make($password),
            'usertype' => 'admin',
            'is_super_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Super Admin user created successfully!');
    }
}
