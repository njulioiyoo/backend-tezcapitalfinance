<?php

namespace App\Http\Controllers\Content\NewsEvents;

use App\Http\Controllers\Controller;
use App\Models\Content;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the News & Events dashboard
     */
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'initialData' => $this->getDashboardData(),
        ]);
    }

    /**
     * Get dashboard data via API
     */
    public function getDashboardData(): array
    {
        $startTime = microtime(true);

        try {
            $newsStats = Content::getNewsStats();
            $eventsStats = Content::getEventsStats();
            $trendingContent = Content::getTrendingContent(5);
            $recentActivity = Content::getRecentActivity(8);
            $categoryStats = Content::getCategoryStats();
            $monthlyStats = Content::getMonthlyStats(6);
            $topAuthors = Content::getTopAuthors(5);

            // Calculate engagement metrics
            $totalEngagement = $this->calculateEngagementMetrics();
            
            // Get performance insights
            $insights = $this->getPerformanceInsights($newsStats, $eventsStats);

            $endTime = microtime(true);
            $responseTime = round(($endTime - $startTime) * 1000, 2);

            return [
                'news_stats' => $newsStats,
                'events_stats' => $eventsStats,
                'trending_content' => $trendingContent,
                'recent_activity' => $recentActivity,
                'category_stats' => $categoryStats,
                'monthly_stats' => $monthlyStats,
                'top_authors' => $topAuthors,
                'engagement_metrics' => $totalEngagement,
                'insights' => $insights,
                'response_time_ms' => $responseTime,
                'last_updated' => now()->format('Y-m-d H:i:s'),
            ];
        } catch (\Exception $e) {
            return [
                'error' => 'Failed to load dashboard data: ' . $e->getMessage(),
                'response_time_ms' => round((microtime(true) - $startTime) * 1000, 2),
            ];
        }
    }

    /**
     * API endpoint for dashboard data
     */
    public function apiData(): JsonResponse
    {
        $data = $this->getDashboardData();

        if (isset($data['error'])) {
            return response()->json([
                'success' => false,
                'message' => $data['error'],
                'response_time_ms' => $data['response_time_ms']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Dashboard data retrieved successfully',
            'data' => $data,
            'response_time_ms' => $data['response_time_ms']
        ]);
    }

    /**
     * Calculate engagement metrics
     */
    private function calculateEngagementMetrics(): array
    {
        $totalViews = Content::whereIn('type', ['news', 'event'])
            ->published()
            ->sum('view_count');

        $totalLikes = Content::whereIn('type', ['news', 'event'])
            ->published()
            ->sum('like_count');

        $totalShares = Content::whereIn('type', ['news', 'event'])
            ->published()
            ->sum('share_count');

        $totalContent = Content::whereIn('type', ['news', 'event'])
            ->published()
            ->count();

        $avgViews = $totalContent > 0 ? round($totalViews / $totalContent, 2) : 0;
        $avgLikes = $totalContent > 0 ? round($totalLikes / $totalContent, 2) : 0;
        $avgShares = $totalContent > 0 ? round($totalShares / $totalContent, 2) : 0;

        // Calculate engagement rate (likes + shares / views)
        $engagementRate = $totalViews > 0 ? round((($totalLikes + $totalShares) / $totalViews) * 100, 2) : 0;

        return [
            'total_views' => $totalViews,
            'total_likes' => $totalLikes,
            'total_shares' => $totalShares,
            'total_content' => $totalContent,
            'avg_views_per_content' => $avgViews,
            'avg_likes_per_content' => $avgLikes,
            'avg_shares_per_content' => $avgShares,
            'engagement_rate' => $engagementRate,
        ];
    }

    /**
     * Get performance insights
     */
    private function getPerformanceInsights(array $newsStats, array $eventsStats): array
    {
        $insights = [];

        // News insights
        if ($newsStats['published'] > 0) {
            $featuredPercentage = round(($newsStats['featured'] / $newsStats['published']) * 100, 1);
            $insights[] = [
                'type' => 'info',
                'title' => 'Featured News',
                'message' => "{$featuredPercentage}% of published news are featured",
                'value' => $featuredPercentage,
                'category' => 'news'
            ];
        }

        // Events insights
        if ($eventsStats['total'] > 0) {
            $upcomingPercentage = round(($eventsStats['upcoming'] / $eventsStats['total']) * 100, 1);
            $insights[] = [
                'type' => 'success',
                'title' => 'Upcoming Events',
                'message' => "{$upcomingPercentage}% of events are upcoming",
                'value' => $upcomingPercentage,
                'category' => 'events'
            ];
        }

        // Content activity insight
        $todayNews = $newsStats['today'] ?? 0;
        if ($todayNews > 0) {
            $insights[] = [
                'type' => 'positive',
                'title' => 'Today\'s Activity',
                'message' => "{$todayNews} news published today",
                'value' => $todayNews,
                'category' => 'activity'
            ];
        }

        // Weekly performance
        $weeklyNews = $newsStats['this_week'] ?? 0;
        if ($weeklyNews >= 5) {
            $insights[] = [
                'type' => 'success',
                'title' => 'High Activity',
                'message' => "{$weeklyNews} news published this week",
                'value' => $weeklyNews,
                'category' => 'performance'
            ];
        } elseif ($weeklyNews < 2) {
            $insights[] = [
                'type' => 'warning',
                'title' => 'Low Activity',
                'message' => 'Only ' . $weeklyNews . ' news published this week',
                'value' => $weeklyNews,
                'category' => 'performance'
            ];
        }

        // Draft content insight
        $draftCount = $newsStats['draft'] ?? 0;
        if ($draftCount > 10) {
            $insights[] = [
                'type' => 'info',
                'title' => 'Pending Content',
                'message' => "{$draftCount} news drafts pending publication",
                'value' => $draftCount,
                'category' => 'workflow'
            ];
        }

        return $insights;
    }

    /**
     * Get specific content analytics
     */
    public function getContentAnalytics($type = null): JsonResponse
    {
        try {
            $query = Content::query();
            
            if ($type && in_array($type, ['news', 'event'])) {
                $query->where('type', $type);
            } else {
                $query->whereIn('type', ['news', 'event']);
            }

            $analytics = [
                'total_content' => $query->count(),
                'published_content' => $query->published()->count(),
                'featured_content' => $query->featured()->count(),
                'content_by_status' => $query->selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'content_by_month' => $query->published()
                    ->selectRaw('EXTRACT(YEAR FROM published_at) as year, EXTRACT(MONTH FROM published_at) as month, COUNT(*) as count')
                    ->where('published_at', '>=', now()->subMonths(12))
                    ->groupBy('year', 'month')
                    ->orderBy('year')
                    ->orderBy('month')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'period' => \Carbon\Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
                            'count' => $item->count
                        ];
                    }),
            ];

            return response()->json([
                'success' => true,
                'data' => $analytics,
                'type' => $type ?? 'all'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analytics: ' . $e->getMessage(),
            ], 500);
        }
    }
}