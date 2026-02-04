<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * When true, this election's results are shown on the public landing page (Live Election Results).
     */
    public function up(): void
    {
        if (Schema::hasColumn('elections', 'show_live_results')) {
            return;
        }
        Schema::table('elections', function (Blueprint $table) {
            $table->boolean('show_live_results')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elections', function (Blueprint $table) {
            if (Schema::hasColumn('elections', 'show_live_results')) {
                $table->dropColumn('show_live_results');
            }
        });
    }
};
