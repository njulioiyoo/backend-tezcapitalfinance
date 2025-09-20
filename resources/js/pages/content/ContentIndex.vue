<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import { ref, computed } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { toast } from '@/components/ui/toast';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { 
    FileText, 
    Plus,
    Search,
    Filter,
    Calendar,
    MapPin,
    Eye,
    Edit,
    Trash2,
    Globe,
    Languages,
    Star,
    Clock,
    Users
} from 'lucide-vue-next';

interface Content {
    id: number;
    type: string;
    category: string;
    title_id: string;
    title_en: string;
    excerpt_id: string;
    excerpt_en: string;
    featured_image: string | null;
    tags: string[];
    author: string | null;
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
    contents: {
        data?: Content[];
        links?: any[];
        meta?: any;
    };
    type: string;
    types: Record<string, string>;
    categories: Record<string, string>;
    statuses: Record<string, string>;
    filters: {
        search?: string;
        category?: string;
        status?: string;
        featured?: string;
        date_from?: string;
        date_to?: string;
        event_status?: string;
    };
    bilingualEnabled: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    contents: () => ({ data: [], links: [], meta: {} }),
    filters: () => ({}),
    bilingualEnabled: false
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Content Management',
        href: '/content',
    },
];

const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || 'all');
const selectedStatus = ref(props.filters.status || 'all');
const selectedFeatured = ref(props.filters.featured || 'all');
const selectedEventStatus = ref(props.filters.event_status || 'all');
const dateFrom = ref(props.filters.date_from || '');
const dateTo = ref(props.filters.date_to || '');
const currentType = ref(props.type);

const selectedItems = ref(new Set<number>());
const isAllSelected = ref(false);

// Apply filters
const applyFilters = () => {
    const filters: any = {
        type: currentType.value,
        search: search.value || undefined,
        category: selectedCategory.value !== 'all' ? selectedCategory.value : undefined,
        status: selectedStatus.value !== 'all' ? selectedStatus.value : undefined,
        featured: selectedFeatured.value !== 'all' ? selectedFeatured.value : undefined,
    };

    if (currentType.value === 'event') {
        filters.event_status = selectedEventStatus.value !== 'all' ? selectedEventStatus.value : undefined;
        filters.date_from = dateFrom.value || undefined;
        filters.date_to = dateTo.value || undefined;
    }

    router.get(route('content.index'), filters, {
        preserveState: true,
        replace: true,
    });
};

// Switch content type
const switchType = (type: string) => {
    currentType.value = type;
    // Reset filters when switching types
    search.value = '';
    selectedCategory.value = 'all';
    selectedStatus.value = 'all';
    selectedFeatured.value = 'all';
    selectedEventStatus.value = 'all';
    dateFrom.value = '';
    dateTo.value = '';
    
    router.get(route('content.index'), { type }, {
        preserveState: false,
        replace: true,
    });
};

// Clear filters
const clearFilters = () => {
    search.value = '';
    selectedCategory.value = 'all';
    selectedStatus.value = 'all';
    selectedFeatured.value = 'all';
    selectedEventStatus.value = 'all';
    dateFrom.value = '';
    dateTo.value = '';
    applyFilters();
};

// Selection handlers
const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedItems.value.clear();
    } else {
        props.contents.data?.forEach(item => selectedItems.value.add(item.id));
    }
    isAllSelected.value = !isAllSelected.value;
};

const toggleSelectItem = (id: number) => {
    if (selectedItems.value.has(id)) {
        selectedItems.value.delete(id);
    } else {
        selectedItems.value.add(id);
    }
    isAllSelected.value = selectedItems.value.size === props.contents.data?.length;
};

// Bulk actions
const performBulkAction = (action: string) => {
    if (selectedItems.value.size === 0) {
        toast.error('Please select items first');
        return;
    }

    const ids = Array.from(selectedItems.value);
    
    router.post(route('api.system.content.bulk-action'), {
        action,
        ids
    }, {
        onSuccess: () => {
            selectedItems.value.clear();
            isAllSelected.value = false;
            toast.success('Action completed successfully');
        },
        onError: () => {
            toast.error('Action failed');
        }
    });
};

// Delete single item
const deleteItem = (id: number) => {
    if (confirm('Are you sure you want to delete this item?')) {
        router.delete(route('content.destroy', id), {
            onSuccess: () => {
                toast.success('Item deleted successfully');
            },
            onError: () => {
                toast.error('Failed to delete item');
            }
        });
    }
};

// Get localized title
const getLocalizedTitle = (item: Content) => {
    return props.bilingualEnabled ? (item.title_en || item.title_id) : item.title_id;
};

// Get localized excerpt
const getLocalizedExcerpt = (item: Content) => {
    return props.bilingualEnabled ? (item.excerpt_en || item.excerpt_id) : item.excerpt_id;
};

// Get localized location (for events)
const getLocalizedLocation = (item: Content) => {
    return props.bilingualEnabled ? (item.location_en || item.location_id) : item.location_id;
};

// Format date
const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString();
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
</script>

<template>
    <Head :title="`${types[currentType]} Management`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Content Management</h1>
                    <p class="text-muted-foreground">
                        Manage {{ types[currentType].toLowerCase() }} and other content
                    </p>
                </div>
                <Button asChild>
                    <Link :href="route('content.create', { type: currentType })">
                        <Plus class="h-4 w-4 mr-2" />
                        New {{ types[currentType] }}
                    </Link>
                </Button>
            </div>

            <!-- Content Type Tabs -->
            <Tabs :model-value="currentType" @update:model-value="switchType">
                <TabsList class="grid w-full grid-cols-4">
                    <TabsTrigger 
                        v-for="(label, key) in types" 
                        :key="key"
                        :value="key"
                    >
                        {{ label }}
                    </TabsTrigger>
                </TabsList>

                <TabsContent :value="currentType" class="space-y-6">
                    <!-- Filters -->
                    <Card>
                        <CardHeader>
                            <CardTitle class="flex items-center">
                                <Filter class="h-4 w-4 mr-2" />
                                Filters
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Search -->
                                <div class="relative">
                                    <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                                    <Input
                                        v-model="search"
                                        placeholder="Search..."
                                        class="pl-8"
                                        @keyup.enter="applyFilters"
                                    />
                                </div>

                                <!-- Category -->
                                <Select v-model="selectedCategory">
                                    <option value="all">All Categories</option>
                                    <option 
                                        v-for="(label, key) in categories" 
                                        :key="key" 
                                        :value="key"
                                    >
                                        {{ label }}
                                    </option>
                                </Select>

                                <!-- Status -->
                                <Select v-model="selectedStatus">
                                    <option value="all">All Status</option>
                                    <option 
                                        v-for="(label, key) in statuses" 
                                        :key="key" 
                                        :value="key"
                                    >
                                        {{ label }}
                                    </option>
                                </Select>

                                <!-- Featured -->
                                <Select v-model="selectedFeatured">
                                    <option value="all">All Items</option>
                                    <option value="1">Featured Only</option>
                                    <option value="0">Non-Featured</option>
                                </Select>
                            </div>

                            <!-- Event-specific filters -->
                            <div v-if="currentType === 'event'" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <Select v-model="selectedEventStatus">
                                    <option value="all">All Events</option>
                                    <option value="upcoming">Upcoming</option>
                                    <option value="ongoing">Ongoing</option>
                                    <option value="past">Past</option>
                                </Select>
                                
                                <Input
                                    v-model="dateFrom"
                                    type="date"
                                    placeholder="From Date"
                                />
                                
                                <Input
                                    v-model="dateTo"
                                    type="date"
                                    placeholder="To Date"
                                />
                            </div>

                            <div class="flex gap-2">
                                <Button @click="applyFilters">Apply Filters</Button>
                                <Button variant="outline" @click="clearFilters">Clear</Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Bulk Actions -->
                    <Card v-if="selectedItems.size > 0">
                        <CardContent class="pt-6">
                            <div class="flex flex-wrap gap-2">
                                <Button 
                                    size="sm" 
                                    variant="outline"
                                    @click="performBulkAction('publish')"
                                >
                                    Publish Selected
                                </Button>
                                <Button 
                                    size="sm" 
                                    variant="outline"
                                    @click="performBulkAction('unpublish')"
                                >
                                    Unpublish Selected
                                </Button>
                                <Button 
                                    size="sm" 
                                    variant="outline"
                                    @click="performBulkAction('feature')"
                                >
                                    Feature Selected
                                </Button>
                                <Button 
                                    size="sm" 
                                    variant="outline"
                                    @click="performBulkAction('archive')"
                                >
                                    Archive Selected
                                </Button>
                                <Button 
                                    v-if="currentType === 'event'"
                                    size="sm" 
                                    variant="outline"
                                    @click="performBulkAction('cancel')"
                                >
                                    Cancel Events
                                </Button>
                                <Button 
                                    size="sm" 
                                    variant="destructive"
                                    @click="performBulkAction('delete')"
                                >
                                    Delete Selected
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Content List -->
                    <div class="grid gap-4">
                        <div v-if="contents.data?.length === 0" class="text-center py-12">
                            <FileText class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                            <p class="text-muted-foreground">No {{ types[currentType].toLowerCase() }} found.</p>
                        </div>

                        <Card v-for="item in contents.data" :key="item.id" class="transition-all hover:shadow-md">
                            <CardContent class="p-6">
                                <div class="flex items-start gap-4">
                                    <!-- Checkbox -->
                                    <input
                                        type="checkbox"
                                        :checked="selectedItems.has(item.id)"
                                        @change="toggleSelectItem(item.id)"
                                        class="mt-1"
                                    />

                                    <!-- Featured Image -->
                                    <div v-if="item.featured_image" class="flex-shrink-0">
                                        <img 
                                            :src="`/storage/${item.featured_image}`" 
                                            :alt="getLocalizedTitle(item)"
                                            class="w-16 h-16 object-cover rounded-lg"
                                        />
                                    </div>

                                    <!-- Content Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <h3 class="text-lg font-semibold truncate">
                                                    {{ getLocalizedTitle(item) }}
                                                </h3>
                                                
                                                <p v-if="getLocalizedExcerpt(item)" class="text-muted-foreground text-sm mt-1 line-clamp-2">
                                                    {{ getLocalizedExcerpt(item) }}
                                                </p>

                                                <!-- Event specific info -->
                                                <div v-if="item.type === 'event' && item.start_date" class="flex items-center gap-4 mt-2 text-sm text-muted-foreground">
                                                    <div class="flex items-center gap-1">
                                                        <Calendar class="h-4 w-4" />
                                                        {{ formatDate(item.start_date) }}
                                                        <span v-if="item.end_date">- {{ formatDate(item.end_date) }}</span>
                                                    </div>
                                                    <div v-if="getLocalizedLocation(item)" class="flex items-center gap-1">
                                                        <MapPin class="h-4 w-4" />
                                                        {{ getLocalizedLocation(item) }}
                                                    </div>
                                                    <div v-if="item.max_participants" class="flex items-center gap-1">
                                                        <Users class="h-4 w-4" />
                                                        {{ item.registered_count }}/{{ item.max_participants }}
                                                    </div>
                                                </div>

                                                <!-- Meta info -->
                                                <div class="flex items-center gap-4 mt-2">
                                                    <Badge :variant="getStatusVariant(item.status)">
                                                        {{ statuses[item.status] }}
                                                    </Badge>
                                                    
                                                    <Badge v-if="item.category" variant="outline">
                                                        {{ categories[item.category] }}
                                                    </Badge>
                                                    
                                                    <Badge v-if="item.is_featured" variant="secondary">
                                                        <Star class="h-3 w-3 mr-1" />
                                                        Featured
                                                    </Badge>

                                                    <div class="flex items-center gap-1 text-sm text-muted-foreground">
                                                        <Eye class="h-4 w-4" />
                                                        {{ item.view_count }}
                                                    </div>

                                                    <div v-if="item.author" class="text-sm text-muted-foreground">
                                                        by {{ item.author }}
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Actions -->
                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <Button size="sm" variant="outline" asChild>
                                                    <Link :href="route('content.show', item.id)">
                                                        <Eye class="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                                
                                                <Button size="sm" variant="outline" asChild>
                                                    <Link :href="route('content.edit', item.id)">
                                                        <Edit class="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                                
                                                <Button 
                                                    size="sm" 
                                                    variant="destructive"
                                                    @click="deleteItem(item.id)"
                                                >
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Pagination -->
                    <div v-if="contents.links" class="flex justify-center">
                        <div class="flex gap-2">
                            <Link
                                v-for="link in contents.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    'px-3 py-2 text-sm rounded-md',
                                    link.active 
                                        ? 'bg-primary text-primary-foreground' 
                                        : 'bg-background border hover:bg-accent',
                                    !link.url && 'opacity-50 cursor-not-allowed'
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </TabsContent>
            </Tabs>
        </div>
    </AppLayout>
</template>