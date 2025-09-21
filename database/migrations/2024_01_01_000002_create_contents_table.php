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
            $table->string('type'); // news, event, article, announcement, partner, service
            $table->string('category')->nullable();
            $table->string('slug')->unique();
            $table->string('title_id');
            $table->string('title_en')->nullable();
            $table->text('excerpt_id')->nullable();
            $table->text('excerpt_en')->nullable();
            $table->longText('content_id')->nullable();
            $table->longText('content_en')->nullable();
            $table->string('featured_image')->nullable();
            $table->json('gallery')->nullable();
            $table->json('tags')->nullable();
            $table->string('author')->nullable();
            $table->string('source_url')->nullable();
            $table->string('location_id')->nullable();
            $table->string('location_en')->nullable();
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
            $table->string('organizer')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('registered_count')->default(0);
            $table->boolean('is_published')->default(false);
            $table->datetime('published_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('draft'); // draft, published, archived, cancelled
            $table->string('meta_title_id')->nullable();
            $table->string('meta_title_en')->nullable();
            $table->text('meta_description_id')->nullable();
            $table->text('meta_description_en')->nullable();
            $table->integer('sort_order')->default(0);
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['type', 'is_published']);
            $table->index(['type', 'category']);
            $table->index(['status']);
            $table->index(['published_at']);
            $table->index(['is_featured']);
            $table->index(['slug']);
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