<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PostgreSQL doesn't support reordering columns directly
        // We need to recreate the table with correct order
        
        // First, backup the data
        DB::statement('CREATE TEMPORARY TABLE contents_backup AS SELECT * FROM contents');
        
        // Drop the existing table
        Schema::dropIfExists('contents');
        
        // Recreate table with correct column order
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
        
        // Restore the data
        DB::statement('
            INSERT INTO contents (
                id, type, category, slug, title_id, title_en, excerpt_id, excerpt_en,
                content_id, content_en, featured_image, gallery, tags, author,
                source_url, location_id, location_en, start_date, end_date, organizer,
                price, max_participants, registered_count, is_published, published_at,
                is_featured, status, meta_title_id, meta_title_en, meta_description_id,
                meta_description_en, sort_order, view_count, like_count, share_count,
                created_at, updated_at, deleted_at
            )
            SELECT 
                id, type, category, slug, title_id, title_en, excerpt_id, excerpt_en,
                content_id, content_en, featured_image, gallery, tags, author,
                source_url, location_id, location_en, start_date, end_date, organizer,
                price, max_participants, registered_count, is_published, published_at,
                is_featured, status, meta_title_id, meta_title_en, meta_description_id,
                meta_description_en, sort_order, view_count, like_count, share_count,
                created_at, updated_at, deleted_at
            FROM contents_backup
        ');
        
        // Reset the sequence
        DB::statement('SELECT setval(\'contents_id_seq\', (SELECT MAX(id) FROM contents))');
        
        // Drop the backup table
        DB::statement('DROP TABLE contents_backup');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse this migration easily
        // Would need to recreate with old order
        throw new Exception('Cannot reverse this migration. Column reordering is not reversible.');
    }
};