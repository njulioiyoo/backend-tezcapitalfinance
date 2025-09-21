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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama pengadu');
            $table->string('email')->comment('Email pengadu');
            $table->string('phone')->nullable()->comment('Nomor telepon pengadu');
            $table->string('subject')->comment('Judul pengaduan');
            $table->text('message')->comment('Isi pengaduan/komentar');
            $table->enum('status', ['pending', 'in_review', 'resolved', 'rejected'])->default('pending')->comment('Status pengaduan');
            $table->text('admin_response')->nullable()->comment('Respon dari admin');
            $table->timestamp('responded_at')->nullable()->comment('Waktu respon admin');
            $table->unsignedBigInteger('responded_by')->nullable()->comment('Admin yang merespon');
            $table->string('ip_address')->nullable()->comment('IP address pengadu');
            $table->string('user_agent')->nullable()->comment('User agent pengadu');
            $table->timestamps();
            
            // Add indexes for better performance
            $table->index(['status', 'created_at']);
            $table->index(['email']);
            $table->index(['created_at']);
            
            // Foreign key for admin who responded
            $table->foreign('responded_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
