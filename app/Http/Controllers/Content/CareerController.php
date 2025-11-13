<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class CareerController extends Controller
{
    public function index()
    {
        return Inertia::render('content/careers/Careers');
    }

    public function getData(Request $request)
    {
        $query = Content::careers()->with([])->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_id', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('department_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('location')) {
            $query->where('location_id', $request->location);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $careers = $query->paginate($perPage);

        return response()->json($careers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_id' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'department_id' => 'required|string|max:255',
            'excerpt_id' => 'nullable|string',
            'excerpt_en' => 'nullable|string',
            'content_id' => 'required|string',
            'content_en' => 'required|string',
            'requirements_id' => 'nullable|string',
            'requirements_en' => 'nullable|string',
            'benefits_id' => 'nullable|string',
            'benefits_en' => 'nullable|string',
            'location_id' => 'required|string|max:255',
            'location_en' => 'required|string|max:255',
            'tags' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_published' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $career = Content::create(array_merge($request->all(), [
            'type' => 'career',
            'published_at' => $request->is_published ? now() : null,
        ]));

        return response()->json([
            'message' => 'Career created successfully',
            'data' => $career
        ], 201);
    }

    public function show($id)
    {
        $content = Content::careers()->findOrFail($id);
        return response()->json(['data' => $content]);
    }

    public function update(Request $request, $id)
    {
        $content = Content::careers()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title_id' => 'required|string|max:255',
            'title_en' => 'required|string|max:255',
            'department_id' => 'required|string|max:255',
            'excerpt_id' => 'nullable|string',
            'excerpt_en' => 'nullable|string',
            'content_id' => 'required|string',
            'content_en' => 'required|string',
            'requirements_id' => 'nullable|string',
            'requirements_en' => 'nullable|string',
            'benefits_id' => 'nullable|string',
            'benefits_en' => 'nullable|string',
            'location_id' => 'required|string|max:255',
            'location_en' => 'required|string|max:255',
            'tags' => 'nullable|array',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_published' => 'boolean',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $content->update(array_merge($request->all(), [
            'published_at' => $request->is_published ? ($content->published_at ?? now()) : null,
        ]));

        return response()->json([
            'message' => 'Career updated successfully',
            'data' => $content->fresh()
        ]);
    }

    public function destroy($id)
    {
        $content = Content::careers()->findOrFail($id);
        $content->delete();

        return response()->json([
            'message' => 'Career deleted successfully'
        ]);
    }

    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,publish,unpublish,archive',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:contents,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $careers = Content::careers()->whereIn('id', $request->ids);

        switch ($request->action) {
            case 'delete':
                $careers->delete();
                $message = 'Selected careers deleted successfully';
                break;
            case 'publish':
                $careers->update([
                    'is_published' => true,
                    'status' => 'published',
                    'published_at' => now()
                ]);
                $message = 'Selected careers published successfully';
                break;
            case 'unpublish':
                $careers->update([
                    'is_published' => false,
                    'published_at' => null
                ]);
                $message = 'Selected careers unpublished successfully';
                break;
            case 'archive':
                $careers->update(['status' => 'archived']);
                $message = 'Selected careers archived successfully';
                break;
        }

        return response()->json(['message' => $message]);
    }
}