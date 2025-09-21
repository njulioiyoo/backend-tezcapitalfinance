<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'admin_response',
        'responded_at',
        'responded_by',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_REJECTED = 'rejected';

    // Get all available statuses
    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_IN_REVIEW => 'In Review',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    // Get status label
    public function getStatusLabelAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? 'Unknown';
    }

    // Relationship with admin who responded
    public function respondedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    // Scope for filtering by status
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope for recent complaints
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
