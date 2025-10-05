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
            $table->json('interest_table')->nullable()->after('fees_list');
            $table->json('document_table')->nullable()->after('interest_table');
            $table->json('fees_table')->nullable()->after('document_table');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn([
                'interest_table',
                'document_table',
                'fees_table'
            ]);
        });
    }
};
