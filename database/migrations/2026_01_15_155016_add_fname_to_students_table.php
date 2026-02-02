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
        // Skip if fname column already exists
        if (Schema::hasColumn('students', 'fname')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {
            $table->string('fname')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('students', 'fname')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('fname');
            });
        }
    }
};
