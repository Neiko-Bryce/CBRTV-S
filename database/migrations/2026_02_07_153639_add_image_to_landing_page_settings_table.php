<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('landing_page_settings', 'image')) {
            Schema::table('landing_page_settings', function (Blueprint $table) {
                $table->string('image')->nullable()->after('value');
            });
        }
    }

    public function down(): void
    {
        Schema::table('landing_page_settings', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
