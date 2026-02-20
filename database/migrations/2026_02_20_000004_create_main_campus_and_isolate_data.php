<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\School;
use App\Models\User;
use App\Models\Election;
use App\Models\Organization;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create or ensure "Main Campus" exists
        $mainCampus = School::updateOrCreate(
            ['slug' => 'main-campus'],
            ['name' => 'Main Campus']
        );

        // 2. Find the main admin
        $admin = User::where('email', 'admin@gmail.com')->first();

        if ($admin) {
            $oldSchoolId = $admin->school_id;
            
            // 3. Reassign admin to Main Campus
            $admin->update(['school_id' => $mainCampus->id]);

            // 4. Migrate admin's organizations
            // If they were Sipalay (ID 1) or NULL, move them to Main Campus
            Organization::withoutGlobalScopes()
                ->where('school_id', $oldSchoolId)
                ->orWhereNull('school_id')
                ->update(['school_id' => $mainCampus->id]);

            // 5. Migrate admin's elections
            Election::withoutGlobalScopes()
                ->where('school_id', $oldSchoolId)
                ->orWhereNull('school_id')
                ->update(['school_id' => $mainCampus->id]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Logic to reverse if needed (not strictly required for a one-way fix)
    }
};
