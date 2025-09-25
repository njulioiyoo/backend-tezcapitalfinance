<?php

namespace App\Traits;

use App\Models\Content;
use Illuminate\Http\Request;

trait ContentHelpers
{
    /**
     * Determine content type from request or route
     */
    protected function determineContentType(Request $request): string
    {
        $type = $request->get('type');
        
        if (!$type) {
            $routeDefaults = $request->route()->defaults ?? [];
            $type = $routeDefaults['type'] ?? null;
        }
        
        if (!$type) {
            $routeName = $request->route()->getName();
            if (str_contains($routeName, 'partners')) {
                $type = 'partner';
            } elseif (str_contains($routeName, 'services')) {
                $type = 'service';
            } else {
                $type = 'news';
            }
        }
        
        return $type ?: 'news';
    }

    /**
     * Apply search filters to query
     */
    protected function applySearchFilters($query, Request $request, string $type)
    {
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search, $type) {
                $q->where('title_id', 'like', "%{$search}%")
                    ->orWhere('title_en', 'like', "%{$search}%")
                    ->orWhere('excerpt_id', 'like', "%{$search}%")
                    ->orWhere('excerpt_en', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%");
                
                if ($type === 'event') {
                    $q->orWhere('location_id', 'like', "%{$search}%")
                      ->orWhere('location_en', 'like', "%{$search}%")
                      ->orWhere('organizer', 'like', "%{$search}%");
                }
            });
        }

        return $query;
    }

    /**
     * Apply standard filters to query
     */
    protected function applyStandardFilters($query, Request $request)
    {
        if ($request->filled('category')) {
            $query->byCategory($request->get('category'));
        }

        if ($request->filled('status')) {
            $query->byStatus($request->get('status'));
        }

        if ($request->filled('featured')) {
            $query->featured();
        }

        return $query;
    }

    /**
     * Apply event-specific filters
     */
    protected function applyEventFilters($query, Request $request)
    {
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

        return $query;
    }

    /**
     * Get categories for content type
     */
    protected function getCategoriesForType(string $type): array
    {
        return match($type) {
            'event' => Content::getEventCategories(),
            'partner' => Content::getPartnerCategories(),
            'service' => Content::getServiceCategories(),
            default => Content::getNewsCategories(),
        };
    }

    /**
     * Get ordering configuration for content type
     */
    protected function getOrderingForType(string $type): array
    {
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

        return [$orderBy, $orderDir];
    }

    /**
     * Get component name for content type
     */
    protected function getComponentForType(string $type): string
    {
        return match($type) {
            'partner' => 'content/partners/Partners',
            'service' => 'content/services/Services',
            default => 'content/ContentBasic'
        };
    }

    /**
     * Get route name for content type
     */
    protected function getRouteNameForType(string $type): string
    {
        return match($type) {
            'service' => 'content.services.index',
            'partner' => 'content.partners.index',
            default => 'content.news-events.index'
        };
    }

    /**
     * Handle file upload for content
     */
    protected function handleFileUpload(Request $request, string $type): ?string
    {
        if (!$request->hasFile('featured_image')) {
            return null;
        }

        $folder = $type === 'partner' ? 'content/partner' : 'content';
        return $request->file('featured_image')->store($folder, 'public');
    }

    /**
     * Convert boolean form data
     */
    protected function convertBooleanFormData(Request $request): array
    {
        $data = $request->all();
        
        if (isset($data['is_published'])) {
            $data['is_published'] = in_array($data['is_published'], ['1', 'true', true], true);
        }
        
        if (isset($data['is_featured'])) {
            $data['is_featured'] = in_array($data['is_featured'], ['1', 'true', true], true);
        }
        
        if (isset($data['show_credit_simulation'])) {
            $data['show_credit_simulation'] = in_array($data['show_credit_simulation'], ['1', 'true', true], true);
        }

        return $data;
    }
}