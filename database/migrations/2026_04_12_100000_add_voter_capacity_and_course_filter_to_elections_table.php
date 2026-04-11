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
        Schema::table('elections', function (Blueprint $table) {
            if (! Schema::hasColumn('elections', 'voter_capacity')) {
                $table->unsignedInteger('voter_capacity')->nullable()->after('status');
            }
            if (! Schema::hasColumn('elections', 'course_filter_mode')) {
                $table->string('course_filter_mode', 20)->default('all')->after('voter_capacity');
            }
            if (! Schema::hasColumn('elections', 'allowed_courses')) {
                $table->json('allowed_courses')->nullable()->after('course_filter_mode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            $table->dropColumn(['voter_capacity', 'course_filter_mode', 'allowed_courses']);
        });
    }
};
