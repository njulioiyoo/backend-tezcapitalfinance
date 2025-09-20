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
        DB::statement("ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_group_check");
        DB::statement("ALTER TABLE configurations ADD CONSTRAINT configurations_group_check CHECK (\"group\" IN ('general', 'branding', 'homepage', 'credit', 'maintenance', 'contact', 'language', 'about', 'banners'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE configurations DROP CONSTRAINT IF EXISTS configurations_group_check");
        DB::statement("ALTER TABLE configurations ADD CONSTRAINT configurations_group_check CHECK (\"group\" IN ('general', 'branding', 'homepage', 'credit', 'maintenance', 'contact', 'language', 'about'))");
    }
};
