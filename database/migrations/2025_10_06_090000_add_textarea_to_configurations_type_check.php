<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing check constraint
        DB::statement('ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_type_check');
        
        // Add new check constraint with all the required types
        DB::statement("ALTER TABLE configurations ADD CONSTRAINT configurations_type_check CHECK (type IN ('string', 'text', 'integer', 'boolean', 'json', 'file', 'email', 'url', 'textarea'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the constraint and add back the old one
        DB::statement('ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_type_check');
        DB::statement("ALTER TABLE configurations ADD CONSTRAINT configurations_type_check CHECK (type IN ('string', 'text', 'integer', 'boolean', 'json', 'file', 'email', 'url'))");
    }
};
