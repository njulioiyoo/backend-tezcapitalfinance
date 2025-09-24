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
        // Drop the existing constraint if it exists
        DB::statement("ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_group_check");

        // Add the new constraint including 'ojk' group
        DB::statement("
            ALTER TABLE configurations 
            ADD CONSTRAINT configurations_group_check 
            CHECK (\"group\" IN ('general', 'branding', 'homepage', 'credit', 'maintenance', 'contact', 'language', 'about', 'banners', 'ojk'))
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the constraint with 'ojk'
        DB::statement("ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_group_check");

        // Restore the original constraint without 'ojk'
        DB::statement("
            ALTER TABLE configurations 
            ADD CONSTRAINT configurations_group_check 
            CHECK (\"group\" IN ('general', 'branding', 'homepage', 'credit', 'maintenance', 'contact', 'language', 'about', 'banners'))
        ");
    }
};
