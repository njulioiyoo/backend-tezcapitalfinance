<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';

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
    per_page: '20'
});

const currentPage = ref(1);
const totalPages = ref(1);
const totalItems = ref(0);

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
        per_page: '20'
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
    let end = Math.min(totalPages.value, start + maxVisible - 1);
    
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
    
    <AppLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Audit Log</h1>
                    <p class="text-muted-foreground">
                        Track all system activities and changes
                    </p>
                </div>
            </div>

            <!-- Filters -->
            <Card>
                <CardHeader>
                    <CardTitle>Filters</CardTitle>
                    <CardDescription>
                        Filter audit logs by various criteria
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <!-- User Filter -->
                        <div class="space-y-2">
                            <Label>User</Label>
                            <Select v-model="filters.user_id">
                                <option value="">All Users</option>
                                <option 
                                    v-for="user in filterOptions.users" 
                                    :key="user.id" 
                                    :value="user.id.toString()"
                                >
                                    {{ user.text }}
                                </option>
                            </Select>
                        </div>

                        <!-- Model Filter -->
                        <div class="space-y-2">
                            <Label>Model</Label>
                            <Select v-model="filters.auditable_type">
                                <option value="">All Models</option>
                                <option 
                                    v-for="model in filterOptions.models" 
                                    :key="model.id" 
                                    :value="model.id"
                                >
                                    {{ model.text }}
                                </option>
                            </Select>
                        </div>

                        <!-- Event Filter -->
                        <div class="space-y-2">
                            <Label>Event</Label>
                            <Select v-model="filters.event">
                                <option value="">All Events</option>
                                <option 
                                    v-for="event in filterOptions.events" 
                                    :key="event.id" 
                                    :value="event.id"
                                >
                                    {{ event.text }}
                                </option>
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
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </Select>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <Button @click="applyFilters">Apply Filters</Button>
                        <Button variant="outline" @click="clearFilters">Clear Filters</Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Audit Log Table -->
            <Card>
                <CardHeader>
                    <CardTitle>Audit Entries</CardTitle>
                    <CardDescription>
                        Total: {{ totalItems }} entries
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div v-if="loading" class="flex justify-center py-8">
                        <div class="text-muted-foreground">Loading...</div>
                    </div>
                    
                    <div v-else-if="audits.length === 0" class="text-center py-8 text-muted-foreground">
                        No audit entries found.
                    </div>

                    <div v-else class="space-y-4">
                        <div 
                            v-for="audit in audits" 
                            :key="audit.id"
                            class="border rounded-lg p-4 space-y-3"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <Badge :variant="getEventBadgeVariant(audit.event)">
                                        {{ audit.event }}
                                    </Badge>
                                    <span class="font-medium">{{ audit.auditable_type }}</span>
                                    <span class="text-sm text-muted-foreground">#{{ audit.auditable_id }}</span>
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    {{ audit.created_at_human }}
                                </div>
                            </div>

                            <div class="grid gap-2 text-sm">
                                <div v-if="audit.user" class="flex gap-2">
                                    <span class="font-medium">User:</span>
                                    <span>{{ audit.user.name }} ({{ audit.user.email }})</span>
                                </div>
                                
                                <div class="flex gap-2">
                                    <span class="font-medium">IP:</span>
                                    <span>{{ audit.ip_address }}</span>
                                </div>

                                <div v-if="audit.url" class="flex gap-2">
                                    <span class="font-medium">URL:</span>
                                    <span class="text-blue-600 break-all">{{ audit.url }}</span>
                                </div>

                                <div v-if="Object.keys(audit.old_values).length > 0" class="mt-2">
                                    <span class="font-medium">Old Values:</span>
                                    <pre class="text-xs bg-gray-50 dark:bg-gray-800 p-2 rounded mt-1 overflow-auto">{{ JSON.stringify(audit.old_values, null, 2) }}</pre>
                                </div>

                                <div v-if="Object.keys(audit.new_values).length > 0" class="mt-2">
                                    <span class="font-medium">New Values:</span>
                                    <pre class="text-xs bg-gray-50 dark:bg-gray-800 p-2 rounded mt-1 overflow-auto">{{ JSON.stringify(audit.new_values, null, 2) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="totalPages > 1" class="flex justify-center gap-2 mt-6">
                        <Button 
                            variant="outline" 
                            size="sm"
                            :disabled="currentPage === 1"
                            @click="goToPage(currentPage - 1)"
                        >
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
                        </Button>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>