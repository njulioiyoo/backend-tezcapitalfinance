<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Content;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class DownloadSampleImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:download-samples';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download sample images for news articles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to download sample images for news articles...');

        // Sample business/finance related images from placeholder services
        $sampleImages = [
            'news_business_1.jpg' => 'https://picsum.photos/800/600?random=1',
            'news_business_2.jpg' => 'https://picsum.photos/800/600?random=2', 
            'news_company_1.jpg' => 'https://picsum.photos/800/600?random=3',
            'news_company_2.jpg' => 'https://picsum.photos/800/600?random=4',
            'news_press_1.jpg' => 'https://picsum.photos/800/600?random=5',
            'news_press_2.jpg' => 'https://picsum.photos/800/600?random=6',
            'news_press_3.jpg' => 'https://picsum.photos/800/600?random=7',
            'news_highlights_1.jpg' => 'https://picsum.photos/800/600?random=8',
            'news_highlights_2.jpg' => 'https://picsum.photos/800/600?random=9',
            'news_highlights_3.jpg' => 'https://picsum.photos/800/600?random=10',
        ];

        $downloadedImages = [];

        foreach ($sampleImages as $filename => $url) {
            try {
                $this->info("Downloading {$filename}...");
                
                $response = Http::timeout(30)->get($url);
                
                if ($response->successful()) {
                    Storage::disk('public')->put("news/{$filename}", $response->body());
                    $downloadedImages[] = "news/{$filename}";
                    $this->info("✓ Downloaded: {$filename}");
                } else {
                    $this->error("✗ Failed to download: {$filename}");
                }
            } catch (\Exception $e) {
                $this->error("✗ Error downloading {$filename}: " . $e->getMessage());
            }
        }

        if (count($downloadedImages) > 0) {
            $this->info("\nUpdating news articles with downloaded images...");
            $this->updateNewsImages($downloadedImages);
        }

        $this->info("\n✅ Image download process completed!");
        $this->info("Downloaded " . count($downloadedImages) . " images");
        
        return 0;
    }

    private function updateNewsImages($downloadedImages)
    {
        $newsArticles = Content::where('type', 'news')->get();
        
        $imagesByCategory = [
            'business' => ['news/news_business_1.jpg', 'news/news_business_2.jpg'],
            'company-activities' => ['news/news_company_1.jpg', 'news/news_company_2.jpg'],
            'press-release' => ['news/news_press_1.jpg', 'news/news_press_2.jpg', 'news/news_press_3.jpg'],
            'highlights' => ['news/news_highlights_1.jpg', 'news/news_highlights_2.jpg', 'news/news_highlights_3.jpg'],
        ];

        foreach ($newsArticles as $index => $article) {
            $categoryImages = $imagesByCategory[$article->category] ?? $downloadedImages;
            $imageIndex = $index % count($categoryImages);
            
            $article->update([
                'featured_image' => $categoryImages[$imageIndex]
            ]);
            
            $this->info("Updated: {$article->title_id} -> {$categoryImages[$imageIndex]}");
        }
    }
}
