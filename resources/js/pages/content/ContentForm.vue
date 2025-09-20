<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive, computed, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import RichTextEditor from '@/components/ui/RichTextEditor.vue';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Badge } from '@/components/ui/badge';
import { toast } from '@/components/ui/toast';
import { 
    Save, 
    ArrowLeft, 
    FileText, 
    Languages,
    FileImage,
    Tag,
    Eye,
    Calendar,
    Globe,
    Hash,
    X,
    MapPin,
    DollarSign,
    Users
} from 'lucide-vue-next';

interface Content {
    id?: number;
    type: string;
    category: string;
    title_id: string;
    title_en: string;
    excerpt_id: string;
    excerpt_en: string;
    content_id: string;
    content_en: string;
    featured_image?: string;
    gallery?: string[];
    tags: string[];
    author: string;
    source_url: string;
    location_id: string;
    location_en: string;
    start_date: string;
    end_date: string;
    organizer: string;
    price: number | null;
    max_participants: number | null;
    registered_count: number;
    is_published: boolean;
    is_featured: boolean;
    published_at: string | null;
    status: string;
    meta_title_id: string;
    meta_title_en: string;
    meta_description_id: string;
    meta_description_en: string;
    sort_order: number;
}

interface Props {
    content: Content | null;
    type: string;
    types: Record<string, string>;
    categories: Record<string, string>;
    statuses: Record<string, string>;
    bilingualEnabled: boolean;
}

const props = defineProps<Props>();

const isEditing = computed(() => !!props.content?.id);
const pageTitle = computed(() => 
    isEditing.value 
        ? `Edit ${props.types[props.type]}` 
        : `Create ${props.types[props.type]}`
);

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Content Management', href: '/content' },
    { title: props.types[props.type], href: `/content?type=${props.type}` },
    { title: isEditing.value ? 'Edit' : 'Create', href: '' }
];

const form = reactive<Content>({
    type: props.content?.type || props.type,
    category: props.content?.category || '',
    title_id: props.content?.title_id || '',
    title_en: props.content?.title_en || '',
    excerpt_id: props.content?.excerpt_id || '',
    excerpt_en: props.content?.excerpt_en || '',
    content_id: props.content?.content_id || '',
    content_en: props.content?.content_en || '',
    featured_image: props.content?.featured_image || '',
    gallery: props.content?.gallery || [],
    tags: props.content?.tags || [],
    author: props.content?.author || '',
    source_url: props.content?.source_url || '',
    location_id: props.content?.location_id || '',
    location_en: props.content?.location_en || '',
    start_date: props.content?.start_date || '',
    end_date: props.content?.end_date || '',
    organizer: props.content?.organizer || '',
    price: props.content?.price || null,
    max_participants: props.content?.max_participants || null,
    registered_count: props.content?.registered_count || 0,
    is_published: props.content?.is_published || false,
    is_featured: props.content?.is_featured || false,
    published_at: props.content?.published_at || null,
    status: props.content?.status || 'draft',
    meta_title_id: props.content?.meta_title_id || '',
    meta_title_en: props.content?.meta_title_en || '',
    meta_description_id: props.content?.meta_description_id || '',
    meta_description_en: props.content?.meta_description_en || '',
    sort_order: props.content?.sort_order || 0,
});

const errors = ref<Record<string, string>>({});
const isLoading = ref(false);
const newTag = ref('');
const imageFile = ref<File | null>(null);
const activeTab = ref('content');

// Auto-generate meta titles from main titles
watch(() => form.title_id, (newValue) => {
    if (!form.meta_title_id || form.meta_title_id === form.title_id) {
        form.meta_title_id = newValue ? `${newValue} - TEZ Capital` : '';
    }
});

watch(() => form.title_en, (newValue) => {
    if (!form.meta_title_en || form.meta_title_en === form.title_en) {
        form.meta_title_en = newValue ? `${newValue} - TEZ Capital` : '';
    }
});

// Handle image upload
const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        imageFile.value = target.files[0];
    }
};

// Add tag
const addTag = () => {
    if (newTag.value.trim() && !form.tags.includes(newTag.value.trim())) {
        form.tags.push(newTag.value.trim());
        newTag.value = '';
    }
};

// Remove tag
const removeTag = (tagToRemove: string) => {
    form.tags = form.tags.filter(tag => tag !== tagToRemove);
};

// Handle form submission
const submitForm = async () => {
    if (isLoading.value) return;

    isLoading.value = true;
    errors.value = {};

    try {
        const formData = new FormData();
        
        // Add all form data
        Object.keys(form).forEach(key => {
            const value = form[key as keyof Content];
            if (value !== null && value !== undefined) {
                if (Array.isArray(value)) {
                    formData.append(key, JSON.stringify(value));
                } else {
                    formData.append(key, String(value));
                }
            }
        });

        // Add image file if selected
        if (imageFile.value) {
            formData.append('featured_image', imageFile.value);
        }

        // Determine the route and method
        const route = isEditing.value 
            ? 'content.update' 
            : 'content.store';
        const params = isEditing.value ? [props.content!.id] : [];

        if (isEditing.value) {
            formData.append('_method', 'PUT');
        }

        router.post(route(...params), formData, {
            onSuccess: () => {
                toast.success(`${props.types[props.type]} ${isEditing.value ? 'updated' : 'created'} successfully!`);
                router.visit(`/content?type=${props.type}`);
            },
            onError: (err) => {
                errors.value = err;
                toast.error('Please check the form for errors');
            },
            onFinish: () => {
                isLoading.value = false;
            }
        });
    } catch (error) {
        // Form submission error
        toast.error('An error occurred while saving');
        isLoading.value = false;
    }
};

// Go back
const goBack = () => {
    router.visit(`/content?type=${props.type}`);
};

// Check if field is event-specific
const isEventField = computed(() => form.type === 'event');
</script>

<template>
    <Head :title="pageTitle" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight">{{ pageTitle }}</h1>
                    <p class="text-muted-foreground">
                        {{ isEditing ? 'Edit' : 'Create' }} {{ types[form.type].toLowerCase() }} content
                    </p>
                </div>
                <Button variant="outline" @click="goBack">
                    <ArrowLeft class="h-4 w-4 mr-2" />
                    Back to List
                </Button>
            </div>

            <!-- Form -->
            <form @submit.prevent="submitForm" class="space-y-6">
                <Tabs v-model="activeTab" class="space-y-4">
                    <TabsList class="grid w-full grid-cols-4">
                        <TabsTrigger value="content">Content</TabsTrigger>
                        <TabsTrigger value="event" v-if="isEventField">Event Details</TabsTrigger>
                        <TabsTrigger value="settings">Settings</TabsTrigger>
                        <TabsTrigger value="seo">SEO</TabsTrigger>
                    </TabsList>

                    <!-- Content Tab -->
                    <TabsContent value="content" class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center">
                                    <FileText class="h-4 w-4 mr-2" />
                                    Basic Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <!-- Type and Category -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <Label for="type">Content Type</Label>
                                        <Select v-model="form.type" disabled>
                                            <option 
                                                v-for="(label, key) in types" 
                                                :key="key" 
                                                :value="key"
                                            >
                                                {{ label }}
                                            </option>
                                        </Select>
                                    </div>

                                    <div>
                                        <Label for="category">Category</Label>
                                        <Select v-model="form.category">
                                            <option value="">Select Category</option>
                                            <option 
                                                v-for="(label, key) in categories" 
                                                :key="key" 
                                                :value="key"
                                            >
                                                {{ label }}
                                            </option>
                                        </Select>
                                        <div v-if="errors.category" class="text-sm text-destructive mt-1">
                                            {{ errors.category }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Title -->
                                <div v-if="bilingualEnabled" class="space-y-4">
                                    <div>
                                        <Label for="title_id">Title (Indonesian)</Label>
                                        <Input
                                            v-model="form.title_id"
                                            placeholder="Enter title in Indonesian"
                                            :class="errors.title_id ? 'border-destructive' : ''"
                                        />
                                        <div v-if="errors.title_id" class="text-sm text-destructive mt-1">
                                            {{ errors.title_id }}
                                        </div>
                                    </div>

                                    <div>
                                        <Label for="title_en">Title (English)</Label>
                                        <Input
                                            v-model="form.title_en"
                                            placeholder="Enter title in English"
                                            :class="errors.title_en ? 'border-destructive' : ''"
                                        />
                                        <div v-if="errors.title_en" class="text-sm text-destructive mt-1">
                                            {{ errors.title_en }}
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <Label for="title_id">Title</Label>
                                    <Input
                                        v-model="form.title_id"
                                        placeholder="Enter title"
                                        :class="errors.title_id ? 'border-destructive' : ''"
                                    />
                                    <div v-if="errors.title_id" class="text-sm text-destructive mt-1">
                                        {{ errors.title_id }}
                                    </div>
                                </div>

                                <!-- Excerpt -->
                                <div v-if="bilingualEnabled" class="space-y-4">
                                    <div>
                                        <Label for="excerpt_id">Excerpt (Indonesian)</Label>
                                        <Textarea
                                            v-model="form.excerpt_id"
                                            placeholder="Brief description in Indonesian"
                                            rows="3"
                                        />
                                    </div>

                                    <div>
                                        <Label for="excerpt_en">Excerpt (English)</Label>
                                        <Textarea
                                            v-model="form.excerpt_en"
                                            placeholder="Brief description in English"
                                            rows="3"
                                        />
                                    </div>
                                </div>
                                <div v-else>
                                    <Label for="excerpt_id">Excerpt</Label>
                                    <Textarea
                                        v-model="form.excerpt_id"
                                        placeholder="Brief description"
                                        rows="3"
                                    />
                                </div>

                                <!-- Rich Content -->
                                <div v-if="bilingualEnabled" class="space-y-4">
                                    <div>
                                        <Label>Content (Indonesian)</Label>
                                        <RichTextEditor 
                                            v-model="form.content_id"
                                            :upload-url="route('content.upload-image')"
                                            placeholder="Write your content in Indonesian..."
                                        />
                                    </div>

                                    <div>
                                        <Label>Content (English)</Label>
                                        <RichTextEditor 
                                            v-model="form.content_en"
                                            :upload-url="route('content.upload-image')"
                                            placeholder="Write your content in English..."
                                        />
                                    </div>
                                </div>
                                <div v-else>
                                    <Label>Content</Label>
                                    <RichTextEditor 
                                        v-model="form.content_id"
                                        :upload-url="route('content.upload-image')"
                                        placeholder="Write your content..."
                                    />
                                </div>

                                <!-- Featured Image -->
                                <div>
                                    <Label for="featured_image">Featured Image</Label>
                                    <Input
                                        type="file"
                                        accept="image/*"
                                        @change="handleImageUpload"
                                        class="file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-primary-foreground hover:file:bg-primary/80"
                                    />
                                    <div v-if="form.featured_image" class="mt-2">
                                        <img 
                                            :src="`/storage/${form.featured_image}`" 
                                            :alt="form.title_id"
                                            class="w-32 h-32 object-cover rounded-lg"
                                        />
                                    </div>
                                </div>

                                <!-- Tags -->
                                <div>
                                    <Label>Tags</Label>
                                    <div class="flex gap-2 mb-2">
                                        <Input
                                            v-model="newTag"
                                            placeholder="Add a tag"
                                            @keyup.enter="addTag"
                                        />
                                        <Button type="button" @click="addTag">
                                            <Tag class="h-4 w-4" />
                                        </Button>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <Badge
                                            v-for="tag in form.tags"
                                            :key="tag"
                                            variant="secondary"
                                            class="flex items-center gap-1"
                                        >
                                            {{ tag }}
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="ghost"
                                                @click="removeTag(tag)"
                                                class="h-3 w-3 p-0 hover:bg-destructive hover:text-destructive-foreground"
                                            >
                                                <X class="h-2 w-2" />
                                            </Button>
                                        </Badge>
                                    </div>
                                </div>

                                <!-- Author & Source -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <Label for="author">Author</Label>
                                        <Input
                                            v-model="form.author"
                                            placeholder="Author name"
                                        />
                                    </div>

                                    <div>
                                        <Label for="source_url">Source URL</Label>
                                        <Input
                                            v-model="form.source_url"
                                            type="url"
                                            placeholder="https://example.com"
                                        />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <!-- Event Details Tab -->
                    <TabsContent value="event" v-if="isEventField" class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center">
                                    <Calendar class="h-4 w-4 mr-2" />
                                    Event Information
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <!-- Dates -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <Label for="start_date">Start Date & Time</Label>
                                        <Input
                                            v-model="form.start_date"
                                            type="datetime-local"
                                            :class="errors.start_date ? 'border-destructive' : ''"
                                        />
                                        <div v-if="errors.start_date" class="text-sm text-destructive mt-1">
                                            {{ errors.start_date }}
                                        </div>
                                    </div>

                                    <div>
                                        <Label for="end_date">End Date & Time</Label>
                                        <Input
                                            v-model="form.end_date"
                                            type="datetime-local"
                                        />
                                    </div>
                                </div>

                                <!-- Location -->
                                <div v-if="bilingualEnabled" class="space-y-4">
                                    <div>
                                        <Label for="location_id">Location (Indonesian)</Label>
                                        <Input
                                            v-model="form.location_id"
                                            placeholder="Event location in Indonesian"
                                        />
                                    </div>

                                    <div>
                                        <Label for="location_en">Location (English)</Label>
                                        <Input
                                            v-model="form.location_en"
                                            placeholder="Event location in English"
                                        />
                                    </div>
                                </div>
                                <div v-else>
                                    <Label for="location_id">Location</Label>
                                    <Input
                                        v-model="form.location_id"
                                        placeholder="Event location"
                                    />
                                </div>

                                <!-- Organizer -->
                                <div>
                                    <Label for="organizer">Organizer</Label>
                                    <Input
                                        v-model="form.organizer"
                                        placeholder="Event organizer"
                                    />
                                </div>

                                <!-- Pricing & Participants -->
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <Label for="price">Price</Label>
                                        <Input
                                            v-model="form.price"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            placeholder="0.00"
                                        />
                                    </div>

                                    <div>
                                        <Label for="max_participants">Max Participants</Label>
                                        <Input
                                            v-model="form.max_participants"
                                            type="number"
                                            min="1"
                                            placeholder="Unlimited"
                                        />
                                    </div>

                                    <div>
                                        <Label for="registered_count">Registered</Label>
                                        <Input
                                            v-model="form.registered_count"
                                            type="number"
                                            min="0"
                                            :readonly="!isEditing"
                                        />
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <!-- Settings Tab -->
                    <TabsContent value="settings" class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle>Publication Settings</CardTitle>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <Label for="status">Status</Label>
                                        <Select v-model="form.status">
                                            <option 
                                                v-for="(label, key) in statuses" 
                                                :key="key" 
                                                :value="key"
                                            >
                                                {{ label }}
                                            </option>
                                        </Select>
                                    </div>

                                    <div>
                                        <Label for="sort_order">Sort Order</Label>
                                        <Input
                                            v-model="form.sort_order"
                                            type="number"
                                            min="0"
                                        />
                                    </div>
                                </div>

                                <div class="flex items-center space-x-6">
                                    <div class="flex items-center space-x-2">
                                        <Switch v-model="form.is_published" />
                                        <Label>Published</Label>
                                    </div>

                                    <div class="flex items-center space-x-2">
                                        <Switch v-model="form.is_featured" />
                                        <Label>Featured</Label>
                                    </div>
                                </div>

                                <div v-if="form.is_published">
                                    <Label for="published_at">Publish Date & Time</Label>
                                    <Input
                                        v-model="form.published_at"
                                        type="datetime-local"
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>

                    <!-- SEO Tab -->
                    <TabsContent value="seo" class="space-y-6">
                        <Card>
                            <CardHeader>
                                <CardTitle class="flex items-center">
                                    <Globe class="h-4 w-4 mr-2" />
                                    SEO Settings
                                </CardTitle>
                                <CardDescription>
                                    Optimize your content for search engines
                                </CardDescription>
                            </CardHeader>
                            <CardContent class="space-y-4">
                                <!-- Meta Title -->
                                <div v-if="bilingualEnabled" class="space-y-4">
                                    <div>
                                        <Label for="meta_title_id">Meta Title (Indonesian)</Label>
                                        <Input
                                            v-model="form.meta_title_id"
                                            placeholder="SEO title in Indonesian"
                                            maxlength="60"
                                        />
                                        <div class="text-xs text-muted-foreground">
                                            {{ form.meta_title_id.length }}/60 characters
                                        </div>
                                    </div>

                                    <div>
                                        <Label for="meta_title_en">Meta Title (English)</Label>
                                        <Input
                                            v-model="form.meta_title_en"
                                            placeholder="SEO title in English"
                                            maxlength="60"
                                        />
                                        <div class="text-xs text-muted-foreground">
                                            {{ form.meta_title_en.length }}/60 characters
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <Label for="meta_title_id">Meta Title</Label>
                                    <Input
                                        v-model="form.meta_title_id"
                                        placeholder="SEO title"
                                        maxlength="60"
                                    />
                                    <div class="text-xs text-muted-foreground">
                                        {{ form.meta_title_id.length }}/60 characters
                                    </div>
                                </div>

                                <!-- Meta Description -->
                                <div v-if="bilingualEnabled" class="space-y-4">
                                    <div>
                                        <Label for="meta_description_id">Meta Description (Indonesian)</Label>
                                        <Textarea
                                            v-model="form.meta_description_id"
                                            placeholder="SEO description in Indonesian"
                                            rows="3"
                                            maxlength="160"
                                        />
                                        <div class="text-xs text-muted-foreground">
                                            {{ form.meta_description_id.length }}/160 characters
                                        </div>
                                    </div>

                                    <div>
                                        <Label for="meta_description_en">Meta Description (English)</Label>
                                        <Textarea
                                            v-model="form.meta_description_en"
                                            placeholder="SEO description in English"
                                            rows="3"
                                            maxlength="160"
                                        />
                                        <div class="text-xs text-muted-foreground">
                                            {{ form.meta_description_en.length }}/160 characters
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <Label for="meta_description_id">Meta Description</Label>
                                    <Textarea
                                        v-model="form.meta_description_id"
                                        placeholder="SEO description"
                                        rows="3"
                                        maxlength="160"
                                    />
                                    <div class="text-xs text-muted-foreground">
                                        {{ form.meta_description_id.length }}/160 characters
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    </TabsContent>
                </Tabs>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-2">
                    <Button type="button" variant="outline" @click="goBack">
                        Cancel
                    </Button>
                    <Button type="submit" :disabled="isLoading">
                        <Save class="h-4 w-4 mr-2" />
                        {{ isLoading ? 'Saving...' : (isEditing ? 'Update' : 'Create') }}
                    </Button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>