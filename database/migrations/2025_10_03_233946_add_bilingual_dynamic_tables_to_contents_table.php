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
            // Add bilingual versions of dynamic tables
            $table->json('interest_list_id')->nullable()->after('fees_table');
            $table->json('interest_list_en')->nullable()->after('interest_list_id');
            $table->json('document_list_id')->nullable()->after('interest_list_en');
            $table->json('document_list_en')->nullable()->after('document_list_id');
            $table->json('fees_list_id')->nullable()->after('document_list_en');
            $table->json('fees_list_en')->nullable()->after('fees_list_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn([
                'interest_list_id',
                'interest_list_en',
                'document_list_id',
                'document_list_en',
                'fees_list_id',
                'fees_list_en'
            ]);
        });
    }
};