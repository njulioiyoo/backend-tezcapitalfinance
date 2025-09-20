<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

class Content extends Model implements Auditable
{
    use HasFactory, AuditableTrait, SoftDeletes;

    protected $fillable = [
        'type',
        'category',
        'title_id',
        'title_en',
        'excerpt_id',
        'excerpt_en',
        'content_id',
        'content_en',
        'featured_image',
        'gallery',
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
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'published_at' => 'datetime',
            'price' => 'decimal:2',
            'max_participants' => 'integer',
            'registered_count' => 'integer',
            'view_count' => 'integer',
            'like_count' => 'integer',
            'share_count' => 'integer',
            'sort_order' => 'integer',
        ];
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
}
