<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Create landing_page_settings table if it doesn't exist
if (!Schema::hasTable('landing_page_settings')) {
    echo "Creating landing_page_settings table...\n";
    
    DB::statement("
        CREATE TABLE IF NOT EXISTS landing_page_settings (
            id BIGSERIAL PRIMARY KEY,
            section VARCHAR(255) NOT NULL,
            key VARCHAR(255) NOT NULL,
            value TEXT,
            extra JSON,
            created_at TIMESTAMP,
            updated_at TIMESTAMP,
            UNIQUE(section, key)
        )
    ");
    
    echo "✓ Table created successfully\n";
} else {
    echo "✓ Table already exists\n";
}

// Check if image column exists, if not add it
$hasImageColumn = DB::select("
    SELECT column_name 
    FROM information_schema.columns 
    WHERE table_name='landing_page_settings' 
    AND column_name='image'
");

if (empty($hasImageColumn)) {
    echo "Adding image column...\n";
    DB::statement("ALTER TABLE landing_page_settings ADD COLUMN image VARCHAR(255)");
    echo "✓ Image column added\n";
} else {
    echo "✓ Image column already exists\n";
}

echo "Database initialization complete!\n";
