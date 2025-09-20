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
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('news'); // news, event
            $table->string('category')->nullable();
            $table->string('title_id')->nullable();
            $table->string('title_en')->nullable();
            $table->text('excerpt_id')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->text('content_id')->nullable();
            $table->text('content_en')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('tags')->nullable();
            $table->string('author')->nullable();
            $table->string('source_url')->nullable();
            
            // Event specific fields
            $table->string('location_id')->nullable();
            $table->string('location_en')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('organizer')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('registered_count')->default(0);
            
            // Publishing fields
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('draft'); // draft, published, archived
            
            // SEO fields
            $table->string('meta_title_id')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->text('meta_description_id')->nullable();
            $table->text('meta_description_en')->nullable();
            
            // Analytics
            $table->integer('sort_order')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('share_count')->default(0);
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'category']);
            $table->index(['type', 'is_published', 'published_at']);
            $table->index(['type', 'status']);
            $table->index(['is_featured', 'published_at']);
            $table->index(['start_date', 'end_date']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};
