<?php

namespace App\Http\Controllers\Content\NewsEvents;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Traits\ContentHelpers;
use App\Http\Requests\ContentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class NewsEventController extends Controller
{
    use ContentHelpers;
    public function index(Request $request): Response
    {
        return Inertia::render('content/news-events/NewsEvents', [
            'newsEvents' => $this->getNewsEventsData($request),
            'categories' => $this->getCategories(),
            'types' => Content::getTypes(),
            'statuses' => Content::getStatuses(),
            'filters' => $request->only(['search', 'type', 'category', 'status']),
        ]);
    }

    public function getData(Request $request): JsonResponse
    {
        return response()->json($this->getNewsEventsData($request));
    }

    public function store(ContentRequest $request)
    {
        $type = $request->input('type', 'news');
        $requestData = $this->convertBooleanFormData($request);
        $request->merge($requestData);
        $validated = $request->validated();

        // Handle file upload
        if ($request->hasFile('featured_image')) {
            if ($imagePath = $this->handleFileUpload($request, $type)) {
                $validated['featured_image'] = $imagePath;
            }
        }

        // Set published_at if publishing
        if ($validated['is_published'] && $validated['status'] === 'published' && !isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $content = Content::create($validated);

        // Return appropriate response based on request type
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($content->type) . ' created successfully',
                'data' => $content,
            ]);
        }

        return redirect()->route('system.news-events.index')
            ->with('message', ucfirst($content->type) . ' created successfully');
    }

    public function show($content): JsonResponse
    {
        // Find content by ID since route model binding uses slug
        $content = Content::whereIn('type', ['news', 'event', 'article', 'announcement'])->findOrFail($content);
        
        // Add featured_image_url for frontend
        $content->featured_image_url = $content->featured_image ? asset('storage/' . $content->featured_image) : null;
        
        return response()->json([
            'data' => $content,
        ]);
    }

    public function update(ContentRequest $request, $content)
    {
        // Find content by ID since route model binding uses slug
        $content = Content::whereIn('type', ['news', 'event', 'article', 'announcement'])->findOrFail($content);
        
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

        // Return appropriate response based on request type
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => ucfirst($content->type) . ' updated successfully',
                'data' => $content,
            ]);
        }

        return back()->with('success', ucfirst($content->type) . ' updated successfully');
    }

    public function destroy($content): JsonResponse
    {
        try {
            $content = Content::whereIn('type', ['news', 'event', 'article', 'announcement'])->findOrFail($content);
            
            // Delete featured image if exists
            if ($content->featured_image && \Storage::disk('public')->exists($content->featured_image)) {
                \Storage::disk('public')->delete($content->featured_image);
            }
            
            $type = $content->type;
            $content->delete();

            return response()->json([
                'success' => true,
                'message' => ucfirst($type) . ' deleted successfully',
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'News or event not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete content: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:delete,publish,unpublish,archive,feature,unfeature',
            'ids' => 'required|array',
            'ids.*' => 'exists:contents,id',
        ]);

        $contents = Content::whereIn('id', $validated['ids']);
        $count = $contents->count();

        switch ($validated['action']) {
            case 'delete':
                $contents->delete();
                break;
            case 'publish':
                $contents->update(['is_published' => true, 'published_at' => now(), 'status' => 'published']);
                break;
            case 'unpublish':
                $contents->update(['is_published' => false, 'status' => 'draft']);
                break;
            case 'archive':
                $contents->update(['status' => 'archived']);
                break;
            case 'feature':
                $contents->update(['is_featured' => true]);
                break;
            case 'unfeature':
                $contents->update(['is_featured' => false]);
                break;
        }

        return response()->json([
            'message' => "{$count} items {$validated['action']}d successfully",
        ]);
    }

    private function getNewsEventsData(?Request $request = null)
    {
        $query = Content::query()
            ->whereIn('type', ['news', 'event', 'article', 'announcement']);

        if ($request) {
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('title_id', 'like', "%{$search}%")
                        ->orWhere('title_en', 'like', "%{$search}%")
                        ->orWhere('author', 'like', "%{$search}%")
                        ->orWhere('category', 'like', "%{$search}%");
                });
            }

            if ($request->filled('type')) {
                $query->where('type', $request->get('type'));
            }

            if ($request->filled('category')) {
                $query->where('category', $request->get('category'));
            }

            if ($request->filled('status')) {
                $query->where('status', $request->get('status'));
            }
        }

        $newsEvents = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();
        
        // Transform data for frontend
        $newsEvents->getCollection()->transform(function ($content) {
            return [
                'id' => $content->id,
                'type' => $content->type,
                'category' => $content->category,
                'title_id' => $content->title_id,
                'title_en' => $content->title_en,
                'excerpt_id' => $content->excerpt_id,
                'featured_image' => $content->featured_image,
                'author' => $content->author,
                'is_published' => $content->is_published,
                'is_featured' => $content->is_featured,
                'status' => $content->status,
                'published_at' => $content->published_at?->format('Y-m-d H:i'),
                'start_date' => $content->start_date?->format('Y-m-d H:i'),
                'end_date' => $content->end_date?->format('Y-m-d H:i'),
                'location_id' => $content->location_id,
                'organizer' => $content->organizer,
                'view_count' => $content->view_count,
                'created_at' => $content->created_at->format('Y-m-d H:i'),
            ];
        });
        
        return $newsEvents;
    }

    private function getCategories(): array
    {
        return [
            'news' => Content::getNewsCategories(),
            'event' => Content::getEventCategories(),
        ];
    }

}