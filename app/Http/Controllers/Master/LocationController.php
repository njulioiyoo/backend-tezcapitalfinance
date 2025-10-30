<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LocationController extends Controller
{
    /**
     * Display a listing of the locations.
     */
    public function index()
    {
        return Inertia::render('master/locations/Locations');
    }

    /**
     * Get locations data for DataTable.
     */
    public function getData(Request $request)
    {
        $query = Location::query()->orderBy('sort_order', 'asc')->orderBy('name_id', 'asc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_id', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('province', 'like', "%{$search}%")
                  ->orWhere('description_id', 'like', "%{$search}%")
                  ->orWhere('description_en', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'true' || $request->is_active === '1');
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('province')) {
            $query->where('province', $request->province);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $locations = $query->paginate($perPage);

        return response()->json($locations);
    }

    /**
     * Store a newly created location.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name_id' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:locations,slug',
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name_id']);
        }

        $location = Location::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Location created successfully',
            'data' => $location
        ], 201);
    }

    /**
     * Display the specified location.
     */
    public function show($id)
    {
        $location = Location::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Location retrieved successfully',
            'data' => $location
        ]);
    }

    /**
     * Update the specified location.
     */
    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name_id' => 'required|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'slug' => 'nullable|string|max:255|unique:locations,slug,' . $id,
            'description_id' => 'nullable|string',
            'description_en' => 'nullable|string',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $validator->validated();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name_id']);
        }

        $location->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Location updated successfully',
            'data' => $location
        ]);
    }

    /**
     * Remove the specified location.
     */
    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location deleted successfully'
        ]);
    }

    /**
     * Bulk actions for locations.
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,activate,deactivate',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:locations,id'
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

        $locations = Location::whereIn('id', $ids);

        switch ($action) {
            case 'delete':
                $locations->delete();
                $message = 'Locations deleted successfully';
                break;
            case 'activate':
                $locations->update(['is_active' => true]);
                $message = 'Locations activated successfully';
                break;
            case 'deactivate':
                $locations->update(['is_active' => false]);
                $message = 'Locations deactivated successfully';
                break;
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid action'
                ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Get all active locations (for dropdowns).
     */
    public function getActive()
    {
        $locations = Location::active()->ordered()->get(['id', 'name_id', 'name_en', 'slug', 'city', 'province']);

        return response()->json([
            'success' => true,
            'data' => $locations
        ]);
    }
}
