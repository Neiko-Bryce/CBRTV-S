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
        // Skip if elections table doesn't exist or election_id is already VARCHAR
        if (!Schema::hasTable('elections') || !Schema::hasColumn('elections', 'election_id')) {
            return;
        }

        // The create_elections_table migration already creates election_id as string
        // This migration is only needed if upgrading from an older version
        // For fresh installs, skip this
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse
    }
};
