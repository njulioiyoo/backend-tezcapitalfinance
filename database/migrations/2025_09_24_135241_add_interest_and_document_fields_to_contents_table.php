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
            $table->json('interest_list')->nullable()->after('gallery');
            $table->json('document_list')->nullable()->after('interest_list');
            $table->decimal('interest_rate', 5, 2)->nullable()->after('document_list');
            $table->string('service_duration')->nullable()->after('interest_rate');
            $table->text('requirements_id')->nullable()->after('service_duration');
            $table->text('requirements_en')->nullable()->after('requirements_id');
            $table->text('benefits_id')->nullable()->after('requirements_en');
            $table->text('benefits_en')->nullable()->after('benefits_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropColumn([
                'interest_list',
                'document_list',
                'interest_rate',
                'service_duration',
                'requirements_id',
                'requirements_en',
                'benefits_id',
                'benefits_en'
            ]);
        });
    }
};
