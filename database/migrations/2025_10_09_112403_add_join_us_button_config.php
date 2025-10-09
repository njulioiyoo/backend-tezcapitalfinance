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
        // Insert the new configuration
        Configuration::updateOrCreate(
            ['key' => 'button_join_us_enabled'],
            [
                'key' => 'button_join_us_enabled',
                'value' => true,
                'type' => Configuration::TYPE_BOOLEAN,
                'group' => Configuration::GROUP_JOIN_US,
                'description' => 'Enable/disable Join Us button in header',
                'is_public' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the configuration
        Configuration::where('key', 'button_join_us_enabled')->delete();
    }
};
