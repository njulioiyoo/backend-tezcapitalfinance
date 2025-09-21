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
        Schema::table('contents', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('category');
        });

        // Generate slugs for existing content
        $contents = DB::table('contents')->get();
        foreach ($contents as $content) {
            $title = $content->title_id ?: $content->title_en ?: 'content-' . $content->id;
            $slug = \Illuminate\Support\Str::slug($title);
            
            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (DB::table('contents')->where('slug', $slug)->where('id', '!=', $content->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            
            DB::table('contents')->where('id', $content->id)->update(['slug' => $slug]);
        }

        // Now make slug not nullable and unique
        Schema::table('contents', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
            $table->index(['slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contents', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropColumn('slug');
        });
    }
};