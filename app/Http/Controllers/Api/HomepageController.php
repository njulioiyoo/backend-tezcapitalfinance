<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    /**
     * Get all homepage data in a single API call
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            $language = $request->get('lang', 'id'); // Default to Indonesian
            $bilingualEnabled = Configuration::get('bilingual_enabled', false);

            $data = [
                'hero' => $this->getHeroData($language, $bilingualEnabled),
                'banners' => $this->getBannerData($language, $bilingualEnabled),
                'six_reasons' => $this->getSixReasonsData($language, $bilingualEnabled),
                'application_process' => $this->getApplicationProcessData($language, $bilingualEnabled),
                'services' => $this->getServicesData($language, $bilingualEnabled),
                'partners' => $this->getPartnersData($language, $bilingualEnabled),
                'news' => $this->getNewsData($language, $bilingualEnabled),
                'faq' => $this->getFaqData($language, $bilingualEnabled),
                'meta' => [
                    'language' => $language,
                    'bilingual_enabled' => $bilingualEnabled,
                    'generated_at' => now()->toISOString()
                ]
            ];
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Homepage data retrieved successfully',
                'data' => $data,
                'response_time_ms' => $responseTime,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve homepage data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get hero section data
     */
    private function getHeroData(string $language, bool $bilingualEnabled): array
    {
        return [
            'title' => Configuration::get('homepage_hero_title', 'Welcome to TEZ Capital'),
            'subtitle' => Configuration::get('homepage_hero_subtitle', 'Your Trusted Financial Partner'),
            'description' => Configuration::get('homepage_hero_description', ''),
            'background_image' => Configuration::get('homepage_hero_background', ''),
            'cta_primary' => [
                'text' => Configuration::get('homepage_hero_cta_primary_text', 'Get Started'),
                'url' => Configuration::get('homepage_hero_cta_primary_url', '#contact')
            ],
            'cta_secondary' => [
                'text' => Configuration::get('homepage_hero_cta_secondary_text', 'Learn More'),
                'url' => Configuration::get('homepage_hero_cta_secondary_url', '#about')
            ]
        ];
    }

    /**
     * Get banner slideshow data
     */
    private function getBannerData(string $language, bool $bilingualEnabled): array
    {
        $banners = Configuration::get('homepage_banners', []);
        
        if (!is_array($banners)) {
            $banners = json_decode($banners, true) ?: [];
        }

        return [
            'enabled' => !empty($banners),
            'items' => collect($banners)->map(function ($banner, $index) {
                // Get banner image from separate configuration (same pattern as frontend)
                $imageKey = "homepage_banner_" . ($index + 1) . "_image";
                $image = Configuration::get($imageKey, $banner['image'] ?? '');
                
                // Convert relative path to full URL
                $imageUrl = '';
                if ($image) {
                    if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
                        $imageUrl = $image;
                    } else {
                        $imageUrl = config('app.url') . '/storage/' . $image;
                    }
                }
                
                return [
                    'title' => $banner['title'] ?? '',
                    'subtitle' => $banner['subtitle'] ?? '',
                    'image' => $imageUrl,
                    'link' => $banner['link'] ?? '',
                    'order' => $index + 1
                ];
            })->filter(function ($banner) {
                // Only include banners with images
                return !empty($banner['image']);
            })->values()->toArray()
        ];
    }

    /**
     * Get Six Reasons section data
     */
    private function getSixReasonsData(string $language, bool $bilingualEnabled): array
    {
        $sixReasons = Configuration::get('homepage_six_reasons_items', []);
        
        if (!is_array($sixReasons)) {
            $sixReasons = json_decode($sixReasons, true) ?: [];
        }

        $titleKey = $bilingualEnabled && $language === 'en' ? 'homepage_six_reasons_title_en' : 'homepage_six_reasons_title_id';
        $subtitleKey = $bilingualEnabled && $language === 'en' ? 'homepage_six_reasons_subtitle_en' : 'homepage_six_reasons_subtitle_id';

        return [
            'title' => Configuration::get($titleKey, 'Six Reasons to Choose TEZ'),
            'subtitle' => Configuration::get($subtitleKey, ''),
            'items' => collect($sixReasons)->map(function ($item, $index) use ($language, $bilingualEnabled) {
                // Get icon from separate configuration (same pattern as frontend)
                $iconKey = "homepage_six_reasons_item_" . ($index + 1) . "_icon";
                $icon = Configuration::get($iconKey, $item['icon'] ?? '');
                
                return [
                    'title' => $bilingualEnabled && $language === 'en' 
                        ? ($item['title_en'] ?? $item['title_id'] ?? '') 
                        : ($item['title_id'] ?? ''),
                    'description' => $bilingualEnabled && $language === 'en' 
                        ? ($item['description_en'] ?? $item['description_id'] ?? '') 
                        : ($item['description_id'] ?? ''),
                    'icon' => $icon,
                    'order' => $item['order'] ?? $index
                ];
            })->sortBy('order')->values()->toArray()
        ];
    }

    /**
     * Get Application Process section data
     */
    private function getApplicationProcessData(string $language, bool $bilingualEnabled): array
    {
        $applicationProcess = Configuration::get('homepage_app_process_steps', []);
        
        if (!is_array($applicationProcess)) {
            $applicationProcess = json_decode($applicationProcess, true) ?: [];
        }
        
        if (empty($applicationProcess)) {
            return [
                'title' => $bilingualEnabled && $language === 'en' 
                    ? Configuration::get('homepage_app_process_title_en', 'Application Process')
                    : Configuration::get('homepage_app_process_title_id', 'Proses Aplikasi'),
                'subtitle' => $bilingualEnabled && $language === 'en' 
                    ? Configuration::get('homepage_app_process_subtitle_en', '')
                    : Configuration::get('homepage_app_process_subtitle_id', ''),
                'steps' => []
            ];
        }

        return [
            'title' => $bilingualEnabled && $language === 'en' 
                ? Configuration::get('homepage_app_process_title_en', 'Application Process')
                : Configuration::get('homepage_app_process_title_id', 'Proses Aplikasi'),
            'subtitle' => $bilingualEnabled && $language === 'en' 
                ? Configuration::get('homepage_app_process_subtitle_en', '')
                : Configuration::get('homepage_app_process_subtitle_id', ''),
            'steps' => collect($applicationProcess)->map(function ($step, $index) use ($language, $bilingualEnabled) {
                // Get icon from separate configuration (same pattern as frontend)
                $iconKey = "homepage_app_process_step_" . ($index + 1) . "_icon";
                $icon = Configuration::get($iconKey, $step['icon'] ?? '');
                
                return [
                    'step' => $index + 1,
                    'title' => $bilingualEnabled && $language === 'en' 
                        ? ($step['title_en'] ?? $step['title_id'] ?? '') 
                        : ($step['title_id'] ?? ''),
                    'description' => $bilingualEnabled && $language === 'en' 
                        ? ($step['description_en'] ?? $step['description_id'] ?? '') 
                        : ($step['description_id'] ?? ''),
                    'icon' => $icon,
                    'order' => $step['order'] ?? $index + 1
                ];
            })->sortBy('order')->values()->toArray()
        ];
    }

    /**
     * Get services data (from Content model with type service)
     */
    private function getServicesData(string $language, bool $bilingualEnabled): array
    {
        $services = Content::where('type', 'service')
            ->where('is_published', true)
            ->where('status', 'published')
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order', 'asc')
            ->limit(6)
            ->get();

        return [
            'title' => Configuration::get('services_section_title', 'Our Services'),
            'subtitle' => Configuration::get('services_section_subtitle', ''),
            'items' => $services->map(function ($service) use ($language, $bilingualEnabled) {
                return [
                    'id' => $service->id,
                    'title' => $bilingualEnabled && $language === 'en' 
                        ? ($service->title_en ?? $service->title_id) 
                        : $service->title_id,
                    'excerpt' => $bilingualEnabled && $language === 'en' 
                        ? ($service->excerpt_en ?? $service->excerpt_id) 
                        : $service->excerpt_id,
                    'content' => $bilingualEnabled && $language === 'en' 
                        ? ($service->content_en ?? $service->content_id) 
                        : $service->content_id,
                    'featured_image' => $service->featured_image ? config('app.url') . '/storage/' . $service->featured_image : null,
                    'category' => $service->category,
                    'is_featured' => $service->is_featured,
                    'sort_order' => $service->sort_order,
                    'created_at' => $service->created_at?->toISOString(),
                    'updated_at' => $service->updated_at?->toISOString(),
                    'url' => url('/services/' . $service->id)
                ];
            })->toArray()
        ];
    }

    /**
     * Get partners data (from Content model with type partner)
     */
    private function getPartnersData(string $language, bool $bilingualEnabled): array
    {
        $partners = Content::where('type', 'partner')
            ->where('is_published', true)
            ->where('status', 'published')
            ->orderBy('is_featured', 'desc')
            ->orderBy('sort_order', 'asc')
            ->limit(12)
            ->get();

        return [
            'title' => Configuration::get('partners_section_title', 'Our Partners'),
            'subtitle' => Configuration::get('partners_section_subtitle', ''),
            'items' => $partners->map(function ($partner) use ($language, $bilingualEnabled) {
                return [
                    'id' => $partner->id,
                    'name' => $bilingualEnabled && $language === 'en' 
                        ? ($partner->title_en ?? $partner->title_id) 
                        : $partner->title_id,
                    'description' => $bilingualEnabled && $language === 'en' 
                        ? ($partner->excerpt_en ?? $partner->excerpt_id) 
                        : $partner->excerpt_id,
                    'logo' => $partner->featured_image ? config('app.url') . '/storage/' . $partner->featured_image : null,
                    'website' => $partner->source_url,
                    'category' => $partner->category,
                    'is_featured' => $partner->is_featured,
                    'sort_order' => $partner->sort_order
                ];
            })->toArray()
        ];
    }

    /**
     * Get latest news data (from Content model with type news)
     */
    private function getNewsData(string $language, bool $bilingualEnabled): array
    {
        $news = Content::where('type', 'news')
            ->where('is_published', true)
            ->where('status', 'published')
            ->orderBy('is_featured', 'desc')
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        return [
            'title' => Configuration::get('news_section_title', 'Latest News'),
            'subtitle' => Configuration::get('news_section_subtitle', ''),
            'items' => $news->map(function ($article) use ($language, $bilingualEnabled) {
                return [
                    'id' => $article->id,
                    'title' => $bilingualEnabled && $language === 'en' 
                        ? ($article->title_en ?? $article->title_id) 
                        : $article->title_id,
                    'excerpt' => $bilingualEnabled && $language === 'en' 
                        ? ($article->excerpt_en ?? $article->excerpt_id) 
                        : $article->excerpt_id,
                    'content' => $bilingualEnabled && $language === 'en' 
                        ? ($article->content_en ?? $article->content_id) 
                        : $article->content_id,
                    'featured_image' => $article->featured_image ? config('app.url') . '/storage/' . $article->featured_image : null,
                    'category' => $article->category,
                    'author' => $article->author,
                    'published_at' => $article->published_at?->toISOString(),
                    'published_at_human' => $article->published_at?->diffForHumans(),
                    'is_featured' => $article->is_featured,
                    'url' => url('/news/' . $article->id)
                ];
            })->toArray()
        ];
    }

    /**
     * Get FAQ section data
     */
    private function getFaqData(string $language, bool $bilingualEnabled): array
    {
        $faqItems = Configuration::get('homepage_faq_items', []);
        
        if (!is_array($faqItems)) {
            $faqItems = json_decode($faqItems, true) ?: [];
        }

        return [
            'enabled' => Configuration::get('homepage_faq_enabled', true),
            'title' => $bilingualEnabled && $language === 'en' 
                ? Configuration::get('homepage_faq_title_en', 'Frequently Asked Questions')
                : Configuration::get('homepage_faq_title_id', 'Pertanyaan yang Sering Diajukan'),
            'subtitle' => $bilingualEnabled && $language === 'en' 
                ? Configuration::get('homepage_faq_subtitle_en', 'Find answers to common questions about TEZ Capital & Finance services')
                : Configuration::get('homepage_faq_subtitle_id', 'Temukan jawaban atas pertanyaan umum tentang layanan TEZ Capital & Finance'),
            'items' => collect($faqItems)
                ->filter(function ($item) {
                    // Only include active items, default to true if is_active not set
                    return ($item['is_active'] ?? true) === true;
                })
                ->map(function ($item, $index) use ($language, $bilingualEnabled) {
                    return [
                        'id' => $item['id'] ?? ($index + 1),
                        'question' => $bilingualEnabled && $language === 'en' 
                            ? ($item['question_en'] ?? $item['question_id'] ?? '') 
                            : ($item['question_id'] ?? ''),
                        'answer' => $bilingualEnabled && $language === 'en' 
                            ? ($item['answer_en'] ?? $item['answer_id'] ?? '') 
                            : ($item['answer_id'] ?? ''),
                        'order' => $item['order'] ?? ($index + 1)
                    ];
                })
                ->sortBy('order')
                ->values()
                ->toArray()
        ];
    }


    /**
     * Get specific section data
     */
    public function getSection(Request $request, string $section): JsonResponse
    {
        try {
            $startTime = microtime(true);
            $language = $request->get('lang', 'id');
            $bilingualEnabled = Configuration::get('bilingual_enabled', false);

            $sectionData = match($section) {
                'hero' => $this->getHeroData($language, $bilingualEnabled),
                'banners' => $this->getBannerData($language, $bilingualEnabled),
                'six-reasons' => $this->getSixReasonsData($language, $bilingualEnabled),
                'application-process' => $this->getApplicationProcessData($language, $bilingualEnabled),
                'services' => $this->getServicesData($language, $bilingualEnabled),
                'partners' => $this->getPartnersData($language, $bilingualEnabled),
                'news' => $this->getNewsData($language, $bilingualEnabled),
                'faq' => $this->getFaqData($language, $bilingualEnabled),
                default => null
            };

            if ($sectionData === null) {
                return response()->json([
                    'success' => false,
                    'message' => "Section '{$section}' not found",
                    'data' => [
                        'available_sections' => ['hero', 'banners', 'six-reasons', 'application-process', 'services', 'partners', 'news', 'faq']
                    ]
                ], 404);
            }

            $data = [
                'section' => $section,
                $section => $sectionData,
                'meta' => [
                    'language' => $language,
                    'bilingual_enabled' => $bilingualEnabled,
                    'generated_at' => now()->toISOString()
                ]
            ];
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => "Homepage section '{$section}' retrieved successfully",
                'data' => $data,
                'response_time_ms' => $responseTime,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to retrieve homepage section '{$section}'",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}