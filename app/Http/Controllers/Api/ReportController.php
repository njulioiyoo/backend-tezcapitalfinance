<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReportController extends Controller
{
    /**
     * Get all reports grouped by type
     */
    public function index(Request $request)
    {
        try {
            $startTime = microtime(true);
            $cacheKey = 'reports_grouped';
            $cacheTTL = config('cache.ttl', 3600); // 1 hour default

            $reports = Cache::remember($cacheKey, $cacheTTL, function () {
                return Report::published()
                    ->latest()
                    ->get()
                    ->groupBy('type');
            });

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Reports data retrieved successfully',
                'data' => [
                    'financial_reports' => $reports->get('laporan-keuangan', collect())->values(),
                    'annual_reports' => $reports->get('laporan-tahunan', collect())->values(),
                ],
                'response_time_ms' => $responseTime
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch reports: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve reports data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get financial reports with optional filtering
     */
    public function financial(Request $request)
    {
        try {
            $startTime = microtime(true);
            
            $request->validate([
                'period' => 'nullable|string|in:all,monthly,quarterly',
                'year' => 'nullable|integer|min:2000',
                'month' => 'nullable|integer|min:1|max:12',
                'quarter' => 'nullable|integer|min:1|max:4',
                'search' => 'nullable|string|max:255',
                'page' => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1|max:100',
                'lang' => 'nullable|string|in:id,en'
            ]);

            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            $search = $request->get('search');
            $period = $request->get('period', 'all');
            $year = $request->get('year');
            $month = $request->get('month');
            $quarter = $request->get('quarter');
            $lang = $request->get('lang', 'id');

            $query = Report::where('type', 'laporan-keuangan')
                ->where('is_published', true);

            // Apply filters
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title_id', 'LIKE', "%{$search}%")
                      ->orWhere('title_en', 'LIKE', "%{$search}%");
                });
            }

            if ($period !== 'all') {
                $query->where('period', $period);
            }

            if ($year) {
                $query->where('year', $year);
            }

            if ($month) {
                $query->where('month', $month);
            }

            if ($quarter) {
                $query->where('quarter', $quarter);
            }

            // Get total count for pagination
            $total = $query->count();

            // Get paginated reports for grouping with proper sorting by year desc then created_at desc
            $allReports = $query->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->orderBy('quarter', 'desc')
                ->orderBy('created_at', 'desc')
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get();

            // Group by period for frontend tabs
            $groupedReports = [
                'all-category' => [
                    'title' => $lang === 'en' ? 'All Categories' : 'Semua Kategori',
                    'title_id' => 'Semua Kategori',
                    'title_en' => 'All Categories',
                    'value' => 'all-category',
                    'subKeuangan' => $allReports->map(function ($report) use ($lang) {
                        return [
                            'id' => $report->id,
                            'year' => (string) $report->year,
                            'month' => $report->month ?? $report->quarter ?? '',
                            'desc' => $lang === 'en' ? ($report->title_en ?: $report->title_id) : $report->title_id,
                            'desc_id' => $report->title_id,
                            'desc_en' => $report->title_en ?: $report->title_id,
                            'file_url' => $report->file_path ? config('app.url') . '/storage/' . $report->file_path : null,
                            'file_size' => $this->formatFileSize($this->getActualFileSize($report)),
                            'created_at' => $report->created_at
                        ];
                    })->values()
                ],
                'bulanan' => [
                    'title' => $lang === 'en' ? 'Monthly Financial Reports' : 'Laporan Keuangan Bulanan',
                    'title_id' => 'Laporan Keuangan Bulanan',
                    'title_en' => 'Monthly Financial Reports',
                    'value' => 'bulanan',
                    'subKeuangan' => $allReports->filter(function ($report) {
                        return $report->period === 'monthly';
                    })->map(function ($report) use ($lang) {
                        return [
                            'id' => $report->id,
                            'year' => (string) $report->year,
                            'month' => $report->month ?? '',
                            'desc' => $lang === 'en' ? ($report->title_en ?: $report->title_id) : $report->title_id,
                            'desc_id' => $report->title_id,
                            'desc_en' => $report->title_en ?: $report->title_id,
                            'file_url' => $report->file_path ? config('app.url') . '/storage/' . $report->file_path : null,
                            'file_size' => $this->formatFileSize($this->getActualFileSize($report)),
                            'created_at' => $report->created_at
                        ];
                    })->values()
                ],
                'triwulan' => [
                    'title' => $lang === 'en' ? 'Quarterly Financial Reports' : 'Laporan Keuangan Triwulan',
                    'title_id' => 'Laporan Keuangan Triwulan',
                    'title_en' => 'Quarterly Financial Reports',
                    'value' => 'triwulan',
                    'subKeuangan' => $allReports->filter(function ($report) {
                        return $report->period === 'quarterly';
                    })->map(function ($report) use ($lang) {
                        return [
                            'id' => $report->id,
                            'year' => (string) $report->year,
                            'month' => $report->quarter ?? '',
                            'desc' => $lang === 'en' ? ($report->title_en ?: $report->title_id) : $report->title_id,
                            'desc_id' => $report->title_id,
                            'desc_en' => $report->title_en ?: $report->title_id,
                            'file_url' => $report->file_path ? config('app.url') . '/storage/' . $report->file_path : null,
                            'file_size' => $this->formatFileSize($this->getActualFileSize($report)),
                            'created_at' => $report->created_at
                        ];
                    })->values()
                ]
            ];

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Financial reports data retrieved successfully',
                'data' => $groupedReports,
                'pagination' => [
                    'current_page' => (int) $page,
                    'per_page' => (int) $limit,
                    'total' => (int) $total,
                    'last_page' => (int) ceil($total / $limit),
                    'has_more' => $page < ceil($total / $limit)
                ],
                'response_time_ms' => $responseTime
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch financial reports: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve financial reports data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get annual reports
     */
    public function annual(Request $request)
    {
        try {
            $startTime = microtime(true);
            
            $request->validate([
                'year' => 'nullable|integer|min:2000',
                'search' => 'nullable|string|max:255',
                'page' => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1|max:100',
                'lang' => 'nullable|string|in:id,en'
            ]);

            $page = $request->get('page', 1);
            $limit = $request->get('limit', 20);
            $search = $request->get('search');
            $year = $request->get('year');
            $lang = $request->get('lang', 'id');

            $query = Report::where('type', 'laporan-tahunan')
                ->where('is_published', true);

            // Apply filters
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title_id', 'LIKE', "%{$search}%")
                      ->orWhere('title_en', 'LIKE', "%{$search}%");
                });
            }

            if ($year) {
                $query->where('year', $year);
            }

            // Get total count for pagination
            $total = $query->count();

            // Apply pagination with proper sorting by year desc then created_at desc
            $reports = $query->orderBy('year', 'desc')
                ->orderBy('created_at', 'desc')
                ->offset(($page - 1) * $limit)
                ->limit($limit)
                ->get()
                ->map(function ($report) use ($lang) {
                    return [
                        'id' => $report->id,
                        'year' => (string) $report->year,
                        'desc' => $lang === 'en' ? ($report->title_en ?: $report->title_id) : $report->title_id,
                        'desc_id' => $report->title_id,
                        'desc_en' => $report->title_en ?: $report->title_id,
                        'link' => $report->file_path ? config('app.url') . '/storage/' . $report->file_path : null,
                        'file_size' => $this->formatFileSize($this->getActualFileSize($report)),
                        'created_at' => $report->created_at
                    ];
                });

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Annual reports data retrieved successfully',
                'data' => $reports,
                'pagination' => [
                    'current_page' => (int) $page,
                    'per_page' => (int) $limit,
                    'total' => (int) $total,
                    'last_page' => (int) ceil($total / $limit),
                    'has_more' => $page < ceil($total / $limit)
                ],
                'response_time_ms' => $responseTime
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch annual reports: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve annual reports data',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Get single report details
     */
    public function show($id)
    {
        try {
            $startTime = microtime(true);
            
            $report = Report::where('is_published', true)->findOrFail($id);

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Report detail retrieved successfully',
                'data' => [
                    'id' => $report->id,
                    'title' => $report->title_id,
                    'type' => $report->type,
                    'period' => $report->period,
                    'year' => $report->year,
                    'month' => $report->month,
                    'quarter' => $report->quarter,
                    'file_url' => $report->file_path ? config('app.url') . '/storage/' . $report->file_path : null,
                    'file_size' => $this->formatFileSize($this->getActualFileSize($report)),
                    'created_at' => $report->created_at
                ],
                'response_time_ms' => $responseTime
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch report detail: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Report not found',
                'error' => config('app.debug') ? $e->getMessage() : 'Report not found'
            ], 404);
        }
    }

    /**
     * Download report file and increment download count
     */
    public function download($id)
    {
        try {
            $startTime = microtime(true);
            
            $report = Report::where('is_published', true)->findOrFail($id);

            if (!$report->file_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not available for download'
                ], 404);
            }

            // Increment download count (you can uncomment this if needed)
            // $report->increment('download_count');

            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'success' => true,
                'message' => 'Download URL retrieved successfully',
                'data' => [
                    'download_url' => config('app.url') . '/storage/' . $report->file_path,
                    'filename' => basename($report->file_path) ?? "report-{$report->id}.pdf"
                ],
                'response_time_ms' => $responseTime
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to get download URL: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Report not found',
                'error' => config('app.debug') ? $e->getMessage() : 'Report not found'
            ], 404);
        }
    }

    /**
     * Get actual file size from storage
     */
    private function getActualFileSize($report)
    {
        if (!$report->file_path) {
            return null;
        }
        
        try {
            $filePath = storage_path('app/public/' . $report->file_path);
            if (file_exists($filePath)) {
                return filesize($filePath);
            }
        } catch (\Exception $e) {
            \Log::warning('Could not get file size for report: ' . $report->id, [
                'file_path' => $report->file_path,
                'error' => $e->getMessage()
            ]);
        }
        
        return null;
    }

    /**
     * Format file size in human readable format
     */
    private function formatFileSize($bytes)
    {
        if ($bytes === null || $bytes === 0) return null;
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $factor), 2) . ' ' . $units[$factor];
    }
}