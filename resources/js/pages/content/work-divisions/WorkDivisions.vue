<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';
import { route } from 'ziggy-js';
import axios from 'axios';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { toast } from '@/components/ui/toast';
import { Search, Plus, Filter, Edit, Trash2, ExternalLink, Upload, Image, X, Save, Star } from 'lucide-vue-next';

interface WorkDivision {
    id: number;
    type: string;
    category: string;
    slug: string;
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
        data?: WorkDivision[];
        links?: any[];
        meta?: any;
    };
    type: string;
    types: Record<string, string>;
    categories: Record<string, string>;
    statuses: Record<string, string>;
    filters: {
        search?: string;
        status?: string;
        featured?: boolean;
    };
    bilingualEnabled: boolean;
}

const props = defineProps<Props>();

// Breadcrumb navigation
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Content Management',
        href: route('content.news-events.index'),
    },
    {
        title: 'Work Divisions',
        href: route('content.work-divisions.index'),
    },
];

// State management
const filters = reactive({
    search: props.filters.search || '',
    status: props.filters.status || '',
    featured: props.filters.featured || false,
});

const isDialogOpen = ref(false);
const isConfirmDialogOpen = ref(false);
const selectedItem = ref<WorkDivision | null>(null);
const itemToDelete = ref<WorkDivision | null>(null);
const selectedItems = ref<number[]>([]);

// Form state
const formData = reactive({
    title_id: '',
    title_en: '',
    content_id: '',
    content_en: '',
    status: 'draft',
    is_published: false,
    is_featured: false,
    sort_order: 0,
    featured_image: '',
});

const imageFile = ref<File | null>(null);
const imagePreview = ref<string>('');

// Computed properties
const hasData = computed(() => props.contents.data && props.contents.data.length > 0);
const allSelected = computed(() => 
    selectedItems.value.length > 0 && selectedItems.value.length === props.contents.data?.length
);
const hasActiveFilters = computed(() => {
    return !!(filters.search || filters.status || filters.featured);
});

// Methods
const applyFilters = () => {
    router.get(route('content.work-divisions.index'), {
        search: filters.search || undefined,
        status: filters.status || undefined,
        featured: filters.featured || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    Object.assign(filters, {
        search: '',
        status: '',
        featured: false,
    });
    applyFilters();
};

const openCreateDialog = () => {
    selectedItem.value = null;
    resetFormData();
    isDialogOpen.value = true;
};

const openEditDialog = (item: WorkDivision) => {
    selectedItem.value = item;
    populateFormData(item);
    isDialogOpen.value = true;
};

const closeDialog = () => {
    isDialogOpen.value = false;
    selectedItem.value = null;
    resetFormData();
};

const resetFormData = () => {
    Object.assign(formData, {
        title_id: '',
        title_en: '',
        content_id: '',
        content_en: '',
        status: 'draft',
        is_published: false,
        is_featured: false,
        sort_order: 0,
        featured_image: '',
    });
    
    imageFile.value = null;
    imagePreview.value = '';
};

const populateFormData = (item: WorkDivision) => {
    Object.assign(formData, {
        title_id: item.title_id || '',
        title_en: item.title_en || '',
        content_id: item.content_id || '',
        content_en: item.content_en || '',
        status: item.status || 'draft',
        is_published: item.is_published || false,
        is_featured: item.is_featured || false,
        sort_order: item.sort_order || 0,
        featured_image: item.featured_image || '',
    });
    
    // Reset file upload for editing (keep existing image path but clear new file)
    imageFile.value = null;
    
    // For existing items, show the image URL as preview
    if (item.featured_image && !item.featured_image.startsWith('data:')) {
        imagePreview.value = `/storage/${item.featured_image}`;
    } else if (item.featured_image) {
        imagePreview.value = item.featured_image;
    } else {
        imagePreview.value = '';
    }
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
    formData.featured_image = '';
    // Clear the file input
    const input = document.querySelector('input[type="file"]') as HTMLInputElement;
    if (input) input.value = '';
};

const submitForm = async () => {
    try {
        const formDataObj = new FormData();
        
        // Add form fields (excluding featured_image for now)
        Object.entries(formData).forEach(([key, value]) => {
            if (key !== 'featured_image' && value !== null && value !== undefined) {
                if (typeof value === 'boolean') {
                    formDataObj.append(key, value ? '1' : '0');
                } else {
                    formDataObj.append(key, String(value));
                }
            }
        });
        
        // Add file only if there's a new upload
        if (imageFile.value) {
            formDataObj.append('featured_image', imageFile.value);
        }
        // For edits without new file upload, preserve existing image
        else if (selectedItem.value && !imageFile.value && formData.featured_image) {
            // Don't append featured_image field - backend will handle keeping existing image
        }
        
        formDataObj.append('type', 'work-division');

        // Debug FormData
        console.log('🔧 FormData entries:');
        for (const pair of formDataObj.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        if (selectedItem.value) {
            // Update existing item
            formDataObj.append('_method', 'PUT');
            console.log('🔧 Updating work division ID:', selectedItem.value.id);
            
            await axios.post(route('content.work-divisions.update', selectedItem.value.id), formDataObj, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
        } else {
            // Create new item
            console.log('🔧 Creating new work division');
            
            await axios.post(route('content.work-divisions.store'), formDataObj, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                }
            });
        }
        
        closeDialog();
        // Reload page to show changes
        window.location.reload();
    } catch (error) {
        console.error('🔧 Error saving work division:', error);
        console.error('🔧 Error response:', error.response);
        if (error.response && error.response.data && error.response.data.errors) {
            console.error('🔧 Validation errors:', error.response.data.errors);
            
            // Log each validation error in detail
            Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                console.error(`🔧 ${field}:`, messages);
            });
        }
    }
};

const confirmDelete = (item: WorkDivision) => {
    itemToDelete.value = item;
    isConfirmDialogOpen.value = true;
};

const deleteItem = () => {
    if (itemToDelete.value) {
        router.delete(route('content.work-divisions.destroy', itemToDelete.value.id), {
            onSuccess: () => {
                toast.success('Work division deleted successfully');
                isConfirmDialogOpen.value = false;
                itemToDelete.value = null;
            },
            onError: () => {
                toast.error('Failed to delete work division');
            },
        });
    }
};

const toggleSelectAll = () => {
    if (allSelected.value) {
        selectedItems.value = [];
    } else {
        selectedItems.value = props.contents.data?.map(item => item.id) || [];
    }
};


const getStatusColor = (status: string) => {
    switch (status) {
        case 'published': return 'bg-green-100 text-green-800';
        case 'draft': return 'bg-yellow-100 text-yellow-800';
        case 'archived': return 'bg-gray-100 text-gray-800';
        default: return 'bg-gray-100 text-gray-800';
    }
};

const getImageUrl = (imagePath: string | null) => {
    if (!imagePath) return '/img/Sorotan.svg';
    return imagePath.startsWith('http') ? imagePath : `/storage/${imagePath}`;
};
</script>

<template>
    <Head title="Work Divisions" />
    
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Work Divisions</h1>
                    <p class="text-muted-foreground">
                        Manage work divisions for "Get to Know Our Works" section
                    </p>
                </div>
                <Button @click="openCreateDialog" class="gap-2">
                    <Plus class="h-4 w-4" />
                    Add Work Division
                </Button>
            </div>

            <!-- Filters Section -->
            <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <PlaceholderPattern />
                <Card class="relative z-10 border-0 shadow-none bg-transparent">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Filter class="w-4 h-4" />
                            Filters
                            <Badge v-if="hasActiveFilters" variant="secondary" class="ml-2">
                                Active
                            </Badge>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <Label>Search</Label>
                                <Input
                                    v-model="filters.search"
                                    placeholder="Search work divisions..."
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                            
                            <div class="space-y-2">
                                <Label>Status</Label>
                                <Select v-model="filters.status">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Statuses" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Statuses</SelectItem>
                                        <SelectItem 
                                            v-for="(label, value) in statuses" 
                                            :key="value" 
                                            :value="value"
                                        >
                                            {{ label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            
                            <div class="space-y-2">
                                <Label>Featured Only</Label>
                                <div class="flex items-center space-x-2 h-10">
                                    <input 
                                        type="checkbox" 
                                        v-model="filters.featured" 
                                        id="showFeaturedOnly" 
                                        class="rounded border-gray-300"
                                    />
                                    <label for="showFeaturedOnly" class="text-sm">Show featured only</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex gap-2 mt-4">
                            <Button @click="applyFilters" size="sm">
                                <Search class="w-4 h-4 mr-2" />
                                Apply Filters
                            </Button>
                            <Button @click="resetFilters" variant="outline" size="sm" v-if="hasActiveFilters">
                                Clear Filters
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Content Table -->
            <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                <PlaceholderPattern />
                <Card class="relative z-10 border-0 shadow-none bg-transparent">
                    <CardHeader>
                        <CardTitle>Work Divisions List</CardTitle>
                        <CardDescription>
                            Showing {{ contents.from || 0 }}-{{ contents.to || 0 }} of {{ contents.total || 0 }} work divisions
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                    <div v-if="!hasData" class="text-center py-8">
                        <div class="text-muted-foreground mb-4">
                            No work divisions found. Add your first work division.
                        </div>
                        <Button @click="openCreateDialog">
                            <Plus class="w-4 h-4 mr-2" />
                            Add New Work Division
                        </Button>
                    </div>
                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-4 font-medium">
                                        <input 
                                            type="checkbox" 
                                            :checked="allSelected"
                                            @change="toggleSelectAll"
                                        />
                                    </th>
                                    <th class="text-left p-4 font-medium">Photo</th>
                                    <th class="text-left p-4 font-medium">Division</th>
                                    <th class="text-left p-4 font-medium">Status</th>
                                    <th class="text-left p-4 font-medium">Views</th>
                                    <th class="text-left p-4 font-medium">Updated</th>
                                    <th class="text-right p-4 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr 
                                    v-for="item in contents.data" 
                                    :key="item.id"
                                    class="border-b hover:bg-muted/50"
                                >
                                    <td class="p-4">
                                        <input 
                                            type="checkbox" 
                                            :value="item.id"
                                            v-model="selectedItems"
                                        />
                                    </td>
                                    <td class="p-4">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-muted">
                                            <img
                                                :src="getImageUrl(item.featured_image)"
                                                :alt="item.title_id || item.title_en"
                                                class="w-full h-full object-cover"
                                                @error="(e) => e.target.src = '/img/Sorotan.svg'"
                                            />
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <div>
                                            <div class="font-medium">{{ item.title_id || item.title_en }}</div>
                                            <div v-if="item.content_id || item.content_en" class="text-sm text-muted-foreground truncate max-w-xs">
                                                {{ item.content_id || item.content_en }}
                                            </div>
                                            <div class="flex items-center gap-1 mt-1">
                                                <Badge v-if="item.is_featured" variant="default" class="bg-yellow-500 text-xs">
                                                    Featured
                                                </Badge>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
                                        <Badge :variant="item.status === 'published' ? 'default' : 'outline'">
                                            {{ statuses[item.status] || item.status }}
                                        </Badge>
                                    </td>
                                    <td class="p-4">{{ item.view_count || 0 }}</td>
                                    <td class="p-4 text-muted-foreground">
                                        {{ new Date(item.updated_at).toLocaleDateString() }}
                                    </td>
                                    <td class="p-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <Button
                                                @click="openEditDialog(item)"
                                                size="sm"
                                                variant="outline"
                                            >
                                                <Edit class="h-4 w-4" />
                                            </Button>
                                            <Button
                                                @click="confirmDelete(item)"
                                                size="sm"
                                                variant="destructive"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div v-if="contents.links && contents.links.length > 3" class="flex items-center justify-between mt-6">
                        <div class="text-sm text-muted-foreground">
                            Showing {{ contents.from }}-{{ contents.to }} of {{ contents.total }} results
                        </div>
                        <div class="flex items-center gap-1">
                            <Button
                                v-for="link in contents.links"
                                :key="link.label"
                                variant="outline"
                                size="sm"
                                :disabled="!link.url"
                                :class="{ 'bg-primary text-primary-foreground': link.active }"
                                @click="link.url && router.visit(link.url)"
                                v-html="link.label"
                            >
                            </Button>
                        </div>
                    </div>
                    </CardContent>
                </Card>
            </div>
            </div>
        </div>

        <!-- Create/Edit Dialog -->
        <Dialog v-model:open="isDialogOpen">
            <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>
                        {{ selectedItem ? 'Edit Work Division' : 'Create Work Division' }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ selectedItem ? 'Update work division details' : 'Add a new work division to the system' }}
                    </DialogDescription>
                </DialogHeader>
                
                <div class="space-y-8">
                    <!-- Name Fields -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8" v-if="bilingualEnabled">
                        <!-- Indonesian Fields -->
                        <div class="space-y-6">
                            <h4 class="font-semibold text-lg border-b pb-2">Indonesian</h4>
                            <div class="space-y-2">
                                <Label for="title_id">Division Name *</Label>
                                <Input 
                                    id="title_id" 
                                    v-model="formData.title_id" 
                                    maxlength="25"
                                    required 
                                />
                                <p class="text-xs text-muted-foreground">
                                    {{ formData.title_id?.length || 0 }}/25 characters
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="content_id">Description</Label>
                                <Textarea 
                                    id="content_id" 
                                    v-model="formData.content_id" 
                                    rows="4" 
                                    maxlength="100"
                                    placeholder="Describe this work division..." 
                                />
                                <p class="text-xs text-muted-foreground">
                                    {{ formData.content_id?.length || 0 }}/100 characters
                                </p>
                            </div>
                        </div>
                        
                        <!-- English Fields -->
                        <div class="space-y-6">
                            <h4 class="font-semibold text-lg border-b pb-2">English</h4>
                            <div class="space-y-2">
                                <Label for="title_en">Division Name</Label>
                                <Input 
                                    id="title_en" 
                                    v-model="formData.title_en" 
                                    maxlength="25"
                                />
                                <p class="text-xs text-muted-foreground">
                                    {{ formData.title_en?.length || 0 }}/25 characters
                                </p>
                            </div>
                            <div class="space-y-2">
                                <Label for="content_en">Description</Label>
                                <Textarea 
                                    id="content_en" 
                                    v-model="formData.content_en" 
                                    rows="4" 
                                    maxlength="100"
                                    placeholder="Describe this work division..." 
                                />
                                <p class="text-xs text-muted-foreground">
                                    {{ formData.content_en?.length || 0 }}/100 characters
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Single Language Fields -->
                    <div v-else class="space-y-6">
                        <div class="space-y-2">
                            <Label for="title_single">Division Name *</Label>
                            <Input 
                                id="title_single" 
                                v-model="formData.title_id" 
                                maxlength="25"
                                required 
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ formData.title_id?.length || 0 }}/25 characters
                            </p>
                        </div>
                        <div class="space-y-2">
                            <Label for="content_single">Description</Label>
                            <Textarea 
                                id="content_single" 
                                v-model="formData.content_id" 
                                rows="4" 
                                maxlength="100"
                                placeholder="Describe this work division..." 
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ formData.content_id?.length || 0 }}/100 characters
                            </p>
                        </div>
                    </div>
                    
                    <!-- Settings & Image -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Settings -->
                        <div class="space-y-6">
                            <h4 class="font-semibold text-lg border-b pb-2">Settings</h4>
                            
                            <div class="space-y-2">
                                <Label for="status">Status</Label>
                                <Select v-model="formData.status">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select Status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem 
                                            v-for="(label, value) in statuses" 
                                            :key="value" 
                                            :value="value"
                                        >
                                            {{ label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="sort_order">Sort Order</Label>
                                <Input 
                                    id="sort_order" 
                                    v-model="formData.sort_order" 
                                    type="number" 
                                    min="0"
                                    placeholder="0"
                                />
                                <p class="text-sm text-gray-500 mt-1">Lower numbers appear first</p>
                            </div>
                            
                            <div class="space-y-3">
                                <Label>Options</Label>
                                <div class="flex flex-col space-y-2">
                                    <div class="flex items-center space-x-2">
                                        <input type="checkbox" v-model="formData.is_featured" id="is_featured" class="rounded border-gray-300" />
                                        <Label for="is_featured">Featured</Label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Upload -->
                        <div class="space-y-6">
                            <h4 class="font-semibold text-lg border-b pb-2">Featured Image</h4>
                            
                            <div class="space-y-2">
                                <Label>Featured Image</Label>
                                <div class="border-2 border-dashed border-input rounded-lg p-6">
                                    <div v-if="imagePreview" class="space-y-4">
                                        <div class="flex items-center justify-center">
                                            <img 
                                                :src="imagePreview.startsWith('data:') || imagePreview.startsWith('http') ? imagePreview : `/storage/${imagePreview}`" 
                                                alt="Image Preview" 
                                                class="max-h-32 max-w-48 object-contain rounded-lg border"
                                            />
                                        </div>
                                        <div class="flex justify-center gap-2">
                                            <Button size="sm" variant="outline" @click="removeImage">
                                                <X class="w-4 h-4 mr-2" />
                                                Remove
                                            </Button>
                                            <Button size="sm" variant="outline" @click="$refs.imageInput.click()">
                                                <Upload class="w-4 h-4 mr-2" />
                                                Change
                                            </Button>
                                        </div>
                                    </div>
                                    <div v-else class="text-center">
                                        <Image class="mx-auto h-12 w-12 text-muted-foreground" />
                                        <div class="mt-2">
                                            <Button variant="outline" @click="$refs.imageInput.click()">
                                                <Upload class="w-4 h-4 mr-2" />
                                                Upload Image
                                            </Button>
                                        </div>
                                        <p class="text-sm text-muted-foreground mt-2">PNG, JPG, SVG up to 5MB</p>
                                    </div>
                                    <input
                                        ref="imageInput"
                                        type="file"
                                        accept="image/*"
                                        class="hidden"
                                        @change="handleImageUpload"
                                    />
                                </div>
                                <p class="text-sm text-muted-foreground">
                                    Featured image for the work division (recommended size: 350x280px)
                                </p>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <DialogFooter>
                    <Button @click="closeDialog" variant="outline">Cancel</Button>
                    <Button @click="submitForm" class="gap-2">
                        <Save class="h-4 w-4" />
                        {{ selectedItem ? 'Update' : 'Create' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Confirm Delete Dialog -->
        <ConfirmDialog
            v-model:open="isConfirmDialogOpen"
            title="Delete Work Division"
            :message="`Are you sure you want to delete '${itemToDelete?.title_id || itemToDelete?.title_en}'? This action cannot be undone.`"
            confirm-text="Delete"
            cancel-text="Cancel"
            variant="destructive"
            @confirm="deleteItem"
        />
    </AppLayout>
</template>