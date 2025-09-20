<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Badge } from '@/components/ui/badge';
import { Save, ArrowLeft, Upload, Image, X } from 'lucide-vue-next';

interface Partner {
    id?: number;
    type: string;
    category: string;
    title_id: string;
    title_en: string;
    excerpt_id: string;
    excerpt_en: string;
    featured_image: string;
    source_url: string;
    status: string;
    is_published: boolean;
    is_featured: boolean;
    sort_order: number;
}

interface Props {
    content?: Partner;
    type: string;
    types: Record<string, string>;
    categories: Record<string, string>;
    statuses: Record<string, string>;
    bilingualEnabled: boolean;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Content Management', href: '#' },
    { title: 'Our Partners', href: '/partners' },
    { title: props.content ? 'Edit Partner' : 'Add Partner', href: '#' },
];

const form = useForm({
    type: props.content?.type || 'partner',
    category: props.content?.category || 'other',
    title_id: props.content?.title_id || '',
    title_en: props.content?.title_en || '',
    excerpt_id: props.content?.excerpt_id || '',
    excerpt_en: props.content?.excerpt_en || '',
    featured_image: props.content?.featured_image || '',
    source_url: props.content?.source_url || '',
    status: props.content?.status || 'published',
    is_published: props.content?.is_published ?? true,
    is_featured: props.content?.is_featured ?? false,
    sort_order: props.content?.sort_order || 0,
});

const imageFile = ref<File | null>(null);
const imagePreview = ref<string>(props.content?.featured_image || '');

// Handle image upload
const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        imageFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
            form.featured_image = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

// Remove image
const removeImage = () => {
    imageFile.value = null;
    imagePreview.value = '';
    form.featured_image = '';
};

// Submit form
const submit = () => {
    if (props.content?.id) {
        // Update existing partner
        form.put(route('content.partners.update', props.content.id), {
            preserveScroll: true,
        });
    } else {
        // Create new partner
        form.post(route('content.partners.store'), {
            preserveScroll: true,
        });
    }
};

// Get category badge variant
const getCategoryVariant = (category: string) => {
    switch (category) {
        case 'bank': return 'default';
        case 'insurance': return 'secondary';
        case 'consulting': return 'outline';
        case 'technology': return 'destructive';
        case 'finance': return 'default';
        default: return 'secondary';
    }
};
</script>

<template>
    <Head :title="content ? 'Edit Partner' : 'Add Partner'" />
    
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">
                        {{ content ? 'Edit Partner' : 'Add Partner' }}
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        {{ content ? 'Update partner information' : 'Add a new strategic partner' }}
                    </p>
                </div>
                <Button variant="outline" @click="$inertia.visit('/content/partners')">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back to Partners
                </Button>
            </div>
        </template>

        <div class="w-full max-w-none">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Basic Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Basic Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Partner Logo -->
                        <div class="space-y-2">
                            <Label>Partner Logo *</Label>
                            <div class="border-2 border-dashed border-input rounded-lg p-6 dark:border-input">
                                <div v-if="imagePreview" class="space-y-4">
                                    <div class="relative inline-block">
                                        <img 
                                            :src="imagePreview" 
                                            alt="Partner Logo Preview" 
                                            class="w-32 h-32 object-contain border rounded-lg"
                                        />
                                        <Button 
                                            @click="removeImage"
                                            variant="destructive"
                                            size="sm"
                                            class="absolute -top-2 -right-2 h-6 w-6 p-0 rounded-full"
                                        >
                                            <X class="w-3 h-3" />
                                        </Button>
                                    </div>
                                    <Button variant="outline" @click="$refs.imageInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Change Logo
                                    </Button>
                                </div>
                                <div v-else class="text-center">
                                    <Image class="mx-auto h-12 w-12 text-gray-400 mb-3" />
                                    <Button variant="outline" @click="$refs.imageInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Upload Partner Logo
                                    </Button>
                                    <p class="text-sm text-gray-500 mt-2">
                                        PNG, JPG, GIF up to 5MB. Recommended: 200x200px
                                    </p>
                                </div>
                                <input
                                    ref="imageInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleImageUpload"
                                />
                            </div>
                            <div v-if="form.errors.featured_image" class="text-red-500 text-sm">
                                {{ form.errors.featured_image }}
                            </div>
                        </div>

                        <!-- Partner Name -->
                        <div class="grid lg:grid-cols-2 gap-6" v-if="bilingualEnabled">
                            <div class="space-y-2">
                                <Label for="title_id">Partner Name (Indonesian) *</Label>
                                <Input
                                    id="title_id"
                                    v-model="form.title_id"
                                    placeholder="Enter partner name in Indonesian"
                                    :disabled="form.processing"
                                />
                                <div v-if="form.errors.title_id" class="text-red-500 text-sm">
                                    {{ form.errors.title_id }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="title_en">Partner Name (English) *</Label>
                                <Input
                                    id="title_en"
                                    v-model="form.title_en"
                                    placeholder="Enter partner name in English"
                                    :disabled="form.processing"
                                />
                                <div v-if="form.errors.title_en" class="text-red-500 text-sm">
                                    {{ form.errors.title_en }}
                                </div>
                            </div>
                        </div>
                        <div v-else class="space-y-2">
                            <Label for="title_id">Partner Name *</Label>
                            <Input
                                id="title_id"
                                v-model="form.title_id"
                                placeholder="Enter partner name"
                                :disabled="form.processing"
                            />
                            <div v-if="form.errors.title_id" class="text-red-500 text-sm">
                                {{ form.errors.title_id }}
                            </div>
                        </div>

                        <!-- Category & Status -->
                        <div class="grid lg:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <Label for="category">Category</Label>
                                <select 
                                    id="category"
                                    v-model="form.category"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-background dark:text-foreground dark:border-input"
                                    :disabled="form.processing"
                                >
                                    <option v-for="(label, value) in categories" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                                <div v-if="form.errors.category" class="text-red-500 text-sm">
                                    {{ form.errors.category }}
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="status">Status</Label>
                                <select 
                                    id="status"
                                    v-model="form.status"
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-background dark:text-foreground dark:border-input"
                                    :disabled="form.processing"
                                >
                                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
                                <div v-if="form.errors.status" class="text-red-500 text-sm">
                                    {{ form.errors.status }}
                                </div>
                            </div>
                        </div>

                        <!-- Partner Website -->
                        <div class="space-y-2">
                            <Label for="source_url">Website URL</Label>
                            <Input
                                id="source_url"
                                v-model="form.source_url"
                                type="url"
                                placeholder="https://example.com"
                                :disabled="form.processing"
                            />
                            <div v-if="form.errors.source_url" class="text-red-500 text-sm">
                                {{ form.errors.source_url }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Additional Information -->
                <Card>
                    <CardHeader>
                        <CardTitle>Additional Information</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <!-- Description -->
                        <div class="grid lg:grid-cols-2 gap-6" v-if="bilingualEnabled">
                            <div class="space-y-2">
                                <Label for="excerpt_id">Description (Indonesian)</Label>
                                <Textarea
                                    id="excerpt_id"
                                    v-model="form.excerpt_id"
                                    placeholder="Brief description about the partnership"
                                    :rows="3"
                                    :disabled="form.processing"
                                />
                                <div v-if="form.errors.excerpt_id" class="text-red-500 text-sm">
                                    {{ form.errors.excerpt_id }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                <Label for="excerpt_en">Description (English)</Label>
                                <Textarea
                                    id="excerpt_en"
                                    v-model="form.excerpt_en"
                                    placeholder="Brief description about the partnership"
                                    :rows="3"
                                    :disabled="form.processing"
                                />
                                <div v-if="form.errors.excerpt_en" class="text-red-500 text-sm">
                                    {{ form.errors.excerpt_en }}
                                </div>
                            </div>
                        </div>
                        <div v-else class="space-y-2">
                            <Label for="excerpt_id">Description</Label>
                            <Textarea
                                id="excerpt_id"
                                v-model="form.excerpt_id"
                                placeholder="Brief description about the partnership"
                                :rows="3"
                                :disabled="form.processing"
                            />
                            <div v-if="form.errors.excerpt_id" class="text-red-500 text-sm">
                                {{ form.errors.excerpt_id }}
                            </div>
                        </div>

                        <!-- Sort Order -->
                        <div class="space-y-2">
                            <Label for="sort_order">Sort Order</Label>
                            <Input
                                id="sort_order"
                                v-model.number="form.sort_order"
                                type="number"
                                min="0"
                                placeholder="0"
                                :disabled="form.processing"
                            />
                            <p class="text-sm text-gray-500">Lower numbers appear first</p>
                            <div v-if="form.errors.sort_order" class="text-red-500 text-sm">
                                {{ form.errors.sort_order }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Settings -->
                <Card>
                    <CardHeader>
                        <CardTitle>Settings</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <Label class="text-base font-medium">Published</Label>
                                    <p class="text-sm text-gray-500">Partner will be visible on the website</p>
                                </div>
                                <Switch 
                                    :checked="form.is_published" 
                                    @update:checked="form.is_published = $event"
                                    :disabled="form.processing"
                                />
                            </div>

                            <div class="flex items-center justify-between">
                                <div>
                                    <Label class="text-base font-medium">Featured Partner</Label>
                                    <p class="text-sm text-gray-500">Show this partner prominently</p>
                                </div>
                                <Switch 
                                    :checked="form.is_featured" 
                                    @update:checked="form.is_featured = $event"
                                    :disabled="form.processing"
                                />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Preview -->
                <Card v-if="form.title_id">
                    <CardHeader>
                        <CardTitle>Preview</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="border rounded-lg p-4 max-w-sm">
                            <div class="aspect-video bg-gray-50 dark:bg-gray-800 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                                <img 
                                    v-if="imagePreview" 
                                    :src="imagePreview" 
                                    :alt="form.title_id"
                                    class="w-full h-full object-contain"
                                />
                                <div v-else class="text-gray-400 text-sm">No Logo</div>
                            </div>
                            <h3 class="font-semibold text-sm mb-2">{{ form.title_id }}</h3>
                            <div class="flex items-center gap-2 mb-2">
                                <Badge :variant="getCategoryVariant(form.category)" class="text-xs">
                                    {{ categories[form.category] }}
                                </Badge>
                                <Badge v-if="form.is_featured" variant="default" class="text-xs">
                                    ⭐ Featured
                                </Badge>
                            </div>
                            <div v-if="form.source_url" class="text-xs text-gray-500 truncate">
                                {{ form.source_url }}
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t">
                    <Button 
                        type="button" 
                        variant="outline" 
                        @click="$inertia.visit('/content/partners')"
                        :disabled="form.processing"
                    >
                        Cancel
                    </Button>
                    <Button 
                        type="submit" 
                        :disabled="form.processing || !form.title_id || !form.featured_image"
                        class="min-w-[120px]"
                    >
                        <Save class="w-4 h-4 mr-2" />
                        {{ form.processing ? 'Saving...' : (content ? 'Update Partner' : 'Create Partner') }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>