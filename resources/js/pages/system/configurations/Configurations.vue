<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent } from '@/components/ui/card';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Button } from '@/components/ui/button';
import { toast } from '@/components/ui/toast';
import { 
    Settings, 
    Palette, 
    Home, 
    // CreditCard, 
    Wrench, 
    Phone,
    Globe,
    RefreshCw,
    Image,
    Users,
} from 'lucide-vue-next';
import GeneralSettings from '@/components/configurations/GeneralSettings.vue';
import BrandingSettings from '@/components/configurations/BrandingSettings.vue';
import HomepageSettings from '@/components/configurations/HomepageSettings.vue';
// import CreditSettings from '@/components/configurations/CreditSettings.vue';
import MaintenanceSettings from '@/components/configurations/MaintenanceSettings.vue';
import ContactSettings from '@/components/configurations/ContactSettings.vue';
import LanguageSettings from '@/components/configurations/LanguageSettings.vue';
import BannerSettings from '@/components/configurations/BannerSettings.vue';
import OjkSettings from '@/components/configurations/OjkSettings.vue';
import JoinUsSettings from '@/components/configurations/JoinUsSettings.vue';
import { useConfigurations } from '@/composables/useConfigurations.js';

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'System', href: '/system' },
    { label: 'Configurations', href: '/system/configurations' }
];

const activeTab = ref('general');

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



onMounted(() => {
    loadConfigurations();
});


// Debug configurations
watch(configurations, (newConfigs) => {
    console.log('Configurations updated:', newConfigs);
    console.log('Banners data:', newConfigs.banners);
}, { deep: true });
</script>

<template>
    <Head title="Website Configurations" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Settings class="w-8 h-8 text-primary" />
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Website Configurations</h1>
                            <p class="text-muted-foreground">
                                Manage your website settings, branding, content, and system configurations
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
                <Card>
                    <CardContent>
                        <Tabs v-model:modelValue="activeTab" class="w-full">
                            <TabsList class="grid w-full grid-cols-9">
                                <TabsTrigger value="general" class="flex items-center gap-2">
                                    <Settings class="w-4 h-4" />
                                    General
                                </TabsTrigger>
                                <TabsTrigger value="branding" class="flex items-center gap-2">
                                    <Palette class="w-4 h-4" />
                                    Branding
                                </TabsTrigger>
                                <TabsTrigger value="homepage" class="flex items-center gap-2">
                                    <Home class="w-4 h-4" />
                                    Homepage
                                </TabsTrigger>
                                <!-- <TabsTrigger value="credit" class="flex items-center gap-2">
                                    <CreditCard class="w-4 h-4" />
                                    Credit
                                </TabsTrigger> -->
                                <TabsTrigger value="maintenance" class="flex items-center gap-2">
                                    <Wrench class="w-4 h-4" />
                                    Maintenance
                                </TabsTrigger>
                                <TabsTrigger value="contact" class="flex items-center gap-2">
                                    <Phone class="w-4 h-4" />
                                    Contact
                                </TabsTrigger>
                                <TabsTrigger value="language" class="flex items-center gap-2">
                                    <Globe class="w-4 h-4" />
                                    Language
                                </TabsTrigger>
                                <TabsTrigger value="banners" class="flex items-center gap-2">
                                    <Image class="w-4 h-4" />
                                    Banners
                                </TabsTrigger>
                                <TabsTrigger value="ojk" class="flex items-center gap-2">
                                    <Image class="w-4 h-4" />
                                    OJK
                                </TabsTrigger>
                                <TabsTrigger value="join-us" class="flex items-center gap-2">
                                    <Users class="w-4 h-4" />
                                    Join Us
                                </TabsTrigger>
                            </TabsList>

                            <div class="mt-6">
                                <TabsContent value="general" class="space-y-4">
                                    <GeneralSettings 
                                        :configurations="configurations.general || {}"
                                        :is-loading="isLoading"
                                        :is-saving="isSaving"
                                        @save="saveConfiguration"
                                        @update="updateConfiguration"
                                        @bulk-save="saveBulkConfigurations"
                                    />
                                </TabsContent>

                                <TabsContent value="branding" class="space-y-4">
                                    <BrandingSettings 
                                        :configurations="configurations.branding || {}"
                                        :is-loading="isLoading"
                                        :is-saving="isSaving"
                                        @save="saveConfiguration"
                                        @update="updateConfiguration"
                                        @bulk-save="saveBulkConfigurations"
                                    />
                                </TabsContent>

                                <TabsContent value="homepage" class="space-y-4">
                                    <HomepageSettings 
                                        :configurations="configurations.homepage || {}"
                                        :is-loading="isLoading"
                                        :is-saving="isSaving"
                                        @save="saveConfiguration"
                                        @update="updateConfiguration"
                                        @bulk-save="saveBulkConfigurations"
                                    />
                                </TabsContent>

                                <!-- <TabsContent value="credit" class="space-y-4">
                                    <CreditSettings 
                                        :configurations="configurations.credit || {}"
                                        :is-loading="isLoading"
                                        :is-saving="isSaving"
                                        @save="saveConfiguration"
                                        @update="updateConfiguration"
                                        @bulk-save="saveBulkConfigurations"
                                    />
                                </TabsContent> -->

                                <TabsContent value="maintenance" class="space-y-4">
                                    <MaintenanceSettings 
                                        :configurations="configurations.maintenance || {}"
                                        :is-loading="isLoading"
                                        :is-saving="isSaving"
                                        @save="saveConfiguration"
                                        @update="updateConfiguration"
                                        @bulk-save="saveBulkConfigurations"
                                    />
                                </TabsContent>

                                <TabsContent value="contact" class="space-y-4">
                                    <ContactSettings 
                                        :configurations="configurations.contact || {}"
                                        :is-loading="isLoading"
                                        :is-saving="isSaving"
                                        @save="saveConfiguration"
                                        @update="updateConfiguration"
                                        @bulk-save="saveBulkConfigurations"
                                    />
                                </TabsContent>

                                <TabsContent value="language" class="space-y-4">
                                    <LanguageSettings 
                                        :configurations="configurations.language || {}"
                                        :is-loading="isLoading"
                                        :is-saving="isSaving"
                                        @save="saveConfiguration"
                                        @update="updateConfiguration"
                                        @bulk-save="saveBulkConfigurations"
                                    />
                                </TabsContent>

                                <TabsContent value="banners" class="space-y-4">
                                    <BannerSettings 
                                        :configurations="configurations.banners || {}"
                                        :is-loading="isLoading"
                                        :is-saving="isSaving"
                                        @save="saveConfiguration"
                                        @update="updateConfiguration"
                                        @bulkSave="saveBulkConfigurations"
                                    />
                                </TabsContent>

                                <TabsContent value="ojk" class="space-y-4">
                                    <OjkSettings 
                                        :configurations="configurations.ojk || {}"
                                        :is-loading="isLoading"
                                        :is-saving="isSaving"
                                        @save="saveConfiguration"
                                        @update="updateConfiguration"
                                        @bulk-save="saveBulkConfigurations"
                                    />
                                </TabsContent>

                                <TabsContent value="join-us" class="space-y-4">
                                    <JoinUsSettings 
                                        :configurations="configurations.join_us || {}"
                                        :is-loading="isLoading"
                                        :is-saving="isSaving"
                                        @save="saveConfiguration"
                                        @update="updateConfiguration"
                                        @bulk-save="saveBulkConfigurations"
                                    />
                                </TabsContent>

                            </div>
                        </Tabs>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>