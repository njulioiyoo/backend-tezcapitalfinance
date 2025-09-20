<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';

interface Content {
    id: number;
    type: string;
    title_id: string;
    title_en: string;
    excerpt_id: string;
    excerpt_en: string;
    is_published: boolean;
    status: string;
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
    {
        title: 'Content Management',
        href: '/content',
    },
];

// Get localized title
const getLocalizedTitle = (item: Content) => {
    return props.bilingualEnabled ? (item.title_en || item.title_id) : item.title_id;
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
                        Manage {{ types[type].toLowerCase() }} and other content
                    </p>
                </div>
                <Button asChild>
                    <Link :href="`/content/create?type=${type}`">
                        New {{ types[type] }}
                    </Link>
                </Button>
            </div>

            <!-- Simple Content List -->
            <div class="grid gap-4">
                <div v-if="contents.data?.length === 0" class="text-center py-12">
                    <p class="text-muted-foreground">No {{ types[type].toLowerCase() }} found.</p>
                </div>

                <Card v-for="item in contents.data" :key="item.id">
                    <CardHeader>
                        <CardTitle>{{ getLocalizedTitle(item) }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-muted-foreground">
                                    Status: {{ statuses[item.status] || item.status }}
                                </p>
                                <p class="text-sm text-muted-foreground">
                                    Views: {{ item.view_count }}
                                </p>
                            </div>
                            <div class="flex gap-2">
                                <Button size="sm" variant="outline" asChild>
                                    <Link :href="`/content/${item.id}`">
                                        View
                                    </Link>
                                </Button>
                                <Button size="sm" variant="outline" asChild>
                                    <Link :href="`/content/${item.id}/edit`">
                                        Edit
                                    </Link>
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Debug Info -->
            <Card>
                <CardHeader>
                    <CardTitle>Debug Info</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-2 text-sm">
                        <p>Current Type: {{ type }}</p>
                        <p>Available Types: {{ JSON.stringify(types) }}</p>
                        <p>Contents Count: {{ contents.data?.length || 0 }}</p>
                        <p>Bilingual Enabled: {{ bilingualEnabled }}</p>
                        <p>Categories: {{ JSON.stringify(categories) }}</p>
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>