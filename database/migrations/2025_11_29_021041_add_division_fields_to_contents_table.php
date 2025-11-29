<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            // Add division fields for team members
            $table->string('division_id', 20)->nullable()->after('position_en');
            $table->string('division_en', 20)->nullable()->after('division_id');
            
            // Add index for efficient filtering
            $table->index(['type', 'division_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropIndex(['type', 'division_id']);
            $table->dropColumn(['division_id', 'division_en']);
        });
    }
};
