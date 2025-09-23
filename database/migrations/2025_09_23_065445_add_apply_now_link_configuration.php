<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Configuration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add apply_now_link configuration
        Configuration::create([
            'key' => 'apply_now_link',
            'value' => 'https://docs.google.com/forms/d/e/1FAIpQLSdknDsyg8EluPjeYKWQhl14TGnpN6_hXGdGyTyk8bnalvxGGw/viewform',
            'type' => Configuration::TYPE_URL,
            'group' => Configuration::GROUP_GENERAL,
            'description' => 'Apply Now button link URL',
            'is_public' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove apply_now_link configuration
        Configuration::where('key', 'apply_now_link')->delete();
    }
};
