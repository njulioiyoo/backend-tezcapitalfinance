<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive, computed, onMounted } from 'vue';
import { route } from 'ziggy-js';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import RichTextEditor from '@/components/ui/RichTextEditor.vue';
import { toast } from '@/components/ui/toast';
import { Search, Plus, Filter, Edit, Trash2, ExternalLink, Upload, Image, X, Save } from 'lucide-vue-next';

interface Service {
    id: number;
    type: string;
    category: string;
    title_id: string;
    title_en: string;
    excerpt_id: string;
    excerpt_en: string;
    content_id: string;
    content_en: string;
    featured_image: string;
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
        data?: Service[];
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

// Reactive state
const contents = ref(props.contents);
const showCreateModal = ref(false);
const showEditModal = ref(false);
const showDeleteConfirm = ref(false);
const currentService = ref<Service | null>(null);
const isSubmitting = ref(false);

// Search and filters
const filters = reactive({
    search: props.filters.search || '',
    category: props.filters.category || '',
    status: props.filters.status || '',
});

// Form data
const form = reactive({
    title_id: '',
    title_en: '',
    excerpt_id: '',
    excerpt_en: '',
    content_id: '',
    content_en: '',
    category: '',
    featured_image: null as File | null,
    status: 'draft',
    is_published: false,
    is_featured: false,
    sort_order: 0,
    meta_title_id: '',
    meta_title_en: '',
    meta_description_id: '',
    meta_description_en: ''
});

// Featured image preview
const featuredImagePreview = ref<string | null>(null);

// Computed
const breadcrumbItems = computed<BreadcrumbItem[]>(() => [
    { label: 'Content', href: '#', current: false },
    { label: 'Services', href: route('content.services.index'), current: true }
]);

// Methods
const resetForm = () => {
    Object.assign(form, {
        title_id: '',
        title_en: '',
        excerpt_id: '',
        excerpt_en: '',
        content_id: '',
        content_en: '',
        category: '',
        featured_image: null,
        status: 'draft',
        is_published: false,
        is_featured: false,
        sort_order: 0,
        meta_title_id: '',
        meta_title_en: '',
        meta_description_id: '',
        meta_description_en: ''
    });
    featuredImagePreview.value = null;
};

const openCreateModal = () => {
    resetForm();
    currentService.value = null;
    showCreateModal.value = true;
};

const openEditModal = (service: Service) => {
    currentService.value = service;
    Object.assign(form, {
        title_id: service.title_id || '',
        title_en: service.title_en || '',
        excerpt_id: service.excerpt_id || '',
        excerpt_en: service.excerpt_en || '',
        content_id: service.content_id || '',
        content_en: service.content_en || '',
        category: service.category || '',
        featured_image: null,
        status: service.status || 'draft',
        is_published: service.is_published || false,
        is_featured: service.is_featured || false,
        sort_order: service.sort_order || 0,
        meta_title_id: service.meta_title_id || '',
        meta_title_en: service.meta_title_en || '',
        meta_description_id: service.meta_description_id || '',
        meta_description_en: service.meta_description_en || ''
    });
    showEditModal.value = true;
};

const handleImageUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files[0]) {
        const file = input.files[0];
        form.featured_image = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            featuredImagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const removeImage = () => {
    form.featured_image = null;
    featuredImagePreview.value = null;
};

const getImageUrl = (path: string) => {
    if (!path) return null;
    if (path.startsWith('http')) return path;
    return `/storage/${path}`;
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const submitForm = async () => {
    if (isSubmitting.value) return;
    
    isSubmitting.value = true;
    
    try {
        // Validate required fields first
        if (!form.title_id?.trim()) {
            toast({
                title: 'Error',
                description: 'Title (Indonesian) is required',
                variant: 'error'
            });
            isSubmitting.value = false;
            return;
        }

        if (!form.title_en?.trim()) {
            toast({
                title: 'Error',
                description: 'Title (English) is required',
                variant: 'error'
            });
            isSubmitting.value = false;
            return;
        }

        if (!form.status) {
            toast({
                title: 'Error',
                description: 'Status is required',
                variant: 'error'
            });
            isSubmitting.value = false;
            return;
        }

        // Clean form data - ensure all fields are strings and not empty
        const cleanFormData = {
            type: 'service',
            title_id: form.title_id.trim(),
            title_en: form.title_en.trim(),
            excerpt_id: form.excerpt_id?.trim() || '',
            excerpt_en: form.excerpt_en?.trim() || '',
            content_id: form.content_id?.trim() || '',
            content_en: form.content_en?.trim() || '',
            category: form.category || '',
            status: form.status || 'draft',
            is_published: form.is_published ? '1' : '0',  // Convert boolean to string
            is_featured: form.is_featured ? '1' : '0',    // Convert boolean to string
            sort_order: String(parseInt(form.sort_order) || 0),
            meta_title_id: form.meta_title_id?.trim() || '',
            meta_title_en: form.meta_title_en?.trim() || '',
            meta_description_id: form.meta_description_id?.trim() || '',
            meta_description_en: form.meta_description_en?.trim() || ''
        };

        // Add image if present
        if (form.featured_image) {
            cleanFormData.featured_image = form.featured_image;
        }


        if (currentService.value) {
            // Update existing service
            if (form.featured_image) {
                // Create custom FormData to ensure all fields are included
                const formData = new FormData();
                
                // Add all text fields explicitly
                Object.keys(cleanFormData).forEach(key => {
                    if (key !== 'featured_image') {
                        formData.append(key, cleanFormData[key]);
                    }
                });
                
                // Add the file
                formData.append('featured_image', form.featured_image);
                formData.append('_method', 'PUT');
                
                router.post(route('content.services.update', currentService.value.id), formData, {
                    onSuccess: (page) => {
                        toast({
                            title: 'Success',
                            description: 'Service updated successfully',
                            variant: 'success'
                        });
                        
                        // Refresh data to show updated image
                        setTimeout(() => {
                            router.visit(window.location.href, {
                                preserveScroll: true,
                                only: ['contents']
                            });
                        }, 200);
                        
                        showEditModal.value = false;
                        resetForm();
                    },
                    onError: (errors) => {
                        //('Update errors:', errors);
                        const errorMessage = Object.values(errors).flat().join(', ') || 'Failed to update service';
                        toast({
                            title: 'Error', 
                            description: errorMessage,
                            variant: 'error'
                        });
                    },
                    onFinish: () => {
                        isSubmitting.value = false;
                    }
                });
            } else {
                // Use regular JSON for non-file data
                router.put(route('content.services.update', currentService.value.id), cleanFormData, {
                    onSuccess: (page) => {
                        toast({
                            title: 'Success',
                            description: 'Service updated successfully',
                            variant: 'success'
                        });
                        showEditModal.value = false;
                        resetForm();
                        // Refresh data to show changes
                        setTimeout(() => {
                            router.visit(window.location.href, {
                                preserveScroll: true,
                                only: ['contents']
                            });
                        }, 200);
                    },
                    onError: (errors) => {
                        //('Update errors:', errors);
                        const errorMessage = Object.values(errors).flat().join(', ') || 'Failed to update service';
                        toast({
                            title: 'Error', 
                            description: errorMessage,
                            variant: 'error'
                        });
                    },
                    onFinish: () => {
                        isSubmitting.value = false;
                    }
                });
            }
        } else {
            // Create new service
            if (form.featured_image) {
                // Create custom FormData to ensure all fields are included
                const formData = new FormData();
                
                // Add all text fields explicitly
                Object.keys(cleanFormData).forEach(key => {
                    if (key !== 'featured_image') {
                        formData.append(key, cleanFormData[key]);
                    }
                });
                
                // Add the file
                formData.append('featured_image', form.featured_image);
                
                router.post(route('content.services.store'), formData, {
                    onSuccess: (page) => {
                        toast({
                            title: 'Success',
                            description: 'Service created successfully',
                            variant: 'success'
                        });
                        showCreateModal.value = false;
                        resetForm();
                        // Refresh data to show changes
                        setTimeout(() => {
                            router.visit(window.location.href, {
                                preserveScroll: true,
                                only: ['contents']
                            });
                        }, 200);
                    },
                    onError: (errors) => {
                        //('Create errors:', errors);
                        const errorMessage = Object.values(errors).flat().join(', ') || 'Failed to create service';
                        toast({
                            title: 'Error',
                            description: errorMessage,
                            variant: 'error'
                        });
                    },
                    onFinish: () => {
                        isSubmitting.value = false;
                    }
                });
            } else {
                // Use regular JSON for non-file data
                router.post(route('content.services.store'), cleanFormData, {
                    onSuccess: (page) => {
                        toast({
                            title: 'Success',
                            description: 'Service created successfully',
                            variant: 'success'
                        });
                        showCreateModal.value = false;
                        resetForm();
                        // Refresh data to show changes
                        setTimeout(() => {
                            router.visit(window.location.href, {
                                preserveScroll: true,
                                only: ['contents']
                            });
                        }, 200);
                    },
                    onError: (errors) => {
                        //('Create errors:', errors);
                        const errorMessage = Object.values(errors).flat().join(', ') || 'Failed to create service';
                        toast({
                            title: 'Error',
                            description: errorMessage,
                            variant: 'error'
                        });
                    },
                    onFinish: () => {
                        isSubmitting.value = false;
                    }
                });
            }
        }
    } catch (error) {
        //('Error saving service:', error);
        toast({
            title: 'Error',
            description: 'An error occurred while saving',
            variant: 'error'
        });
        isSubmitting.value = false;
    }
};

const confirmDelete = (service: Service) => {
    currentService.value = service;
    showDeleteConfirm.value = true;
};

const deleteService = async () => {
    if (!currentService.value || isSubmitting.value) return;
    
    isSubmitting.value = true;
    
    try {
        router.delete(route('content.services.destroy', currentService.value.id), {
            onSuccess: (page) => {
                toast({
                    title: 'Success',
                    description: 'Service deleted successfully',
                    variant: 'success'
                });
                showDeleteConfirm.value = false;
                currentService.value = null;
            },
            onError: (errors) => {
                //('Delete errors:', errors);
                const errorMessage = Object.values(errors).flat().join(', ') || 'Failed to delete service';
                toast({
                    title: 'Error',
                    description: errorMessage,
                    variant: 'error'
                });
            },
            onFinish: () => {
                isSubmitting.value = false;
            }
        });
    } catch (error) {
        //('Error deleting service:', error);
        toast({
            title: 'Error',
            description: 'An error occurred while deleting',
            variant: 'error'
        });
        isSubmitting.value = false;
    }
};

const applyFilters = () => {
    const params = new URLSearchParams();
    
    if (filters.search) params.set('search', filters.search);
    if (filters.category) params.set('category', filters.category);
    if (filters.status) params.set('status', filters.status);
    
    const queryString = params.toString();
    const url = queryString ? `${route('content.services.index')}?${queryString}` : route('content.services.index');
    
    router.visit(url);
};

const clearFilters = () => {
    Object.assign(filters, { search: '', category: '', status: '' });
    router.visit(route('content.services.index'));
};
</script>

<template>
    <Head title="Services" />
    
    <AppLayout :breadcrumbs="breadcrumbItems">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Services</h1>
                        <p class="text-muted-foreground">Manage your services content and information</p>
                    </div>
                    <Button @click="openCreateModal" class="gap-2">
                        <Plus class="h-4 w-4" />
                        Add Service
                    </Button>
                </div>

                <!-- Filters -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Filter class="h-5 w-5" />
                            Filters
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="space-y-2">
                                <Label>Search</Label>
                                <Input 
                                    v-model="filters.search" 
                                    placeholder="Search by title, category, or content..."
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                            
                            <div class="space-y-2">
                                <Label>Category</Label>
                                <Select v-model="filters.category">
                                    <option value="">All Categories</option>
                                    <option v-for="(label, value) in categories" :key="value" :value="value">
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

                <!-- Services Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card v-for="service in contents.data" :key="service.id" class="group hover:shadow-lg transition-shadow">
                    <CardHeader class="pb-3">
                        <div class="flex items-start justify-between">
                            <div class="space-y-1 flex-1">
                                <div class="flex items-center gap-2">
                                    <Badge :variant="service.is_featured ? 'default' : 'secondary'">
                                        {{ service.category || 'Uncategorized' }}
                                    </Badge>
                                    <Badge v-if="service.is_featured" variant="outline" class="text-yellow-600 border-yellow-600">
                                        Featured
                                    </Badge>
                                </div>
                                <CardTitle class="line-clamp-2">{{ service.title_id }}</CardTitle>
                                <p v-if="service.title_en && bilingualEnabled" class="text-sm text-muted-foreground line-clamp-1">
                                    {{ service.title_en }}
                                </p>
                            </div>
                            <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <Button @click="openEditModal(service)" size="sm" variant="outline">
                                    <Edit class="h-4 w-4" />
                                </Button>
                                <Button @click="confirmDelete(service)" size="sm" variant="outline" class="text-red-600 hover:text-red-700">
                                    <Trash2 class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-3">
                            <!-- Featured Image -->
                            <div v-if="service.featured_image" class="aspect-video rounded-lg overflow-hidden bg-muted">
                                <img
                                    :src="getImageUrl(service.featured_image)"
                                    :alt="service.title_id"
                                    class="w-full h-full object-contain"
                                />
                            </div>
                            <div v-else class="aspect-video rounded-lg bg-muted flex items-center justify-center">
                                <Image class="h-12 w-12 text-muted-foreground" />
                            </div>
                            
                            <!-- Excerpt -->
                            <p class="text-sm text-muted-foreground line-clamp-3">
                                {{ service.excerpt_id }}
                            </p>
                            
                            <!-- Status and Stats -->
                            <div class="flex items-center justify-between pt-2 border-t">
                                <div class="flex items-center gap-2">
                                    <Badge 
                                        :variant="service.is_published ? 'default' : 'secondary'"
                                        :class="{
                                            'bg-green-100 text-green-800': service.is_published && service.status === 'published',
                                            'bg-yellow-100 text-yellow-800': service.status === 'draft',
                                            'bg-red-100 text-red-800': service.status === 'archived',
                                        }"
                                    >
                                        {{ service.status }}
                                    </Badge>
                                </div>
                                <div class="text-xs text-muted-foreground">
                                    {{ service.view_count || 0 }} views
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
                
                <!-- Empty State -->
                <div v-if="!contents.data?.length" class="col-span-full">
                    <Card class="text-center py-12">
                        <CardContent>
                            <Image class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                            <h3 class="text-lg font-medium mb-2">No services found</h3>
                            <p class="text-muted-foreground mb-4">Get started by creating your first service.</p>
                            <Button @click="openCreateModal">
                                <Plus class="h-4 w-4 mr-2" />
                                Add Service
                            </Button>
                        </CardContent>
                    </Card>
                </div>
                </div>

                <!-- Pagination -->
                <div v-if="contents.links && contents.links.length > 3" class="flex justify-center">
                <nav class="flex items-center gap-1">
                    <template v-for="(link, index) in contents.links" :key="index">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="px-3 py-2 text-sm font-medium rounded-lg transition-colors"
                            :class="{
                                'bg-primary text-primary-foreground': link.active,
                                'hover:bg-muted': !link.active
                            }"
                            v-html="link.label"
                        />
                        <span
                            v-else
                            class="px-3 py-2 text-sm font-medium text-muted-foreground"
                            v-html="link.label"
                        />
                    </template>
                </nav>
                </div>

        <!-- Create Modal -->
        <Dialog v-model:open="showCreateModal">
            <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Add New Service</DialogTitle>
                    <DialogDescription>
                        Create a new service to showcase your offerings.
                    </DialogDescription>
                </DialogHeader>
                
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="title_id">Title (Indonesian) *</Label>
                            <Input
                                id="title_id"
                                v-model="form.title_id"
                                required
                                placeholder="Enter service title in Indonesian"
                            />
                        </div>
                        
                        <div v-if="bilingualEnabled" class="space-y-2">
                            <Label for="title_en">Title (English) *</Label>
                            <Input
                                id="title_en"
                                v-model="form.title_en"
                                required
                                placeholder="Enter service title in English"
                            />
                        </div>
                    </div>

                    <!-- Category and Status -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <Label for="category">Category</Label>
                            <Select v-model="form.category">
                                <option value="">Select category</option>
                                <option v-for="(label, value) in categories" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </Select>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="status">Status</Label>
                            <Select v-model="form.status">
                                <option value="draft">Draft</option>
                                <option value="review">Review</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </Select>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="sort_order">Sort Order</Label>
                            <Input
                                id="sort_order"
                                v-model="form.sort_order"
                                type="number"
                                min="0"
                                placeholder="0"
                            />
                        </div>
                    </div>

                    <!-- Excerpt -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="excerpt_id">Short Description (Indonesian) *</Label>
                            <Textarea
                                id="excerpt_id"
                                v-model="form.excerpt_id"
                                required
                                placeholder="Brief description in Indonesian"
                                rows="3"
                            />
                        </div>
                        
                        <div v-if="bilingualEnabled" class="space-y-2">
                            <Label for="excerpt_en">Short Description (English) *</Label>
                            <Textarea
                                id="excerpt_en"
                                v-model="form.excerpt_en"
                                required
                                placeholder="Brief description in English"
                                rows="3"
                            />
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="content_id">Content (Indonesian) *</Label>
                            <RichTextEditor
                                v-model="form.content_id"
                                placeholder="Detailed service content in Indonesian"
                            />
                        </div>
                        
                        <div v-if="bilingualEnabled" class="space-y-2">
                            <Label for="content_en">Content (English) *</Label>
                            <RichTextEditor
                                v-model="form.content_en"
                                placeholder="Detailed service content in English"
                            />
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="space-y-2">
                        <Label>Featured Image</Label>
                        <div class="space-y-4">
                            <!-- Custom Upload Button -->
                            <div class="flex items-center gap-4">
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageUpload"
                                    class="hidden"
                                    id="featured-image-create"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="() => document.getElementById('featured-image-create')?.click()"
                                    class="gap-2"
                                >
                                    <Upload class="h-4 w-4" />
                                    {{ form.featured_image ? 'Change Image' : 'Choose Image' }}
                                </Button>
                                <span v-if="form.featured_image" class="text-sm text-muted-foreground">
                                    {{ form.featured_image.name }}
                                </span>
                            </div>
                            
                            <div v-if="featuredImagePreview" class="relative inline-block">
                                <img
                                    :src="featuredImagePreview"
                                    alt="Preview"
                                    class="h-32 w-32 object-cover rounded-lg border"
                                />
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="destructive"
                                    class="absolute -top-2 -right-2 h-6 w-6 rounded-full p-0"
                                    @click="removeImage"
                                >
                                    <X class="h-4 w-4" />
                                </Button>
                            </div>
                        </div>
                    </div>

                    <!-- Checkboxes -->
                    <div class="flex gap-6">
                        <div class="flex items-center space-x-2">
                            <Switch id="is_published" v-model:checked="form.is_published" />
                            <Label for="is_published">Published</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Switch id="is_featured" v-model:checked="form.is_featured" />
                            <Label for="is_featured">Featured</Label>
                        </div>
                    </div>
                </form>
                
                <DialogFooter>
                    <Button type="button" variant="outline" @click="showCreateModal = false">
                        Cancel
                    </Button>
                    <Button @click="submitForm" :disabled="isSubmitting">
                        <Save class="h-4 w-4 mr-2" />
                        {{ isSubmitting ? 'Creating...' : 'Create Service' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Edit Modal -->
        <Dialog v-model:open="showEditModal">
            <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Edit Service</DialogTitle>
                    <DialogDescription>
                        Update the service information.
                    </DialogDescription>
                </DialogHeader>
                
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Same form content as Create Modal -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="edit_title_id">Title (Indonesian) *</Label>
                            <Input
                                id="edit_title_id"
                                v-model="form.title_id"
                                required
                                placeholder="Enter service title in Indonesian"
                            />
                        </div>
                        
                        <div v-if="bilingualEnabled" class="space-y-2">
                            <Label for="edit_title_en">Title (English) *</Label>
                            <Input
                                id="edit_title_en"
                                v-model="form.title_en"
                                required
                                placeholder="Enter service title in English"
                            />
                        </div>
                    </div>

                    <!-- Category and Status -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="space-y-2">
                            <Label for="edit_category">Category</Label>
                            <Select v-model="form.category">
                                <option value="">Select category</option>
                                <option v-for="(label, value) in categories" :key="value" :value="value">
                                    {{ label }}
                                </option>
                            </Select>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="edit_status">Status</Label>
                            <Select v-model="form.status">
                                <option value="draft">Draft</option>
                                <option value="review">Review</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </Select>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="edit_sort_order">Sort Order</Label>
                            <Input
                                id="edit_sort_order"
                                v-model="form.sort_order"
                                type="number"
                                min="0"
                                placeholder="0"
                            />
                        </div>
                    </div>

                    <!-- Excerpt -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="edit_excerpt_id">Short Description (Indonesian) *</Label>
                            <Textarea
                                id="edit_excerpt_id"
                                v-model="form.excerpt_id"
                                required
                                placeholder="Brief description in Indonesian"
                                rows="3"
                            />
                        </div>
                        
                        <div v-if="bilingualEnabled" class="space-y-2">
                            <Label for="edit_excerpt_en">Short Description (English) *</Label>
                            <Textarea
                                id="edit_excerpt_en"
                                v-model="form.excerpt_en"
                                required
                                placeholder="Brief description in English"
                                rows="3"
                            />
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label for="edit_content_id">Content (Indonesian) *</Label>
                            <RichTextEditor
                                v-model="form.content_id"
                                placeholder="Detailed service content in Indonesian"
                            />
                        </div>
                        
                        <div v-if="bilingualEnabled" class="space-y-2">
                            <Label for="edit_content_en">Content (English) *</Label>
                            <RichTextEditor
                                v-model="form.content_en"
                                placeholder="Detailed service content in English"
                            />
                        </div>
                    </div>

                    <!-- Featured Image -->
                    <div class="space-y-2">
                        <Label>Featured Image</Label>
                        <div class="space-y-4">
                            <!-- Custom Upload Button -->
                            <div class="flex items-center gap-4">
                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageUpload"
                                    class="hidden"
                                    id="featured-image-edit"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="() => document.getElementById('featured-image-edit')?.click()"
                                    class="gap-2"
                                >
                                    <Upload class="h-4 w-4" />
                                    {{ form.featured_image ? 'Change Image' : (currentService?.featured_image ? 'Replace Image' : 'Choose Image') }}
                                </Button>
                                <span v-if="form.featured_image" class="text-sm text-muted-foreground">
                                    {{ form.featured_image.name }}
                                </span>
                            </div>
                            
                            <!-- Current Image -->
                            <div v-if="currentService?.featured_image && !featuredImagePreview" class="relative inline-block">
                                <img
                                    :src="getImageUrl(currentService.featured_image)"
                                    alt="Current image"
                                    class="h-32 w-32 object-cover rounded-lg border"
                                />
                                <p class="text-xs text-muted-foreground mt-1">Current image</p>
                            </div>
                            
                            <!-- New Image Preview -->
                            <div v-if="featuredImagePreview" class="relative inline-block">
                                <img
                                    :src="featuredImagePreview"
                                    alt="New preview"
                                    class="h-32 w-32 object-cover rounded-lg border"
                                />
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="destructive"
                                    class="absolute -top-2 -right-2 h-6 w-6 rounded-full p-0"
                                    @click="removeImage"
                                >
                                    <X class="h-4 w-4" />
                                </Button>
                                <p class="text-xs text-muted-foreground mt-1">New image</p>
                            </div>
                        </div>
                    </div>

                    <!-- Checkboxes -->
                    <div class="flex gap-6">
                        <div class="flex items-center space-x-2">
                            <Switch id="edit_is_published" v-model:checked="form.is_published" />
                            <Label for="edit_is_published">Published</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <Switch id="edit_is_featured" v-model:checked="form.is_featured" />
                            <Label for="edit_is_featured">Featured</Label>
                        </div>
                    </div>
                </form>
                
                <DialogFooter>
                    <Button type="button" variant="outline" @click="showEditModal = false">
                        Cancel
                    </Button>
                    <Button @click="submitForm" :disabled="isSubmitting">
                        <Save class="h-4 w-4 mr-2" />
                        {{ isSubmitting ? 'Updating...' : 'Update Service' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            v-model:open="showDeleteConfirm"
            title="Delete Service"
            :description="`Are you sure you want to delete '${currentService?.title_id}'? This action cannot be undone.`"
            confirmText="Delete"
            cancelText="Cancel"
            variant="destructive"
            @confirm="deleteService"
            :loading="isSubmitting"
        />
            </div>
        </div>
    </AppLayout>
</template>