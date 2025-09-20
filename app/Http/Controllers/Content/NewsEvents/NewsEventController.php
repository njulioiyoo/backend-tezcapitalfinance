<?php

namespace App\Http\Controllers\Content\NewsEvents;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NewsEventController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('content/news-events/NewsEvents', [
            'newsEvents' => $this->getNewsEventsData($request),
            'categories' => $this->getCategories(),
            'types' => Content::getTypes(),
            'statuses' => Content::getStatuses(),
            'filters' => $request->only(['search', 'type', 'category', 'status']),
            'auth' => [
                'user' => $request->user(),
            ],
        ]);
    }

    public function getData(Request $request): JsonResponse
    {
        return response()->json($this->getNewsEventsData($request));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:news,event,article,announcement',
            'category' => 'required|string|max:255',
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'excerpt_id' => 'required|string|max:500',
            'excerpt_en' => 'nullable|string|max:500',
            'content_id' => 'required|string',
            'content_en' => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB
            'author' => 'required|string|max:255',
            'location_id' => 'nullable|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'organizer' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'is_published' => 'nullable|in:0,1',
            'is_featured' => 'nullable|in:0,1',
            'status' => 'required|in:draft,published,archived,cancelled',
        ]);

        // Handle file upload
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $path = $file->store('content/news', 'public');
            $validated['featured_image'] = $path;
        }

        // Convert boolean values
        $validated['is_published'] = $validated['is_published'] === '1';
        $validated['is_featured'] = $validated['is_featured'] === '1';

        if ($validated['is_published'] && !isset($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $content = Content::create($validated);

        // Return Inertia response for web routes, JSON for API routes
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'message' => ucfirst($content->type) . ' created successfully',
                'data' => $content,
            ]);
        }

        return redirect()->route('system.news-events.index')
            ->with('message', ucfirst($content->type) . ' created successfully');
    }

    public function show(Content $content): JsonResponse
    {
        return response()->json([
            'data' => $content,
        ]);
    }

    public function update(Request $request, Content $content)
    {
        $validated = $request->validate([
            'type' => 'required|in:news,event,article,announcement',
            'category' => 'required|string|max:255',
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'excerpt_id' => 'required|string|max:500',
            'excerpt_en' => 'nullable|string|max:500',
            'content_id' => 'required|string',
            'content_en' => 'nullable|string',
            'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:5120', // 5MB
            'author' => 'required|string|max:255',
            'location_id' => 'nullable|string|max:255',
            'location_en' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'organizer' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'max_participants' => 'nullable|integer|min:1',
            'is_published' => 'nullable|in:0,1',
            'is_featured' => 'nullable|in:0,1',
            'status' => 'required|in:draft,published,archived,cancelled',
        ]);

        // Handle file upload
        if ($request->hasFile('featured_image')) {
            // Delete old image if exists
            if ($content->featured_image && \Storage::disk('public')->exists($content->featured_image)) {
                \Storage::disk('public')->delete($content->featured_image);
            }
            
            $file = $request->file('featured_image');
            $path = $file->store('content/news', 'public');
            $validated['featured_image'] = $path;
        }

        // Convert boolean values
        $validated['is_published'] = $validated['is_published'] === '1';
        $validated['is_featured'] = $validated['is_featured'] === '1';

        if ($validated['is_published'] && !$content->published_at) {
            $validated['published_at'] = now();
        }

        $content->update($validated);

        // Return Inertia response for web routes, JSON for API routes
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'message' => ucfirst($content->type) . ' updated successfully',
                'data' => $content->fresh(),
            ]);
        }

        return redirect()->route('system.news-events.index')
            ->with('message', ucfirst($content->type) . ' updated successfully');
    }

    public function destroy(Content $content): JsonResponse
    {
        $type = $content->type;
        $content->delete();

        return response()->json([
            'message' => ucfirst($type) . ' deleted successfully',
        ]);
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

    private function getNewsEventsData(Request $request = null)
    {
        $query = Content::query();

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