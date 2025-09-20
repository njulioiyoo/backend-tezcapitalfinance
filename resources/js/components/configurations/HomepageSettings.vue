<script setup lang="ts">
import { ref, reactive, watch } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Upload, Save, Loader2, Image, X, Plus, Trash2, Move, ChevronDown, ChevronUp } from 'lucide-vue-next';

interface Configuration {
    [key: string]: {
        value: any;
        type: string;
        description: string;
        is_public: boolean;
    };
}

interface Props {
    configurations: Configuration;
    isLoading: boolean;
    isSaving: boolean;
}

interface Banner {
    image: string;
    title: string;
    subtitle: string;
    link: string;
}

interface Feature {
    title: string;
    description: string;
    icon: string;
}

interface SixReasonItem {
    icon: string;
    title_id: string;
    title_en: string;
    description_id: string;
    description_en: string;
}

interface AppProcessStep {
    icon: string;
    title_id: string;
    title_en: string;
    description_id: string;
    description_en: string;
}

interface FaqItem {
    id: number;
    question_id: string;
    question_en: string;
    answer_id: string;
    answer_en: string;
    order: number;
    is_active: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    save: [group: string, key: string, value: any, type: string];
    update: [configId: number, group: string, key: string, value: any, type: string];
    bulkSave: [group: string, changes: Array<{key: string, value: any, type: string}>];
}>();

const form = reactive({
    hero_title: '',
    hero_subtitle: '',
    banners: [] as Banner[],
    features: [] as Feature[],
    // Six Reasons Section
    six_reasons_title_id: '',
    six_reasons_title_en: '',
    six_reasons_subtitle_id: '',
    six_reasons_subtitle_en: '',
    six_reasons_items: [] as SixReasonItem[],
    // Application Process Section
    app_process_title_id: '',
    app_process_title_en: '',
    app_process_subtitle_id: '',
    app_process_subtitle_en: '',
    app_process_steps: [] as AppProcessStep[],
    // FAQ Section
    faq_title_id: '',
    faq_title_en: '',
    faq_subtitle_id: '',
    faq_subtitle_en: '',
    faq_items: [] as FaqItem[],
});

const bannerFiles = ref<File[]>([]);
const sixReasonsIconFiles = ref<File[]>([]);
const appProcessIconFiles = ref<File[]>([]);

// Preview states for file uploads
const bannerPreviews = ref<string[]>([]);
const sixReasonsIconPreviews = ref<string[]>([]);
const appProcessIconPreviews = ref<string[]>([]);

// Helper function to convert storage path to full URL
const getImageUrl = (path: string) => {
    if (!path) return '';
    if (path.startsWith('data:') || path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }
    // Add cache busting timestamp
    const timestamp = new Date().getTime();
    return `/storage/${path}?t=${timestamp}`;
};

// Collapse state for sections
const collapsedSections = ref({
    hero: true,
    sixReasons: true,
    appProcess: true,
    faq: true,
    banners: true,
    features: true,
});

// Watch for configuration changes and update form
watch(() => props.configurations, (newConfigs) => {
    if (newConfigs) {
        form.hero_title = newConfigs.homepage_hero_title?.value || '';
        form.hero_subtitle = newConfigs.homepage_hero_subtitle?.value || '';
        
        try {
            form.banners = JSON.parse(newConfigs.homepage_banners?.value || '[]');
        } catch {
            form.banners = [];
        }
        
        try {
            form.features = JSON.parse(newConfigs.homepage_features?.value || '[]');
        } catch {
            form.features = [
                { title: 'Aman & Terpercaya', description: 'Sistem keamanan berlapis dengan teknologi enkripsi terdepan', icon: 'shield' },
                { title: 'Bunga Kompetitif', description: 'Dapatkan return investasi hingga 15% per tahun', icon: 'trending-up' },
                { title: 'Proses Cepat', description: 'Approval dalam 24 jam dengan persyaratan yang mudah', icon: 'clock' }
            ];
        }

        // Six Reasons Section
        form.six_reasons_title_id = newConfigs.homepage_six_reasons_title_id?.value || '';
        form.six_reasons_title_en = newConfigs.homepage_six_reasons_title_en?.value || '';
        form.six_reasons_subtitle_id = newConfigs.homepage_six_reasons_subtitle_id?.value || '';
        form.six_reasons_subtitle_en = newConfigs.homepage_six_reasons_subtitle_en?.value || '';
        
        try {
            const sixReasonsData = newConfigs.homepage_six_reasons_items?.value || '[]';
            const parsedSixReasons = typeof sixReasonsData === 'string' ? JSON.parse(sixReasonsData) : sixReasonsData;
            form.six_reasons_items = parsedSixReasons;
        } catch (error) {
            // Error parsing six reasons items
            form.six_reasons_items = [];
        }

        // Application Process Section
        form.app_process_title_id = newConfigs.homepage_app_process_title_id?.value || '';
        form.app_process_title_en = newConfigs.homepage_app_process_title_en?.value || '';
        form.app_process_subtitle_id = newConfigs.homepage_app_process_subtitle_id?.value || '';
        form.app_process_subtitle_en = newConfigs.homepage_app_process_subtitle_en?.value || '';
        
        // Debug application process steps
        // Application Process Debug
        
        try {
            const stepsData = newConfigs.homepage_app_process_steps?.value || '[]';
            const parsedSteps = typeof stepsData === 'string' ? JSON.parse(stepsData) : stepsData;
            
            // Update icons from individual configuration files if available
            parsedSteps.forEach((step: any, index: number) => {
                const iconKey = `homepage_app_process_step_${index + 1}_icon`;
                if (newConfigs[iconKey]?.value) {
                    // Use full URL from configuration value (Configuration model getValue() returns full URL)
                    step.icon = newConfigs[iconKey].value;
                }
            });
            
            form.app_process_steps = parsedSteps;
            // Parsed app process steps
        } catch (error) {
            // Error parsing app process steps
            form.app_process_steps = [];
        }

        // FAQ Section
        form.faq_title_id = newConfigs.homepage_faq_title_id?.value || '';
        form.faq_title_en = newConfigs.homepage_faq_title_en?.value || '';
        form.faq_subtitle_id = newConfigs.homepage_faq_subtitle_id?.value || '';
        form.faq_subtitle_en = newConfigs.homepage_faq_subtitle_en?.value || '';
        
        try {
            const faqItemsRaw = newConfigs.homepage_faq_items?.value;
            form.faq_items = typeof faqItemsRaw === 'string' ? JSON.parse(faqItemsRaw) : (faqItemsRaw || []);
        } catch (error) {
            // Error parsing FAQ items
            form.faq_items = [];
        }
    }
}, { immediate: true, deep: true });

const addBanner = () => {
    form.banners.push({
        image: '',
        title: '',
        subtitle: '',
        link: ''
    });
};

const removeBanner = (index: number) => {
    form.banners.splice(index, 1);
    bannerFiles.value.splice(index, 1);
};

const handleBannerUpload = (event: Event, index: number) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        bannerFiles.value[index] = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            bannerPreviews.value[index] = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const addFeature = () => {
    form.features.push({
        title: '',
        description: '',
        icon: 'star'
    });
};

const removeFeature = (index: number) => {
    form.features.splice(index, 1);
};

const addSixReasonItem = () => {
    form.six_reasons_items.push({
        icon: '/images/icons/star.svg',
        title_id: '',
        title_en: '',
        description_id: '',
        description_en: ''
    });
};

const removeSixReasonItem = (index: number) => {
    form.six_reasons_items.splice(index, 1);
    sixReasonsIconFiles.value.splice(index, 1);
};

const addAppProcessStep = () => {
    const newStep = {
        icon: 'shield',
        title_id: '',
        title_en: '',
        description_id: '',
        description_en: '',
        order: form.app_process_steps.length + 1
    };
    
    form.app_process_steps.push(newStep);
    
    // Force Vue reactivity by updating the array reference
    form.app_process_steps = [...form.app_process_steps];
    
    // Added new app process step
};

const removeAppProcessStep = (index: number) => {
    form.app_process_steps.splice(index, 1);
    appProcessIconFiles.value.splice(index, 1);
    
    // Force Vue reactivity by updating the array reference
    form.app_process_steps = [...form.app_process_steps];
    
    // Removed app process step
};

const addFaqItem = () => {
    form.faq_items.push({
        id: Date.now(),
        question_id: '',
        question_en: '',
        answer_id: '',
        answer_en: '',
        order: form.faq_items.length + 1,
        is_active: true
    });
};

const removeFaqItem = (index: number) => {
    form.faq_items.splice(index, 1);
    // Update order for remaining items
    form.faq_items.forEach((item, idx) => {
        item.order = idx + 1;
    });
};

const handleSixReasonsIconUpload = (event: Event, index: number) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        sixReasonsIconFiles.value[index] = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            sixReasonsIconPreviews.value[index] = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const handleAppProcessIconUpload = (event: Event, index: number) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        appProcessIconFiles.value[index] = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            appProcessIconPreviews.value[index] = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const saveSettings = () => {
    const changes = [];
    const fileUploads = [];

    // Check for changes and collect them
    // Checking homepage changes

    if (props.configurations.homepage_hero_title?.value !== form.hero_title) {
        changes.push({ key: 'homepage_hero_title', value: form.hero_title, type: 'string' });
    }

    if (props.configurations.homepage_hero_subtitle?.value !== form.hero_subtitle) {
        changes.push({ key: 'homepage_hero_subtitle', value: form.hero_subtitle, type: 'text' });
    }

    const currentBannersStr = props.configurations.homepage_banners?.value || '[]';
    const formBannersStr = JSON.stringify(form.banners);
    if (currentBannersStr !== formBannersStr) {
        changes.push({ key: 'homepage_banners', value: form.banners, type: 'json' });
    }

    const currentFeaturesStr = props.configurations.homepage_features?.value || '[]';
    const formFeaturesStr = JSON.stringify(form.features);
    if (currentFeaturesStr !== formFeaturesStr) {
        changes.push({ key: 'homepage_features', value: form.features, type: 'json' });
    }

    // Six Reasons Section
    if (props.configurations.homepage_six_reasons_title_id?.value !== form.six_reasons_title_id) {
        changes.push({ key: 'homepage_six_reasons_title_id', value: form.six_reasons_title_id, type: 'text' });
    }

    if (props.configurations.homepage_six_reasons_title_en?.value !== form.six_reasons_title_en) {
        changes.push({ key: 'homepage_six_reasons_title_en', value: form.six_reasons_title_en, type: 'text' });
    }

    if (props.configurations.homepage_six_reasons_subtitle_id?.value !== form.six_reasons_subtitle_id) {
        changes.push({ key: 'homepage_six_reasons_subtitle_id', value: form.six_reasons_subtitle_id, type: 'text' });
    }

    if (props.configurations.homepage_six_reasons_subtitle_en?.value !== form.six_reasons_subtitle_en) {
        changes.push({ key: 'homepage_six_reasons_subtitle_en', value: form.six_reasons_subtitle_en, type: 'text' });
    }

    const currentSixReasonsStr = props.configurations.homepage_six_reasons_items?.value || '[]';
    const formSixReasonsStr = JSON.stringify(form.six_reasons_items);
    if (currentSixReasonsStr !== formSixReasonsStr) {
        changes.push({ key: 'homepage_six_reasons_items', value: form.six_reasons_items, type: 'json' });
    }

    // Application Process Section
    if (props.configurations.homepage_app_process_title_id?.value !== form.app_process_title_id) {
        changes.push({ key: 'homepage_app_process_title_id', value: form.app_process_title_id, type: 'text' });
    }

    if (props.configurations.homepage_app_process_title_en?.value !== form.app_process_title_en) {
        changes.push({ key: 'homepage_app_process_title_en', value: form.app_process_title_en, type: 'text' });
    }

    if (props.configurations.homepage_app_process_subtitle_id?.value !== form.app_process_subtitle_id) {
        changes.push({ key: 'homepage_app_process_subtitle_id', value: form.app_process_subtitle_id, type: 'text' });
    }

    if (props.configurations.homepage_app_process_subtitle_en?.value !== form.app_process_subtitle_en) {
        changes.push({ key: 'homepage_app_process_subtitle_en', value: form.app_process_subtitle_en, type: 'text' });
    }

    const currentAppProcessStr = props.configurations.homepage_app_process_steps?.value || '[]';
    const formAppProcessStr = JSON.stringify(form.app_process_steps);
    if (currentAppProcessStr !== formAppProcessStr) {
        changes.push({ key: 'homepage_app_process_steps', value: form.app_process_steps, type: 'json' });
    }

    // FAQ Section
    if (props.configurations.homepage_faq_title_id?.value !== form.faq_title_id) {
        changes.push({ key: 'homepage_faq_title_id', value: form.faq_title_id, type: 'text' });
    }

    if (props.configurations.homepage_faq_title_en?.value !== form.faq_title_en) {
        changes.push({ key: 'homepage_faq_title_en', value: form.faq_title_en, type: 'text' });
    }

    if (props.configurations.homepage_faq_subtitle_id?.value !== form.faq_subtitle_id) {
        changes.push({ key: 'homepage_faq_subtitle_id', value: form.faq_subtitle_id, type: 'text' });
    }

    if (props.configurations.homepage_faq_subtitle_en?.value !== form.faq_subtitle_en) {
        changes.push({ key: 'homepage_faq_subtitle_en', value: form.faq_subtitle_en, type: 'text' });
    }

    const currentFaqStr = props.configurations.homepage_faq_items?.value || '[]';
    const formFaqStr = JSON.stringify(form.faq_items);
    if (currentFaqStr !== formFaqStr) {
        changes.push({ key: 'homepage_faq_items', value: form.faq_items, type: 'json' });
    }

    // Handle file uploads for Six Reasons icons
    sixReasonsIconFiles.value.forEach((file, index) => {
        if (file) {
            changes.push({
                key: `homepage_six_reasons_item_${index + 1}_icon`,
                value: file,
                type: 'file'
            });
        }
    });

    // Handle file uploads for App Process icons  
    appProcessIconFiles.value.forEach((file, index) => {
        if (file) {
            changes.push({
                key: `homepage_app_process_step_${index + 1}_icon`, 
                value: file,
                type: 'file'
            });
        }
    });

    // Homepage changes detected

    // Only save if there are changes
    if (changes.length > 0) {
        emit('bulkSave', 'homepage', changes);
        
        // Clear file references after save to prevent conflicts
        appProcessIconFiles.value = [];
        sixReasonsIconFiles.value = [];
    } else {
        // No homepage changes to save
    }
};

const toggleSection = (section: string) => {
    collapsedSections.value[section] = !collapsedSections.value[section];
};

const iconOptions = [
    'shield', 'trending-up', 'clock', 'star', 'heart', 'award', 'check-circle',
    'users', 'globe', 'lock', 'zap', 'target', 'thumbs-up', 'gift'
];
</script>

<template>
    <div class="space-y-6">
        <!-- Hero Section -->
        <Card>
            <CardHeader>
                <CardTitle 
                    class="flex items-center justify-between cursor-pointer" 
                    @click="toggleSection('hero')"
                >
                    <span>Hero Section</span>
                    <ChevronDown v-if="!collapsedSections.hero" class="w-4 h-4" />
                    <ChevronUp v-else class="w-4 h-4" />
                </CardTitle>
                <CardDescription v-if="!collapsedSections.hero">
                    Main homepage banner content that visitors see first
                </CardDescription>
            </CardHeader>
            <Transition name="collapse">
                <CardContent v-if="!collapsedSections.hero" class="space-y-4">
                <div class="space-y-2">
                    <Label for="hero_title">Hero Title</Label>
                    <Input
                        id="hero_title"
                        v-model="form.hero_title"
                        placeholder="Enter hero title"
                        :disabled="isLoading"
                    />
                </div>

                <div class="space-y-2">
                    <Label for="hero_subtitle">Hero Subtitle</Label>
                    <Textarea
                        id="hero_subtitle"
                        v-model="form.hero_subtitle"
                        placeholder="Enter hero subtitle"
                        :disabled="isLoading"
                        :rows="3"
                    />
                </div>
                </CardContent>
            </Transition>
        </Card>

        <!-- Six Reasons Section -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center justify-between">
                    <div 
                        class="cursor-pointer flex-1"
                        @click="toggleSection('sixReasons')"
                    >
                        <span>Six Reasons to Choose TEZ</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button 
                            v-if="!collapsedSections.sixReasons"
                            @click="addSixReasonItem" 
                            variant="outline" 
                            size="sm"
                        >
                            <Plus class="w-4 h-4 mr-2" />
                            Add Reason
                        </Button>
                        <ChevronDown 
                            v-if="!collapsedSections.sixReasons" 
                            class="w-4 h-4 cursor-pointer" 
                            @click="toggleSection('sixReasons')"
                        />
                        <ChevronUp 
                            v-else 
                            class="w-4 h-4 cursor-pointer" 
                            @click="toggleSection('sixReasons')"
                        />
                    </div>
                </CardTitle>
                <CardDescription v-if="!collapsedSections.sixReasons">
                    Six key reasons why customers should choose your services
                </CardDescription>
            </CardHeader>
            <Transition name="collapse">
                <CardContent v-if="!collapsedSections.sixReasons" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="six_reasons_title_id">Section Title (Indonesian)</Label>
                        <Input
                            id="six_reasons_title_id"
                            v-model="form.six_reasons_title_id"
                            placeholder="Enter section title in Indonesian"
                            :disabled="isLoading"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="six_reasons_title_en">Section Title (English)</Label>
                        <Input
                            id="six_reasons_title_en"
                            v-model="form.six_reasons_title_en"
                            placeholder="Enter section title in English"
                            :disabled="isLoading"
                        />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="six_reasons_subtitle_id">Section Subtitle (Indonesian)</Label>
                        <Textarea
                            id="six_reasons_subtitle_id"
                            v-model="form.six_reasons_subtitle_id"
                            placeholder="Enter section subtitle in Indonesian"
                            :disabled="isLoading"
                            :rows="2"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="six_reasons_subtitle_en">Section Subtitle (English)</Label>
                        <Textarea
                            id="six_reasons_subtitle_en"
                            v-model="form.six_reasons_subtitle_en"
                            placeholder="Enter section subtitle in English"
                            :disabled="isLoading"
                            :rows="2"
                        />
                    </div>
                </div>

                <div class="space-y-4">
                    <div v-if="form.six_reasons_items.length === 0" class="text-center py-8 text-muted-foreground">
                        No reasons configured. Click "Add Reason" to create one.
                    </div>

                    <div v-for="(item, index) in form.six_reasons_items" :key="index" class="border border-input rounded-lg p-4 space-y-4 dark:border-input">
                        <div class="flex items-center justify-between">
                            <Badge variant="outline">Reason {{ index + 1 }}</Badge>
                            <Button @click="removeSixReasonItem(index)" variant="destructive" size="sm">
                                <Trash2 class="w-4 h-4" />
                            </Button>
                        </div>

                        <div class="grid gap-4">
                            <div class="space-y-2">
                                <Label>Icon</Label>
                                <div class="border-2 border-dashed border-input rounded-lg p-4 dark:border-input">
                                    <div v-if="sixReasonsIconPreviews[index] || item.icon" class="space-y-2">
                                        <img :src="sixReasonsIconPreviews[index] || getImageUrl(item.icon)" alt="Icon Preview" class="w-16 h-16 object-contain mx-auto" />
                                        <Button size="sm" variant="outline" @click="$refs[`sixReasonsIconInput${index}`][0].click()">
                                            <Upload class="w-4 h-4 mr-2" />
                                            Change Icon
                                        </Button>
                                    </div>
                                    <div v-else class="text-center">
                                        <Image class="mx-auto h-8 w-8 text-gray-400" />
                                        <Button size="sm" variant="outline" @click="$refs[`sixReasonsIconInput${index}`][0].click()">
                                            <Upload class="w-4 h-4 mr-2" />
                                            Upload Icon
                                        </Button>
                                    </div>
                                    <input
                                        :ref="`sixReasonsIconInput${index}`"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleSixReasonsIconUpload($event, index)"
                                    />
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label>Title (Indonesian)</Label>
                                    <Input
                                        v-model="item.title_id"
                                        placeholder="Enter title in Indonesian"
                                        :disabled="isLoading"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label>Title (English)</Label>
                                    <Input
                                        v-model="item.title_en"
                                        placeholder="Enter title in English"
                                        :disabled="isLoading"
                                    />
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label>Description (Indonesian)</Label>
                                    <Textarea
                                        v-model="item.description_id"
                                        placeholder="Enter description in Indonesian"
                                        :disabled="isLoading"
                                        :rows="2"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label>Description (English)</Label>
                                    <Textarea
                                        v-model="item.description_en"
                                        placeholder="Enter description in English"
                                        :disabled="isLoading"
                                        :rows="2"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </CardContent>
            </Transition>
        </Card>

        <!-- Application Process Section -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center justify-between">
                    <div 
                        class="cursor-pointer flex-1"
                        @click="toggleSection('appProcess')"
                    >
                        <span>Application Process</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button 
                            v-if="!collapsedSections.appProcess"
                            @click="addAppProcessStep" 
                            variant="outline" 
                            size="sm"
                        >
                            <Plus class="w-4 h-4 mr-2" />
                            Add Step
                        </Button>
                        <ChevronDown 
                            v-if="!collapsedSections.appProcess" 
                            class="w-4 h-4 cursor-pointer" 
                            @click="toggleSection('appProcess')"
                        />
                        <ChevronUp 
                            v-else 
                            class="w-4 h-4 cursor-pointer" 
                            @click="toggleSection('appProcess')"
                        />
                    </div>
                </CardTitle>
                <CardDescription v-if="!collapsedSections.appProcess">
                    Step-by-step process for application workflow
                </CardDescription>
            </CardHeader>
            <Transition name="collapse">
                <CardContent v-if="!collapsedSections.appProcess" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="app_process_title_id">Section Title (Indonesian)</Label>
                        <Input
                            id="app_process_title_id"
                            v-model="form.app_process_title_id"
                            placeholder="Enter section title in Indonesian"
                            :disabled="isLoading"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="app_process_title_en">Section Title (English)</Label>
                        <Input
                            id="app_process_title_en"
                            v-model="form.app_process_title_en"
                            placeholder="Enter section title in English"
                            :disabled="isLoading"
                        />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="app_process_subtitle_id">Section Subtitle (Indonesian)</Label>
                        <Textarea
                            id="app_process_subtitle_id"
                            v-model="form.app_process_subtitle_id"
                            placeholder="Enter section subtitle in Indonesian"
                            :disabled="isLoading"
                            :rows="2"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="app_process_subtitle_en">Section Subtitle (English)</Label>
                        <Textarea
                            id="app_process_subtitle_en"
                            v-model="form.app_process_subtitle_en"
                            placeholder="Enter section subtitle in English"
                            :disabled="isLoading"
                            :rows="2"
                        />
                    </div>
                </div>

                <div class="space-y-4">
                    <div v-if="form.app_process_steps.length === 0" class="text-center py-8 text-muted-foreground">
                        No process steps configured. Click "Add Step" to create one.
                    </div>

                    <div v-for="(step, index) in form.app_process_steps" :key="index" class="border border-input rounded-lg p-4 space-y-4 dark:border-input">
                        <div class="flex items-center justify-between">
                            <Badge variant="outline">Step {{ index + 1 }}</Badge>
                            <Button @click="removeAppProcessStep(index)" variant="destructive" size="sm">
                                <Trash2 class="w-4 h-4" />
                            </Button>
                        </div>

                        <div class="grid gap-4">
                            <div class="space-y-2">
                                <Label>Icon</Label>
                                <div class="border-2 border-dashed border-input rounded-lg p-4 dark:border-input">
                                    <div v-if="appProcessIconPreviews[index] || step.icon" class="space-y-2">
                                        <img :src="appProcessIconPreviews[index] || getImageUrl(step.icon)" alt="Icon Preview" class="w-16 h-16 object-contain mx-auto" />
                                        <Button size="sm" variant="outline" @click="$refs[`appProcessIconInput${index}`][0].click()">
                                            <Upload class="w-4 h-4 mr-2" />
                                            Change Icon
                                        </Button>
                                    </div>
                                    <div v-else class="text-center">
                                        <Image class="mx-auto h-8 w-8 text-gray-400" />
                                        <Button size="sm" variant="outline" @click="$refs[`appProcessIconInput${index}`][0].click()">
                                            <Upload class="w-4 h-4 mr-2" />
                                            Upload Icon
                                        </Button>
                                    </div>
                                    <input
                                        :ref="`appProcessIconInput${index}`"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleAppProcessIconUpload($event, index)"
                                    />
                                </div>
                            </div>
                            
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label>Title (Indonesian)</Label>
                                    <Input
                                        v-model="step.title_id"
                                        placeholder="Enter step title in Indonesian"
                                        :disabled="isLoading"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label>Title (English)</Label>
                                    <Input
                                        v-model="step.title_en"
                                        placeholder="Enter step title in English"
                                        :disabled="isLoading"
                                    />
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-2">
                                    <Label>Description (Indonesian)</Label>
                                    <Textarea
                                        v-model="step.description_id"
                                        placeholder="Enter step description in Indonesian"
                                        :disabled="isLoading"
                                        :rows="2"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label>Description (English)</Label>
                                    <Textarea
                                        v-model="step.description_en"
                                        placeholder="Enter step description in English"
                                        :disabled="isLoading"
                                        :rows="2"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </CardContent>
            </Transition>
        </Card>

        <!-- FAQ Section -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center justify-between">
                    <div 
                        class="cursor-pointer flex-1"
                        @click="toggleSection('faq')"
                    >
                        <span>FAQ Section</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button 
                            v-if="!collapsedSections.faq"
                            @click="addFaqItem" 
                            variant="outline" 
                            size="sm"
                        >
                            <Plus class="w-4 h-4 mr-2" />
                            Add FAQ
                        </Button>
                        <ChevronDown 
                            v-if="!collapsedSections.faq" 
                            class="w-4 h-4 cursor-pointer" 
                            @click="toggleSection('faq')"
                        />
                        <ChevronUp 
                            v-else 
                            class="w-4 h-4 cursor-pointer" 
                            @click="toggleSection('faq')"
                        />
                    </div>
                </CardTitle>
                <CardDescription v-if="!collapsedSections.faq">
                    Frequently asked questions for your customers
                </CardDescription>
            </CardHeader>
            <Transition name="collapse">
                <CardContent v-if="!collapsedSections.faq" class="space-y-4">
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="faq_title_id">Section Title (Indonesian)</Label>
                        <Input
                            id="faq_title_id"
                            v-model="form.faq_title_id"
                            placeholder="Enter FAQ section title in Indonesian"
                            :disabled="isLoading"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="faq_title_en">Section Title (English)</Label>
                        <Input
                            id="faq_title_en"
                            v-model="form.faq_title_en"
                            placeholder="Enter FAQ section title in English"
                            :disabled="isLoading"
                        />
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="faq_subtitle_id">Section Subtitle (Indonesian)</Label>
                        <Textarea
                            id="faq_subtitle_id"
                            v-model="form.faq_subtitle_id"
                            placeholder="Enter FAQ section subtitle in Indonesian"
                            :disabled="isLoading"
                            :rows="2"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="faq_subtitle_en">Section Subtitle (English)</Label>
                        <Textarea
                            id="faq_subtitle_en"
                            v-model="form.faq_subtitle_en"
                            placeholder="Enter FAQ section subtitle in English"
                            :disabled="isLoading"
                            :rows="2"
                        />
                    </div>
                </div>

                <div class="space-y-4">
                    <div v-if="form.faq_items.length === 0" class="text-center py-8 text-muted-foreground">
                        No FAQ items configured. Click "Add FAQ" to create one.
                    </div>

                    <div v-for="(item, index) in form.faq_items" :key="item.id" class="border border-input rounded-lg p-4 space-y-4 dark:border-input">
                        <div class="flex items-center justify-between">
                            <Badge variant="outline">FAQ {{ index + 1 }}</Badge>
                            <Button @click="removeFaqItem(index)" variant="destructive" size="sm">
                                <Trash2 class="w-4 h-4" />
                            </Button>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label>Question (Indonesian)</Label>
                                <Textarea
                                    v-model="item.question_id"
                                    placeholder="Enter question in Indonesian"
                                    :disabled="isLoading"
                                    :rows="2"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Question (English)</Label>
                                <Textarea
                                    v-model="item.question_en"
                                    placeholder="Enter question in English"
                                    :disabled="isLoading"
                                    :rows="2"
                                />
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label>Answer (Indonesian)</Label>
                                <Textarea
                                    v-model="item.answer_id"
                                    placeholder="Enter answer in Indonesian"
                                    :disabled="isLoading"
                                    :rows="4"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Answer (English)</Label>
                                <Textarea
                                    v-model="item.answer_en"
                                    placeholder="Enter answer in English"
                                    :disabled="isLoading"
                                    :rows="4"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                </CardContent>
            </Transition>
        </Card>

        <!-- Banner Slides -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center justify-between">
                    <div 
                        class="cursor-pointer flex-1"
                        @click="toggleSection('banners')"
                    >
                        <span>Banner Slides</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button 
                            v-if="!collapsedSections.banners"
                            @click="addBanner" 
                            variant="outline" 
                            size="sm"
                        >
                            <Plus class="w-4 h-4 mr-2" />
                            Add Banner
                        </Button>
                        <ChevronDown 
                            v-if="!collapsedSections.banners" 
                            class="w-4 h-4 cursor-pointer" 
                            @click="toggleSection('banners')"
                        />
                        <ChevronUp 
                            v-else 
                            class="w-4 h-4 cursor-pointer" 
                            @click="toggleSection('banners')"
                        />
                    </div>
                </CardTitle>
                <CardDescription v-if="!collapsedSections.banners">
                    Image slides for homepage carousel/banner section
                </CardDescription>
            </CardHeader>
            <Transition name="collapse">
                <CardContent v-if="!collapsedSections.banners" class="space-y-4">
                <div v-if="form.banners.length === 0" class="text-center py-8 text-muted-foreground">
                    No banners configured. Click "Add Banner" to create one.
                </div>

                <div v-for="(banner, index) in form.banners" :key="index" class="border border-input rounded-lg p-4 space-y-4 dark:border-input">
                    <div class="flex items-center justify-between">
                        <Badge variant="outline">Banner {{ index + 1 }}</Badge>
                        <Button @click="removeBanner(index)" variant="destructive" size="sm">
                            <Trash2 class="w-4 h-4" />
                        </Button>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label>Banner Image</Label>
                                <div class="border-2 border-dashed border-input rounded-lg p-4 dark:border-input">
                                    <div v-if="bannerPreviews[index] || banner.image" class="space-y-2">
                                        <img :src="bannerPreviews[index] || getImageUrl(banner.image)" alt="Banner Preview" class="w-full h-32 object-cover rounded" />
                                        <Button size="sm" variant="outline" @click="$refs[`bannerInput${index}`][0].click()">
                                            <Upload class="w-4 h-4 mr-2" />
                                            Change Image
                                        </Button>
                                    </div>
                                    <div v-else class="text-center">
                                        <Image class="mx-auto h-8 w-8 text-gray-400" />
                                        <Button size="sm" variant="outline" @click="$refs[`bannerInput${index}`][0].click()">
                                            <Upload class="w-4 h-4 mr-2" />
                                            Upload Image
                                        </Button>
                                    </div>
                                    <input
                                        :ref="`bannerInput${index}`"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleBannerUpload($event, index)"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="space-y-2">
                                <Label>Banner Title</Label>
                                <Input
                                    v-model="banner.title"
                                    placeholder="Enter banner title"
                                    :disabled="isLoading"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>Banner Subtitle</Label>
                                <Textarea
                                    v-model="banner.subtitle"
                                    placeholder="Enter banner subtitle"
                                    :disabled="isLoading"
                                    :rows="2"
                                />
                            </div>

                            <div class="space-y-2">
                                <Label>Link URL (Optional)</Label>
                                <Input
                                    v-model="banner.link"
                                    placeholder="https://example.com"
                                    :disabled="isLoading"
                                />
                            </div>
                        </div>
                    </div>
                </div>
                </CardContent>
            </Transition>
        </Card>

        <!-- Features Section -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center justify-between">
                    <div 
                        class="cursor-pointer flex-1"
                        @click="toggleSection('features')"
                    >
                        <span>Features Section</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <Button 
                            v-if="!collapsedSections.features"
                            @click="addFeature" 
                            variant="outline" 
                            size="sm"
                        >
                            <Plus class="w-4 h-4 mr-2" />
                            Add Feature
                        </Button>
                        <ChevronDown 
                            v-if="!collapsedSections.features" 
                            class="w-4 h-4 cursor-pointer" 
                            @click="toggleSection('features')"
                        />
                        <ChevronUp 
                            v-else 
                            class="w-4 h-4 cursor-pointer" 
                            @click="toggleSection('features')"
                        />
                    </div>
                </CardTitle>
                <CardDescription v-if="!collapsedSections.features">
                    Key features and benefits displayed on homepage
                </CardDescription>
            </CardHeader>
            <Transition name="collapse">
                <CardContent v-if="!collapsedSections.features" class="space-y-4">
                <div v-for="(feature, index) in form.features" :key="index" class="border border-input rounded-lg p-4 space-y-4 dark:border-input">
                    <div class="flex items-center justify-between">
                        <Badge variant="outline">Feature {{ index + 1 }}</Badge>
                        <Button @click="removeFeature(index)" variant="destructive" size="sm">
                            <Trash2 class="w-4 h-4" />
                        </Button>
                    </div>

                    <div class="grid md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <Label>Feature Title</Label>
                            <Input
                                v-model="feature.title"
                                placeholder="Enter feature title"
                                :disabled="isLoading"
                            />
                        </div>

                        <div class="space-y-2">
                            <Label>Icon</Label>
                            <select 
                                v-model="feature.icon" 
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-background dark:text-foreground dark:border-input"
                                :disabled="isLoading"
                            >
                                <option v-for="icon in iconOptions" :key="icon" :value="icon">
                                    {{ icon }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <Label>Description</Label>
                            <Textarea
                                v-model="feature.description"
                                placeholder="Enter feature description"
                                :disabled="isLoading"
                                :rows="2"
                            />
                        </div>
                    </div>
                </div>
                </CardContent>
            </Transition>
        </Card>

        <div class="flex justify-end pt-4">
            <Button 
                @click="saveSettings" 
                :disabled="isSaving || isLoading"
                class="min-w-[120px]"
            >
                <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
                <Save v-else class="w-4 h-4 mr-2" />
                {{ isSaving ? 'Saving...' : 'Save Changes' }}
            </Button>
        </div>
    </div>
</template>

<style scoped>
.collapse-enter-active,
.collapse-leave-active {
    transition: all 0.3s ease-in-out;
    overflow: hidden;
}

.collapse-enter-from,
.collapse-leave-to {
    max-height: 0;
    opacity: 0;
    padding-top: 0;
    padding-bottom: 0;
    margin-top: 0;
    margin-bottom: 0;
}

.collapse-enter-to,
.collapse-leave-from {
    max-height: 2000px;
    opacity: 1;
}
</style>