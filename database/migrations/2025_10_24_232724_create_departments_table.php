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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name_id')->comment('Department name in Indonesian');
            $table->string('name_en')->nullable()->comment('Department name in English');
            $table->string('slug')->unique()->comment('URL-friendly slug');
            $table->text('description_id')->nullable()->comment('Description in Indonesian');
            $table->text('description_en')->nullable()->comment('Description in English');
            $table->boolean('is_active')->default(true)->comment('Active status');
            $table->integer('sort_order')->default(0)->comment('Display order');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('is_active');
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
