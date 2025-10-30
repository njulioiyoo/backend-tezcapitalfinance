<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContentRequest;
use App\Models\Configuration;
use App\Models\Content;
use App\Traits\ContentHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ContentController extends Controller
{
    use ContentHelpers;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $type = $this->determineContentType($request);
        $query = Content::ofType($type);

        $query = $this->applySearchFilters($query, $request, $type);
        $query = $this->applyStandardFilters($query, $request);
        
        if ($type === 'event') {
            $query = $this->applyEventFilters($query, $request);
        }

        [$orderBy, $orderDir] = $this->getOrderingForType($type);
        
        $contents = $query->orderBy('is_featured', 'desc')
            ->orderBy('sort_order', 'asc')
            ->orderBy($orderBy, $orderDir)
            ->paginate(15)
            ->withQueryString();


        $categories = $this->getCategoriesForType($type);
        $component = $this->getComponentForType($type);
        
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
        $type = $this->determineContentType($request);
        $categories = $this->getCategoriesForType($type);
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
    public function store(ContentRequest $request)
    {
        $type = $request->get('type', 'news');
        $requestData = $this->convertBooleanFormData($request);
        $request->merge($requestData);
        $validated = $request->validated();

        if ($imagePath = $this->handleFileUpload($request, $type)) {
            $validated['featured_image'] = $imagePath;
        }

        // Set published_at if publishing
        if ($validated['is_published'] && $validated['status'] === 'published') {
            if (!isset($validated['published_at']) || empty($validated['published_at'])) {
                $validated['published_at'] = now();
            }
        }

        $content = Content::create($validated);

        // Return success response for AJAX/Inertia requests
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->header('X-Inertia')) {
            return response()->json([
                'message' => ucfirst($type) . ' created successfully',
                'data' => $content
            ], 201);
        }

        $routeName = $this->getRouteNameForType($type);
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
        $categories = $this->getCategoriesForType($content->type);
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
    public function update(ContentRequest $request, $id)
    {
        $content = Content::findOrFail($id);
        $type = $content->type;
        $requestData = $this->convertBooleanFormData($request);
        $request->merge($requestData);
        $validated = $request->validated();

        if ($request->hasFile('featured_image')) {
            if ($content->featured_image) {
                Storage::disk('public')->delete($content->featured_image);
            }
            if ($imagePath = $this->handleFileUpload($request, $type)) {
                $validated['featured_image'] = $imagePath;
            }
        } else {
            // Don't update featured_image field if no new file upload
            unset($validated['featured_image']);
        }

        // Set published_at if publishing
        if ($validated['is_published'] && $validated['status'] === 'published') {
            if (!isset($validated['published_at']) || empty($validated['published_at'])) {
                // Only set current time if no published_at was provided and content doesn't have one
                if (!$content->published_at) {
                    $validated['published_at'] = now();
                }
            }
        }

        // Ensure show_credit_simulation is included
        if ($request->has('show_credit_simulation') && !isset($validated['show_credit_simulation'])) {
            $validated['show_credit_simulation'] = $request->boolean('show_credit_simulation');
        }
        
        $content->update($validated);

        // Update event status if it's an event
        if ($content->isEvent()) {
            $content->updateEventStatus();
        }

        // Return success response for AJAX/Inertia requests
        if ($request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest' || $request->header('X-Inertia')) {
            return response()->json([
                'message' => ucfirst($type) . ' updated successfully',
                'data' => $content
            ], 200);
        }

        $routeName = $this->getRouteNameForType($type);
        return redirect()->route($routeName)->with('success', ucfirst($type) . ' updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, $id)
    {
        $content = Content::find($id);
        if (!$content) {
            return redirect()->back()->with('error', 'Record not found');
        }
        
        try {
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

            $routeName = $this->getRouteNameForType($type);
            return redirect()->route($routeName)->with('success', ucfirst($type) . ' deleted successfully');
            
        } catch (\Exception $e) {
            \Log::error('Error deleting content: ' . $e->getMessage());
            return redirect()->back()->with('error', 'An error occurred while deleting the record');
        }
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
     * API endpoint for workplace content
     */
    public function workplaceApi(Request $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            $query = Content::workplace()->published();

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
                        ->orWhere('content_id', 'like', "%{$search}%")
                        ->orWhere('content_en', 'like', "%{$search}%")
                        ->orWhere('excerpt_id', 'like', "%{$search}%")
                        ->orWhere('excerpt_en', 'like', "%{$search}%");
                });
            }

            $content = $query->orderBy('is_featured', 'desc')
                ->orderBy('sort_order', 'asc')
                ->orderBy('published_at', 'desc')
                ->paginate(6);

            // Transform the data to include full image URLs
            $content->getCollection()->transform(function ($item) {
                $item->featured_image_url = $item->featured_image ? config('app.url') . '/storage/' . $item->featured_image : null;
                return $item;
            });

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Workplace data retrieved successfully',
                'data' => $content,
                'categories' => Content::getWorkplaceCategories(),
                'response_time_ms' => $responseTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve workplace data: ' . $e->getMessage(),
            ], 500);
        }
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
            } elseif ($type === 'announcement') {
                // Return announcements only
                $query = Content::where('type', 'announcement')->published();
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
                        ->orWhere('title_en', 'like', "%{$search}%")
                        ->orWhere('content_id', 'like', "%{$search}%")
                        ->orWhere('content_en', 'like', "%{$search}%")
                        ->orWhere('excerpt_id', 'like', "%{$search}%")
                        ->orWhere('excerpt_en', 'like', "%{$search}%");
                    
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
                $item->featured_image_url = $item->featured_image ? config('app.url') . '/storage/' . $item->featured_image : null;
                return $item;
            });

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            $message = match($type) {
                'event' => 'Events data retrieved successfully',
                'announcement' => 'Announcements data retrieved successfully',
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
                'url' => config('app.url') . '/storage/' . $path,
                'path' => $path,
            ]);
        }

        return response()->json([
            'message' => 'No file uploaded',
        ], 400);
    }

    /**
     * API endpoint for frontend (single news detail by slug)
     */
    public function showNews(Request $request, $slug): JsonResponse
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
            
            $news = Content::whereIn('type', ['news', 'announcement'])
                ->where('is_published', true)
                ->where('status', 'published')
                ->where('slug', $slug)
                ->firstOrFail();

            // Transform the data to include full image URLs
            $news->featured_image_url = $news->featured_image ? config('app.url') . '/storage/' . $news->featured_image : null;
            
            // Parse gallery images if they exist
            if ($news->gallery) {
                try {
                    $galleryImages = json_decode($news->gallery, true);
                    if (is_array($galleryImages)) {
                        $news->gallery_urls = array_map(function($image) {
                            return config('app.url') . '/storage/' . $image;
                        }, $galleryImages);
                    }
                } catch (\Exception $e) {
                    $news->gallery_urls = [];
                }
            } else {
                $news->gallery_urls = [];
            }
            
            // Parse tags if they exist
            if ($news->tags) {
                try {
                    $news->tags_array = json_decode($news->tags, true) ?: [];
                } catch (\Exception $e) {
                    $news->tags_array = [];
                }
            } else {
                $news->tags_array = [];
            }

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Content detail retrieved successfully',
                'data' => $news,
                'categories' => Content::getNewsCategories(),
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
                'message' => 'Content not found or failed to retrieve content detail: ' . $e->getMessage(),
            ], 404);
        }
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
