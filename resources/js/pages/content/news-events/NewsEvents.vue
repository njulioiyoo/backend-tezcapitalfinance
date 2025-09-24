<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive, computed, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { useCsrfToken } from '@/composables/useCsrfToken';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Select } from '@/components/ui/select';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Textarea } from '@/components/ui/textarea';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import { toast } from '@/components/ui/toast';
import RichTextEditor from '@/components/ui/RichTextEditor.vue';
import { Plus, Edit, Trash2, Calendar, FileText, Eye, EyeOff, Star, Search, Filter, Upload, Image, X } from 'lucide-vue-next';

interface NewsEvent {
    id: number;
    type: string;
    category: string;
    title_id: string;
    title_en?: string;
    excerpt_id: string;
    featured_image?: string;
    author: string;
    is_published: boolean;
    is_featured: boolean;
    status: string;
    published_at?: string;
    start_date?: string;
    end_date?: string;
    location_id?: string;
    organizer?: string;
    view_count: number;
    created_at: string;
}

interface Props {
    newsEvents: {
        data: NewsEvent[];
        links: any[];
        meta: any;
    };
    categories: {
        news: Record<string, string>;
        event: Record<string, string>;
    };
    types: Record<string, string>;
    statuses: Record<string, string>;
    filters: {
        search?: string;
        type?: string;
        category?: string;
        status?: string;
    };
    auth?: {
        user: {
            id: number;
            name: string;
            email: string;
        };
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'System Management', href: '/system' },
    { title: 'News & Events', href: '/system/news-events' },
];

const dialogOpen = ref(false);
const editingItem = ref<NewsEvent | null>(null);
const confirmDialog = ref({
    open: false,
    itemId: 0,
    loading: false
});

const form = reactive({
    type: 'news',
    category: '',
    title_id: '',
    title_en: '',
    excerpt_id: '',
    excerpt_en: '',
    content_id: '',
    content_en: '',
    featured_image: null,
    featured_image_file: null,
    author: '',
    location_id: '',
    location_en: '',
    start_date: '',
    end_date: '',
    organizer: '',
    price: null,
    max_participants: null,
    is_featured: false,
    status: 'draft',
    loading: false
});

const filters = reactive({
    search: props.filters.search || '',
    type: props.filters.type || '',
    category: props.filters.category || '',
    status: props.filters.status || '',
});

const currentCategories = computed(() => {
    return props.categories[form.type as keyof typeof props.categories] || {};
});

const isEvent = computed(() => form.type === 'event');
const isAnnouncement = computed(() => form.type === 'announcement');
const categoryRequired = computed(() => !isAnnouncement.value);

const openCreateDialog = () => {
    resetForm();
    editingItem.value = null;
    // Auto-fill author with current user
    if (props.auth?.user?.name) {
        form.author = props.auth.user.name;
    }
    dialogOpen.value = true;
};

const openEditDialog = (item: NewsEvent) => {
    resetForm();
    editingItem.value = item;
    
    form.type = item.type;
    form.category = item.category;
    form.title_id = item.title_id;
    form.title_en = item.title_en || '';
    form.excerpt_id = item.excerpt_id;
    form.featured_image = item.featured_image || null;
    form.featured_image_file = null;
    form.author = item.author;
    form.location_id = item.location_id || '';
    form.organizer = item.organizer || '';
    form.start_date = item.start_date ? item.start_date.substring(0, 16) : '';
    form.end_date = item.end_date ? item.end_date.substring(0, 16) : '';
    form.is_featured = item.is_featured;
    form.status = item.status;
    
    dialogOpen.value = true;
};

const resetForm = () => {
    form.type = 'news';
    form.category = '';
    form.title_id = '';
    form.title_en = '';
    form.excerpt_id = '';
    form.excerpt_en = '';
    form.content_id = '';
    form.content_en = '';
    form.featured_image = null;
    form.featured_image_file = null;
    form.author = '';
    form.location_id = '';
    form.location_en = '';
    form.start_date = '';
    form.end_date = '';
    form.organizer = '';
    form.price = null;
    form.max_participants = null;
    form.is_featured = false;
    form.status = 'draft';
    form.loading = false;
};

const { getCsrfToken } = useCsrfToken();

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!allowedTypes.includes(file.type)) {
            toast({
                title: 'Error',
                description: 'Please select a valid image file (JPEG, PNG, or WebP)',
                variant: 'error'
            });
            target.value = '';
            return;
        }
        
        // Validate file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            toast({
                title: 'Error', 
                description: 'File size must be less than 5MB',
                variant: 'error'
            });
            target.value = '';
            return;
        }
        
        form.featured_image_file = file;
        
        // Create preview URL
        const reader = new FileReader();
        reader.onload = (e) => {
            form.featured_image = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const clearFeaturedImage = () => {
    form.featured_image = null;
    form.featured_image_file = null;
    // Clear the input value
    const input = document.querySelector('input[type="file"]') as HTMLInputElement;
    if (input) {
        input.value = '';
    }
};

const handleSubmit = async () => {
    form.loading = true;
    
    try {
        const url = editingItem.value 
            ? `/system/news-events/${editingItem.value.id}`
            : '/system/news-events';

        // Create FormData for proper file upload
        const formData = new FormData();
        
        // Add all form fields
        formData.append('type', form.type);
        formData.append('category', form.category);
        formData.append('title_id', form.title_id);
        formData.append('title_en', form.title_en);
        formData.append('excerpt_id', form.excerpt_id);
        formData.append('excerpt_en', form.excerpt_en);
        formData.append('content_id', form.content_id);
        formData.append('content_en', form.content_en);
        formData.append('author', form.author);
        formData.append('location_id', form.location_id);
        formData.append('location_en', form.location_en);
        formData.append('start_date', form.start_date);
        formData.append('end_date', form.end_date);
        formData.append('organizer', form.organizer);
        formData.append('price', form.price || '');
        formData.append('max_participants', form.max_participants || '');
        formData.append('is_published', form.status === 'published' ? '1' : '0');
        formData.append('is_featured', form.is_featured ? '1' : '0');
        formData.append('status', form.status);
        
        // Add CSRF token
        formData.append('_token', getCsrfToken());
        
        // Add method override for PUT requests
        if (editingItem.value) {
            formData.append('_method', 'PUT');
        }
        
        // Add file if exists
        if (form.featured_image_file) {
            formData.append('featured_image', form.featured_image_file);
        }

        // Use native fetch for proper file upload
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include',
            body: formData,
        });

        const data = await response.json();

        if (response.ok) {
            toast({
                title: 'Success',
                description: editingItem.value ? 'Content updated successfully' : 'Content created successfully',
                variant: 'success'
            });
            dialogOpen.value = false;
            // Reload the page to show updated data
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            let errorMessage = 'An error occurred';
            
            if (data.errors) {
                const errorMessages = [];
                for (const [field, messages] of Object.entries(data.errors)) {
                    if (Array.isArray(messages)) {
                        errorMessages.push(...messages);
                    } else {
                        errorMessages.push(messages);
                    }
                }
                errorMessage = errorMessages.join(', ');
            } else if (data.message) {
                errorMessage = data.message;
            }
            
            toast({
                title: 'Error',
                description: errorMessage,
                variant: 'error'
            });
        }
    } catch (error) {
        toast({
            title: 'Error',
            description: 'An error occurred while saving',
            variant: 'error'
        });
    } finally {
        form.loading = false;
    }
};


const confirmDelete = (id: number) => {
    confirmDialog.value = {
        open: true,
        itemId: id,
        loading: false
    };
};

const handleDelete = async () => {
    confirmDialog.value.loading = true;
    
    try {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        
        const response = await fetch(`/system/news-events/${confirmDialog.value.itemId}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            credentials: 'include',
            body: formData,
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            toast({
                title: 'Success',
                description: data.message,
                variant: 'success'
            });
            confirmDialog.value.open = false;
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            toast({
                title: 'Error',
                description: data.message || 'Failed to delete item',
                variant: 'error'
            });
        }
    } catch (error) {
        toast({
            title: 'Error',
            description: 'An error occurred while deleting',
            variant: 'error'
        });
    } finally {
        confirmDialog.value.loading = false;
    }
};

const getStatusBadgeVariant = (status: string) => {
    switch (status) {
        case 'published': return 'default';
        case 'draft': return 'secondary';
        case 'archived': return 'outline';
        case 'cancelled': return 'destructive';
        default: return 'secondary';
    }
};

const getTypeBadgeVariant = (type: string) => {
    switch (type) {
        case 'news': return 'default';
        case 'event': return 'secondary';
        case 'article': return 'outline';
        case 'announcement': return 'destructive';
        default: return 'secondary';
    }
};

const applyFilters = () => {
    const params = new URLSearchParams();
    
    if (filters.search) params.set('search', filters.search);
    if (filters.type) params.set('type', filters.type);
    if (filters.category) params.set('category', filters.category);
    if (filters.status) params.set('status', filters.status);
    
    const queryString = params.toString();
    const url = queryString ? `/system/news-events?${queryString}` : '/system/news-events';
    
    router.visit(url);
};

const clearFilters = () => {
    Object.assign(filters, { search: '', type: '', category: '', status: '' });
    router.visit('/system/news-events');
};

// Watch for type changes and clear category if announcement
watch(() => form.type, (newType) => {
    if (newType === 'announcement') {
        form.category = '';
    }
});
</script>

<template>
    <Head title="News & Events" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">News & Events</h1>
                        <p class="text-muted-foreground">Manage news articles and events content</p>
                    </div>
                    
                    <Dialog v-model:open="dialogOpen">
                        <DialogTrigger as-child>
                            <Button @click="openCreateDialog">
                                Create Content
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                            <DialogHeader>
                                <DialogTitle>
                                    {{ editingItem ? 'Edit' : 'Create' }} {{ types[form.type] || 'Content' }}
                                </DialogTitle>
                                <DialogDescription>
                                    {{ editingItem ? 'Update' : 'Create new' }} {{ form.type }} content
                                </DialogDescription>
                            </DialogHeader>

                            <div class="space-y-6">
                                <!-- Type & Category -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label for="type">Type *</Label>
                                        <Select v-model="form.type">
                                            <option value="">Select type</option>
                                            <option v-for="(label, value) in types" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </Select>
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="category">Category {{ categoryRequired ? '*' : '' }}</Label>
                                        <Select v-model="form.category" :disabled="isAnnouncement">
                                            <option value="">{{ isAnnouncement ? 'Not applicable for announcements' : 'Select category' }}</option>
                                            <option v-for="(label, value) in currentCategories" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </Select>
                                    </div>
                                </div>

                                <!-- Titles -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="space-y-2">
                                        <Label for="title_id">Title (Indonesian) *</Label>
                                        <Input
                                            id="title_id"
                                            v-model="form.title_id"
                                            placeholder="Enter Indonesian title"
                                            required
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="title_en">Title (English)</Label>
                                        <Input
                                            id="title_en"
                                            v-model="form.title_en"
                                            placeholder="Enter English title"
                                        />
                                    </div>
                                </div>

                                <!-- Excerpts -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="space-y-2">
                                        <Label for="excerpt_id">Excerpt (Indonesian) *</Label>
                                        <Textarea
                                            id="excerpt_id"
                                            v-model="form.excerpt_id"
                                            placeholder="Enter Indonesian excerpt"
                                            rows="3"
                                            required
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="excerpt_en">Excerpt (English)</Label>
                                        <Textarea
                                            id="excerpt_en"
                                            v-model="form.excerpt_en"
                                            placeholder="Enter English excerpt"
                                            rows="3"
                                        />
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="space-y-2">
                                        <Label for="content_id">Content (Indonesian) *</Label>
                                        <RichTextEditor
                                            v-model="form.content_id"
                                            placeholder="Enter Indonesian content"
                                            :height="300"
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <Label for="content_en">Content (English)</Label>
                                        <RichTextEditor
                                            v-model="form.content_en"
                                            placeholder="Enter English content"
                                            :height="300"
                                        />
                                    </div>
                                </div>

                                <!-- Author -->
                                <div class="space-y-2">
                                    <Label for="author">Author *</Label>
                                    <Input
                                        id="author"
                                        v-model="form.author"
                                        placeholder="Author name"
                                        readonly
                                        class="bg-muted"
                                        required
                                    />
                                    <p class="text-sm text-muted-foreground">
                                        Author is automatically set to current user
                                    </p>
                                </div>

                                <!-- Featured Image Upload -->
                                <div class="space-y-2">
                                    <Label>Featured Image</Label>
                                    <div class="border-2 border-dashed border-input rounded-lg p-6">
                                        <div v-if="form.featured_image" class="space-y-4">
                                            <div class="flex items-center justify-center">
                                                <img 
                                                    :src="form.featured_image.startsWith('data:') ? form.featured_image : `/storage/${form.featured_image}`"
                                                    alt="Featured Image Preview" 
                                                    class="max-h-32 rounded-lg border bg-white object-contain"
                                                />
                                            </div>
                                            <div class="flex justify-center space-x-2">
                                                <Button size="sm" variant="outline" @click="clearFeaturedImage">
                                                    <X class="w-4 h-4 mr-2" />
                                                    Remove
                                                </Button>
                                                <Button size="sm" variant="outline" @click="$refs.featuredImageInput.click()">
                                                    <Upload class="w-4 h-4 mr-2" />
                                                    Change
                                                </Button>
                                            </div>
                                        </div>
                                        <div v-else class="text-center">
                                            <Image class="mx-auto h-12 w-12 text-muted-foreground" />
                                            <div class="mt-2">
                                                <Button variant="outline" @click="$refs.featuredImageInput.click()">
                                                    <Upload class="w-4 h-4 mr-2" />
                                                    Upload Image
                                                </Button>
                                            </div>
                                            <p class="text-sm text-muted-foreground mt-2">JPEG, PNG, WebP up to 5MB</p>
                                        </div>
                                        <input
                                            ref="featuredImageInput"
                                            type="file"
                                            accept="image/jpeg,image/jpg,image/png,image/webp"
                                            class="hidden"
                                            @change="handleFileUpload"
                                        />
                                    </div>
                                    <p class="text-sm text-muted-foreground">
                                        Featured image for the content (recommended size: 800x600px)
                                    </p>
                                </div>

                                <!-- Event-specific fields -->
                                <div v-if="isEvent" class="space-y-4 border-t pt-4">
                                    <h4 class="font-medium flex items-center gap-2">
                                        <Calendar class="h-4 w-4" />
                                        Event Details
                                    </h4>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <Label for="location_id">Location (Indonesian)</Label>
                                            <Input
                                                id="location_id"
                                                v-model="form.location_id"
                                                placeholder="Enter Indonesian location"
                                            />
                                        </div>
                                        <div class="space-y-2">
                                            <Label for="location_en">Location (English)</Label>
                                            <Input
                                                id="location_en"
                                                v-model="form.location_en"
                                                placeholder="Enter English location"
                                            />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <Label for="start_date">Start Date</Label>
                                            <Input
                                                id="start_date"
                                                v-model="form.start_date"
                                                type="datetime-local"
                                            />
                                        </div>
                                        <div class="space-y-2">
                                            <Label for="end_date">End Date</Label>
                                            <Input
                                                id="end_date"
                                                v-model="form.end_date"
                                                type="datetime-local"
                                            />
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="space-y-2">
                                            <Label for="organizer">Organizer</Label>
                                            <Input
                                                id="organizer"
                                                v-model="form.organizer"
                                                placeholder="Enter organizer name"
                                            />
                                        </div>
                                        <div class="space-y-2">
                                            <Label for="max_participants">Max Participants</Label>
                                            <Input
                                                id="max_participants"
                                                v-model="form.max_participants"
                                                type="number"
                                                min="1"
                                                placeholder="Enter max participants"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <!-- Status & Publishing -->
                                <div class="grid grid-cols-2 gap-4 border-t pt-4">
                                    <div class="space-y-2">
                                        <Label for="status">Status *</Label>
                                        <Select v-model="form.status">
                                            <option value="">Select status</option>
                                            <option v-for="(label, value) in statuses" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </Select>
                                    </div>
                                    <div class="flex items-center space-x-4 pt-6">
                                        <label class="flex items-center space-x-2">
                                            <input v-model="form.is_featured" type="checkbox" />
                                            <span>Featured</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <DialogFooter class="mt-6">
                                <Button type="button" variant="outline" @click="dialogOpen = false">
                                    Cancel
                                </Button>
                                <Button @click="handleSubmit" :disabled="form.loading">
                                    {{ form.loading ? 'Saving...' : (editingItem ? 'Update' : 'Create') }}
                                </Button>
                            </DialogFooter>
                        </DialogContent>
                    </Dialog>
                </div>

                <!-- Filters -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Filter class="w-4 h-4" />
                            Filters
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="space-y-2">
                                <Label>Search</Label>
                                <Input 
                                    v-model="filters.search" 
                                    placeholder="Search by title, author, category..."
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                            
                            <div class="space-y-2">
                                <Label>Type</Label>
                                <Select v-model="filters.type">
                                    <option value="">All Types</option>
                                    <option v-for="(label, value) in types" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </Select>
                            </div>
                            
                            <div class="space-y-2">
                                <Label>Status</Label>
                                <Select v-model="filters.status">
                                    <option value="">All Statuses</option>
                                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </Select>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 mt-4">
                            <Button @click="applyFilters">
                                <Search class="w-4 h-4 mr-2" />
                                Apply Filters
                            </Button>
                            <Button variant="outline" @click="clearFilters">Clear Filters</Button>
                        </div>
                    </CardContent>
                </Card>

                <!-- Data Table -->
                <Card>
                    <CardHeader>
                        <CardTitle>Content List</CardTitle>
                        <CardDescription>
                            {{ newsEvents.meta?.total || 0 }} items total
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left p-4">Type</th>
                                        <th class="text-left p-4">Title</th>
                                        <th class="text-left p-4">Category</th>
                                        <th class="text-left p-4">Author</th>
                                        <th class="text-left p-4">Status</th>
                                        <th class="text-left p-4">Published</th>
                                        <th class="text-left p-4">Views</th>
                                        <th class="text-left p-4">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="item in newsEvents.data" :key="item.id" class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="p-4">
                                            <Badge :variant="getTypeBadgeVariant(item.type)">
                                                {{ types[item.type] }}
                                            </Badge>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <div>
                                                    <div class="font-medium">{{ item.title_id }}</div>
                                                    <div v-if="item.title_en" class="text-sm text-gray-500">{{ item.title_en }}</div>
                                                </div>
                                                <Star v-if="item.is_featured" class="h-4 w-4 text-yellow-500" />
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <Badge variant="outline">{{ item.category }}</Badge>
                                        </td>
                                        <td class="p-4">{{ item.author }}</td>
                                        <td class="p-4">
                                            <Badge :variant="getStatusBadgeVariant(item.status)">
                                                {{ statuses[item.status] }}
                                            </Badge>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <Eye v-if="item.is_published" class="h-4 w-4 text-green-500" />
                                                <EyeOff v-else class="h-4 w-4 text-gray-400" />
                                                <span class="text-sm">{{ item.published_at || '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="p-4">{{ item.view_count }}</td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <Button @click="openEditDialog(item)" variant="outline" size="sm">
                                                    <Edit class="h-4 w-4" />
                                                </Button>
                                                <Button @click="confirmDelete(item.id)" variant="outline" size="sm">
                                                    <Trash2 class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div v-if="newsEvents.data.length === 0" class="text-center py-8 text-muted-foreground">
                                No content found. Create your first content to get started.
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <div v-if="newsEvents.links" class="flex justify-center mt-4">
                            <div class="flex gap-2">
                                <template v-for="(link, index) in newsEvents.links" :key="index">
                                    <Button
                                        v-if="link.url"
                                        :variant="link.active ? 'default' : 'outline'"
                                        size="sm"
                                        @click="router.visit(link.url)"
                                        v-html="link.label"
                                    />
                                    <span
                                        v-else
                                        class="px-3 py-1 text-sm text-muted-foreground"
                                        v-html="link.label"
                                    />
                                </template>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog
            v-model:open="confirmDialog.open"
            title="Delete Content"
            description="Are you sure you want to delete this content? This action cannot be undone."
            :loading="confirmDialog.loading"
            @confirm="handleDelete"
        />
    </AppLayout>
</template>
EOF < /dev/null