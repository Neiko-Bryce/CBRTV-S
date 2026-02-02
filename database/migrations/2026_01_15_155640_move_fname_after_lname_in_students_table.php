<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // This migration is for column reordering which PostgreSQL doesn't support
        // and isn't necessary for functionality. Skip for fresh installs.
        if (!Schema::hasColumn('students', 'fname')) {
            // Column doesn't exist, add it
            Schema::table('students', function (Blueprint $table) {
                $table->string('fname')->nullable();
            });
        }
        // Column position doesn't affect functionality, so we skip reordering
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse
    }
};
