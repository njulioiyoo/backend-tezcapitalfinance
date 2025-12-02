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
            // Change division fields size from 20 to 25 characters
            $table->string('division_id', 25)->nullable()->change();
            $table->string('division_en', 25)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            // Revert division fields size back to 20 characters
            $table->string('division_id', 20)->nullable()->change();
            $table->string('division_en', 20)->nullable()->change();
        });
    }
};
