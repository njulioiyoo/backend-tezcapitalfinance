<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * API endpoint for frontend (services)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            $limit = $request->get('limit', 5);
            \Log::info('Services API called with limit: ' . $limit);
            
            $query = Content::where('type', 'service')->where('is_published', true)->where('status', 'published');

            if ($request->filled('category')) {
                $query->byCategory($request->get('category'));
            }

            if ($request->filled('featured')) {
                $query->featured();
            }

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title_id', 'like', "%{$search}%")
                        ->orWhere('title_en', 'like', "%{$search}%")
                        ->orWhere('excerpt_id', 'like', "%{$search}%")
                        ->orWhere('excerpt_en', 'like', "%{$search}%");
                });
            }

            $services = $query->orderBy('is_featured', 'desc')
                ->orderBy('sort_order', 'asc')
                ->orderBy('published_at', 'desc')
                ->paginate($limit);

            // Transform the data to include full image URLs
            $services->getCollection()->transform(function ($service) {
                $service->featured_image_url = $service->featured_image ? config('app.url') . '/storage/' . $service->featured_image : null;
                return $service;
            });

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Services data retrieved successfully',
                'data' => $services,
                'categories' => Content::getServiceCategories(),
                'response_time_ms' => $responseTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve services data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API endpoint for frontend (single service detail)
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            $service = Content::where('type', 'service')
                ->where('is_published', true)
                ->where('status', 'published')
                ->findOrFail($id);

            // Transform the data to include full image URLs
            $service->featured_image_url = $service->featured_image ? config('app.url') . '/storage/' . $service->featured_image : null;
            
            // Parse gallery images if they exist
            if ($service->gallery) {
                try {
                    $galleryImages = json_decode($service->gallery, true);
                    if (is_array($galleryImages)) {
                        $service->gallery_urls = array_map(function($image) {
                            return asset('storage/' . $image);
                        }, $galleryImages);
                    }
                } catch (\Exception $e) {
                    $service->gallery_urls = [];
                }
            } else {
                $service->gallery_urls = [];
            }
            
            // Parse tags if they exist
            if ($service->tags) {
                try {
                    $service->tags_array = json_decode($service->tags, true) ?: [];
                } catch (\Exception $e) {
                    $service->tags_array = [];
                }
            } else {
                $service->tags_array = [];
            }
            
            // Process interest list - already array from model cast
            $service->interest_list_array = $service->interest_list ?: [];
            
            // Process document list - already array from model cast
            $service->document_list_array = $service->document_list ?: [];

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Service detail retrieved successfully',
                'data' => $service,
                'categories' => Content::getServiceCategories(),
                'response_time_ms' => $responseTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found or failed to retrieve service detail: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * API endpoint for frontend (single service detail by slug)
     */
    public function showBySlug(Request $request, $slug): JsonResponse
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
            
            $bilingualEnabled = \App\Models\Configuration::get('bilingual_enabled', false);
            
            $service = Content::where('type', 'service')
                ->where('is_published', true)
                ->where('status', 'published')
                ->where('slug', $slug)
                ->firstOrFail();

            // Transform the data to include full image URLs
            $service->featured_image_url = $service->featured_image ? config('app.url') . '/storage/' . $service->featured_image : null;
            
            // Parse gallery images if they exist
            if ($service->gallery) {
                try {
                    $galleryImages = json_decode($service->gallery, true);
                    if (is_array($galleryImages)) {
                        $service->gallery_urls = array_map(function($image) {
                            return asset('storage/' . $image);
                        }, $galleryImages);
                    }
                } catch (\Exception $e) {
                    $service->gallery_urls = [];
                }
            } else {
                $service->gallery_urls = [];
            }
            
            // Parse tags if they exist
            if ($service->tags) {
                try {
                    $service->tags_array = json_decode($service->tags, true) ?: [];
                } catch (\Exception $e) {
                    $service->tags_array = [];
                }
            } else {
                $service->tags_array = [];
            }
            
            // Process interest list - already array from model cast
            $service->interest_list_array = $service->interest_list ?: [];
            
            // Process document list - already array from model cast
            $service->document_list_array = $service->document_list ?: [];

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Service detail retrieved successfully',
                'data' => $service,
                'categories' => Content::getServiceCategories(),
                'meta' => [
                    'language' => $language,
                    'bilingual_enabled' => $bilingualEnabled,
                    'generated_at' => now()->toISOString(),
                    'api_version' => 'v1'
                ],
                'response_time_ms' => $responseTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found or failed to retrieve service detail: ' . $e->getMessage(),
            ], 404);
        }
    }
}