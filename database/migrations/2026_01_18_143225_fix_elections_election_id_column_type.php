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
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'pgsql') {
            // PostgreSQL: Drop unique constraint if exists, alter column type, re-add constraint
            Schema::table('elections', function (Blueprint $table) {
                // Drop unique constraint if it exists
                DB::statement('ALTER TABLE elections DROP CONSTRAINT IF EXISTS elections_election_id_unique');
            });
            
            // Alter column type from bigint to varchar
            DB::statement('ALTER TABLE elections ALTER COLUMN election_id TYPE VARCHAR(255) USING election_id::text');
            
            // Re-add unique constraint
            Schema::table('elections', function (Blueprint $table) {
                $table->unique('election_id');
            });
        } else {
            // MySQL/MariaDB
            Schema::table('elections', function (Blueprint $table) {
                $table->string('election_id')->nullable()->unique()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'pgsql') {
            Schema::table('elections', function (Blueprint $table) {
                DB::statement('ALTER TABLE elections DROP CONSTRAINT IF EXISTS elections_election_id_unique');
            });
            
            // Convert back to bigint (only if all values are numeric)
            DB::statement('ALTER TABLE elections ALTER COLUMN election_id TYPE BIGINT USING NULLIF(election_id, \'\')::bigint');
        } else {
            Schema::table('elections', function (Blueprint $table) {
                $table->bigInteger('election_id')->nullable()->unique()->change();
            });
        }
    }
};
