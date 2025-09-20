<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { 
    FileText, 
    RefreshCw,
    Info,
    BookOpen,
    Eye,
    Target,
    Image,
    Zap,
    Shield,
    MessageSquare
} from 'lucide-vue-next';
import AboutContentTabbed from '@/components/content/AboutContentTabbed.vue';
import { useConfigurations } from '@/composables/useConfigurations.js';

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Content Management', href: '/content' },
    { label: 'About Page', href: '/content/about' }
];

// Use the configurations composable
const {
    configurations,
    isLoading,
    isSaving,
    loadConfigurations,
    saveBulkConfigurations,
    saveConfiguration,
    updateConfiguration
} = useConfigurations();

const activeTab = ref('our-story');

onMounted(() => {
    loadConfigurations();
});
</script>

<template>
    <Head title="About Page Content" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <FileText class="w-8 h-8 text-primary" />
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">About Page Content</h1>
                            <p class="text-muted-foreground">
                                Manage your about page content including company story, vision, mission, and values
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Button 
                            @click="loadConfigurations" 
                            variant="outline" 
                            size="sm"
                            :disabled="isLoading"
                        >
                            <RefreshCw :class="{ 'animate-spin': isLoading }" class="w-4 h-4 mr-2" />
                            Refresh
                        </Button>
                    </div>
                </div>

                <!-- Tabs for Better Organization -->
                <Tabs v-model="activeTab" class="w-full">
                    <TabsList class="grid w-full grid-cols-7">
                        <TabsTrigger value="our-story" class="flex items-center gap-2">
                            <BookOpen class="w-4 h-4" />
                            Our Story
                        </TabsTrigger>
                        <TabsTrigger value="vision" class="flex items-center gap-2">
                            <Eye class="w-4 h-4" />
                            Vision
                        </TabsTrigger>
                        <TabsTrigger value="mission" class="flex items-center gap-2">
                            <Target class="w-4 h-4" />
                            Mission
                        </TabsTrigger>
                        <TabsTrigger value="logo-philosophy" class="flex items-center gap-2">
                            <Image class="w-4 h-4" />
                            Logo
                        </TabsTrigger>
                        <TabsTrigger value="fast-values" class="flex items-center gap-2">
                            <Zap class="w-4 h-4" />
                            F.A.S.T
                        </TabsTrigger>
                        <TabsTrigger value="idc-values" class="flex items-center gap-2">
                            <Shield class="w-4 h-4" />
                            I.D.C
                        </TabsTrigger>
                        <TabsTrigger value="closing" class="flex items-center gap-2">
                            <MessageSquare class="w-4 h-4" />
                            Closing
                        </TabsTrigger>
                    </TabsList>

                    <div class="mt-6">
                        <TabsContent value="our-story">
                            <AboutContentTabbed 
                                :configurations="configurations.about || {}"
                                :is-loading="isLoading"
                                :is-saving="isSaving"
                                section="our-story"
                                @save="saveConfiguration"
                                @update="updateConfiguration"
                                @bulk-save="saveBulkConfigurations"
                            />
                        </TabsContent>

                        <TabsContent value="vision">
                            <AboutContentTabbed 
                                :configurations="configurations.about || {}"
                                :is-loading="isLoading"
                                :is-saving="isSaving"
                                section="vision"
                                @save="saveConfiguration"
                                @update="updateConfiguration"
                                @bulk-save="saveBulkConfigurations"
                            />
                        </TabsContent>

                        <TabsContent value="mission">
                            <AboutContentTabbed 
                                :configurations="configurations.about || {}"
                                :is-loading="isLoading"
                                :is-saving="isSaving"
                                section="mission"
                                @save="saveConfiguration"
                                @update="updateConfiguration"
                                @bulk-save="saveBulkConfigurations"
                            />
                        </TabsContent>

                        <TabsContent value="logo-philosophy">
                            <AboutContentTabbed 
                                :configurations="configurations.about || {}"
                                :is-loading="isLoading"
                                :is-saving="isSaving"
                                section="logo-philosophy"
                                @save="saveConfiguration"
                                @update="updateConfiguration"
                                @bulk-save="saveBulkConfigurations"
                            />
                        </TabsContent>

                        <TabsContent value="fast-values">
                            <AboutContentTabbed 
                                :configurations="configurations.about || {}"
                                :is-loading="isLoading"
                                :is-saving="isSaving"
                                section="fast-values"
                                @save="saveConfiguration"
                                @update="updateConfiguration"
                                @bulk-save="saveBulkConfigurations"
                            />
                        </TabsContent>

                        <TabsContent value="idc-values">
                            <AboutContentTabbed 
                                :configurations="configurations.about || {}"
                                :is-loading="isLoading"
                                :is-saving="isSaving"
                                section="idc-values"
                                @save="saveConfiguration"
                                @update="updateConfiguration"
                                @bulk-save="saveBulkConfigurations"
                            />
                        </TabsContent>

                        <TabsContent value="closing">
                            <AboutContentTabbed 
                                :configurations="configurations.about || {}"
                                :is-loading="isLoading"
                                :is-saving="isSaving"
                                section="closing"
                                @save="saveConfiguration"
                                @update="updateConfiguration"
                                @bulk-save="saveBulkConfigurations"
                            />
                        </TabsContent>
                    </div>
                </Tabs>
            </div>
        </div>
    </AppLayout>
</template>