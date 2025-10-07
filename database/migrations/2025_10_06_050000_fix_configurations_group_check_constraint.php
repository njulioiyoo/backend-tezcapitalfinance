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
        DB::statement('ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_group_check');
        
        // Add new check constraint with all the required groups
        DB::statement("ALTER TABLE configurations ADD CONSTRAINT configurations_group_check CHECK (\"group\" IN ('general', 'branding', 'homepage', 'credit', 'maintenance', 'contact', 'language', 'about', 'banners', 'ojk', 'join_us', 'careers'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the constraint and add back the old one
        DB::statement('ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_group_check');
        DB::statement("ALTER TABLE configurations ADD CONSTRAINT configurations_group_check CHECK (\"group\" IN ('general', 'branding', 'homepage', 'credit', 'maintenance', 'contact', 'language', 'about', 'banners', 'ojk', 'join_us'))");
    }
};
