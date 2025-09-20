<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'System Management',
        href: '/system',
    },
    {
        title: 'Audit Log',
        href: '/audit-log',
    },
];

const message = ref('Loading...');

onMounted(() => {
    message.value = 'Audit Log component mounted successfully!';
    // AuditLogSimple component mounted
});
</script>

<template>
    <Head title="Audit Log" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Audit Log</h1>
                        <p class="text-muted-foreground">Track all system activities and changes</p>
                    </div>
                </div>

                <!-- Simple Test Card -->
                <Card>
                    <CardHeader>
                        <CardTitle>System Test</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p>{{ message }}</p>
                        <p class="mt-4 text-sm text-muted-foreground">
                            If you can see this message, the Vue component is working correctly.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>