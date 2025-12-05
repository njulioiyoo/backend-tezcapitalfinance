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
        Schema::table('departments', function (Blueprint $table) {
            $table->text('about_team_description_id')->nullable()->after('description_en')->comment('About the Team description in Indonesian');
            $table->text('about_team_description_en')->nullable()->after('about_team_description_id')->comment('About the Team description in English');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['about_team_description_id', 'about_team_description_en']);
        });
    }
};
