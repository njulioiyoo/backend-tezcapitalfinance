<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

interface Props {
    contents: any;
    type: string;
    types: Record<string, string>;
    categories: Record<string, string>;
    statuses: Record<string, string>;
    filters: any;
    bilingualEnabled: boolean;
}

defineProps<Props>();

const breadcrumbs = [
    { title: 'Content Management', href: '/content' }
];
</script>

<template>
    <Head title="Content Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <h1 class="text-2xl font-bold">Content Management - Test Page</h1>
            <p>This is a simple test to verify Vue components are working.</p>
            
            <div class="bg-white p-4 rounded-lg border">
                <h2 class="text-lg font-semibold mb-2">Debug Info:</h2>
                <p>Type: {{ type }}</p>
                <p>Types: {{ JSON.stringify(types) }}</p>
                <p>Contents count: {{ contents.data?.length || 0 }}</p>
                <p>Bilingual: {{ bilingualEnabled }}</p>
            </div>
        </div>
    </AppLayout>
</template>