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
            // Add department fields after location fields for careers
            $table->string('department_id')->nullable()->after('location_en');
            $table->string('department_en')->nullable()->after('department_id');
            
            // Add index for efficient filtering
            $table->index(['type', 'department_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropIndex(['type', 'department_id']);
            $table->dropColumn(['department_id', 'department_en']);
        });
    }
};
