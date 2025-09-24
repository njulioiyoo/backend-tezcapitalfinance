<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Content extends Model implements Auditable
{
    use HasFactory, AuditableTrait, SoftDeletes;

    protected $fillable = [
        'type',
        'category',
        'slug',
        'title_id',
        'title_en',
        'excerpt_id',
        'excerpt_en',
        'content_id',
        'content_en',
        'featured_image',
        'gallery',
        'interest_list',
        'document_list',
        'interest_rate',
        'service_duration',
        'requirements_id',
        'requirements_en',
        'benefits_id',
        'benefits_en',
        'tags',
        'author',
        'source_url',
        'location_id',
        'location_en',
        'start_date',
        'end_date',
        'organizer',
        'price',
        'max_participants',
        'registered_count',
        'is_published',
        'published_at',
        'is_featured',
        'status',
        'meta_title_id',
        'meta_title_en',
        'meta_description_id',
        'meta_description_en',
        'sort_order',
        'view_count',
        'like_count',
        'share_count',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'gallery' => 'array',
            'interest_list' => 'array',
            'document_list' => 'array',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'published_at' => 'datetime',
            'price' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'max_participants' => 'integer',
            'registered_count' => 'integer',
            'view_count' => 'integer',
            'like_count' => 'integer',
            'share_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($content) {
            // Always generate slug when creating, even if one is provided
            $content->slug = $content->generateSlug($content->title_id ?: $content->title_en);
        });

        static::updating(function ($content) {
            // Only regenerate slug if title changed and no manual slug is set
            if ($content->isDirty(['title_id', 'title_en']) && !$content->isDirty('slug')) {
                $content->slug = $content->generateSlug($content->title_id ?: $content->title_en);
            }
        });
    }

    protected $dates = [
        'start_date',
        'end_date',
        'published_at',
        'deleted_at',
    ];

    // Scopes for content type
    public function scopeNews($query)
    {
        return $query->where('type', 'news');
    }

    public function scopeEvents($query)
    {
        return $query->where('type', 'event');
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope for published content
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->where('status', '!=', 'archived');
    }

    // Scope for featured content
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // Scope for filtering by category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Event-specific scopes
    public function scopeUpcoming($query)
    {
        return $query->where('type', 'event')
            ->where('start_date', '>', now())
            ->where('status', '!=', 'cancelled');
    }

    public function scopeOngoing($query)
    {
        return $query->where('type', 'event')
            ->where('start_date', '<=', now())
            ->where(function($q) {
                $q->whereNull('end_date')
                  ->orWhere('end_date', '>=', now());
            })
            ->where('status', '!=', 'cancelled');
    }

    public function scopePastEvents($query)
    {
        return $query->where('type', 'event')
            ->where(function($q) {
                $q->where('end_date', '<', now())
                  ->orWhere(function($subq) {
                      $subq->whereNull('end_date')
                           ->where('start_date', '<', now()->subDay());
                  });
            });
    }

    // Localization methods
    public function getLocalizedTitle($language = 'id')
    {
        return $language === 'en' ? $this->title_en : $this->title_id;
    }

    public function getLocalizedExcerpt($language = 'id')
    {
        return $language === 'en' ? $this->excerpt_en : $this->excerpt_id;
    }

    public function getLocalizedContent($language = 'id')
    {
        return $language === 'en' ? $this->content_en : $this->content_id;
    }

    public function getLocalizedLocation($language = 'id')
    {
        return $language === 'en' ? $this->location_en : $this->location_id;
    }

    public function getLocalizedMetaTitle($language = 'id')
    {
        return $language === 'en' ? $this->meta_title_en : $this->meta_title_id;
    }

    public function getLocalizedMetaDescription($language = 'id')
    {
        return $language === 'en' ? $this->meta_description_en : $this->meta_description_id;
    }

    // Analytics methods
    public function incrementViews()
    {
        $this->increment('view_count');
    }

    public function incrementLikes()
    {
        $this->increment('like_count');
    }

    public function incrementShares()
    {
        $this->increment('share_count');
    }

    // Event-specific methods
    public function updateEventStatus()
    {
        if ($this->type !== 'event') {
            return;
        }

        $now = now();
        
        if ($this->status === 'cancelled') {
            return; // Don't update cancelled events
        }
        
        if ($this->start_date > $now) {
            $this->status = 'published';
        } elseif ($this->end_date && $this->end_date < $now) {
            $this->status = 'archived';
        } elseif ($this->start_date <= $now && (!$this->end_date || $this->end_date >= $now)) {
            $this->status = 'published'; // ongoing
        }
        
        $this->save();
    }

    public function hasAvailableSlots()
    {
        if ($this->type !== 'event' || !$this->max_participants) {
            return true;
        }
        
        return $this->registered_count < $this->max_participants;
    }

    // Static methods for getting options
    public static function getTypes()
    {
        return [
            'news' => 'News',
            'event' => 'Event',
            'article' => 'Article',
            'announcement' => 'Announcement',
            'partner' => 'Partner',
            'service' => 'Service',
        ];
    }

    public static function getNewsCategories()
    {
        return [
            'business' => 'Bisnis',
            'company-activities' => 'Kegiatan Perusahaan',
            'press-release' => 'Siaran Pers',
            'highlights' => 'Highlights',
        ];
    }

    public static function getEventCategories()
    {
        return [
            'webinar' => 'Webinar',
            'conference' => 'Conference',
            'workshop' => 'Workshop',
            'meetup' => 'Meetup',
            'training' => 'Training',
            'seminar' => 'Seminar',
        ];
    }

    public static function getPartnerCategories()
    {
        return [
            'bank' => 'Bank',
            'insurance' => 'Insurance',
            'consulting' => 'Consulting',
            'technology' => 'Technology',
            'finance' => 'Finance',
            'government' => 'Government',
            'media' => 'Media',
            'other' => 'Other',
        ];
    }

    public static function getServiceCategories()
    {
        return [
            'financing' => 'Financing',
            'investment' => 'Investment',
            'insurance' => 'Insurance',
            'consultation' => 'Consultation',
            'trading' => 'Trading',
            'advisory' => 'Advisory',
            'other' => 'Other',
        ];
    }

    public static function getStatuses()
    {
        return [
            'draft' => 'Draft',
            'published' => 'Published',
            'archived' => 'Archived',
            'cancelled' => 'Cancelled',
        ];
    }

    // Get categories based on type
    public function getCategoriesForType()
    {
        return match($this->type) {
            'news', 'article', 'announcement' => self::getNewsCategories(),
            'event' => self::getEventCategories(),
            'partner' => self::getPartnerCategories(),
            'service' => self::getServiceCategories(),
            default => [],
        };
    }

    // Check if content is event
    public function isEvent()
    {
        return $this->type === 'event';
    }

    // Check if content is news
    public function isNews()
    {
        return $this->type === 'news';
    }

    // Check if content is partner
    public function isPartner()
    {
        return $this->type === 'partner';
    }

    // Check if content is service
    public function isService()
    {
        return $this->type === 'service';
    }

    // Scope for partners
    public function scopePartners($query)
    {
        return $query->where('type', 'partner');
    }

    // Scope for services
    public function scopeServices($query)
    {
        return $query->where('type', 'service');
    }

    /**
     * Analytics Methods for Dashboard
     */
    public static function getNewsStats(): array
    {
        $news = static::news();
        
        return [
            'total' => $news->count(),
            'published' => $news->published()->count(),
            'draft' => $news->where('status', 'draft')->count(),
            'featured' => $news->featured()->count(),
            'this_month' => $news->published()->whereMonth('published_at', now()->month)->count(),
            'this_week' => $news->published()->whereBetween('published_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'today' => $news->published()->whereDate('published_at', now()->toDateString())->count(),
        ];
    }

    public static function getEventsStats(): array
    {
        $events = static::events();
        
        return [
            'total' => $events->count(),
            'upcoming' => $events->upcoming()->count(),
            'ongoing' => $events->ongoing()->count(),
            'past' => $events->pastEvents()->count(),
            'published' => $events->published()->count(),
            'featured' => $events->featured()->count(),
            'this_month' => $events->whereMonth('start_date', now()->month)->count(),
            'cancelled' => $events->where('status', 'cancelled')->count(),
        ];
    }

    public static function getTrendingContent(int $limit = 10): array
    {
        return static::whereIn('type', ['news', 'event'])
            ->published()
            ->orderBy('view_count', 'desc')
            ->orderBy('like_count', 'desc')
            ->orderBy('share_count', 'desc')
            ->limit($limit)
            ->get(['id', 'type', 'title_id', 'title_en', 'view_count', 'like_count', 'share_count', 'published_at'])
            ->toArray();
    }

    public static function getRecentActivity(int $limit = 10): array
    {
        return static::whereIn('type', ['news', 'event'])
            ->published()
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get(['id', 'type', 'title_id', 'title_en', 'published_at', 'view_count', 'is_featured'])
            ->toArray();
    }

    public static function getCategoryStats(): array
    {
        $newsCategories = static::news()->published()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->whereNotNull('category')
            ->get()
            ->pluck('count', 'category')
            ->toArray();

        $eventCategories = static::events()->published()
            ->selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->whereNotNull('category')
            ->get()
            ->pluck('count', 'category')
            ->toArray();

        return [
            'news' => $newsCategories,
            'events' => $eventCategories,
        ];
    }

    public static function getMonthlyStats(int $months = 6): array
    {
        $newsStats = static::news()->published()
            ->selectRaw('EXTRACT(YEAR FROM published_at) as year, EXTRACT(MONTH FROM published_at) as month, COUNT(*) as count')
            ->where('published_at', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                    'count' => $item->count,
                    'type' => 'news'
                ];
            });

        $eventStats = static::events()->published()
            ->selectRaw('EXTRACT(YEAR FROM start_date) as year, EXTRACT(MONTH FROM start_date) as month, COUNT(*) as count')
            ->where('start_date', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                    'count' => $item->count,
                    'type' => 'events'
                ];
            });

        return [
            'news' => $newsStats->toArray(),
            'events' => $eventStats->toArray(),
        ];
    }

    public static function getTopAuthors(int $limit = 5): array
    {
        return static::whereIn('type', ['news', 'event'])
            ->published()
            ->whereNotNull('author')
            ->selectRaw('author, COUNT(*) as content_count, SUM(view_count) as total_views')
            ->groupBy('author')
            ->orderBy('content_count', 'desc')
            ->orderBy('total_views', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Generate unique slug from title
     */
    public function generateSlug($title)
    {
        if (empty($title)) {
            $title = 'content-' . time();
        }

        $slug = Str::slug($title);
        
        // If slug is empty after processing, use fallback
        if (empty($slug)) {
            $slug = 'content-' . time();
        }
        
        $originalSlug = $slug;
        $counter = 1;

        // Check for duplicates and increment counter until unique
        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get content by slug
     */
    public static function findBySlug($slug)
    {
        return static::where('slug', $slug)->first();
    }

    /**
     * Get published content by slug
     */
    public static function findPublishedBySlug($slug)
    {
        return static::where('slug', $slug)->published()->first();
    }

    /**
     * Scope to find by slug
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Get the route key for the model
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Get full URL for frontend
     */
    public function getFrontendUrlAttribute()
    {
        $baseUrl = config('app.frontend_url', 'https://tezcapital.com');
        
        return match($this->type) {
            'news' => $baseUrl . '/news/' . $this->slug,
            'event' => $baseUrl . '/events/' . $this->slug,
            'service' => $baseUrl . '/services/' . $this->slug,
            'partner' => $baseUrl . '/partners/' . $this->slug,
            default => $baseUrl . '/' . $this->type . '/' . $this->slug,
        };
    }
}
