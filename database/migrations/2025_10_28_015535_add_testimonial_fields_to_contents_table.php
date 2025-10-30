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
            // Add testimonial fields for team members
            $table->text('testimonial_id')->nullable()->after('content_en');
            $table->text('testimonial_en')->nullable()->after('testimonial_id');
            $table->string('position_id')->nullable()->after('testimonial_en');
            $table->string('position_en')->nullable()->after('position_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn(['testimonial_id', 'testimonial_en', 'position_id', 'position_en']);
        });
    }
};
