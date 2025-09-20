<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { onMounted, ref, computed } from 'vue';
import { format } from 'date-fns';
import { TrendingUp, TrendingDown, Users, Eye, Heart, Share2, Calendar, FileText, Star, Activity } from 'lucide-vue-next';

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
  initialData: DashboardData;
}>();

const dashboardData = ref<DashboardData>(props.initialData);
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
    const response = await fetch('/api/content/news-events/dashboard');
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

onMounted(() => {
  // Auto-refresh every 5 minutes
  setInterval(refreshData, 5 * 60 * 1000);
});
</script>

<template>
  <Head title="News & Events Dashboard" />
  
  <div class="space-y-6 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">News & Events Dashboard</h1>
        <p class="text-muted-foreground">Analytics and insights for your content</p>
      </div>
      <div class="flex items-center gap-2 text-sm text-muted-foreground">
        <Activity class="h-4 w-4" />
        <span>Last updated: {{ format(new Date(dashboardData.last_updated), 'HH:mm:ss') }}</span>
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
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Total News</CardTitle>
          <FileText class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ dashboardData.news_stats.total }}</div>
          <p class="text-xs text-muted-foreground">
            {{ dashboardData.news_stats.published }} published
          </p>
        </CardContent>
      </Card>

      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Total Events</CardTitle>
          <Calendar class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ dashboardData.events_stats.total }}</div>
          <p class="text-xs text-muted-foreground">
            {{ dashboardData.events_stats.upcoming }} upcoming
          </p>
        </CardContent>
      </Card>

      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Total Views</CardTitle>
          <Eye class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ formatNumber(dashboardData.engagement_metrics.total_views) }}</div>
          <p class="text-xs text-muted-foreground">
            {{ dashboardData.engagement_metrics.avg_views_per_content }} avg per content
          </p>
        </CardContent>
      </Card>

      <Card>
        <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
          <CardTitle class="text-sm font-medium">Engagement Rate</CardTitle>
          <Heart class="h-4 w-4 text-muted-foreground" />
        </CardHeader>
        <CardContent>
          <div class="text-2xl font-bold">{{ dashboardData.engagement_metrics.engagement_rate }}%</div>
          <p class="text-xs text-muted-foreground">
            {{ formatNumber(totalEngagement) }} total engagements
          </p>
        </CardContent>
      </Card>
    </div>

    <!-- Insights -->
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
    <Tabs default-value="overview" class="space-y-4">
      <TabsList>
        <TabsTrigger value="overview">Overview</TabsTrigger>
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
                      Published {{ format(new Date(item.published_at), 'MMM dd, yyyy') }}
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
                      Published {{ format(new Date(item.published_at), 'MMM dd, yyyy') }}
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
</template>