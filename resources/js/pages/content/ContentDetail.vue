<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Separator } from '@/components/ui/separator';
import { 
    ArrowLeft, 
    Edit, 
    Eye,
    Calendar,
    MapPin,
    Users,
    DollarSign,
    Clock,
    Globe,
    Share2,
    Heart,
    Star,
    User,
    ExternalLink
} from 'lucide-vue-next';

interface Content {
    id: number;
    type: string;
    category: string;
    title_id: string;
    title_en: string;
    excerpt_id: string;
    excerpt_en: string;
    content_id: string;
    content_en: string;
    featured_image: string | null;
    tags: string[];
    author: string | null;
    source_url: string | null;
    location_id: string | null;
    location_en: string | null;
    start_date: string | null;
    end_date: string | null;
    organizer: string | null;
    price: number | null;
    max_participants: number | null;
    registered_count: number;
    is_published: boolean;
    is_featured: boolean;
    published_at: string | null;
    status: string;
    view_count: number;
    like_count: number;
    share_count: number;
    sort_order: number;
    created_at: string;
    updated_at: string;
}

interface Props {
    content: Content;
    type: string;
    types: Record<string, string>;
    categories: Record<string, string>;
    statuses: Record<string, string>;
    bilingualEnabled: boolean;
    currentLanguage: string;
}

const props = defineProps<Props>();

const currentLang = ref(props.currentLanguage || 'id');

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Content Management', href: '/content' },
    { title: props.types[props.type], href: `/content?type=${props.type}` },
    { title: getLocalizedTitle(props.content, currentLang.value), href: '' }
];

// Get localized content
function getLocalizedTitle(item: Content, language: string = 'id') {
    return language === 'en' ? (item.title_en || item.title_id) : item.title_id;
}

function getLocalizedExcerpt(item: Content, language: string = 'id') {
    return language === 'en' ? (item.excerpt_en || item.excerpt_id) : item.excerpt_id;
}

function getLocalizedContent(item: Content, language: string = 'id') {
    return language === 'en' ? (item.content_en || item.content_id) : item.content_id;
}

function getLocalizedLocation(item: Content, language: string = 'id') {
    return language === 'en' ? (item.location_en || item.location_id) : item.location_id;
}

// Format date
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

const formatDateTime = (dateString: string) => {
    return new Date(dateString).toLocaleString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Get status badge variant
const getStatusVariant = (status: string) => {
    switch (status) {
        case 'published': return 'success';
        case 'draft': return 'secondary';
        case 'archived': return 'outline';
        case 'cancelled': return 'destructive';
        default: return 'secondary';
    }
};

// Check if event
const isEvent = computed(() => props.content.type === 'event');

// Event status
const eventStatus = computed(() => {
    if (!isEvent.value || !props.content.start_date) return '';
    
    const now = new Date();
    const startDate = new Date(props.content.start_date);
    const endDate = props.content.end_date ? new Date(props.content.end_date) : null;
    
    if (props.content.status === 'cancelled') return 'cancelled';
    if (startDate > now) return 'upcoming';
    if (endDate && endDate < now) return 'completed';
    if (startDate <= now && (!endDate || endDate >= now)) return 'ongoing';
    return '';
});

const eventStatusLabels = {
    upcoming: 'Upcoming',
    ongoing: 'Ongoing', 
    completed: 'Completed',
    cancelled: 'Cancelled'
};

// Toggle language
const toggleLanguage = () => {
    currentLang.value = currentLang.value === 'id' ? 'en' : 'id';
};
</script>

<template>
    <Head :title="getLocalizedTitle(content, currentLang)" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div class="flex items-center gap-4">
                    <Button variant="outline" asChild>
                        <Link :href="`/content?type=${type}`">
                            <ArrowLeft class="h-4 w-4 mr-2" />
                            Back to {{ types[type] }}
                        </Link>
                    </Button>
                    
                    <Button v-if="bilingualEnabled" variant="outline" @click="toggleLanguage">
                        <Globe class="h-4 w-4 mr-2" />
                        {{ currentLang === 'id' ? 'Bahasa' : 'English' }}
                    </Button>
                </div>

                <div class="flex items-center gap-2">
                    <Button variant="outline" asChild>
                        <Link :href="route('content.edit', content.id)">
                            <Edit class="h-4 w-4 mr-2" />
                            Edit
                        </Link>
                    </Button>
                </div>
            </div>

            <!-- Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <Card>
                        <CardContent class="p-6">
                            <!-- Featured Image -->
                            <div v-if="content.featured_image" class="mb-6">
                                <img 
                                    :src="`/storage/${content.featured_image}`" 
                                    :alt="getLocalizedTitle(content, currentLang)"
                                    class="w-full h-64 object-cover rounded-lg"
                                />
                            </div>

                            <!-- Title -->
                            <div class="mb-4">
                                <h1 class="text-3xl font-bold tracking-tight mb-2">
                                    {{ getLocalizedTitle(content, currentLang) }}
                                </h1>

                                <!-- Meta info -->
                                <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                                    <div class="flex items-center gap-1">
                                        <Eye class="h-4 w-4" />
                                        {{ content.view_count }} views
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <Heart class="h-4 w-4" />
                                        {{ content.like_count }} likes
                                    </div>

                                    <div class="flex items-center gap-1">
                                        <Share2 class="h-4 w-4" />
                                        {{ content.share_count }} shares
                                    </div>

                                    <div v-if="content.author" class="flex items-center gap-1">
                                        <User class="h-4 w-4" />
                                        {{ content.author }}
                                    </div>

                                    <div v-if="content.published_at">
                                        <Clock class="h-4 w-4 inline mr-1" />
                                        {{ formatDate(content.published_at) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Excerpt -->
                            <div v-if="getLocalizedExcerpt(content, currentLang)" class="mb-6">
                                <p class="text-lg text-muted-foreground leading-relaxed">
                                    {{ getLocalizedExcerpt(content, currentLang) }}
                                </p>
                            </div>

                            <!-- Event Details -->
                            <div v-if="isEvent && content.start_date" class="mb-6">
                                <Card class="border-l-4 border-l-primary">
                                    <CardHeader>
                                        <CardTitle class="flex items-center">
                                            <Calendar class="h-4 w-4 mr-2" />
                                            Event Information
                                        </CardTitle>
                                    </CardHeader>
                                    <CardContent class="space-y-4">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="flex items-center gap-2">
                                                <Calendar class="h-4 w-4 text-muted-foreground" />
                                                <div>
                                                    <div class="font-medium">Start Date</div>
                                                    <div class="text-sm text-muted-foreground">
                                                        {{ formatDateTime(content.start_date) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div v-if="content.end_date" class="flex items-center gap-2">
                                                <Calendar class="h-4 w-4 text-muted-foreground" />
                                                <div>
                                                    <div class="font-medium">End Date</div>
                                                    <div class="text-sm text-muted-foreground">
                                                        {{ formatDateTime(content.end_date) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div v-if="getLocalizedLocation(content, currentLang)" class="flex items-center gap-2">
                                                <MapPin class="h-4 w-4 text-muted-foreground" />
                                                <div>
                                                    <div class="font-medium">Location</div>
                                                    <div class="text-sm text-muted-foreground">
                                                        {{ getLocalizedLocation(content, currentLang) }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div v-if="content.organizer" class="flex items-center gap-2">
                                                <User class="h-4 w-4 text-muted-foreground" />
                                                <div>
                                                    <div class="font-medium">Organizer</div>
                                                    <div class="text-sm text-muted-foreground">
                                                        {{ content.organizer }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div v-if="content.price" class="flex items-center gap-2">
                                                <DollarSign class="h-4 w-4 text-muted-foreground" />
                                                <div>
                                                    <div class="font-medium">Price</div>
                                                    <div class="text-sm text-muted-foreground">
                                                        ${{ content.price }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div v-if="content.max_participants" class="flex items-center gap-2">
                                                <Users class="h-4 w-4 text-muted-foreground" />
                                                <div>
                                                    <div class="font-medium">Participants</div>
                                                    <div class="text-sm text-muted-foreground">
                                                        {{ content.registered_count }}/{{ content.max_participants }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Event Status -->
                                        <div v-if="eventStatus" class="pt-2">
                                            <Badge 
                                                :variant="eventStatus === 'cancelled' ? 'destructive' : eventStatus === 'completed' ? 'outline' : 'default'"
                                                class="text-sm"
                                            >
                                                {{ eventStatusLabels[eventStatus] }}
                                            </Badge>
                                        </div>
                                    </CardContent>
                                </Card>
                            </div>

                            <Separator class="my-6" />

                            <!-- Content Body -->
                            <div 
                                class="prose prose-gray dark:prose-invert max-w-none"
                                v-html="getLocalizedContent(content, currentLang)"
                            />

                            <!-- Source Link -->
                            <div v-if="content.source_url" class="mt-6 pt-6 border-t">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-muted-foreground">Source:</span>
                                    <a 
                                        :href="content.source_url" 
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="text-sm text-primary hover:underline flex items-center gap-1"
                                    >
                                        {{ content.source_url }}
                                        <ExternalLink class="h-3 w-3" />
                                    </a>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Status & Meta -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Information</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium">Status</span>
                                <Badge :variant="getStatusVariant(content.status)">
                                    {{ statuses[content.status] }}
                                </Badge>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium">Type</span>
                                <Badge variant="outline">
                                    {{ types[content.type] }}
                                </Badge>
                            </div>

                            <div v-if="content.category" class="flex items-center justify-between">
                                <span class="text-sm font-medium">Category</span>
                                <Badge variant="secondary">
                                    {{ categories[content.category] }}
                                </Badge>
                            </div>

                            <div v-if="content.is_featured" class="flex items-center justify-between">
                                <span class="text-sm font-medium">Featured</span>
                                <Badge variant="default">
                                    <Star class="h-3 w-3 mr-1" />
                                    Yes
                                </Badge>
                            </div>

                            <Separator />

                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Created:</span>
                                    <span>{{ formatDate(content.created_at) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Updated:</span>
                                    <span>{{ formatDate(content.updated_at) }}</span>
                                </div>
                                <div v-if="content.published_at" class="flex justify-between">
                                    <span class="text-muted-foreground">Published:</span>
                                    <span>{{ formatDate(content.published_at) }}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Tags -->
                    <Card v-if="content.tags.length > 0">
                        <CardHeader>
                            <CardTitle>Tags</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="flex flex-wrap gap-2">
                                <Badge 
                                    v-for="tag in content.tags" 
                                    :key="tag"
                                    variant="outline"
                                    class="text-xs"
                                >
                                    {{ tag }}
                                </Badge>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Analytics -->
                    <Card>
                        <CardHeader>
                            <CardTitle>Analytics</CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Eye class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm">Views</span>
                                </div>
                                <span class="font-medium">{{ content.view_count }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Heart class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm">Likes</span>
                                </div>
                                <span class="font-medium">{{ content.like_count }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Share2 class="h-4 w-4 text-muted-foreground" />
                                    <span class="text-sm">Shares</span>
                                </div>
                                <span class="font-medium">{{ content.share_count }}</span>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.prose {
    color: inherit;
}

.prose h1,
.prose h2,
.prose h3,
.prose h4,
.prose h5,
.prose h6 {
    color: inherit;
}

.prose a {
    color: hsl(var(--primary));
    text-decoration: none;
}

.prose a:hover {
    text-decoration: underline;
}
</style>