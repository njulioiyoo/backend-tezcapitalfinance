<?php

namespace App\Http\Controllers\System\Menu;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\Menu\MenuItemReorderRequest;
use App\Http\Requests\System\Menu\MenuItemStoreRequest;
use App\Http\Requests\System\Menu\MenuItemUpdateRequest;
use App\Models\MenuItem;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class MenuController extends Controller
{
    /**
     * Display the menu management page.
     */
    public function index(): Response
    {
        $menuItems = MenuItem::getMenuTree();

        return Inertia::render('system/menu/MenuManager', [
            'menuItems' => $menuItems->map(fn ($item) => $item->toMenuArray()),
        ]);
    }

    /**
     * Get menu items via API with permission filtering.
     */
    public function api(): JsonResponse
    {
        $menuItems = MenuItem::getMenuTree();
        $user = auth()->user();
        
        // Filter menu items based on user permissions
        $filteredMenuItems = $menuItems->map(function ($item) use ($user) {
            $menuArray = $item->toMenuArray();
            
            // Filter children based on permissions first
            if (!empty($menuArray['children'])) {
                $menuArray['children'] = array_filter($menuArray['children'], function ($child) use ($user) {
                    return $this->canAccessMenuRoute($child['href'] ?? '', $user);
                });
                
                // Re-index array to avoid gaps
                $menuArray['children'] = array_values($menuArray['children']);
            }
            
            return $menuArray;
        })->filter(function ($menuArray) use ($user) {
            // Filter parent items after children are filtered
            return $this->canAccessMenuItemArray($menuArray, $user);
        });

        return response()->json([
            'data' => $filteredMenuItems->values(),
        ]);
    }
    
    /**
     * Check if user can access a menu item (works with array format after filtering children)
     */
    public function canAccessMenuItemArray($menuArray, $user): bool
    {
        if (!$user) return false;
        
        // Super admin can access everything
        if ($user->hasRole('super-admin')) return true;
        
        // Handle separators
        if ($menuArray['is_separator']) {
            return true;
        }
        
        // For parent items that originally had children
        if (isset($menuArray['children']) && !empty($menuArray['children'])) {
            // This is a parent with children, show it
            return true;
        }
        
        // For parent items that had children but now empty after filtering
        if (isset($menuArray['children']) && empty($menuArray['children']) && empty($menuArray['href'])) {
            // This was a parent item with children, but all children were filtered out
            return false;
        }
        
        // For items with href, check if user can access
        if (!empty($menuArray['href'])) {
            return $this->canAccessMenuRoute($menuArray['href'], $user);
        }
        
        // Default: show item
        return true;
    }
    
    /**
     * Check if user can access a specific route
     */
    public function canAccessMenuRoute(string $href, $user): bool
    {
        if (!$user || empty($href)) return false;
        
        // Super admin can access everything
        if ($user->hasRole('super-admin')) return true;
        
        // Define route to permission mapping
        $routePermissions = [
            // System routes
            '/system/users' => 'users.view',
            '/system/roles' => 'roles.view',
            '/system/permissions' => 'permissions.view',
            '/system/configurations' => 'configurations.view',
            '/system/menu' => 'menu.view',
            '/system/audit-log' => 'audit-log.view',
            '/system/news-events' => 'news-events.view',
            
            // Content routes
            '/content/about' => 'about.view',
            '/content/news-events' => 'news-events.view',
            '/content/partners' => 'partners.view',
            '/content/services' => 'services.view',
            
            // Legacy routes
            '/users' => 'users.view',
            '/roles' => 'roles.view',
            '/configurations' => 'configurations.view',
            '/menu-manager' => 'menu.view',
            '/audit-log' => 'audit-log.view',
            '/content' => 'content.view',
        ];
        
        // Get required permission for this route
        $requiredPermission = $routePermissions[$href] ?? null;
        
        if (!$requiredPermission) {
            // If no specific permission defined, allow access
            // (for dashboard, settings, etc.)
            return true;
        }
        
        return $user->can($requiredPermission);
    }

    /**
     * Get all menu items (flat structure for management)
     */
    public function all(): JsonResponse
    {
        $menuItems = MenuItem::active()->ordered()->get();

        return response()->json([
            'data' => $menuItems,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuItemStoreRequest $request): JsonResponse
    {
        $menuItem = MenuItem::create($request->validated());

        return response()->json([
            'message' => 'Menu item created successfully',
            'data' => $menuItem->toMenuArray(),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MenuItem $menuItem): JsonResponse
    {
        return response()->json([
            'data' => $menuItem->toMenuArray(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MenuItemUpdateRequest $request, MenuItem $menuItem): JsonResponse
    {
        $menuItem->update($request->validated());

        return response()->json([
            'message' => 'Menu item updated successfully',
            'data' => $menuItem->fresh()->toMenuArray(),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MenuItem $menuItem): JsonResponse
    {
        $menuItem->delete();

        return response()->json([
            'message' => 'Menu item deleted successfully',
        ]);
    }

    /**
     * Reorder menu items
     */
    public function reorder(MenuItemReorderRequest $request): JsonResponse
    {
        \Log::info('Reorder request received', $request->validated());

        foreach ($request->validated()['items'] as $item) {
            \Log::info("Updating menu item {$item['id']} to position {$item['position']}");
            MenuItem::where('id', $item['id'])->update(['position' => $item['position']]);
        }

        return response()->json([
            'message' => 'Menu items reordered successfully',
        ]);
    }
}
