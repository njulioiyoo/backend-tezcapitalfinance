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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('href')->nullable();
            $table->string('icon')->nullable();
            $table->integer('position')->default(0);
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->onDelete('cascade');
            $table->string('badge')->nullable();
            $table->boolean('disabled')->default(false);
            $table->boolean('is_separator')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['parent_id', 'position']);
            $table->index(['is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_items');
    }
};