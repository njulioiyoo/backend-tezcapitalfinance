<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { onMounted, ref, computed } from 'vue';
import { format } from 'date-fns';
import { TrendingUp, TrendingDown, Users, Eye, Heart, Share2, Calendar, FileText, Star, Activity, Building2, MapPin, UserCheck, Award, Briefcase } from 'lucide-vue-next';
import PlaceholderPattern from '../components/PlaceholderPattern.vue';

interface DashboardData {
  news_stats: {
    total: number;
    published: number;
    draft: number;
    featured: number;
    this_month: number;
    this_week: number;
    today: number;
  };
  events_stats: {
    total: number;
    upcoming: number;
    ongoing: number;
    past: number;
    published: number;
    featured: number;
    this_month: number;
    cancelled: number;
  };
  trending_content: Array<{
    id: number;
    type: string;
    title_id: string;
    title_en: string;
    view_count: number;
    like_count: number;
    share_count: number;
    published_at: string;
  }>;
  recent_activity: Array<{
    id: number;
    type: string;
    title_id: string;
    title_en: string;
    published_at: string;
    view_count: number;
    is_featured: boolean;
  }>;
  category_stats: {
    news: Record<string, number>;
    events: Record<string, number>;
  };
  monthly_stats: {
    news: Array<{period: string; count: number; type: string}>;
    events: Array<{period: string; count: number; type: string}>;
  };
  top_authors: Array<{
    author: string;
    content_count: number;
    total_views: number;
  }>;
  engagement_metrics: {
    total_views: number;
    total_likes: number;
    total_shares: number;
    total_content: number;
    avg_views_per_content: number;
    avg_likes_per_content: number;
    avg_shares_per_content: number;
    engagement_rate: number;
  };
  insights: Array<{
    type: string;
    title: string;
    message: string;
    value: number;
    category: string;
  }>;
  response_time_ms: number;
  last_updated: string;
}

const props = defineProps<{
  initialData?: DashboardData;
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const dashboardData = ref<DashboardData>(props.initialData || {
  news_stats: { total: 0, published: 0, draft: 0, featured: 0, this_month: 0, this_week: 0, today: 0 },
  events_stats: { total: 0, upcoming: 0, ongoing: 0, past: 0, published: 0, featured: 0, this_month: 0, cancelled: 0 },
  trending_content: [],
  recent_activity: [],
  category_stats: { news: {}, events: {} },
  monthly_stats: { news: [], events: [] },
  top_authors: [],
  engagement_metrics: { total_views: 0, total_likes: 0, total_shares: 0, total_content: 0, avg_views_per_content: 0, avg_likes_per_content: 0, avg_shares_per_content: 0, engagement_rate: 0 },
  insights: [],
  response_time_ms: 0,
  last_updated: new Date().toISOString()
});
const isLoading = ref(false);

const formatNumber = (num: number): string => {
  if (num >= 1000000) {
    return (num / 1000000).toFixed(1) + 'M';
  }
  if (num >= 1000) {
    return (num / 1000).toFixed(1) + 'K';
  }
  return num.toString();
};

const getInsightIcon = (type: string) => {
  switch (type) {
    case 'success': return TrendingUp;
    case 'warning': return TrendingDown;
    case 'positive': return TrendingUp;
    default: return Activity;
  }
};

const getInsightColor = (type: string) => {
  switch (type) {
    case 'success': return 'bg-green-50 text-green-700 border-green-200';
    case 'warning': return 'bg-yellow-50 text-yellow-700 border-yellow-200';
    case 'positive': return 'bg-blue-50 text-blue-700 border-blue-200';
    default: return 'bg-gray-50 text-gray-700 border-gray-200';
  }
};

const refreshData = async () => {
  isLoading.value = true;
  try {
    const response = await fetch('/api/system/content/news-events/dashboard');
    const result = await response.json();
    if (result.success) {
      dashboardData.value = result.data;
    }
  } catch (error) {
    console.error('Failed to refresh dashboard data:', error);
  } finally {
    isLoading.value = false;
  }
};

const totalEngagement = computed(() => {
  const metrics = dashboardData.value.engagement_metrics;
  return metrics.total_likes + metrics.total_shares;
});


// Chart Data Computed Properties
const contentDistribution = computed(() => {
  const stats = dashboardData.value;
  const total = (stats.news_stats?.total || 0) + (stats.events_stats?.total || 0);
  
  if (total === 0) return [];
  
  return [
    { name: 'News', value: stats.news_stats?.total || 0, percentage: Math.round(((stats.news_stats?.total || 0) / total) * 100), color: 'bg-blue-500' },
    { name: 'Events', value: stats.events_stats?.total || 0, percentage: Math.round(((stats.events_stats?.total || 0) / total) * 100), color: 'bg-green-500' }
  ];
});

const newsStatusDistribution = computed(() => {
  const stats = dashboardData.value.news_stats;
  return [
    { name: 'Published', value: stats.published, color: 'bg-green-500' },
    { name: 'Draft', value: stats.draft, color: 'bg-yellow-500' },
    { name: 'Featured', value: stats.featured, color: 'bg-purple-500' }
  ];
});

const eventsStatusDistribution = computed(() => {
  const stats = dashboardData.value.events_stats;
  return [
    { name: 'Upcoming', value: stats.upcoming, color: 'bg-blue-500' },
    { name: 'Ongoing', value: stats.ongoing, color: 'bg-green-500' },
    { name: 'Past', value: stats.past, color: 'bg-gray-500' },
    { name: 'Cancelled', value: stats.cancelled, color: 'bg-red-500' }
  ];
});

const weeklyActivity = computed(() => {
  const stats = dashboardData.value;
  return [
    { name: 'Today', news: stats.news_stats.today, events: 0, color: 'bg-blue-500' },
    { name: 'This Week', news: stats.news_stats.this_week, events: 0, color: 'bg-green-500' },
    { name: 'This Month', news: stats.news_stats.this_month, events: stats.events_stats.this_month, color: 'bg-purple-500' }
  ];
});

const engagementMetrics = computed(() => {
  const metrics = dashboardData.value.engagement_metrics;
  const maxValue = Math.max(metrics.total_views, metrics.total_likes, metrics.total_shares);
  
  return [
    { 
      name: 'Views', 
      value: metrics.total_views, 
      percentage: maxValue > 0 ? (metrics.total_views / maxValue) * 100 : 0,
      color: 'bg-blue-500',
      icon: Eye
    },
    { 
      name: 'Likes', 
      value: metrics.total_likes, 
      percentage: maxValue > 0 ? (metrics.total_likes / maxValue) * 100 : 0,
      color: 'bg-red-500',
      icon: Heart
    },
    { 
      name: 'Shares', 
      value: metrics.total_shares, 
      percentage: maxValue > 0 ? (metrics.total_shares / maxValue) * 100 : 0,
      color: 'bg-green-500',
      icon: Share2
    }
  ];
});

onMounted(async () => {
  // Load initial data if not provided
  if (!props.initialData) {
    await refreshData();
  }
  
  // Auto-refresh every 5 minutes
  setInterval(refreshData, 5 * 60 * 1000);
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4 overflow-x-auto">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                    <p class="text-muted-foreground">News & Events analytics and insights</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    <Activity class="h-4 w-4" />
                    <span>Last updated: {{ dashboardData.last_updated ? format(new Date(dashboardData.last_updated), 'HH:mm:ss') : 'Never' }}</span>
                    <button 
                        @click="refreshData" 
                        :disabled="isLoading"
                        class="ml-2 px-3 py-1 text-xs bg-primary text-primary-foreground rounded hover:bg-primary/90 disabled:opacity-50"
                    >
                        {{ isLoading ? 'Refreshing...' : 'Refresh' }}
                    </button>
                </div>
            </div>

            <!-- Key Metrics Overview -->
            <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <div class="relative z-10 p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-muted-foreground">Total News</span>
                            <FileText class="h-4 w-4 text-muted-foreground" />
                        </div>
                        <div class="text-2xl font-bold">{{ dashboardData.news_stats.total }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ dashboardData.news_stats.published }} published
                        </p>
                    </div>
                </div>

                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <div class="relative z-10 p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-muted-foreground">Total Events</span>
                            <Calendar class="h-4 w-4 text-muted-foreground" />
                        </div>
                        <div class="text-2xl font-bold">{{ dashboardData.events_stats.total }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ dashboardData.events_stats.upcoming }} upcoming
                        </p>
                    </div>
                </div>

                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <div class="relative z-10 p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-muted-foreground">Total Views</span>
                            <Eye class="h-4 w-4 text-muted-foreground" />
                        </div>
                        <div class="text-2xl font-bold">{{ formatNumber(dashboardData.engagement_metrics.total_views) }}</div>
                        <p class="text-xs text-muted-foreground">
                            {{ dashboardData.engagement_metrics.avg_views_per_content }} avg per content
                        </p>
                    </div>
                </div>

                <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <div class="relative z-10 p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-muted-foreground">Engagement Rate</span>
                            <Heart class="h-4 w-4 text-muted-foreground" />
                        </div>
                        <div class="text-2xl font-bold">{{ dashboardData.engagement_metrics.engagement_rate }}%</div>
                        <p class="text-xs text-muted-foreground">
                            {{ formatNumber(totalEngagement) }} total engagements
                        </p>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <!-- Content Distribution Pie Chart -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent h-full">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-lg">Content Distribution</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div v-if="contentDistribution.length > 0" class="space-y-3">
                                <!-- Pie Chart -->
                                <div class="flex items-center justify-center">
                                    <div class="relative w-24 h-24">
                                        <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 36 36">
                                            <circle cx="18" cy="18" r="16" fill="none" class="stroke-muted" stroke-width="2" />
                                            <template v-for="(item, index) in contentDistribution" :key="item.name">
                                                <circle
                                                    cx="18" cy="18" r="16" fill="none"
                                                    :class="item.color.replace('bg-', 'stroke-')"
                                                    stroke-width="3"
                                                    :stroke-dasharray="`${item.percentage} ${100 - item.percentage}`"
                                                    :stroke-dashoffset="contentDistribution.slice(0, index).reduce((sum, prev) => sum - prev.percentage, 0)"
                                                />
                                            </template>
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <div class="text-center">
                                                <div class="text-sm font-bold">{{ contentDistribution.reduce((sum, item) => sum + item.value, 0) }}</div>
                                                <div class="text-xs text-muted-foreground">Total</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Legend -->
                                <div class="space-y-1">
                                    <div v-for="item in contentDistribution" :key="item.name" class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <div :class="['w-2 h-2 rounded-full', item.color]"></div>
                                            <span class="text-xs font-medium">{{ item.name }}</span>
                                        </div>
                                        <span class="text-xs text-muted-foreground">{{ item.value }}</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- News Status Chart -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent h-full">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-lg">News Status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div v-for="item in newsStatusDistribution" :key="item.name" class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">{{ item.name }}</span>
                                        <span class="text-sm font-bold">{{ item.value }}</span>
                                    </div>
                                    <div class="w-full bg-muted rounded-full h-2">
                                        <div :class="[item.color, 'h-2 rounded-full transition-all duration-500']" :style="{ width: `${Math.max(5, (item.value / Math.max(...newsStatusDistribution.map(i => i.value))) * 100)}%` }"></div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Events Status Chart -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent h-full">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-lg">Events Status</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div v-for="item in eventsStatusDistribution" :key="item.name" class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">{{ item.name }}</span>
                                        <span class="text-sm font-bold">{{ item.value }}</span>
                                    </div>
                                    <div class="w-full bg-muted rounded-full h-2">
                                        <div :class="[item.color, 'h-2 rounded-full transition-all duration-500']" :style="{ width: `${Math.max(5, (item.value / Math.max(...eventsStatusDistribution.map(i => i.value))) * 100)}%` }"></div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Weekly Activity Chart -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent h-full">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-lg">Activity Timeline</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="item in weeklyActivity" :key="item.name" class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm font-medium">{{ item.name }}</span>
                                        <span class="text-sm font-bold">{{ item.news + item.events }}</span>
                                    </div>
                                    <!-- Stacked Bar -->
                                    <div class="w-full bg-muted rounded-full h-3 overflow-hidden">
                                        <div class="flex h-full">
                                            <div 
                                                class="bg-blue-500 transition-all duration-500"
                                                :style="{ width: `${Math.max(2, (item.news / Math.max(...weeklyActivity.map(i => i.news + i.events))) * 80)}%` }"
                                            ></div>
                                            <div 
                                                class="bg-green-500 transition-all duration-500"
                                                :style="{ width: `${Math.max(2, (item.events / Math.max(...weeklyActivity.map(i => i.news + i.events))) * 20)}%` }"
                                            ></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                        <div class="flex items-center gap-1">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            <span>{{ item.news }} News</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                            <span>{{ item.events }} Events</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Engagement Metrics Chart -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent h-full">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-lg">Engagement Metrics</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <div v-for="metric in engagementMetrics" :key="metric.name" class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <component :is="metric.icon" class="w-4 h-4 text-muted-foreground" />
                                            <span class="text-sm font-medium">{{ metric.name }}</span>
                                        </div>
                                        <span class="text-sm font-bold">{{ formatNumber(metric.value) }}</span>
                                    </div>
                                    <div class="w-full bg-muted rounded-full h-2">
                                        <div :class="[metric.color, 'h-2 rounded-full transition-all duration-700']" :style="{ width: `${Math.max(5, metric.percentage)}%` }"></div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Content Performance Chart -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent h-full">
                        <CardHeader class="pb-3">
                            <CardTitle class="text-lg">Performance Metrics</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-4">
                                <!-- Engagement Rate -->
                                <div class="text-center">
                                    <div class="relative w-20 h-20 mx-auto">
                                        <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                                            <circle cx="18" cy="18" r="16" fill="none" class="stroke-muted" stroke-width="3" />
                                            <circle 
                                                cx="18" cy="18" r="16" 
                                                fill="none" 
                                                class="stroke-primary" 
                                                stroke-width="3"
                                                :stroke-dasharray="`${dashboardData.engagement_metrics.engagement_rate} ${100 - dashboardData.engagement_metrics.engagement_rate}`"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                        <div class="absolute inset-0 flex items-center justify-center">
                                            <span class="text-sm font-bold">{{ dashboardData.engagement_metrics.engagement_rate }}%</span>
                                        </div>
                                    </div>
                                    <p class="text-xs text-muted-foreground mt-2">Engagement Rate</p>
                                </div>
                                
                                <!-- Stats -->
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-muted-foreground">Avg Views per Content</span>
                                        <span class="text-xs font-bold">{{ dashboardData.engagement_metrics.avg_views_per_content }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-muted-foreground">Total Content</span>
                                        <span class="text-xs font-bold">{{ dashboardData.engagement_metrics.total_content }}</span>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Performance Insights -->
            <Card v-if="dashboardData.insights.length > 0">
                <CardHeader>
                    <CardTitle>Performance Insights</CardTitle>
                    <CardDescription>Key insights about your content performance</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <div 
                            v-for="insight in dashboardData.insights" 
                            :key="insight.title"
                            :class="['p-4 rounded-lg border', getInsightColor(insight.type)]"
                        >
                            <div class="flex items-center gap-2 mb-2">
                                <component :is="getInsightIcon(insight.type)" class="h-4 w-4" />
                                <span class="font-medium text-sm">{{ insight.title }}</span>
                            </div>
                            <p class="text-sm">{{ insight.message }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Detailed Analytics -->
            <div class="relative min-h-[50vh] flex-1 rounded-xl border border-sidebar-border/70 md:min-h-min dark:border-sidebar-border">
                <PlaceholderPattern />
                <Tabs model-value="overview" class="relative z-10 space-y-4 p-6">
                <TabsList>
                    <TabsTrigger value="overview">Overview</TabsTrigger>
                    <TabsTrigger value="modules">Modules</TabsTrigger>
                    <TabsTrigger value="trending">Trending</TabsTrigger>
                    <TabsTrigger value="authors">Authors</TabsTrigger>
                    <TabsTrigger value="categories">Categories</TabsTrigger>
                </TabsList>

                <TabsContent value="overview" class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <!-- News Statistics -->
                        <Card>
                            <CardHeader>
                                <CardTitle>News Statistics</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">Published</span>
                                            <span class="font-medium">{{ dashboardData.news_stats.published }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">Draft</span>
                                            <span class="font-medium">{{ dashboardData.news_stats.draft }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">Featured</span>
                                            <span class="font-medium">{{ dashboardData.news_stats.featured }}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">This Month</span>
                                            <span class="font-medium">{{ dashboardData.news_stats.this_month }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">This Week</span>
                                            <span class="font-medium">{{ dashboardData.news_stats.this_week }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">Today</span>
                                            <span class="font-medium">{{ dashboardData.news_stats.today }}</span>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <!-- Events Statistics -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Events Statistics</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">Upcoming</span>
                                            <span class="font-medium">{{ dashboardData.events_stats.upcoming }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">Ongoing</span>
                                            <span class="font-medium">{{ dashboardData.events_stats.ongoing }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">Past</span>
                                            <span class="font-medium">{{ dashboardData.events_stats.past }}</span>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">Featured</span>
                                            <span class="font-medium">{{ dashboardData.events_stats.featured }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">This Month</span>
                                            <span class="font-medium">{{ dashboardData.events_stats.this_month }}</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-muted-foreground">Cancelled</span>
                                            <span class="font-medium">{{ dashboardData.events_stats.cancelled }}</span>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Recent Activity -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Activity</CardTitle>
                            <CardDescription>Latest published content</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div 
                                    v-for="item in dashboardData.recent_activity" 
                                    :key="item.id"
                                    class="flex items-center justify-between p-3 border rounded-lg"
                                >
                                    <div class="flex items-center gap-3">
                                        <Badge variant="outline" :class="item.type === 'news' ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700'">
                                            {{ item.type === 'news' ? 'News' : 'Event' }}
                                        </Badge>
                                        <div>
                                            <p class="font-medium">{{ item.title_id }}</p>
                                            <p class="text-sm text-muted-foreground">
                                                Published {{ item.published_at ? format(new Date(item.published_at), 'MMM dd, yyyy') : 'Unknown date' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm text-muted-foreground">
                                        <div class="flex items-center gap-1">
                                            <Eye class="h-4 w-4" />
                                            {{ formatNumber(item.view_count) }}
                                        </div>
                                        <Star v-if="item.is_featured" class="h-4 w-4 text-yellow-500" />
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <TabsContent value="modules" class="space-y-4">
                    <!-- Additional Charts for Modules Tab -->
                    <div class="grid gap-4 md:grid-cols-2">
                        <!-- Category Distribution -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Content Categories</CardTitle>
                                <CardDescription>Distribution by category types</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-4">
                                    <!-- News Categories -->
                                    <div>
                                        <h4 class="font-medium text-sm mb-2 text-blue-600">News Categories</h4>
                                        <div class="space-y-2">
                                            <div v-for="(count, category) in dashboardData.category_stats.news" :key="category" class="flex items-center justify-between">
                                                <span class="text-xs">{{ category }}</span>
                                                <div class="flex items-center gap-2">
                                                    <div class="w-16 bg-muted rounded-full h-1.5">
                                                        <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-300" :style="{ width: `${Math.max(10, (count / Math.max(...Object.values(dashboardData.category_stats.news))) * 100)}%` }"></div>
                                                    </div>
                                                    <Badge variant="secondary" class="text-xs px-1.5 py-0.5">{{ count }}</Badge>
                                                </div>
                                            </div>
                                            <div v-if="Object.keys(dashboardData.category_stats.news).length === 0" class="text-xs text-muted-foreground text-center py-2">
                                                No categories found
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Events Categories -->
                                    <div>
                                        <h4 class="font-medium text-sm mb-2 text-green-600">Event Categories</h4>
                                        <div class="space-y-2">
                                            <div v-for="(count, category) in dashboardData.category_stats.events" :key="category" class="flex items-center justify-between">
                                                <span class="text-xs">{{ category }}</span>
                                                <div class="flex items-center gap-2">
                                                    <div class="w-16 bg-muted rounded-full h-1.5">
                                                        <div class="bg-green-500 h-1.5 rounded-full transition-all duration-300" :style="{ width: `${Math.max(10, (count / Math.max(...Object.values(dashboardData.category_stats.events))) * 100)}%` }"></div>
                                                    </div>
                                                    <Badge variant="secondary" class="text-xs px-1.5 py-0.5">{{ count }}</Badge>
                                                </div>
                                            </div>
                                            <div v-if="Object.keys(dashboardData.category_stats.events).length === 0" class="text-xs text-muted-foreground text-center py-2">
                                                No categories found
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                        
                        <!-- Monthly Trends -->
                        <Card>
                            <CardHeader>
                                <CardTitle>Monthly Trends</CardTitle>
                                <CardDescription>Content publication trends</CardDescription>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-4">
                                    <!-- Simple line chart simulation -->
                                    <div v-if="dashboardData.monthly_stats.news.length > 0 || dashboardData.monthly_stats.events.length > 0" class="space-y-3">
                                        <div class="flex items-end justify-between h-20">
                                            <div v-for="(item, index) in dashboardData.monthly_stats.news.slice(0, 6)" :key="index" class="flex flex-col items-center gap-1 flex-1">
                                                <div class="bg-blue-500 rounded-t transition-all duration-300" :style="{ height: `${Math.max(4, (item.count / Math.max(...dashboardData.monthly_stats.news.map(i => i.count))) * 60)}px`, width: '8px' }"></div>
                                                <span class="text-xs text-muted-foreground transform rotate-45 origin-left">{{ item.period.slice(-2) }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="flex items-center gap-4 justify-center text-xs">
                                            <div class="flex items-center gap-1">
                                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                <span>News</span>
                                            </div>
                                            <div class="flex items-center gap-1">
                                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                <span>Events</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div v-else class="text-center py-8 text-muted-foreground text-sm">
                                        No monthly data available
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>

                <TabsContent value="trending" class="space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>Trending Content</CardTitle>
                            <CardDescription>Most viewed and engaged content</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div 
                                    v-for="(item, index) in dashboardData.trending_content" 
                                    :key="item.id"
                                    class="flex items-center justify-between p-3 border rounded-lg"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-8 h-8 bg-primary text-primary-foreground rounded-full text-sm font-bold">
                                            {{ index + 1 }}
                                        </div>
                                        <Badge variant="outline" :class="item.type === 'news' ? 'bg-blue-50 text-blue-700' : 'bg-green-50 text-green-700'">
                                            {{ item.type === 'news' ? 'News' : 'Event' }}
                                        </Badge>
                                        <div>
                                            <p class="font-medium">{{ item.title_id }}</p>
                                            <p class="text-sm text-muted-foreground">
                                                Published {{ item.published_at ? format(new Date(item.published_at), 'MMM dd, yyyy') : 'Unknown date' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm">
                                        <div class="flex items-center gap-1 text-muted-foreground">
                                            <Eye class="h-4 w-4" />
                                            {{ formatNumber(item.view_count) }}
                                        </div>
                                        <div class="flex items-center gap-1 text-muted-foreground">
                                            <Heart class="h-4 w-4" />
                                            {{ formatNumber(item.like_count) }}
                                        </div>
                                        <div class="flex items-center gap-1 text-muted-foreground">
                                            <Share2 class="h-4 w-4" />
                                            {{ formatNumber(item.share_count) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <TabsContent value="authors" class="space-y-4">
                    <Card>
                        <CardHeader>
                            <CardTitle>Top Authors</CardTitle>
                            <CardDescription>Authors with most content and views</CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <div 
                                    v-for="(author, index) in dashboardData.top_authors" 
                                    :key="author.author"
                                    class="flex items-center justify-between p-3 border rounded-lg"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="flex items-center justify-center w-8 h-8 bg-primary text-primary-foreground rounded-full text-sm font-bold">
                                            {{ index + 1 }}
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <Users class="h-4 w-4 text-muted-foreground" />
                                            <span class="font-medium">{{ author.author }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4 text-sm">
                                        <div class="text-center">
                                            <div class="font-medium">{{ author.content_count }}</div>
                                            <div class="text-xs text-muted-foreground">Content</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="font-medium">{{ formatNumber(author.total_views) }}</div>
                                            <div class="text-xs text-muted-foreground">Views</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </TabsContent>

                <TabsContent value="categories" class="space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <Card>
                            <CardHeader>
                                <CardTitle>News Categories</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-2">
                                    <div 
                                        v-for="(count, category) in dashboardData.category_stats.news" 
                                        :key="category"
                                        class="flex items-center justify-between"
                                    >
                                        <span class="text-sm">{{ category }}</span>
                                        <Badge variant="secondary">{{ count }}</Badge>
                                    </div>
                                    <div v-if="Object.keys(dashboardData.category_stats.news).length === 0" class="text-sm text-muted-foreground text-center py-4">
                                        No news categories found
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle>Event Categories</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <div class="space-y-2">
                                    <div 
                                        v-for="(count, category) in dashboardData.category_stats.events" 
                                        :key="category"
                                        class="flex items-center justify-between"
                                    >
                                        <span class="text-sm">{{ category }}</span>
                                        <Badge variant="secondary">{{ count }}</Badge>
                                    </div>
                                    <div v-if="Object.keys(dashboardData.category_stats.events).length === 0" class="text-sm text-muted-foreground text-center py-4">
                                        No event categories found
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </TabsContent>
                </Tabs>
            </div>
        </div>
    </AppLayout>
</template>
