<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title_id',
        'title_en',
        'description_id',
        'description_en',
        'year',
        'period',
        'month',
        'quarter',
        'file_path',
        'file_name',
        'file_size',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'file_size' => 'integer',
        'year' => 'integer',
        'month' => 'integer',
        'quarter' => 'integer',
    ];

    /**
     * Get the URL for the report file
     */
    public function getFileUrlAttribute()
    {
        if (!$this->file_path) {
            return null;
        }
        
        return asset('storage/' . $this->file_path);
    }

    /**
     * Get human readable file size
     */
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return null;
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Get period name in Indonesian
     */
    public function getPeriodNameAttribute()
    {
        $periods = [
            'monthly' => 'Bulanan',
            'quarterly' => 'Triwulan',
            'yearly' => 'Tahunan',
        ];

        return $periods[$this->period] ?? $this->period;
    }

    /**
     * Get month name in Indonesian
     */
    public function getMonthNameAttribute()
    {
        if (!$this->month) {
            return null;
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        return $months[$this->month] ?? null;
    }

    /**
     * Get quarter name in Indonesian
     */
    public function getQuarterNameAttribute()
    {
        if (!$this->quarter) {
            return null;
        }

        $quarters = [
            1 => 'Triwulan I',
            2 => 'Triwulan II', 
            3 => 'Triwulan III',
            4 => 'Triwulan IV'
        ];

        return $quarters[$this->quarter] ?? null;
    }

    /**
     * Get type name in Indonesian
     */
    public function getTypeNameAttribute()
    {
        $types = [
            'laporan-pengaduan' => 'Laporan Pengaduan',
            'laporan-tahunan' => 'Laporan Tahunan',
            'laporan-keuangan' => 'Laporan Keuangan',
        ];

        return $types[$this->type] ?? $this->type;
    }

    /**
     * Get full display title with period info
     */
    public function getFullTitleAttribute()
    {
        $title = $this->title_id;
        
        if ($this->period === 'monthly' && $this->month) {
            $title .= ' - ' . $this->month_name . ' ' . $this->year;
        } elseif ($this->period === 'quarterly' && $this->quarter) {
            $title .= ' - ' . $this->quarter_name . ' ' . $this->year;
        } else {
            $title .= ' - ' . $this->year;
        }

        return $title;
    }

    /**
     * Scope for published reports
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for specific type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope for specific year
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope for ordering by latest
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('year', 'desc')
                    ->orderBy('quarter', 'desc')
                    ->orderBy('month', 'desc')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Delete file when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($report) {
            if ($report->file_path && Storage::disk('public')->exists($report->file_path)) {
                Storage::disk('public')->delete($report->file_path);
            }
        });
    }
}