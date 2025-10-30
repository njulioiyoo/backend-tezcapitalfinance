<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TeamMemberController extends Controller
{
    /**
     * Process team member data to include full URLs for images and add testimonial fields
     */
    private function processTeamMemberData($teamMembers)
    {
        $baseUrl = config('app.url');
        
        if (is_iterable($teamMembers)) {
            foreach ($teamMembers as $member) {
                if ($member->featured_image) {
                    $member->featured_image = $baseUrl . '/storage/' . $member->featured_image;
                }
                
                // Add testimonial and position data for better frontend handling
                $member->testimonial = $member->testimonial_id ?? $member->excerpt_id;
                $member->testimonial_en = $member->testimonial_en ?? $member->excerpt_en;
                $member->position = $member->position_id ?? $member->department_id;
                $member->position_en = $member->position_en ?? $member->department_en;
            }
        } else {
            if ($teamMembers->featured_image) {
                $teamMembers->featured_image = $baseUrl . '/storage/' . $teamMembers->featured_image;
            }
            
            // Add testimonial and position data for better frontend handling
            $teamMembers->testimonial = $teamMembers->testimonial_id ?? $teamMembers->excerpt_id;
            $teamMembers->testimonial_en = $teamMembers->testimonial_en ?? $teamMembers->excerpt_en;
            $teamMembers->position = $teamMembers->position_id ?? $teamMembers->department_id;
            $teamMembers->position_en = $teamMembers->position_en ?? $teamMembers->department_en;
        }
        
        return $teamMembers;
    }
    /**
     * Get all team members
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Content::where('type', 'team-member')
                ->where('is_published', true)
                ->where('status', 'published')
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('department')) {
                $query->where('department_id', $request->department);
            }

            if ($request->filled('featured')) {
                $query->where('is_featured', true);
            }

            // Pagination
            $perPage = $request->get('per_page', 10);
            $teamMembers = $query->paginate($perPage);

            // Process team member data to include full URLs
            $processedItems = $this->processTeamMemberData($teamMembers->items());

            return response()->json([
                'success' => true,
                'message' => 'Team members retrieved successfully',
                'data' => $processedItems,
                'pagination' => [
                    'current_page' => $teamMembers->currentPage(),
                    'last_page' => $teamMembers->lastPage(),
                    'per_page' => $teamMembers->perPage(),
                    'total' => $teamMembers->total(),
                    'from' => $teamMembers->firstItem(),
                    'to' => $teamMembers->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team members: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured team members
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $limit = $request->get('limit', 5);
            
            $teamMembers = Content::where('type', 'team-member')
                ->where('is_published', true)
                ->where('status', 'published')
                ->where('is_featured', true)
                ->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            // Process team member data to include full URLs
            $processedTeamMembers = $this->processTeamMemberData($teamMembers);

            return response()->json([
                'success' => true,
                'message' => 'Featured team members retrieved successfully',
                'data' => $processedTeamMembers
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve featured team members: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get team member by slug
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        try {
            $teamMember = Content::where('type', 'team-member')
                ->where('slug', $slug)
                ->where('is_published', true)
                ->where('status', 'published')
                ->first();

            if (!$teamMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team member not found',
                ], 404);
            }

            // Increment view count
            $teamMember->incrementViews();

            // Process team member data to include full URLs
            $processedTeamMember = $this->processTeamMemberData($teamMember);

            return response()->json([
                'success' => true,
                'message' => 'Team member retrieved successfully',
                'data' => $processedTeamMember
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team member: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search team members
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $query = Content::where('type', 'team-member')
                ->where('is_published', true)
                ->where('status', 'published');

            // Search by name or department
            if ($request->filled('q')) {
                $searchTerm = $request->q;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title_id', 'like', "%{$searchTerm}%")
                      ->orWhere('title_en', 'like', "%{$searchTerm}%")
                      ->orWhere('department_id', 'like', "%{$searchTerm}%")
                      ->orWhere('department_en', 'like', "%{$searchTerm}%");
                });
            }

            // Apply filters
            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('department')) {
                $query->where('department_id', $request->department);
            }

            $query->orderBy('sort_order', 'asc')
                  ->orderBy('created_at', 'desc');

            // Pagination
            $perPage = $request->get('per_page', 10);
            $teamMembers = $query->paginate($perPage);

            // Process team member data to include full URLs
            $processedItems = $this->processTeamMemberData($teamMembers->items());

            return response()->json([
                'success' => true,
                'message' => 'Team members search completed successfully',
                'data' => $processedItems,
                'pagination' => [
                    'current_page' => $teamMembers->currentPage(),
                    'last_page' => $teamMembers->lastPage(),
                    'per_page' => $teamMembers->perPage(),
                    'total' => $teamMembers->total(),
                    'from' => $teamMembers->firstItem(),
                    'to' => $teamMembers->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search team members: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get team member statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $total = Content::where('type', 'team-member')
                ->where('is_published', true)
                ->where('status', 'published')
                ->count();

            $featured = Content::where('type', 'team-member')
                ->where('is_published', true)
                ->where('status', 'published')
                ->where('is_featured', true)
                ->count();

            $categories = Content::where('type', 'team-member')
                ->where('is_published', true)
                ->where('status', 'published')
                ->selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get()
                ->pluck('count', 'category');

            $departments = Content::where('type', 'team-member')
                ->where('is_published', true)
                ->where('status', 'published')
                ->selectRaw('department_id, COUNT(*) as count')
                ->groupBy('department_id')
                ->get()
                ->pluck('count', 'department_id');

            return response()->json([
                'success' => true,
                'message' => 'Team member statistics retrieved successfully',
                'data' => [
                    'total' => $total,
                    'featured' => $featured,
                    'categories' => $categories,
                    'departments' => $departments,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve team member statistics: ' . $e->getMessage(),
            ], 500);
        }
    }
}
