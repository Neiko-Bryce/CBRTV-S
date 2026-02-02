<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip if status column already exists
        if (Schema::hasColumn('elections', 'status')) {
            return;
        }

        Schema::table('elections', function (Blueprint $table) {
            $table->string('status')->default('upcoming');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('elections', 'status')) {
            Schema::table('elections', function (Blueprint $table) {
                $table->dropColumn('status');
            });
        }
    }
};
