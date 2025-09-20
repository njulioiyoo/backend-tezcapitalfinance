<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Content;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing news categories to match the new demo site structure
        $categoryMapping = [
            'Company News' => 'company-activities',
            'Berita Perusahaan' => 'company-activities',
            'Industry Updates' => 'business',
            'Update Industri' => 'business', 
            'Financial Insights' => 'business',
            'Wawasan Keuangan' => 'business',
            'Press Release' => 'press-release',
            'Siaran Pers' => 'press-release',
            'Announcements' => 'highlights',
            'Pengumuman' => 'highlights',
            'breaking' => 'highlights',
            'update' => 'business',
            'announcement' => 'highlights',
            'press-release' => 'press-release',
            'general' => 'business',
        ];

        echo "Updating existing news categories...\n";

        foreach ($categoryMapping as $oldCategory => $newCategory) {
            $updated = Content::where('type', 'news')
                ->where('category', $oldCategory)
                ->update(['category' => $newCategory]);
                
            if ($updated > 0) {
                echo "Updated {$updated} items from '{$oldCategory}' to '{$newCategory}'\n";
            }
        }

        echo "News categories update completed!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the category mapping
        $reverseMapping = [
            'company-activities' => 'Company News',
            'business' => 'Industry Updates', 
            'press-release' => 'Press Release',
            'highlights' => 'Announcements',
        ];

        echo "Reverting news categories...\n";

        foreach ($reverseMapping as $newCategory => $oldCategory) {
            $updated = Content::where('type', 'news')
                ->where('category', $newCategory)
                ->update(['category' => $oldCategory]);
                
            if ($updated > 0) {
                echo "Reverted {$updated} items from '{$newCategory}' to '{$oldCategory}'\n";
            }
        }

        echo "News categories revert completed!\n";
    }
};
