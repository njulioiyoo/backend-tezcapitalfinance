<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Validation\Rule;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        // Get type from request parameter or route defaults
        $type = $request->get('type');
        
        // If no type in request, get from route defaults
        if (!$type) {
            $routeDefaults = $request->route()->defaults ?? [];
            $type = $routeDefaults['type'] ?? null;
        }
        
        // If no type specified, check current route name to determine default type
        if (!$type) {
            $routeName = $request->route()->getName();
            if (str_contains($routeName, 'partners')) {
                $type = 'partner';
            } elseif (str_contains($routeName, 'services')) {
                $type = 'service';
            } else {
                $type = 'news'; // Default fallback
            }
        }
        
        // Ensure type is always set for safety
        $type = $type ?: 'news';
        $query = Content::ofType($type);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search, $type) {
                $q->where('title_id', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%")
                    ->orWhere('excerpt_id', 'like', "%{$search}%")
                    ->orWhere('excerpt_en', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
                
                // Add location search for events
                if ($type === 'event') {
                    $q->orWhere('location_id', 'like', "%{$search}%")
                      ->orWhere('location_en', 'like', "%{$search}%")
                      ->orWhere('organizer', 'like', "%{$search}%");
                }
            });
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->get('category'));
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->get('status'));
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->featured();
        }

        // Event-specific filters
        if ($type === 'event') {
            if ($request->filled('date_from')) {
                $query->where('start_date', '>=', $request->get('date_from'));
            }
            if ($request->filled('date_to')) {
                $query->where('start_date', '<=', $request->get('date_to'));
            }
            if ($request->filled('event_status')) {
                switch ($request->get('event_status')) {
                    case 'upcoming':
                        $query->upcoming();
                        break;
                    case 'ongoing':
                        $query->ongoing();
                        break;
                    case 'past':
                        $query->pastEvents();
                        break;
                }
            }
        }

        // Default ordering for different content types
        $orderBy = match($type) {
            'event' => 'start_date',
            'partner' => 'sort_order',
            default => 'published_at',
        };
        $orderDir = match($type) {
            'event' => 'asc',
            'partner' => 'asc', 
            default => 'desc',
        };

        $contents = $query->orderBy('is_featured', 'desc')
            ->orderBy('sort_order', 'asc')
            ->orderBy($orderBy, $orderDir)
            ->paginate(15)
            ->withQueryString();

        $categories = match($type) {
            'event' => Content::getEventCategories(),
            'partner' => Content::getPartnerCategories(),
            'service' => Content::getServiceCategories(),
            default => Content::getNewsCategories(),
        };

        // Use specific component for partners and services
        $component = match($type) {
            'partner' => 'content/partners/Partners',
            'service' => 'content/services/Services',
            default => 'content/ContentBasic'
        };
        
        return Inertia::render($component, [
            'contents' => $contents,
            'type' => $type,
            'types' => Content::getTypes(),
            'categories' => $categories,
            'statuses' => Content::getStatuses(),
            'filters' => $request->only(['search', 'category', 'status', 'featured', 'date_from', 'date_to', 'event_status']),
            'bilingualEnabled' => Configuration::get('bilingual_enabled', false),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        // Determine type from route or request parameter
        $type = $request->get('type');
        
        // If no type specified, check current route name to determine default type
        if (!$type) {
            $routeName = $request->route()->getName();
            if (str_contains($routeName, 'partners')) {
                $type = 'partner';
            } elseif (str_contains($routeName, 'services')) {
                $type = 'service';
            } else {
                $type = 'news'; // Default fallback
            }
        }
        $categories = match($type) {
            'event' => Content::getEventCategories(),
            'partner' => Content::getPartnerCategories(),
            'service' => Content::getServiceCategories(),
            default => Content::getNewsCategories(),
        };

        // Use specific form component for partners (services use modal)
        $component = $type === 'partner' ? 'content/partners/PartnerForm' : 'content/ContentForm';
        
        return Inertia::render($component, [
            'content' => null,
            'type' => $type,
            'types' => Content::getTypes(),
            'categories' => $categories,
            'statuses' => Content::getStatuses(),
            'bilingualEnabled' => Configuration::get('bilingual_enabled', false),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $bilingualEnabled = Configuration::get('bilingual_enabled', false);
        $type = $request->get('type', 'news');

        $rules = [
            'type' => ['required', Rule::in(array_keys(Content::getTypes()))],
            'tags' => 'nullable|array',
            'author' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'status' => ['required', Rule::in(array_keys(Content::getStatuses()))],
            'sort_order' => 'integer|min:0',
            'gallery' => 'nullable|array',
        ];

        // Category validation based on type
        if ($type === 'event') {
            $rules['category'] = ['nullable', Rule::in(array_keys(Content::getEventCategories()))];
            $rules = array_merge($rules, [
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'organizer' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'max_participants' => 'nullable|integer|min:1',
                'registered_count' => 'integer|min:0',
            ]);
        } elseif ($type === 'partner') {
            $rules['category'] = ['nullable', Rule::in(array_keys(Content::getPartnerCategories()))];
            // For partners, only basic fields are required (title + featured_image)
            $rules['source_url'] = 'nullable|url'; // Partner website URL
        } elseif ($type === 'service') {
            $rules['category'] = ['nullable', Rule::in(array_keys(Content::getServiceCategories()))];
        } else {
            $rules['category'] = ['nullable', Rule::in(array_keys(Content::getNewsCategories()))];
        }

        // Add bilingual validation rules
        if ($bilingualEnabled) {
            $rules = array_merge($rules, [
                'title_id' => 'required|string|max:255',
                'title_en' => 'required|string|max:255',
                'excerpt_id' => 'nullable|string',
                'excerpt_en' => 'nullable|string',
                'content_id' => 'nullable|string',
                'content_en' => 'nullable|string',
                'meta_title_id' => 'nullable|string|max:60',
                'meta_title_en' => 'nullable|string|max:60',
                'meta_description_id' => 'nullable|string|max:160',
                'meta_description_en' => 'nullable|string|max:160',
            ]);

            if ($type === 'event') {
                $rules = array_merge($rules, [
                    'location_id' => 'nullable|string|max:255',
                    'location_en' => 'nullable|string|max:255',
                ]);
            }
        } else {
            $rules = array_merge($rules, [
                'title_id' => 'required|string|max:255',
                'excerpt_id' => 'nullable|string',
                'content_id' => 'nullable|string',
                'meta_title_id' => 'nullable|string|max:60',
                'meta_description_id' => 'nullable|string|max:160',
            ]);

            if ($type === 'event') {
                $rules['location_id'] = 'nullable|string|max:255';
            }
        }

        if ($request->hasFile('featured_image')) {
            $rules['featured_image'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        } elseif ($type === 'partner') {
            // For new partners, require featured_image (either file or existing path)
            $rules['featured_image'] = 'required';
        }

        // Handle boolean conversion from FormData strings
        $requestData = $request->all();
        if (isset($requestData['is_published'])) {
            $requestData['is_published'] = in_array($requestData['is_published'], ['1', 'true', true], true);
        }
        if (isset($requestData['is_featured'])) {
            $requestData['is_featured'] = in_array($requestData['is_featured'], ['1', 'true', true], true);
        }
        
        // Merge back to request for validation
        $request->merge($requestData);

        $validated = $request->validate($rules);

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            $folder = $type === 'partner' ? 'content/partner' : 'content';
            $imagePath = $request->file('featured_image')->store($folder, 'public');
            $validated['featured_image'] = $imagePath;
        }

        // Set published_at if publishing
        if ($validated['is_published'] && $validated['status'] === 'published' && !isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $content = Content::create($validated);

        // Return success response for AJAX requests
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'message' => ucfirst($type) . ' created successfully',
                'data' => $content
            ], 201);
        }

        // Return to the appropriate index page based on type
        $routeName = match($type) {
            'service' => 'content.services.index',
            'partner' => 'content.partners.index',
            default => 'content.news-events.index'
        };
        
        return redirect()->route($routeName)->with('success', ucfirst($type) . ' created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $content): Response
    {
        $content->incrementViews();

        $categories = $content->isEvent() ? Content::getEventCategories() : Content::getNewsCategories();

        return Inertia::render('content/ContentDetail', [
            'content' => $content,
            'type' => $content->type,
            'types' => Content::getTypes(),
            'categories' => $categories,
            'statuses' => Content::getStatuses(),
            'bilingualEnabled' => Configuration::get('bilingual_enabled', false),
            'currentLanguage' => Configuration::get('default_language', 'id'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Content $content): Response
    {
        $categories = match($content->type) {
            'event' => Content::getEventCategories(),
            'partner' => Content::getPartnerCategories(),
            'service' => Content::getServiceCategories(),
            default => Content::getNewsCategories(),
        };

        // Use specific form component for partners (services use modal)
        $component = $content->type === 'partner' ? 'content/partners/PartnerForm' : 'content/ContentForm';

        return Inertia::render($component, [
            'content' => $content,
            'type' => $content->type,
            'types' => Content::getTypes(),
            'categories' => $categories,
            'statuses' => Content::getStatuses(),
            'bilingualEnabled' => Configuration::get('bilingual_enabled', false),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Content $content)
    {

        $bilingualEnabled = Configuration::get('bilingual_enabled', false);
        $type = $content->type;

        $rules = [
            'tags' => 'nullable|array',
            'author' => 'nullable|string|max:255',
            'source_url' => 'nullable|url',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'nullable|date',
            'status' => ['required', Rule::in(array_keys(Content::getStatuses()))],
            'sort_order' => 'integer|min:0',
            'gallery' => 'nullable|array',
        ];

        // Category validation based on type
        if ($type === 'event') {
            $rules['category'] = ['nullable', Rule::in(array_keys(Content::getEventCategories()))];
            $rules = array_merge($rules, [
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'organizer' => 'nullable|string|max:255',
                'price' => 'nullable|numeric|min:0',
                'max_participants' => 'nullable|integer|min:1',
                'registered_count' => 'integer|min:0',
            ]);
        } elseif ($type === 'partner') {
            $rules['category'] = ['nullable', Rule::in(array_keys(Content::getPartnerCategories()))];
            // For partners, only basic fields are required (title + featured_image)
            $rules['source_url'] = 'nullable|url'; // Partner website URL
        } elseif ($type === 'service') {
            $rules['category'] = ['nullable', Rule::in(array_keys(Content::getServiceCategories()))];
        } else {
            $rules['category'] = ['nullable', Rule::in(array_keys(Content::getNewsCategories()))];
        }

        // Add bilingual validation rules
        if ($bilingualEnabled) {
            $rules = array_merge($rules, [
                'title_id' => 'required|string|max:255',
                'title_en' => 'required|string|max:255',
                'excerpt_id' => 'nullable|string',
                'excerpt_en' => 'nullable|string',
                'content_id' => 'nullable|string',
                'content_en' => 'nullable|string',
                'meta_title_id' => 'nullable|string|max:60',
                'meta_title_en' => 'nullable|string|max:60',
                'meta_description_id' => 'nullable|string|max:160',
                'meta_description_en' => 'nullable|string|max:160',
            ]);

            if ($type === 'event') {
                $rules = array_merge($rules, [
                    'location_id' => 'nullable|string|max:255',
                    'location_en' => 'nullable|string|max:255',
                ]);
            }
        } else {
            $rules = array_merge($rules, [
                'title_id' => 'required|string|max:255',
                'excerpt_id' => 'nullable|string',
                'content_id' => 'nullable|string',
                'meta_title_id' => 'nullable|string|max:60',
                'meta_description_id' => 'nullable|string|max:160',
            ]);

            if ($type === 'event') {
                $rules['location_id'] = 'nullable|string|max:255';
            }
        }

        if ($request->hasFile('featured_image')) {
            $rules['featured_image'] = 'image|mimes:jpeg,png,jpg,gif,svg|max:2048';
        }

        // Handle boolean conversion from FormData strings  
        $requestData = $request->all();
        if (isset($requestData['is_published'])) {
            $requestData['is_published'] = in_array($requestData['is_published'], ['1', 'true', true], true);
        }
        if (isset($requestData['is_featured'])) {
            $requestData['is_featured'] = in_array($requestData['is_featured'], ['1', 'true', true], true);
        }
        
        // Merge back to request for validation
        $request->merge($requestData);

        $validated = $request->validate($rules);

        // Handle image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($content->featured_image) {
                Storage::disk('public')->delete($content->featured_image);
            }

            $folder = $type === 'partner' ? 'content/partner' : 'content';
            $imagePath = $request->file('featured_image')->store($folder, 'public');
            $validated['featured_image'] = $imagePath;
        }

        // Set published_at if publishing for the first time
        if ($validated['is_published'] && $validated['status'] === 'published' && !$content->published_at) {
            $validated['published_at'] = now();
        }

        $content->update($validated);

        // Update event status if it's an event
        if ($content->isEvent()) {
            $content->updateEventStatus();
        }

        // Return success response for AJAX requests
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'message' => ucfirst($type) . ' updated successfully',
                'data' => $content
            ], 200);
        }

        // Return to the appropriate index page based on type
        $routeName = match($type) {
            'service' => 'content.services.index',
            'partner' => 'content.partners.index',
            default => 'content.news-events.index'
        };
        
        return redirect()->route($routeName)->with('success', ucfirst($type) . ' updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Content $content)
    {
        // Delete image if exists
        if ($content->featured_image) {
            Storage::disk('public')->delete($content->featured_image);
        }

        // Delete gallery images if exists
        if ($content->gallery) {
            foreach ($content->gallery as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $type = $content->type;
        $content->delete();

        // Return success response for AJAX requests
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json([
                'message' => ucfirst($type) . ' deleted successfully'
            ], 200);
        }

        // Return to the appropriate index page based on type
        $routeName = match($type) {
            'service' => 'content.services.index',
            'partner' => 'content.partners.index',
            default => 'content.news-events.index'
        };
        
        return redirect()->route($routeName)->with('success', ucfirst($type) . ' deleted successfully');
    }

    /**
     * Bulk actions for content
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:publish,unpublish,feature,unfeature,archive,delete,cancel',
            'ids' => 'required|array',
            'ids.*' => 'exists:contents,id',
        ]);

        $contents = Content::whereIn('id', $request->ids);
        $message = 'Action completed successfully';

        switch ($request->action) {
            case 'publish':
                $contents->update([
                    'is_published' => true,
                    'status' => 'published',
                    'published_at' => now(),
                ]);
                $message = 'Content published successfully';
                break;

            case 'unpublish':
                $contents->update([
                    'is_published' => false,
                    'status' => 'draft'
                ]);
                $message = 'Content unpublished successfully';
                break;

            case 'feature':
                $contents->update(['is_featured' => true]);
                $message = 'Content featured successfully';
                break;

            case 'unfeature':
                $contents->update(['is_featured' => false]);
                $message = 'Content unfeatured successfully';
                break;

            case 'archive':
                $contents->update(['status' => 'archived']);
                $message = 'Content archived successfully';
                break;

            case 'cancel':
                $contents->where('type', 'event')->update(['status' => 'cancelled']);
                $message = 'Events cancelled successfully';
                break;

            case 'delete':
                $contents->each(function ($item) {
                    if ($item->featured_image) {
                        Storage::disk('public')->delete($item->featured_image);
                    }
                    if ($item->gallery) {
                        foreach ($item->gallery as $image) {
                            Storage::disk('public')->delete($image);
                        }
                    }
                });
                $contents->delete();
                $message = 'Content deleted successfully';
                break;
        }

        return response()->json(['message' => $message]);
    }

    /**
     * API endpoint for frontend (news & events)
     */
    public function newsApi(Request $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            // Support both news and events, default to news only
            $type = $request->get('type', 'news');
            
            if ($type === 'all') {
                // Return both news and events
                $query = Content::whereIn('type', ['news', 'event'])->published();
            } elseif ($type === 'event') {
                // Return events only
                $query = Content::events()->published();
            } else {
                // Return news only (default)
                $query = Content::news()->published();
            }

            if ($request->filled('category')) {
                $query->byCategory($request->get('category'));
            }

            if ($request->filled('featured')) {
                $query->featured();
            }

            // Events-specific filter
            if ($request->filled('upcoming') && $type !== 'news') {
                $query->upcoming();
            }

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search, $type) {
                    $q->where('title_id', 'like', "%{$search}%")
                        ->orWhere('title_en', 'like', "%{$search}%");
                    
                    // Add location search for events
                    if ($type !== 'news') {
                        $q->orWhere('location_id', 'like', "%{$search}%")
                          ->orWhere('location_en', 'like', "%{$search}%");
                    }
                });
            }

            // Date filtering
            if ($request->filled('date_from')) {
                $query->whereDate('published_at', '>=', $request->get('date_from'));
            }
            
            if ($request->filled('date_to')) {
                $query->whereDate('published_at', '<=', $request->get('date_to'));
            }
            
            if ($request->filled('date')) {
                $query->whereDate('published_at', $request->get('date'));
            }

            // Different ordering for events vs news
            if ($type === 'event') {
                $content = $query->orderBy('is_featured', 'desc')
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('start_date', 'asc')
                    ->paginate(3);
            } else {
                $content = $query->orderBy('is_featured', 'desc')
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('published_at', 'desc')
                    ->paginate(3);
            }

            // Transform the data to include full image URLs
            $content->getCollection()->transform(function ($item) {
                $item->featured_image_url = $item->featured_image ? asset('storage/' . $item->featured_image) : null;
                return $item;
            });

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            $message = match($type) {
                'event' => 'Events data retrieved successfully',
                'all' => 'News and events data retrieved successfully',
                default => 'News data retrieved successfully'
            };

            // Get available categories based on type
            $categories = match($type) {
                'event' => Content::getEventCategories(),
                'all' => array_merge(Content::getNewsCategories(), Content::getEventCategories()),
                default => Content::getNewsCategories(),
            };

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $content,
                'categories' => $categories,
                'response_time_ms' => $responseTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve content data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API endpoint for frontend (services)
     */
    public function servicesApi(Request $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
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
                ->paginate(3);

            // Transform the data to include full image URLs
            $services->getCollection()->transform(function ($service) {
                $service->featured_image_url = $service->featured_image ? asset('storage/' . $service->featured_image) : null;
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
    public function serviceDetailApi(Request $request, $id): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            $service = Content::where('type', 'service')
                ->where('is_published', true)
                ->where('status', 'published')
                ->findOrFail($id);

            // Transform the data to include full image URLs
            $service->featured_image_url = $service->featured_image ? asset('storage/' . $service->featured_image) : null;
            
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
     * Upload image for rich text editor
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('content/images', 'public');

            return response()->json([
                'url' => Storage::disk('public')->url($path),
                'path' => $path,
            ]);
        }

        return response()->json([
            'message' => 'No file uploaded',
        ], 400);
    }

    /**
     * Update event statuses based on current date
     */
    public function updateEventStatuses(): JsonResponse
    {
        $events = Content::events()->where('status', '!=', 'cancelled')->get();

        foreach ($events as $event) {
            $event->updateEventStatus();
        }

        return response()->json([
            'message' => 'Event statuses updated successfully',
            'updated_count' => $events->count(),
        ]);
    }
}
