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
        // Drop the existing check constraint
        DB::statement("ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_group_check");
        
        // Add the new check constraint with 'careers' included
        DB::statement("ALTER TABLE configurations ADD CONSTRAINT configurations_group_check CHECK (\"group\" IN ('general', 'branding', 'homepage', 'credit', 'maintenance', 'contact', 'language', 'about', 'banners', 'ojk', 'join_us', 'careers'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the constraint with 'careers'
        DB::statement("ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_group_check");
        
        // Restore the original constraint without 'careers'
        DB::statement("ALTER TABLE configurations ADD CONSTRAINT configurations_group_check CHECK (\"group\" IN ('general', 'branding', 'homepage', 'credit', 'maintenance', 'contact', 'language', 'about', 'banners', 'ojk', 'join_us'))");
    }
};
