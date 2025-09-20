<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigurationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            $group = $request->query('group');
            $key = $request->query('key');
            
            if ($key) {
                // Filter by specific key
                $config = Configuration::where('key', $key)->first();
                if (!$config) {
                    return response()->json([
                        'success' => false,
                        'message' => "Configuration with key '{$key}' not found",
                    ], 404);
                }
                
                $data = [
                    $config->key => [
                        'value' => $config->getValue(),
                        'type' => $config->type,
                        'description' => $config->description,
                        'is_public' => $config->is_public,
                    ]
                ];
            } elseif ($group) {
                // Filter by group
                $configurations = Configuration::where('group', $group)->get();
                if ($configurations->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => "No configurations found for group '{$group}'",
                    ], 404);
                }
                
                $data = $configurations->mapWithKeys(function ($config) {
                    return [$config->key => [
                        'value' => $config->getValue(),
                        'type' => $config->type,
                        'description' => $config->description,
                        'is_public' => $config->is_public,
                    ]];
                })->toArray();
            } else {
                // Get all configurations, excluding certain groups
                $data = Configuration::getAllGrouped();
                unset($data['about'], $data['homepage']);
                
                // Keep only maintenance, general, and contact with filtered fields
                $filteredData = [];
                
                if (isset($data['maintenance'])) {
                    $filteredData['maintenance'] = $data['maintenance'];
                }
                
                if (isset($data['general'])) {
                    $generalFiltered = [];
                    $allowedGeneralKeys = ['app_name', 'app_description'];
                    foreach ($allowedGeneralKeys as $key) {
                        if (isset($data['general'][$key])) {
                            $generalFiltered[$key] = $data['general'][$key];
                        }
                    }
                    $filteredData['general'] = $generalFiltered;
                }
                
                if (isset($data['contact'])) {
                    $contactFiltered = [];
                    $allowedContactKeys = ['contact_phone', 'contact_email', 'contact_address', 'contact_whatsapp', 'social_media'];
                    foreach ($allowedContactKeys as $key) {
                        if (isset($data['contact'][$key])) {
                            $contactFiltered[$key] = $data['contact'][$key];
                        }
                    }
                    $filteredData['contact'] = $contactFiltered;
                }
                
                if (isset($data['banners'])) {
                    $filteredData['banners'] = $data['banners'];
                }
                
                $data = $filteredData;
            }
                
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Configurations retrieved successfully',
                'data' => $data,
                'response_time_ms' => $responseTime,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve configurations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function public(): JsonResponse
    {
        try {
            $configurations = Configuration::getPublic();

            return response()->json([
                'success' => true,
                'message' => 'Public configurations retrieved successfully',
                'data' => $configurations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve public configurations',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getByGroup(string $group): JsonResponse
    {
        try {
            $configurations = Configuration::getByGroup($group);

            return response()->json([
                'success' => true,
                'message' => "Configurations for group '{$group}' retrieved successfully",
                'data' => $configurations,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Failed to retrieve configurations for group '{$group}'",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(string $key): JsonResponse
    {
        try {
            $configuration = Configuration::where('key', $key)->first();

            if (!$configuration) {
                return response()->json([
                    'success' => false,
                    'message' => "Configuration with key '{$key}' not found",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Configuration retrieved successfully',
                'data' => [
                    'key' => $configuration->key,
                    'value' => $configuration->getValue(),
                    'type' => $configuration->type,
                    'group' => $configuration->group,
                    'description' => $configuration->description,
                    'is_public' => $configuration->is_public,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve configuration',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function branding(): JsonResponse
    {
        return $this->getByGroup(Configuration::GROUP_BRANDING);
    }

    public function general(): JsonResponse
    {
        return $this->getByGroup(Configuration::GROUP_GENERAL);
    }

    public function homepage(): JsonResponse
    {
        return $this->getByGroup(Configuration::GROUP_HOMEPAGE);
    }

    public function contact(): JsonResponse
    {
        return $this->getByGroup(Configuration::GROUP_CONTACT);
    }

    public function language(): JsonResponse
    {
        return $this->getByGroup(Configuration::GROUP_LANGUAGE);
    }

    public function maintenance(): JsonResponse
    {
        return $this->getByGroup(Configuration::GROUP_MAINTENANCE);
    }

    public function credit(): JsonResponse
    {
        return $this->getByGroup(Configuration::GROUP_CREDIT);
    }

    public function banners(): JsonResponse
    {
        return $this->getByGroup(Configuration::GROUP_BANNERS);
    }

    /**
     * Get services data from homepage (same as HomepageController)
     */
    private function getServicesFromHomepage(): array
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
            'items' => $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'title' => $service->title_id,
                    'excerpt' => $service->excerpt_id,
                    'content' => $service->content_id,
                    'featured_image' => $service->featured_image ? 'http://cms.tez-capital.web.local/storage/' . $service->featured_image : null,
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
     * Get FAQ data from homepage (same as HomepageController)
     */
    private function getFaqFromHomepage(): array
    {
        $faqItems = Configuration::get('homepage_faq_items', []);
        
        if (!is_array($faqItems)) {
            $faqItems = json_decode($faqItems, true) ?: [];
        }

        return [
            'enabled' => Configuration::get('homepage_faq_enabled', true),
            'title' => Configuration::get('homepage_faq_title_id', 'Pertanyaan yang Sering Diajukan'),
            'subtitle' => Configuration::get('homepage_faq_subtitle_id', 'Temukan jawaban atas pertanyaan umum tentang layanan TEZ Capital & Finance'),
            'items' => collect($faqItems)
                ->filter(function ($item) {
                    return ($item['is_active'] ?? true) === true;
                })
                ->map(function ($item, $index) {
                    return [
                        'id' => $item['id'] ?? ($index + 1),
                        'question' => $item['question_id'] ?? '',
                        'answer' => $item['answer_id'] ?? '',
                        'order' => $item['order'] ?? ($index + 1)
                    ];
                })
                ->sortBy('order')
                ->values()
                ->toArray()
        ];
    }
}