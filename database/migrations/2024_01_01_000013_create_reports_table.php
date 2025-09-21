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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('laporan-pengaduan, laporan-tahunan, laporan-keuangan');
            $table->string('title_id');
            $table->string('title_en')->nullable();
            $table->text('description_id')->nullable();
            $table->text('description_en')->nullable();
            $table->integer('year');
            $table->enum('period', ['monthly', 'quarterly', 'yearly'])->default('yearly');
            $table->integer('month')->nullable()->comment('1-12 for monthly reports');
            $table->integer('quarter')->nullable()->comment('1-4 for quarterly reports');
            $table->string('file_path')->nullable()->comment('Path to PDF file');
            $table->string('file_name')->nullable()->comment('Original filename');
            $table->bigInteger('file_size')->nullable()->comment('File size in bytes');
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['type', 'is_published']);
            $table->index(['year', 'period']);
            $table->index(['type', 'year', 'period']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};