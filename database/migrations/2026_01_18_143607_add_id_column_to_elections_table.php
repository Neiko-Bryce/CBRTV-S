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
            // Check if id column exists
            $columns = DB::select("SELECT column_name FROM information_schema.columns WHERE table_name = 'elections' AND column_name = 'id'");
            
            if (empty($columns)) {
                // Check current primary key constraint
                $pkConstraint = DB::select("
                    SELECT constraint_name 
                    FROM information_schema.table_constraints 
                    WHERE table_name = 'elections' 
                    AND constraint_type = 'PRIMARY KEY'
                ");
                
                // Drop existing primary key if it exists (likely on election_id)
                if (!empty($pkConstraint)) {
                    $constraintName = $pkConstraint[0]->constraint_name;
                    DB::statement("ALTER TABLE elections DROP CONSTRAINT IF EXISTS {$constraintName}");
                }
                
                // Add id column as SERIAL (auto-incrementing) and make it primary key
                DB::statement('ALTER TABLE elections ADD COLUMN id BIGSERIAL PRIMARY KEY');
            }
        } else {
            // MySQL/MariaDB
            $columns = DB::select("SHOW COLUMNS FROM elections WHERE Field = 'id'");
            
            if (empty($columns)) {
                Schema::table('elections', function (Blueprint $table) {
                    $table->id()->first();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE elections DROP COLUMN IF EXISTS id');
        } else {
            Schema::table('elections', function (Blueprint $table) {
                $table->dropColumn('id');
            });
        }
    }
};
