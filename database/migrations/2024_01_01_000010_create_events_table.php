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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('location')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date')->nullable();
            $table->string('organizer')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('registered_count')->default(0);
            $table->boolean('is_published')->default(false);
            $table->datetime('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('draft');
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['is_published', 'start_date']);
            $table->index(['status']);
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};