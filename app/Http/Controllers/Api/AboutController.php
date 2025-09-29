<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    /**
     * Get about page data for frontend consumption
     * 
     * @param Request $request
     * @return JsonResponse
     * 
     * @queryParam lang string The language code (id|en). Default: id
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "About page data retrieved successfully",
     *   "data": {
     *     "our_story": {
     *       "title": "Cerita Kami",
     *       "content": "<p>Content...</p>",
     *       "image": "http://cms.example.com/storage/configurations/xyz.png"
     *     },
     *     "vision": {...},
     *     "mission": {...},
     *     "logo_philosophy": {...},
     *     "closing_statement": "..."
     *   },
     *   "meta": {
     *     "language": "id",
     *     "bilingual_enabled": true,
     *     "generated_at": "2025-09-10T12:00:00.000Z"
     *   },
     *   "response_time_ms": 25.5
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            // Validate language parameter
            $language = $request->get('lang', 'id');
            $validLanguages = ['id', 'en'];
            if (!in_array($language, $validLanguages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language parameter. Supported languages: ' . implode(', ', $validLanguages),
                    'data' => [
                        'supported_languages' => $validLanguages
                    ]
                ], 400);
            }
            
            $bilingualEnabled = Configuration::get('bilingual_enabled', false);
            
            $aboutData = $this->getAboutData($language, $bilingualEnabled);
            
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);
            
            return response()->json([
                'success' => true,
                'message' => 'About page data retrieved successfully',
                'data' => $aboutData,
                'meta' => [
                    'language' => $language,
                    'bilingual_enabled' => $bilingualEnabled,
                    'generated_at' => now()->toISOString(),
                    'api_version' => 'v1'
                ],
                'response_time_ms' => $responseTime,
            ])->header('X-API-Version', 'v1')
              ->header('Cache-Control', 'public, max-age=300'); // Cache for 5 minutes
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch about data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get structured about page data
     */
    private function getAboutData(string $language, bool $bilingualEnabled): array
    {
        $configurations = $this->getAboutConfigurations();
        
        return [
            'our_story' => [
                'title' => $bilingualEnabled && $language === 'en' 
                    ? ($configurations['about_our_story_title_en'] ?? $configurations['about_our_story_title_id'] ?? '') 
                    : ($configurations['about_our_story_title_id'] ?? ''),
                'content' => $bilingualEnabled && $language === 'en' 
                    ? ($configurations['about_our_story_content_en'] ?? $configurations['about_our_story_content_id'] ?? '') 
                    : ($configurations['about_our_story_content_id'] ?? ''),
                'image' => $this->getImageUrl($configurations['about_our_story_image'] ?? ''),
            ],
            'vision' => [
                'title' => $bilingualEnabled && $language === 'en' 
                    ? ($configurations['about_vision_title_en'] ?? $configurations['about_vision_title_id'] ?? '') 
                    : ($configurations['about_vision_title_id'] ?? ''),
                'content' => $bilingualEnabled && $language === 'en' 
                    ? ($configurations['about_vision_content_en'] ?? $configurations['about_vision_content_id'] ?? '') 
                    : ($configurations['about_vision_content_id'] ?? ''),
            ],
            'mission' => [
                'title' => $bilingualEnabled && $language === 'en' 
                    ? ($configurations['about_mission_title_en'] ?? $configurations['about_mission_title_id'] ?? '') 
                    : ($configurations['about_mission_title_id'] ?? ''),
                'items' => $this->extractLanguageFromItems(
                    $configurations['about_mission_items'] ?? [], 
                    $language,
                    $bilingualEnabled
                ),
            ],
            'logo_philosophy' => [
                'title' => $bilingualEnabled && $language === 'en' 
                    ? ($configurations['about_logo_philosophy_title_en'] ?? $configurations['about_logo_philosophy_title_id'] ?? '') 
                    : ($configurations['about_logo_philosophy_title_id'] ?? ''),
                'image' => $this->getImageUrl($configurations['about_logo_philosophy_image'] ?? ''),
                'points' => $this->extractLanguageFromItems(
                    $configurations['about_logo_philosophy_points'] ?? [], 
                    $language,
                    $bilingualEnabled
                ),
            ],
            'fast_values' => [
                'title' => $bilingualEnabled && $language === 'en' 
                    ? ($configurations['about_fast_values_title_en'] ?? $configurations['about_fast_values_title_id'] ?? '') 
                    : ($configurations['about_fast_values_title_id'] ?? ''),
                'subtitle' => $bilingualEnabled && $language === 'en' 
                    ? ($configurations['about_fast_values_subtitle_en'] ?? $configurations['about_fast_values_subtitle_id'] ?? '') 
                    : ($configurations['about_fast_values_subtitle_id'] ?? ''),
                'items' => $this->extractLanguageFromValueItems(
                    $configurations['about_fast_values_items'] ?? [], 
                    $language,
                    $bilingualEnabled
                ),
            ],
            'idc_values' => [
                'title' => $bilingualEnabled && $language === 'en' 
                    ? ($configurations['about_idc_values_title_en'] ?? $configurations['about_idc_values_title_id'] ?? '') 
                    : ($configurations['about_idc_values_title_id'] ?? ''),
                'subtitle' => $bilingualEnabled && $language === 'en' 
                    ? ($configurations['about_idc_values_subtitle_en'] ?? $configurations['about_idc_values_subtitle_id'] ?? '') 
                    : ($configurations['about_idc_values_subtitle_id'] ?? ''),
                'items' => $this->extractLanguageFromValueItems(
                    $configurations['about_idc_values_items'] ?? [], 
                    $language,
                    $bilingualEnabled
                ),
            ],
            'closing_statement' => $bilingualEnabled && $language === 'en' 
                ? ($configurations['about_closing_statement_en'] ?? $configurations['about_closing_statement_id'] ?? '') 
                : ($configurations['about_closing_statement_id'] ?? ''),
        ];
    }


    /**
     * Convert storage path to full URL
     */
    private function getImageUrl(?string $path): string
    {
        if (!$path) {
            return '';
        }
        
        // If already a full URL, return as is
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        
        // Convert storage path to full URL
        return config('app.url') . '/storage/' . $path;
    }

    /**
     * Extract text for specific language from items array
     */
    private function extractLanguageFromItems(array $items, string $language, bool $bilingualEnabled): array
    {
        return array_map(function ($item) use ($language, $bilingualEnabled) {
            return [
                'id' => $item['id'] ?? null,
                'text' => $bilingualEnabled && $language === 'en' 
                    ? ($item['text_en'] ?? $item['text_id'] ?? '') 
                    : ($item['text_id'] ?? ''),
                'order' => $item['order'] ?? 0,
            ];
        }, $items);
    }

    /**
     * Extract data for specific language from value items array (F.A.S.T and I.D.C values)
     */
    private function extractLanguageFromValueItems(array $items, string $language, bool $bilingualEnabled): array
    {
        return array_map(function ($item) use ($language, $bilingualEnabled) {
            return [
                'id' => $item['id'] ?? null,
                'title' => $bilingualEnabled && $language === 'en' 
                    ? ($item['title_en'] ?? $item['title_id'] ?? '') 
                    : ($item['title_id'] ?? ''),
                'description' => $bilingualEnabled && $language === 'en' 
                    ? ($item['description_en'] ?? $item['description_id'] ?? '') 
                    : ($item['description_id'] ?? ''),
                'icon' => $this->getImageUrl($item['icon'] ?? ''),
                'order' => $item['order'] ?? 0,
            ];
        }, $items);
    }

    /**
     * Get all about configurations from database
     */
    private function getAboutConfigurations(): array
    {
        $keys = [
            'about_our_story_title_id',
            'about_our_story_title_en', 
            'about_our_story_content_id',
            'about_our_story_content_en',
            'about_our_story_image',
            'about_vision_title_id',
            'about_vision_title_en',
            'about_vision_content_id',
            'about_vision_content_en',
            'about_mission_title_id',
            'about_mission_title_en',
            'about_mission_items',
            'about_logo_philosophy_title_id',
            'about_logo_philosophy_title_en',
            'about_logo_philosophy_image',
            'about_logo_philosophy_points',
            'about_fast_values_title_id',
            'about_fast_values_title_en',
            'about_fast_values_subtitle_id',
            'about_fast_values_subtitle_en',
            'about_fast_values_items',
            'about_idc_values_title_id',
            'about_idc_values_title_en',
            'about_idc_values_subtitle_id',
            'about_idc_values_subtitle_en',
            'about_idc_values_items',
            'about_closing_statement_id',
            'about_closing_statement_en',
        ];

        $configurations = Configuration::whereIn('key', $keys)
            ->where('is_public', true)
            ->get()
            ->mapWithKeys(function ($config) {
                return [$config->key => $config->getValue()];
            })
            ->toArray();

        // Ensure JSON fields are arrays (they're already decoded by the model cast)
        foreach (['about_mission_items', 'about_logo_philosophy_points', 'about_fast_values_items', 'about_idc_values_items'] as $jsonKey) {
            if (isset($configurations[$jsonKey])) {
                $configurations[$jsonKey] = is_array($configurations[$jsonKey]) ? $configurations[$jsonKey] : [];
            }
        }

        return $configurations;
    }

    /**
     * Get specific section data
     * 
     * @param Request $request
     * @param string $section The section name (our-story|vision|mission|logo-philosophy|closing-statement)
     * @return JsonResponse
     * 
     * @queryParam lang string The language code (id|en). Default: id
     * 
     * @response 200 {
     *   "success": true,
     *   "message": "About section 'our-story' retrieved successfully",
     *   "data": {
     *     "section": "our-story",
     *     "our_story": {
     *       "title": "Cerita Kami",
     *       "content": "<p>Content...</p>",
     *       "image": "http://cms.example.com/storage/configurations/xyz.png"
     *     },
     *     "meta": {
     *       "language": "id",
     *       "bilingual_enabled": true,
     *       "generated_at": "2025-09-10T12:00:00.000Z"
     *     }
     *   },
     *   "response_time_ms": 15.2
     * }
     * 
     * @response 400 {
     *   "success": false,
     *   "message": "Invalid section. Valid sections: our-story, vision, mission, logo-philosophy, closing-statement",
     *   "data": {
     *     "available_sections": ["our-story", "vision", "mission", "logo-philosophy", "closing-statement"]
     *   }
     * }
     */
    public function getSection(Request $request, string $section): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            // Validate language parameter
            $language = $request->get('lang', 'id');
            $validLanguages = ['id', 'en'];
            if (!in_array($language, $validLanguages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid language parameter. Supported languages: ' . implode(', ', $validLanguages),
                    'data' => [
                        'supported_languages' => $validLanguages
                    ]
                ], 400);
            }
            
            $bilingualEnabled = Configuration::get('bilingual_enabled', false);

            // Validate section
            $validSections = ['our_story', 'our-story', 'vision', 'mission', 'logo_philosophy', 'logo-philosophy', 'fast_values', 'fast-values', 'idc_values', 'idc-values', 'closing_statement', 'closing-statement'];
            if (!in_array($section, $validSections)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid section. Valid sections: our-story, vision, mission, logo-philosophy, fast-values, idc-values, closing-statement',
                    'data' => [
                        'available_sections' => ['our-story', 'vision', 'mission', 'logo-philosophy', 'fast-values', 'idc-values', 'closing-statement']
                    ]
                ], 400);
            }

            // Normalize section name (convert hyphens to underscores)
            $normalizedSection = str_replace('-', '_', $section);

            $aboutData = $this->getAboutData($language, $bilingualEnabled);
            $sectionData = $aboutData[$normalizedSection] ?? null;

            if ($sectionData === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Section data not found'
                ], 404);
            }

            $data = [
                'section' => $section,
                $normalizedSection => $sectionData,
                'meta' => [
                    'language' => $language,
                    'bilingual_enabled' => $bilingualEnabled,
                    'generated_at' => now()->toISOString()
                ]
            ];

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => "About section '{$section}' retrieved successfully",
                'data' => array_merge($data, [
                    'meta' => array_merge($data['meta'], [
                        'api_version' => 'v1'
                    ])
                ]),
                'response_time_ms' => $responseTime,
            ])->header('X-API-Version', 'v1')
              ->header('Cache-Control', 'public, max-age=300'); // Cache for 5 minutes
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch section data: ' . $e->getMessage()
            ], 500);
        }
    }
}