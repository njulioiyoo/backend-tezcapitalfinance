<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Content;
use Faker\Factory as Faker;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $faker = Faker::create();
        $fakerId = Faker::create('id_ID'); // Indonesian faker

        // Categories for news and events (matching the demo site structure)
        $newsCategories = ['business', 'company-activities', 'press-release', 'highlights'];
        $eventCategories = ['webinar', 'conference', 'workshop', 'seminar', 'training'];

        // News data templates
        $newsTemplates = [
            [
                'title_en' => 'TEZ Capital Expands Financing Solutions for Indonesian SMEs',
                'title_id' => 'TEZ Capital Perluas Solusi Pembiayaan untuk UKM Indonesia',
                'excerpt_en' => 'TEZ Capital announces new financing products specifically designed to support small and medium enterprises across Indonesia.',
                'excerpt_id' => 'TEZ Capital mengumumkan produk pembiayaan baru yang dirancang khusus untuk mendukung usaha kecil dan menengah di seluruh Indonesia.',
            ],
            [
                'title_en' => 'Strategic Partnership with Leading Banks Strengthens Market Position',
                'title_id' => 'Kemitraan Strategis dengan Bank Terkemuka Perkuat Posisi Pasar',
                'excerpt_en' => 'New partnerships with major financial institutions enhance TEZ Capital\'s ability to serve customers nationwide.',
                'excerpt_id' => 'Kemitraan baru dengan institusi keuangan besar meningkatkan kemampuan TEZ Capital melayani nasabah di seluruh negeri.',
            ],
            [
                'title_en' => 'TEZ Capital Receives Excellence Award for Customer Service',
                'title_id' => 'TEZ Capital Raih Penghargaan Excellence untuk Layanan Pelanggan',
                'excerpt_en' => 'Recognition for outstanding customer service and innovative financing solutions in the Indonesian market.',
                'excerpt_id' => 'Pengakuan atas layanan pelanggan yang luar biasa dan solusi pembiayaan inovatif di pasar Indonesia.',
            ]
        ];

        // Event data templates  
        $eventTemplates = [
            [
                'title_en' => 'Financial Literacy Workshop for SME Owners',
                'title_id' => 'Workshop Literasi Keuangan untuk Pemilik UKM',
                'excerpt_en' => 'Comprehensive workshop covering financial planning, investment strategies, and business growth for small business owners.',
                'excerpt_id' => 'Workshop komprehensif yang membahas perencanaan keuangan, strategi investasi, dan pertumbuhan bisnis untuk pemilik usaha kecil.',
                'location_en' => 'Jakarta Convention Center',
                'location_id' => 'Jakarta Convention Center',
            ],
            [
                'title_en' => 'Indonesia Banking & Finance Summit 2025',
                'title_id' => 'Summit Perbankan & Keuangan Indonesia 2025',
                'excerpt_en' => 'Annual summit bringing together industry leaders to discuss the future of banking and finance in Indonesia.',
                'excerpt_id' => 'Summit tahunan yang mempertemukan para pemimpin industri untuk membahas masa depan perbankan dan keuangan di Indonesia.',
                'location_en' => 'The Ritz-Carlton Jakarta',
                'location_id' => 'The Ritz-Carlton Jakarta',
            ]
        ];

        echo "Creating news content...\n";
        
        // Create 15 news items
        for ($i = 0; $i < 15; $i++) {
            $isTemplate = $i < count($newsTemplates);
            $template = $isTemplate ? $newsTemplates[$i % count($newsTemplates)] : null;
            
            $categoryIndex = $faker->numberBetween(0, count($newsCategories) - 1);
            $publishedAt = $faker->dateTimeBetween('-6 months', 'now');
            $isFeatured = $faker->boolean(30); // 30% chance of being featured
            $isPublished = $faker->boolean(90); // 90% chance of being published

            Content::create([
                'type' => 'news',
                'category' => $newsCategories[$categoryIndex],
                'title_id' => $template['title_id'] ?? $fakerId->sentence(6),
                'title_en' => $template['title_en'] ?? $faker->sentence(6),
                'excerpt_id' => $template['excerpt_id'] ?? $fakerId->paragraph(2),
                'excerpt_en' => $template['excerpt_en'] ?? $faker->paragraph(2),
                'content_id' => $fakerId->paragraphs(8, true),
                'content_en' => $faker->paragraphs(8, true),
                'featured_image' => 'news/news-' . ($i + 1) . '.jpg',
                'gallery' => json_encode([
                    'news/gallery-' . ($i + 1) . '-1.jpg',
                    'news/gallery-' . ($i + 1) . '-2.jpg',
                ]),
                'tags' => json_encode([
                    $faker->randomElement(['finance', 'banking', 'investment', 'SME', 'business']),
                    $faker->randomElement(['Indonesia', 'capital', 'growth', 'partnership', 'innovation'])
                ]),
                'author' => $faker->name(),
                'source_url' => $faker->optional()->url(),
                'is_published' => $isPublished,
                'published_at' => $isPublished ? $publishedAt : null,
                'is_featured' => $isFeatured,
                'status' => $isPublished ? 'published' : $faker->randomElement(['draft', 'review']),
                'meta_title_id' => $template['title_id'] ?? $fakerId->sentence(4),
                'meta_title_en' => $template['title_en'] ?? $faker->sentence(4),
                'meta_description_id' => $template['excerpt_id'] ?? $fakerId->sentence(10),
                'meta_description_en' => $template['excerpt_en'] ?? $faker->sentence(10),
                'sort_order' => $i + 1,
                'view_count' => $faker->numberBetween(50, 5000),
                'like_count' => $faker->numberBetween(5, 500),
                'share_count' => $faker->numberBetween(0, 100),
                'registered_count' => 0, // Not applicable for news
                'created_at' => $publishedAt,
                'updated_at' => $faker->dateTimeBetween($publishedAt, 'now'),
            ]);

            echo "Created news item " . ($i + 1) . "\n";
        }

        echo "Creating events content...\n";
        
        // Create 10 events
        for ($i = 0; $i < 10; $i++) {
            $isTemplate = $i < count($eventTemplates);
            $template = $isTemplate ? $eventTemplates[$i % count($eventTemplates)] : null;
            
            $categoryIndex = $faker->numberBetween(0, count($eventCategories) - 1);
            $startDate = $faker->dateTimeBetween('now', '+6 months');
            $endDate = (clone $startDate)->modify('+' . $faker->numberBetween(1, 5) . ' days');
            $maxParticipants = $faker->numberBetween(50, 500);
            $registeredCount = $faker->numberBetween(0, $maxParticipants - 10);
            $isPublished = $faker->boolean(95); // 95% chance of being published for events
            $isFeatured = $faker->boolean(40); // 40% chance of being featured

            Content::create([
                'type' => 'event',
                'category' => $eventCategories[$categoryIndex],
                'title_id' => $template['title_id'] ?? $fakerId->sentence(5),
                'title_en' => $template['title_en'] ?? $faker->sentence(5),
                'excerpt_id' => $template['excerpt_id'] ?? $fakerId->paragraph(2),
                'excerpt_en' => $template['excerpt_en'] ?? $faker->paragraph(2),
                'content_id' => $fakerId->paragraphs(6, true),
                'content_en' => $faker->paragraphs(6, true),
                'featured_image' => 'events/event-' . ($i + 1) . '.jpg',
                'gallery' => json_encode([
                    'events/gallery-' . ($i + 1) . '-1.jpg',
                    'events/gallery-' . ($i + 1) . '-2.jpg',
                    'events/gallery-' . ($i + 1) . '-3.jpg',
                ]),
                'tags' => json_encode([
                    $faker->randomElement(['seminar', 'workshop', 'training', 'conference', 'networking']),
                    $faker->randomElement(['finance', 'business', 'education', 'professional', 'growth'])
                ]),
                'author' => $faker->name(),
                'source_url' => null,
                'location_id' => $template['location_id'] ?? $fakerId->city() . ', Indonesia',
                'location_en' => $template['location_en'] ?? $faker->city() . ', Indonesia',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'organizer' => 'TEZ Capital & Finance',
                'price' => $faker->optional(0.3)->randomFloat(0, 100000, 2000000), // 30% chance of paid event
                'max_participants' => $maxParticipants,
                'registered_count' => $registeredCount,
                'is_published' => $isPublished,
                'published_at' => $isPublished ? $faker->dateTimeBetween('-1 month', 'now') : null,
                'is_featured' => $isFeatured,
                'status' => $isPublished ? 'published' : 'draft',
                'meta_title_id' => $template['title_id'] ?? $fakerId->sentence(4),
                'meta_title_en' => $template['title_en'] ?? $faker->sentence(4),
                'meta_description_id' => $template['excerpt_id'] ?? $fakerId->sentence(10),
                'meta_description_en' => $template['excerpt_en'] ?? $faker->sentence(10),
                'sort_order' => $i + 1,
                'view_count' => $faker->numberBetween(20, 2000),
                'like_count' => $faker->numberBetween(2, 200),
                'share_count' => $faker->numberBetween(0, 50),
                'created_at' => $faker->dateTimeBetween('-2 months', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 month', 'now'),
            ]);

            echo "Created event item " . ($i + 1) . "\n";
        }

        echo "Successfully created 15 news items and 10 events!\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all news and events created by this migration
        Content::whereIn('type', ['news', 'event'])->delete();
        echo "Removed all news and events data.\n";
    }
};