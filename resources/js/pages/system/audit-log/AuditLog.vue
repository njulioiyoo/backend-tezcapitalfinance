<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Separator } from '@/components/ui/separator';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { 
    Shield, 
    RefreshCw, 
    Search, 
    Filter,
    Eye,
    Clock,
    User,
    Globe,
    ChevronLeft,
    ChevronRight,
    Calendar,
    Monitor,
    Database,
    Activity,
    Info
} from 'lucide-vue-next';

interface User {
    id: number;
    name: string;
    email: string;
}

interface AuditLogItem {
    id: number;
    event: string;
    auditable_type: string;
    auditable_id: number;
    user: User | null;
    old_values: Record<string, any>;
    new_values: Record<string, any>;
    url: string;
    ip_address: string;
    user_agent: string;
    created_at: string;
    created_at_human: string;
}

interface FilterOptions {
    users: Array<{id: number; text: string}>;
    models: Array<{id: string; text: string}>;
    events: Array<{id: string; text: string}>;
}

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'System', href: '/system' },
    { title: 'Audit Log', href: '/system/audit-log' },
];

// Reactive data
const audits = ref<AuditLogItem[]>([]);
const loading = ref(false);
const filterOptions = ref<FilterOptions>({
    users: [],
    models: [],
    events: []
});

// Filter states
const filters = ref({
    auditable_type: '',
    event: '',
    date_from: '',
    date_to: '',
    user_id: '',
    per_page: '10'
});

const currentPage = ref(1);
const totalPages = ref(1);
const totalItems = ref(0);

// Modal state
const isDetailModalOpen = ref(false);
const selectedAudit = ref<AuditLogItem | null>(null);

// Methods
const loadAudits = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams({
            page: currentPage.value.toString(),
            ...Object.fromEntries(
                Object.entries(filters.value).filter(([_, value]) => value !== '')
            )
        });

        const response = await fetch(`/api/system/audit-log?${params}`);
        const data = await response.json();
        
        audits.value = data.data;
        currentPage.value = data.current_page;
        totalPages.value = data.last_page;
        totalItems.value = data.total;
    } catch (error) {
        // Error loading audits
    } finally {
        loading.value = false;
    }
};

const loadFilterOptions = async () => {
    try {
        const response = await fetch('/api/system/audit-log/filter-options');
        const data = await response.json();
        filterOptions.value = data;
    } catch (error) {
        // Error loading filter options
    }
};

const clearFilters = () => {
    filters.value = {
        auditable_type: '',
        event: '',
        date_from: '',
        date_to: '',
        user_id: '',
        per_page: '10'
    };
    currentPage.value = 1;
    loadAudits();
};

const applyFilters = () => {
    currentPage.value = 1;
    loadAudits();
};

const goToPage = (page: number) => {
    currentPage.value = page;
    loadAudits();
};

const viewAudit = (audit: AuditLogItem) => {
    selectedAudit.value = audit;
    isDetailModalOpen.value = true;
};

const closeDetailModal = () => {
    isDetailModalOpen.value = false;
    selectedAudit.value = null;
};

const formatJsonData = (data: Record<string, any>) => {
    if (!data || Object.keys(data).length === 0) return null;
    return JSON.stringify(data, null, 2);
};

const getEventBadgeVariant = (event: string) => {
    switch (event) {
        case 'created': return 'default';
        case 'updated': return 'secondary';
        case 'deleted': return 'destructive';
        case 'restored': return 'outline';
        default: return 'secondary';
    }
};

// Computed
const paginationPages = computed(() => {
    const pages = [];
    const maxVisible = 5;
    let start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
    const end = Math.min(totalPages.value, start + maxVisible - 1);
    
    if (end - start + 1 < maxVisible) {
        start = Math.max(1, end - maxVisible + 1);
    }
    
    for (let i = start; i <= end; i++) {
        pages.push(i);
    }
    
    return pages;
});

// Lifecycle
onMounted(() => {
    loadFilterOptions();
    loadAudits();
});
</script>

<template>
    <Head title="Audit Log" />
    
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Shield class="w-8 h-8 text-primary" />
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Audit Log</h1>
                            <p class="text-muted-foreground">
                                Track all system activities and changes
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Button 
                            @click="loadAudits" 
                            variant="outline" 
                            size="sm"
                            :disabled="loading"
                        >
                            <RefreshCw :class="{ 'animate-spin': loading }" class="w-4 h-4 mr-2" />
                            Refresh
                        </Button>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid auto-rows-min gap-4 md:grid-cols-4">
                    <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern />
                        <div class="relative z-10 p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-muted-foreground">Total Entries</span>
                                <Activity class="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div class="text-2xl font-bold">{{ totalItems }}</div>
                            <p class="text-xs text-muted-foreground">
                                {{ filters.per_page }} per page
                            </p>
                        </div>
                    </div>

                    <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern />
                        <div class="relative z-10 p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-muted-foreground">Current Page</span>
                                <Database class="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div class="text-2xl font-bold">{{ currentPage }}</div>
                            <p class="text-xs text-muted-foreground">
                                of {{ totalPages }} pages
                            </p>
                        </div>
                    </div>

                    <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern />
                        <div class="relative z-10 p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-muted-foreground">Filter Status</span>
                                <Filter class="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div class="text-2xl font-bold">{{ Object.values(filters).filter(v => v && v !== '10').length }}</div>
                            <p class="text-xs text-muted-foreground">
                                active filters
                            </p>
                        </div>
                    </div>

                    <div class="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern />
                        <div class="relative z-10 p-6">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-muted-foreground">System Status</span>
                                <Shield class="h-4 w-4 text-muted-foreground" />
                            </div>
                            <div class="text-2xl font-bold text-green-600">Active</div>
                            <p class="text-xs text-muted-foreground">
                                audit logging
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent">
                        <CardHeader>
                            <CardTitle class="flex items-center gap-2">
                                <Filter class="w-4 h-4" />
                                Filters
                            </CardTitle>
                            <CardDescription>
                                Filter audit logs by various criteria
                            </CardDescription>
                        </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4">
                            <!-- User Filter -->
                            <div class="space-y-2">
                                <Label>User</Label>
                                <Select v-model="filters.user_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Users" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Users</SelectItem>
                                        <SelectItem 
                                            v-for="user in filterOptions.users" 
                                            :key="user.id" 
                                            :value="user.id.toString()"
                                        >
                                            {{ user.text }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Model Filter -->
                            <div class="space-y-2">
                                <Label>Model</Label>
                                <Select v-model="filters.auditable_type">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Models" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Models</SelectItem>
                                        <SelectItem 
                                            v-for="model in filterOptions.models" 
                                            :key="model.id" 
                                            :value="model.id"
                                        >
                                            {{ model.text }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Event Filter -->
                            <div class="space-y-2">
                                <Label>Event</Label>
                                <Select v-model="filters.event">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Events" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Events</SelectItem>
                                        <SelectItem 
                                            v-for="event in filterOptions.events" 
                                            :key="event.id" 
                                            :value="event.id"
                                        >
                                            {{ event.text }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>

                            <!-- Date From -->
                            <div class="space-y-2">
                                <Label>Date From</Label>
                                <Input
                                    type="date"
                                    v-model="filters.date_from"
                                />
                            </div>

                            <!-- Date To -->
                            <div class="space-y-2">
                                <Label>Date To</Label>
                                <Input
                                    type="date"
                                    v-model="filters.date_to"
                                />
                            </div>

                            <!-- Per Page -->
                            <div class="space-y-2">
                                <Label>Per Page</Label>
                                <Select v-model="filters.per_page">
                                    <SelectTrigger>
                                        <SelectValue placeholder="10" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="10">10</SelectItem>
                                        <SelectItem value="20">20</SelectItem>
                                        <SelectItem value="50">50</SelectItem>
                                        <SelectItem value="100">100</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div class="flex gap-2 mt-4">
                            <Button @click="applyFilters">
                                <Search class="w-4 h-4 mr-2" />
                                Apply Filters
                            </Button>
                            <Button variant="outline" @click="clearFilters">Clear Filters</Button>
                        </div>
                    </CardContent>
                    </Card>
                </div>

                <!-- Audit Log Table -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent">
                    <CardHeader>
                        <CardTitle>Audit Entries</CardTitle>
                        <CardDescription>
                            Showing {{ audits.length > 0 ? ((currentPage - 1) * parseInt(filters.per_page) + 1) : 0 }}-{{ Math.min(currentPage * parseInt(filters.per_page), totalItems) }} of {{ totalItems }} entries
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="loading" class="flex justify-center py-8">
                            <div class="text-muted-foreground">Loading audit logs...</div>
                        </div>
                        
                        <div v-else-if="audits.length === 0" class="text-center py-8">
                            <div class="text-muted-foreground mb-4">
                                No audit entries found.
                            </div>
                        </div>

                        <div v-else class="overflow-x-auto">
                            <table class="w-full min-w-[800px]">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left p-2 sm:p-4 font-medium min-w-[80px]">Event</th>
                                        <th class="text-left p-2 sm:p-4 font-medium min-w-[120px]">Model</th>
                                        <th class="text-left p-2 sm:p-4 font-medium min-w-[150px]">User</th>
                                        <th class="text-left p-2 sm:p-4 font-medium min-w-[100px]">IP Address</th>
                                        <th class="text-left p-2 sm:p-4 font-medium min-w-[120px]">Date</th>
                                        <th class="text-right p-2 sm:p-4 font-medium min-w-[80px]">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr 
                                        v-for="audit in audits" 
                                        :key="audit.id"
                                        class="border-b hover:bg-muted/50"
                                    >
                                        <td class="p-2 sm:p-4">
                                            <Badge :variant="getEventBadgeVariant(audit.event)">
                                                {{ audit.event }}
                                            </Badge>
                                        </td>
                                        <td class="p-2 sm:p-4">
                                            <div>
                                                <div class="font-medium text-sm">{{ audit.auditable_type }}</div>
                                                <div class="text-xs text-muted-foreground">#{{ audit.auditable_id }}</div>
                                            </div>
                                        </td>
                                        <td class="p-2 sm:p-4">
                                            <div v-if="audit.user">
                                                <div class="font-medium text-sm">{{ audit.user.name }}</div>
                                                <div class="text-xs text-muted-foreground truncate max-w-[120px]">{{ audit.user.email }}</div>
                                            </div>
                                            <span v-else class="text-muted-foreground text-sm">System</span>
                                        </td>
                                        <td class="p-2 sm:p-4">
                                            <div class="flex items-center gap-1 sm:gap-2">
                                                <Globe class="w-3 h-3 sm:w-4 sm:h-4 text-muted-foreground flex-shrink-0" />
                                                <span class="text-xs sm:text-sm font-mono truncate">{{ audit.ip_address }}</span>
                                            </div>
                                        </td>
                                        <td class="p-2 sm:p-4">
                                            <div class="flex items-center gap-1 sm:gap-2">
                                                <Clock class="w-3 h-3 sm:w-4 sm:h-4 text-muted-foreground flex-shrink-0" />
                                                <div>
                                                    <div class="text-xs sm:text-sm">{{ audit.created_at_human }}</div>
                                                    <div class="text-xs text-muted-foreground hidden sm:block">{{ new Date(audit.created_at).toLocaleString() }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-2 sm:p-4">
                                            <div class="flex items-center justify-end gap-2">
                                                <Button 
                                                    variant="outline" 
                                                    size="sm" 
                                                    @click="viewAudit(audit)"
                                                >
                                                    <Eye class="w-4 h-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="totalPages > 1" class="flex items-center justify-between mt-6">
                            <div class="text-sm text-muted-foreground">
                                Page {{ currentPage }} of {{ totalPages }}
                            </div>
                            <div class="flex items-center gap-2">
                                <Button 
                                    variant="outline" 
                                    size="sm"
                                    :disabled="currentPage === 1"
                                    @click="goToPage(currentPage - 1)"
                                >
                                    <ChevronLeft class="w-4 h-4" />
                                    Previous
                                </Button>

                                <Button
                                    v-for="page in paginationPages"
                                    :key="page"
                                    :variant="page === currentPage ? 'default' : 'outline'"
                                    size="sm"
                                    @click="goToPage(page)"
                                >
                                    {{ page }}
                                </Button>

                                <Button 
                                    variant="outline" 
                                    size="sm"
                                    :disabled="currentPage === totalPages"
                                    @click="goToPage(currentPage + 1)"
                                >
                                    Next
                                    <ChevronRight class="w-4 h-4" />
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                    </Card>
                </div>
            </div>
        </div>

        <!-- Audit Detail Modal -->
        <Dialog :open="isDetailModalOpen" @update:open="closeDetailModal">
            <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle class="flex items-center gap-2">
                        <Activity class="w-5 h-5" />
                        Audit Details
                    </DialogTitle>
                    <DialogDescription>
                        Detailed information about this audit log entry
                    </DialogDescription>
                </DialogHeader>

                <div v-if="selectedAudit" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                            <PlaceholderPattern />
                            <Card class="relative z-10 border-0 shadow-none bg-transparent">
                                <CardHeader class="pb-3">
                                    <CardTitle class="text-sm font-medium flex items-center gap-2">
                                        <Info class="w-4 h-4" />
                                        Event Information
                                    </CardTitle>
                                </CardHeader>
                                <CardContent class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-muted-foreground">Event Type</span>
                                        <Badge :variant="getEventBadgeVariant(selectedAudit.event)">
                                            {{ selectedAudit.event.toUpperCase() }}
                                        </Badge>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-muted-foreground">Model</span>
                                        <span class="text-sm font-medium">{{ selectedAudit.auditable_type }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-muted-foreground">Record ID</span>
                                        <span class="text-sm font-mono">#{{ selectedAudit.auditable_id }}</span>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>

                        <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                            <PlaceholderPattern />
                            <Card class="relative z-10 border-0 shadow-none bg-transparent">
                                <CardHeader class="pb-3">
                                    <CardTitle class="text-sm font-medium flex items-center gap-2">
                                        <User class="w-4 h-4" />
                                        User Information
                                    </CardTitle>
                                </CardHeader>
                                <CardContent class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-muted-foreground">User</span>
                                        <div class="text-right">
                                            <div class="text-sm font-medium">{{ selectedAudit.user?.name || 'System' }}</div>
                                            <div class="text-xs text-muted-foreground">{{ selectedAudit.user?.email || 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-muted-foreground">IP Address</span>
                                        <span class="text-sm font-mono">{{ selectedAudit.ip_address }}</span>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </div>

                    <!-- Timestamp and URL -->
                    <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern />
                        <Card class="relative z-10 border-0 shadow-none bg-transparent">
                            <CardHeader class="pb-3">
                                <CardTitle class="text-sm font-medium flex items-center gap-2">
                                    <Calendar class="w-4 h-4" />
                                    Request Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-muted-foreground">Timestamp</span>
                                    <div class="text-right">
                                        <div class="text-sm font-medium">{{ selectedAudit.created_at_human }}</div>
                                        <div class="text-xs text-muted-foreground">{{ new Date(selectedAudit.created_at).toLocaleString() }}</div>
                                    </div>
                                </div>
                                <div v-if="selectedAudit.url" class="flex items-start justify-between">
                                    <span class="text-sm text-muted-foreground">URL</span>
                                    <span class="text-sm font-mono text-blue-600 max-w-md text-right break-all">{{ selectedAudit.url }}</span>
                                </div>
                                <div v-if="selectedAudit.user_agent" class="flex items-start justify-between">
                                    <span class="text-sm text-muted-foreground">User Agent</span>
                                    <span class="text-xs text-muted-foreground max-w-md text-right break-all">{{ selectedAudit.user_agent }}</span>
                                </div>
                            </CardContent>
                        </Card>
                    </div>

                    <!-- Data Changes -->
                    <div v-if="formatJsonData(selectedAudit.old_values) || formatJsonData(selectedAudit.new_values)" class="space-y-4">
                        <Separator />
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <!-- Old Values -->
                            <div v-if="formatJsonData(selectedAudit.old_values)">
                                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                                    <PlaceholderPattern />
                                    <Card class="relative z-10 border-0 shadow-none bg-transparent">
                                        <CardHeader class="pb-3">
                                            <CardTitle class="text-sm font-medium flex items-center gap-2">
                                                <Database class="w-4 h-4 text-red-500" />
                                                Previous Values
                                            </CardTitle>
                                        </CardHeader>
                                        <CardContent>
                                            <pre class="text-xs bg-red-50 dark:bg-red-950/20 p-3 rounded border overflow-x-auto whitespace-pre-wrap">{{ formatJsonData(selectedAudit.old_values) }}</pre>
                                        </CardContent>
                                    </Card>
                                </div>
                            </div>

                            <!-- New Values -->
                            <div v-if="formatJsonData(selectedAudit.new_values)">
                                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                                    <PlaceholderPattern />
                                    <Card class="relative z-10 border-0 shadow-none bg-transparent">
                                        <CardHeader class="pb-3">
                                            <CardTitle class="text-sm font-medium flex items-center gap-2">
                                                <Database class="w-4 h-4 text-green-500" />
                                                New Values
                                            </CardTitle>
                                        </CardHeader>
                                        <CardContent>
                                            <pre class="text-xs bg-green-50 dark:bg-green-950/20 p-3 rounded border overflow-x-auto whitespace-pre-wrap">{{ formatJsonData(selectedAudit.new_values) }}</pre>
                                        </CardContent>
                                    </Card>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state for no data changes -->
                    <div v-else class="text-center py-8">
                        <Monitor class="w-12 h-12 text-muted-foreground mx-auto mb-2" />
                        <p class="text-sm text-muted-foreground">No data changes recorded for this audit entry</p>
                    </div>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>