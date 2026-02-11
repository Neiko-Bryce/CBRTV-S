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
        Schema::table('users', function (Blueprint $table) {
            // Drop the old global unique constraint
            // In Laravel/Postgres, this is typically users_email_unique
            DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS users_email_unique');
            
            // Add the new composite unique constraint
            $table->unique(['organization_id', 'email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop composite unique
            $table->dropUnique(['organization_id', 'email']);
            
            // Restore global unique
            $table->unique('email');
        });
    }
};
