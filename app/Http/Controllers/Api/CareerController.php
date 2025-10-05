<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CareerController extends Controller
{
    /**
     * Get all published careers with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            $limit = $request->get('limit', 12);
            
            $query = Content::careers()
                ->published()
                ->select([
                    'id',
                    'slug',
                    'title_id',
                    'title_en',
                    'excerpt_id',
                    'excerpt_en',
                    'content_id',
                    'content_en',
                    'location_id',
                    'location_en',
                    'department_id',
                    'department_en',
                    'tags',
                    'start_date',
                    'end_date',
                    'published_at',
                    'created_at'
                ])
                ->orderBy('published_at', 'desc');

            // Apply filters
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title_id', 'like', "%{$search}%")
                      ->orWhere('title_en', 'like', "%{$search}%")
                      ->orWhere('department_id', 'like', "%{$search}%")
                      ->orWhere('department_en', 'like', "%{$search}%")
                      ->orWhere('location_id', 'like', "%{$search}%")
                      ->orWhere('location_en', 'like', "%{$search}%");
                });
            }


            if ($request->filled('location')) {
                $query->where(function($q) use ($request) {
                    $q->where('location_id', 'like', "%{$request->location}%")
                      ->orWhere('location_en', 'like', "%{$request->location}%");
                });
            }

            if ($request->filled('department')) {
                $query->where(function($q) use ($request) {
                    $q->where('department_id', $request->department)
                      ->orWhere('department_en', $request->department)
                      ->orWhereJsonContains('tags', $request->department);
                });
            }

            if ($request->filled('featured')) {
                $query->featured();
            }

            // Pagination
            $careers = $query->paginate($limit);

            // Transform the data
            $careers->getCollection()->transform(function ($career) {
                $career->tags_array = $career->tags ?: [];
                return $career;
            });

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Careers data retrieved successfully',
                'data' => $careers,
                'departments' => ['Finance', 'People & Operation', 'Technology', 'Marketing', 'Sales'],
                'response_time_ms' => $responseTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve careers data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get career details by slug
     */
    public function show(string $slug): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            $career = Content::careers()
                ->published()
                ->where('slug', $slug)
                ->select([
                    'id',
                    'slug',
                    'title_id',
                    'title_en',
                    'content_id',
                    'content_en',
                    'requirements_id',
                    'requirements_en',
                    'benefits_id',
                    'benefits_en',
                    'location_id',
                    'location_en',
                    'department_id',
                    'department_en',
                    'tags',
                    'start_date',
                    'end_date',
                    'published_at',
                    'view_count',
                    'created_at',
                    'updated_at'
                ])
                ->first();

            if (!$career) {
                return response()->json([
                    'success' => false,
                    'message' => 'Career not found'
                ], 404);
            }

            // Transform arrays for tags
            $career->tags_array = $career->tags ?: [];

            // Increment view count
            $career->incrementViews();

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Career detail retrieved successfully',
                'data' => $career,
                'departments' => ['Finance', 'People & Operation', 'Technology', 'Marketing', 'Sales'],
                'response_time_ms' => $responseTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Career not found or failed to retrieve career detail: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Get featured/recent careers (for homepage/widgets)
     */
    public function featured(Request $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            $limit = min($request->get('limit', 6), 12); // Max 12 items

            $careers = Content::careers()
                ->published()
                ->select([
                    'id',
                    'slug',
                    'title_id',
                    'title_en',
                    'excerpt_id',
                    'excerpt_en',
                    'location_id',
                    'location_en',
                    'department_id',
                    'department_en',
                    'start_date',
                    'end_date',
                    'published_at'
                ])
                ->orderBy('published_at', 'desc')
                ->limit($limit)
                ->get();

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Featured careers retrieved successfully',
                'data' => $careers,
                'response_time_ms' => $responseTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve featured careers: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get career statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            $totalCareers = Content::careers()->published()->count();
            $totalDepartments = Content::careers()->published()->distinct('department_id')->count();
            $totalLocations = Content::careers()->published()->distinct('location_id')->count();
            
            $departmentsWithCount = Content::careers()
                ->published()
                ->selectRaw('department_id as department, COUNT(*) as count')
                ->whereNotNull('department_id')
                ->groupBy('department_id')
                ->orderBy('count', 'desc')
                ->get()
                ->pluck('count', 'department');

            $locationsWithCount = Content::careers()
                ->published()
                ->selectRaw('location_en as location, COUNT(*) as count')
                ->groupBy('location_en')
                ->orderBy('count', 'desc')
                ->get()
                ->pluck('count', 'location');

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Career statistics retrieved successfully',
                'data' => [
                    'total_careers' => $totalCareers,
                    'total_departments' => $totalDepartments,
                    'total_locations' => $totalLocations,
                    'departments' => $departmentsWithCount,
                    'locations' => $locationsWithCount,
                ],
                'response_time_ms' => $responseTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve career statistics: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search careers with advanced filtering
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $startTime = microtime(true);
            
            $query = Content::careers()
                ->published()
                ->select([
                    'id',
                    'slug',
                    'title_id',
                    'title_en',
                    'excerpt_id',
                    'excerpt_en',
                    'location_id',
                    'location_en',
                    'department_id',
                    'department_en',
                    'tags',
                    'start_date',
                    'end_date',
                    'published_at'
                ]);

            // Full text search
            if ($request->filled('q')) {
                $searchTerm = $request->q;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('title_id', 'like', "%{$searchTerm}%")
                      ->orWhere('title_en', 'like', "%{$searchTerm}%")
                      ->orWhere('content_id', 'like', "%{$searchTerm}%")
                      ->orWhere('content_en', 'like', "%{$searchTerm}%")
                      ->orWhere('department_id', 'like', "%{$searchTerm}%")
                      ->orWhere('department_en', 'like', "%{$searchTerm}%");
                });
            }

            // Filters
            if ($request->filled('departments')) {
                $departments = is_array($request->departments) ? $request->departments : [$request->departments];
                $query->whereIn('department_id', $departments);
            }

            if ($request->filled('locations')) {
                $locations = is_array($request->locations) ? $request->locations : [$request->locations];
                $query->where(function($q) use ($locations) {
                    foreach ($locations as $location) {
                        $q->orWhere('location_id', 'like', "%{$location}%")
                          ->orWhere('location_en', 'like', "%{$location}%");
                    }
                });
            }

            if ($request->filled('departments')) {
                $departments = is_array($request->departments) ? $request->departments : [$request->departments];
                $query->where(function($q) use ($departments) {
                    foreach ($departments as $department) {
                        $q->orWhere('department_id', $department)
                          ->orWhere('department_en', $department)
                          ->orWhereJsonContains('tags', $department);
                    }
                });
            }

            // Date range filter
            if ($request->filled('posted_after')) {
                $query->where('published_at', '>=', $request->posted_after);
            }

            if ($request->filled('deadline_before')) {
                $query->where('end_date', '<=', $request->deadline_before);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'published_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            $allowedSorts = ['published_at', 'start_date', 'end_date', 'title_en', 'view_count'];
            if (in_array($sortBy, $allowedSorts)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('published_at', 'desc');
            }

            $limit = min($request->get('limit', 12), 50);
            $careers = $query->paginate($limit);

            // Transform the data
            $careers->getCollection()->transform(function ($career) {
                $career->tags_array = $career->tags ?: [];
                return $career;
            });

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Career search completed successfully',
                'data' => $careers,
                'departments' => ['Finance', 'People & Operation', 'Technology', 'Marketing', 'Sales'],
                'response_time_ms' => $responseTime
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search careers: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get available filter options
     */
    protected function getAvailableCategories(): array
    {
        return Content::careers()
            ->published()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }

    protected function getAvailableLocations(): array
    {
        return Content::careers()
            ->published()
            ->distinct()
            ->pluck('location_en')
            ->filter()
            ->sort()
            ->values()
            ->toArray();
    }

    protected function getAvailableDepartments(): array
    {
        $tags = Content::careers()
            ->published()
            ->whereNotNull('tags')
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->filter()
            ->sort()
            ->values();

        return $tags->toArray();
    }
}