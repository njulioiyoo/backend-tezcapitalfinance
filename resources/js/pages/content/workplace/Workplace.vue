<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';
import { route } from 'ziggy-js';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
// import { Switch } from '@/components/ui/switch'; // Disabled - causes auto-submit issue
import { Textarea } from '@/components/ui/textarea';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import { toast } from '@/components/ui/toast';
import { Search, Plus, Filter, Edit, Trash2, ExternalLink, Upload, Image, X, Save, Star } from 'lucide-vue-next';

interface Workplace {
    id: number;
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
    view_count: number;
    created_at: string;
    updated_at: string;
}

interface Props {
    contents: {
        data?: Workplace[];
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
    { title: 'Content Management', href: '#' },
    { title: 'Workplace', href: '/content/workplace' },
];

// Local state for filters
const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const selectedStatus = ref(props.filters.status || '');
const showFeaturedOnly = ref(props.filters.featured || false);

// Modal state
const dialogOpen = ref(false);
const editingWorkplace = ref<Workplace | null>(null);
const confirmDialog = ref({
    open: false,
    workplaceId: 0,
    loading: false
});

// Form state
const form = reactive({
    type: 'workplace',
    category: 'environment',
    title_id: '',
    title_en: '',
    excerpt_id: '',
    excerpt_en: '',
    featured_image: '',
    source_url: '',
    status: 'published',
    is_published: true,
    is_featured: false,
    sort_order: 0,
    loading: false
});

const imageFile = ref<File | null>(null);
const imagePreview = ref<string>('');

// Get localized title
const getLocalizedTitle = (workplace: Workplace) => {
    return props.bilingualEnabled ? (workplace.title_en || workplace.title_id) : workplace.title_id;
};

// Get workplace image URL
const getWorkplaceImageUrl = (workplace: Workplace) => {
    if (!workplace.featured_image) return null;
    if (workplace.featured_image.startsWith('http') || workplace.featured_image.startsWith('/storage/')) {
        return workplace.featured_image;
    }
    return `/storage/${workplace.featured_image}`;
};

// Get status badge variant
const getStatusVariant = (status: string) => {
    switch (status) {
        case 'published': return 'default';
        case 'draft': return 'secondary';
        case 'archived': return 'outline';
        default: return 'secondary';
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

// Filter and search
const applyFilters = () => {
    const params = new URLSearchParams();
    
    if (search.value) params.set('search', search.value);
    if (selectedCategory.value) params.set('category', selectedCategory.value);
    if (selectedStatus.value) params.set('status', selectedStatus.value);
    if (showFeaturedOnly.value) params.set('featured', '1');
    
    const queryString = params.toString();
    const url = queryString ? `/content/workplace?${queryString}` : '/content/workplace';
    
    router.visit(url);
};

const clearFilters = () => {
    search.value = '';
    selectedCategory.value = '';
    selectedStatus.value = '';
    showFeaturedOnly.value = false;
    
    router.visit('/content/workplace');
};

// Modal functions  
const openCreateDialog = () => {
    resetForm();
    editingWorkplace.value = null;
    dialogOpen.value = true;
};

const openEditDialog = (workplace: Workplace) => {
    resetForm();
    editingWorkplace.value = workplace;
    
    form.type = workplace.type;
    form.category = workplace.category;
    form.title_id = workplace.title_id;
    form.title_en = workplace.title_en || '';
    form.excerpt_id = workplace.excerpt_id;
    form.excerpt_en = workplace.excerpt_en || '';
    form.featured_image = workplace.featured_image;
    form.source_url = workplace.source_url;
    form.status = workplace.status;
    form.is_published = Boolean(workplace.is_published);
    form.is_featured = Boolean(workplace.is_featured);
    form.sort_order = parseInt(workplace.sort_order) || 0;
    
    // For existing workplaces, show the image URL as preview
    if (workplace.featured_image && !workplace.featured_image.startsWith('data:')) {
        imagePreview.value = `/storage/${workplace.featured_image}`;
    } else {
        imagePreview.value = workplace.featured_image;
    }
    dialogOpen.value = true;
};

const resetForm = () => {
    form.type = 'workplace';
    form.category = 'environment';
    form.title_id = '';
    form.title_en = '';
    form.excerpt_id = '';
    form.excerpt_en = '';
    form.featured_image = '';
    form.source_url = '';
    form.status = 'published';
    form.is_published = true;
    form.is_featured = false;
    form.sort_order = 0;
    form.loading = false;
    
    imageFile.value = null;
    imagePreview.value = '';
};

// Handle image upload
const handleImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    
    if (file) {
        imageFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

// Remove image
const removeImage = () => {
    imageFile.value = null;
    imagePreview.value = '';
    form.featured_image = '';
    // Clear the file input
    const input = document.querySelector('input[type="file"]') as HTMLInputElement;
    if (input) input.value = '';
};

// Get CSRF token
const getCsrfToken = () => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

// Submit form
const handleSubmit = async () => {
    // Validate required fields
    if (!form.title_id?.trim()) {
        toast({
            title: 'Error',
            description: 'Workplace name (Indonesian) is required',
            variant: 'error'
        });
        return;
    }
    
    form.loading = true;
    
    try {
        const url = editingWorkplace.value 
            ? route('content.workplace.update', editingWorkplace.value.id)
            : route('content.workplace.store');
        
        const method = editingWorkplace.value ? 'PUT' : 'POST';
        
        const formData = new FormData();
        
        // Add form fields (excluding loading and featured_image)
        Object.keys(form).forEach(key => {
            if (key !== 'loading' && key !== 'featured_image') {
                // Skip empty URLs to avoid validation errors
                if (key === 'source_url' && !form[key]) {
                    return;
                }
                
                // Handle boolean fields properly
                if (key === 'is_published' || key === 'is_featured') {
                    formData.append(key, form[key] ? '1' : '0');
                } else if (key === 'sort_order') {
                    // Handle integer fields
                    formData.append(key, parseInt(form[key]) || 0);
                } else {
                    formData.append(key, form[key] || '');
                }
            }
        });
        
        // Add file if exists (new upload)
        if (imageFile.value) {
            formData.append('featured_image', imageFile.value);
        } else if (editingWorkplace.value && editingWorkplace.value.featured_image && !editingWorkplace.value.featured_image.startsWith('data:')) {
            // For existing workplaces with existing image path, include the existing path
            formData.append('featured_image', editingWorkplace.value.featured_image);
        }
        
        // Add method for Laravel method spoofing
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        const response = await fetch(url, {
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
        
        if (response.ok) {
            toast({
                title: 'Success',
                description: data.message,
                variant: 'success'
            });
            dialogOpen.value = false;
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            toast({
                title: 'Error',
                description: data.message || 'An error occurred',
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

// Delete workplace
const confirmDelete = (workplaceId: number) => {
    confirmDialog.value = {
        open: true,
        workplaceId: workplaceId,
        loading: false
    };
};

const handleDelete = () => {
    const workplaceIdToDelete = confirmDialog.value.workplaceId;
    confirmDialog.value.loading = true;
    
    router.delete(route('content.workplace.destroy', workplaceIdToDelete), {
        onSuccess: (page) => {
            toast({
                title: 'Success',
                description: 'Workplace deleted successfully',
                variant: 'success'
            });
            
            confirmDialog.value.open = false;
            confirmDialog.value.loading = false;
            
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        },
        onError: (errors) => {
            toast({
                title: 'Error',
                description: errors.message || errors[0] || 'Failed to delete workplace',
                variant: 'error'
            });
            confirmDialog.value.loading = false;
        },
        onFinish: () => {
            confirmDialog.value.loading = false;
        }
    });
};


const hasFilters = computed(() => {
    return search.value || selectedCategory.value || selectedStatus.value || showFeaturedOnly.value;
});

</script>

<template>
    <Head title="Workplace" />
    
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Workplace</h1>
                        <p class="text-muted-foreground">
                            Manage workplace highlights and company culture
                        </p>
                    </div>
                    <Button @click="openCreateDialog">
                        <Plus class="w-4 h-4 mr-2" />
                        Add Workplace
                    </Button>
                </div>

                <!-- Filters Section -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Filter class="w-4 h-4" />
                            Filters
                            <Badge v-if="hasFilters" variant="secondary" class="ml-2">
                                Active
                            </Badge>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                <div class="grid md:grid-cols-4 gap-4">
                    <div class="space-y-2">
                        <Label>Search</Label>
                        <Input
                            v-model="search"
                            placeholder="Search workplaces..."
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    
                    <div class="space-y-2">
                        <Label>Category</Label>
                        <Select v-model="selectedCategory">
                            <option value="">All Categories</option>
                            <option v-for="(label, value) in categories" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </Select>
                    </div>
                    
                    <div class="space-y-2">
                        <Label>Status</Label>
                        <Select v-model="selectedStatus">
                            <option value="">All Statuses</option>
                            <option v-for="(label, value) in statuses" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </Select>
                    </div>
                    
                    <div class="space-y-2">
                        <Label>Featured Only</Label>
                        <div class="flex items-center space-x-2 h-10">
                            <input 
                                type="checkbox" 
                                v-model="showFeaturedOnly" 
                                id="showFeaturedOnly" 
                                class="rounded border-gray-300"
                            />
                            <label for="showFeaturedOnly" class="text-sm">Show featured workplaces only</label>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2 mt-4">
                    <Button @click="applyFilters" size="sm">
                        <Search class="w-4 h-4 mr-2" />
                        Apply Filters
                    </Button>
                    <Button @click="clearFilters" variant="outline" size="sm" v-if="hasFilters">
                        Clear Filters
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Partners Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div 
                v-for="workplace in contents.data"
                :key="workplace.id"
                class="group relative"
            >
                <Card class="h-full transition-all duration-200 hover:shadow-lg border border-gray-200 dark:border-gray-700">
                    <CardContent class="p-6">
                        <!-- Partner Logo -->
                        <div class="aspect-video bg-gray-50 dark:bg-gray-800 rounded-lg mb-4 flex items-center justify-center overflow-hidden p-4">
                            <img 
                                v-if="getWorkplaceImageUrl(workplace)" 
                                :src="getWorkplaceImageUrl(workplace)" 
                                :alt="getLocalizedTitle(workplace)"
                                class="max-w-full max-h-full object-contain"
                            />
                            <div v-else class="text-gray-400 text-sm text-center">
                                <div class="text-2xl mb-2">🏢</div>
                                <div>No Logo</div>
                            </div>
                        </div>
                        
                        <!-- Partner Info -->
                        <div class="space-y-3">
                            <h3 class="font-semibold text-base truncate" :title="getLocalizedTitle(workplace)">
                                {{ getLocalizedTitle(workplace) }}
                            </h3>
                            
                            <div class="flex flex-wrap items-center gap-2">
                                <Badge :variant="getCategoryVariant(workplace.category)" class="text-xs">
                                    {{ categories[workplace.category] || workplace.category }}
                                </Badge>
                                <Badge :variant="getStatusVariant(workplace.status)" class="text-xs">
                                    {{ statuses[workplace.status] || workplace.status }}
                                </Badge>
                                <Badge v-if="workplace.is_featured" variant="default" class="text-xs">
                                    ⭐ Featured
                                </Badge>
                            </div>
                            
                            <!-- Partner URL -->
                            <div v-if="workplace.source_url" class="text-xs text-gray-500 truncate" :title="workplace.source_url">
                                🌐 {{ workplace.source_url }}
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <div class="flex gap-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-1">
                                <Button 
                                    v-if="workplace.source_url" 
                                    :href="workplace.source_url" 
                                    target="_blank"
                                    variant="ghost" 
                                    size="sm" 
                                    class="h-8 w-8 p-0 hover:bg-blue-50 dark:hover:bg-blue-900"
                                >
                                    <ExternalLink class="w-3 h-3" />
                                </Button>
                                <Button 
                                    @click="openEditDialog(workplace)"
                                    variant="ghost" 
                                    size="sm" 
                                    class="h-8 w-8 p-0 hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <Edit class="w-3 h-3" />
                                </Button>
                                <Button 
                                    @click="confirmDelete(workplace.id)" 
                                    variant="ghost" 
                                    size="sm" 
                                    class="h-8 w-8 p-0 hover:bg-red-50 dark:hover:bg-red-900 text-red-600 dark:text-red-400"
                                >
                                    <Trash2 class="w-3 h-3" />
                                </Button>
                            </div>
                        </div>

                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Empty State -->
        <Card v-if="!contents.data?.length" class="text-center py-16">
            <CardContent class="py-8">
                <div class="mx-auto max-w-md">
                    <div class="text-gray-400 text-6xl mb-6">🤝</div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">No workplaces found</h3>
                    <p class="text-gray-500 mb-8 text-base">
                        {{ hasFilters ? 'No workplaces match your current filters.' : 'Get started by adding your first workplace.' }}
                    </p>
                    <div class="flex gap-3 justify-center">
                        <Button v-if="hasFilters" @click="clearFilters" variant="outline">
                            <Filter class="w-4 h-4 mr-2" />
                            Clear Filters
                        </Button>
                        <Button @click="openCreateDialog">
                            <Plus class="w-4 h-4 mr-2" />
                            Add First Workplace
                        </Button>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Pagination -->
        <Card v-if="contents.links && contents.data?.length" class="mt-8">
            <CardContent class="py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">{{ contents.meta?.from || 0 }}</span> to <span class="font-medium">{{ contents.meta?.to || 0 }}</span> of <span class="font-medium">{{ contents.meta?.total || 0 }}</span> workplaces
                    </div>
                    <div class="flex gap-1">
                        <template v-for="link in contents.links" :key="link.label">
                            <Link 
                                v-if="link.url"
                                :href="link.url"
                                class="px-3 py-2 text-sm border rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-700 transition-colors"
                                :class="link.active ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 dark:border-gray-600'"
                                v-html="link.label"
                            />
                            <span 
                                v-else 
                                class="px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md text-gray-400 dark:text-gray-500"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Modal Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>
                        {{ editingWorkplace ? 'Edit' : 'Add' }} Workplace
                    </DialogTitle>
                    <DialogDescription>
                        {{ editingWorkplace ? 'Update workplace information' : 'Add a new workplace highlight' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Workplace Name -->
                    <div class="grid lg:grid-cols-2 gap-6" v-if="bilingualEnabled">
                        <div class="space-y-2">
                            <Label for="title_id">Workplace Name (Indonesian) *</Label>
                            <Input
                                id="title_id"
                                v-model="form.title_id"
                                placeholder="Enter workplace name in Indonesian"
                                :disabled="form.loading"
                                required
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="title_en">Workplace Name (English) *</Label>
                            <Input
                                id="title_en"
                                v-model="form.title_en"
                                placeholder="Enter workplace name in English"
                                :disabled="form.loading"
                                required
                            />
                        </div>
                    </div>
                    <div v-else class="space-y-2">
                        <Label for="title_id">Workplace Name *</Label>
                        <Input
                            id="title_id"
                            v-model="form.title_id"
                            placeholder="Enter workplace name"
                            :disabled="form.loading"
                            required
                        />
                    </div>

                    <!-- Category & Status -->
                    <div class="grid lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="category">Category</Label>
                            <Select v-model="form.category">
                                <option v-for="(label, value) in categories" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </Select>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="status">Status</Label>
                            <Select v-model="form.status">
                                <option v-for="(label, value) in statuses" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </Select>
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
                            :disabled="form.loading"
                        />
                    </div>

                    <!-- Description -->
                    <div class="grid lg:grid-cols-2 gap-6" v-if="bilingualEnabled">
                        <div class="space-y-2">
                            <Label for="excerpt_id">Description (Indonesian)</Label>
                            <Textarea
                                id="excerpt_id"
                                v-model="form.excerpt_id"
                                placeholder="Brief description about the workplaceship"
                                :rows="3"
                                :disabled="form.loading"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="excerpt_en">Description (English)</Label>
                            <Textarea
                                id="excerpt_en"
                                v-model="form.excerpt_en"
                                placeholder="Brief description about the workplaceship"
                                :rows="3"
                                :disabled="form.loading"
                            />
                        </div>
                    </div>
                    <div v-else class="space-y-2">
                        <Label for="excerpt_id">Description</Label>
                        <Textarea
                            id="excerpt_id"
                            v-model="form.excerpt_id"
                            placeholder="Brief description about the workplaceship"
                            :rows="3"
                            :disabled="form.loading"
                        />
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
                            :disabled="form.loading"
                        />
                        <p class="text-sm text-gray-500">Lower numbers appear first</p>
                    </div>

                    <!-- Checkboxes -->
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" v-model="form.is_published" id="is_published" class="rounded border-gray-300" />
                            <Label for="is_published">Published</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" v-model="form.is_featured" id="is_featured" class="rounded border-gray-300" />
                            <Label for="is_featured">Featured</Label>
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="space-y-2">
                        <Label>Featured Image</Label>
                        <div class="space-y-4">
                            <!-- Custom Upload Button -->
                            <div class="flex items-center gap-4">
                                <input
                                    ref="imageInput"
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageUpload"
                                    class="hidden"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="$refs.imageInput.click()"
                                    class="gap-2"
                                >
                                    <Upload class="h-4 w-4" />
                                    {{ imageFile ? 'Change Image' : (editingWorkplace?.featured_image ? 'Replace Image' : 'Choose Image') }}
                                </Button>
                                <span v-if="imageFile" class="text-sm text-muted-foreground">
                                    {{ imageFile.name }}
                                </span>
                            </div>
                            
                            <!-- Current Image -->
                            <div v-if="editingWorkplace?.featured_image && !imagePreview" class="relative inline-block">
                                <img
                                    :src="`/storage/${editingWorkplace.featured_image}`"
                                    alt="Current image"
                                    class="h-32 w-32 object-cover rounded-lg border"
                                />
                                <p class="text-xs text-muted-foreground mt-1">Current image</p>
                            </div>
                            
                            <!-- New Image Preview -->
                            <div v-if="imagePreview" class="relative inline-block">
                                <img
                                    :src="imagePreview"
                                    alt="Preview"
                                    class="h-32 w-32 object-cover rounded-lg border"
                                />
                                <Button
                                    type="button"
                                    variant="destructive"
                                    size="sm"
                                    @click="removeImage"
                                    class="absolute -top-2 -right-2 h-6 w-6 p-0 rounded-full"
                                >
                                    <X class="h-3 w-3" />
                                </Button>
                                <p class="text-xs text-muted-foreground mt-1">New image</p>
                            </div>
                        </div>
                    </div>
                </form>

                <DialogFooter class="mt-6">
                    <Button type="button" variant="outline" @click="dialogOpen = false" :disabled="form.loading">
                        Cancel
                    </Button>
                    <Button type="button" @click="handleSubmit" :disabled="form.loading">
                        <Save v-if="!form.loading" class="w-4 h-4 mr-2" />
                        {{ form.loading ? 'Saving...' : (editingWorkplace ? 'Update Workplace' : 'Create Workplace') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

                <!-- Confirm Delete Dialog -->
                <ConfirmDialog
                    v-model:open="confirmDialog.open"
                    title="Delete Workplace"
                    description="Are you sure you want to delete this workplace? This action cannot be undone."
                    :loading="confirmDialog.loading"
                    @confirm="handleDelete"
                />
            </div>
        </div>
    </AppLayout>
</template>