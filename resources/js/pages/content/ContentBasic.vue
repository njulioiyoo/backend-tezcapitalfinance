<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';

interface Content {
    id: number;
    type: string;
    title_id: string;
    title_en: string;
    excerpt_id: string;
    excerpt_en: string;
    status: string;
    is_featured: boolean;
    view_count: number;
    created_at: string;
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
    filters: any;
    bilingualEnabled: boolean;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Content Management', href: '/content' },
];

// Get localized title
const getLocalizedTitle = (item: Content) => {
    return props.bilingualEnabled ? (item.title_en || item.title_id) : item.title_id;
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
    <Head :title="`${types[type]} Management`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">Content Management</h1>
                    <p class="text-muted-foreground">
                        Manage {{ types[type].toLowerCase() }} content
                    </p>
                </div>
                <Button asChild>
                    <Link :href="route('content.create', { type: type })">
                        New {{ types[type] }}
                    </Link>
                </Button>
            </div>

            <!-- Type Selector -->
            <Card>
                <CardHeader>
                    <CardTitle>Content Type</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="flex gap-2">
                        <Button 
                            v-for="(label, key) in types" 
                            :key="key"
                            :variant="key === type ? 'default' : 'outline'"
                            size="sm"
                            asChild
                        >
                            <Link :href="route('content.index', { type: key })">
                                {{ label }}
                            </Link>
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Content List -->
            <div class="grid gap-4">
                <div v-if="!contents.data || contents.data.length === 0" class="text-center py-12">
                    <p class="text-muted-foreground">No {{ types[type].toLowerCase() }} found.</p>
                    <Button asChild class="mt-4">
                        <Link :href="route('content.create', { type: type })">
                            Create First {{ types[type] }}
                        </Link>
                    </Button>
                </div>

                <Card v-for="item in contents.data" :key="item.id" class="transition-all hover:shadow-md">
                    <CardContent class="p-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold truncate mb-2">
                                    {{ getLocalizedTitle(item) }}
                                </h3>
                                
                                <div class="flex items-center gap-4 mb-2">
                                    <Badge :variant="getStatusVariant(item.status)">
                                        {{ statuses[item.status] || item.status }}
                                    </Badge>
                                    
                                    <Badge v-if="item.is_featured" variant="secondary">
                                        Featured
                                    </Badge>

                                    <span class="text-sm text-muted-foreground">
                                        {{ item.view_count }} views
                                    </span>
                                </div>

                                <p class="text-sm text-muted-foreground">
                                    Created: {{ new Date(item.created_at).toLocaleDateString() }}
                                </p>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <Button size="sm" variant="outline" asChild>
                                    <Link :href="route('content.show', item.id)">
                                        View
                                    </Link>
                                </Button>
                                
                                <Button size="sm" variant="outline" asChild>
                                    <Link :href="route('content.edit', item.id)">
                                        Edit
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Pagination -->
            <div v-if="contents.links && contents.links.length > 3" class="flex justify-center">
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
        </div>
    </AppLayout>
</template>