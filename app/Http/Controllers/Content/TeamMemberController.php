<?php

namespace App\Http\Controllers\Content;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TeamMemberController extends Controller
{
    public function index()
    {
        return Inertia::render('content/team-members/TeamMembers');
    }

    public function getData(Request $request)
    {
        $query = Content::where('type', 'team-member')->with([])->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_id', 'like', "%{$search}%")
                  ->orWhere('title_en', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $teamMembers = $query->paginate($perPage);

        return response()->json($teamMembers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'department_id' => 'required|string|max:255',
            'department_en' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published,archived,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['type'] = 'team-member';
        $data['slug'] = Str::slug($data['title_id']);

        // Handle file upload
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $path = $file->store('team-members', 'public');
            $data['featured_image'] = $path;
        }

        // Set published_at if publishing
        if ($data['is_published'] && $data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $teamMember = Content::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Team member created successfully',
            'data' => $teamMember
        ], 201);
    }

    public function show($id)
    {
        $teamMember = Content::where('type', 'team-member')->findOrFail($id);
        
        return Inertia::render('content/team-members/TeamMemberDetail', [
            'teamMember' => $teamMember
        ]);
    }

    public function update(Request $request, $id)
    {
        $teamMember = Content::where('type', 'team-member')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title_id' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'category' => 'required|string|max:255',
            'department_id' => 'required|string|max:255',
            'department_en' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|in:draft,published,archived,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        $data['slug'] = Str::slug($data['title_id']);

        // Handle file upload
        if ($request->hasFile('featured_image')) {
            $file = $request->file('featured_image');
            $path = $file->store('team-members', 'public');
            $data['featured_image'] = $path;
        }

        // Set published_at if publishing
        if ($data['is_published'] && $data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $teamMember->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Team member updated successfully',
            'data' => $teamMember
        ]);
    }

    public function destroy($id)
    {
        $teamMember = Content::where('type', 'team-member')->findOrFail($id);
        $teamMember->delete();

        return response()->json([
            'success' => true,
            'message' => 'Team member deleted successfully'
        ]);
    }

    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,publish,unpublish,archive',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:contents,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $action = $request->action;
        $ids = $request->ids;

        $teamMembers = Content::where('type', 'team-member')->whereIn('id', $ids);

        switch ($action) {
            case 'delete':
                $teamMembers->delete();
                $message = 'Team members deleted successfully';
                break;
            case 'publish':
                $teamMembers->update([
                    'is_published' => true,
                    'status' => 'published',
                    'published_at' => now()
                ]);
                $message = 'Team members published successfully';
                break;
            case 'unpublish':
                $teamMembers->update([
                    'is_published' => false,
                    'status' => 'draft'
                ]);
                $message = 'Team members unpublished successfully';
                break;
            case 'archive':
                $teamMembers->update(['status' => 'archived']);
                $message = 'Team members archived successfully';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
