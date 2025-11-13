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
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { toast } from '@/components/ui/toast';
import { Search, Plus, Filter, Edit, Trash2, ExternalLink, Upload, Image, X, Save, Star } from 'lucide-vue-next';

interface Partner {
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
        data?: Partner[];
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
    { title: 'Our Partners', href: '/content/partners' },
];

// Local state for data and loading
const partnersData = ref(props.contents);
const loading = ref(false);

// Local state for filters
const filters = reactive({
    search: '',
    category: '',
    status: '',
    featured: false,
    per_page: 12,
    page: 1
});

// Modal state
const dialogOpen = ref(false);
const editingPartner = ref<Partner | null>(null);
const confirmDialog = ref({
    open: false,
    partnerId: 0,
    loading: false
});

// Form state
const form = reactive({
    type: 'partner',
    category: 'other',
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
const getLocalizedTitle = (partner: Partner) => {
    return props.bilingualEnabled ? (partner.title_en || partner.title_id) : partner.title_id;
};

// Get partner image URL
const getPartnerImageUrl = (partner: Partner) => {
    if (!partner.featured_image) return null;
    if (partner.featured_image.startsWith('http') || partner.featured_image.startsWith('/storage/')) {
        return partner.featured_image;
    }
    return `/storage/${partner.featured_image}`;
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

// Get CSRF token
const getCsrfToken = () => {
    const tokenFromMeta = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (tokenFromMeta) return tokenFromMeta;
    
    const tokenFromCookie = document.cookie
        .split('; ')
        .find(row => row.startsWith('XSRF-TOKEN='))
        ?.split('=')[1];
    
    if (tokenFromCookie) {
        try {
            return decodeURIComponent(tokenFromCookie);
        } catch (e) {
            // Failed to decode CSRF token from cookie
        }
    }
    return '';
};

// Load partners with router.get (Inertia way)
const loadPartners = () => {
    const params = {
        page: filters.page,
        preserveState: true,
        preserveScroll: true,
        only: ['contents'],
        onStart: () => loading.value = true,
        onFinish: () => loading.value = false,
        onSuccess: (page) => {
            partnersData.value = page.props.contents;
        }
    };
    
    if (filters.search) params.search = filters.search;
    if (filters.category) params.category = filters.category;
    if (filters.status) params.status = filters.status;
    if (filters.featured) params.featured = 1;
    
    console.log('Loading partners with params:', params);
    
    router.get('/content/partners', params);
};

// Filter and search
const applyFilters = () => {
    filters.page = 1;
    loadPartners();
};

const clearFilters = () => {
    filters.search = '';
    filters.category = '';
    filters.status = '';
    filters.featured = false;
    filters.page = 1;
    loadPartners();
};

// Modal functions  
const openCreateDialog = () => {
    resetForm();
    editingPartner.value = null;
    dialogOpen.value = true;
};

const openEditDialog = (partner: Partner) => {
    resetForm();
    editingPartner.value = partner;
    
    form.type = partner.type;
    form.category = partner.category;
    form.title_id = partner.title_id;
    form.title_en = partner.title_en || '';
    form.excerpt_id = partner.excerpt_id;
    form.excerpt_en = partner.excerpt_en || '';
    form.featured_image = partner.featured_image;
    form.source_url = partner.source_url;
    form.status = partner.status;
    form.is_published = partner.is_published;
    form.is_featured = partner.is_featured;
    form.sort_order = partner.sort_order;
    
    // For existing partners, show the image URL as preview
    if (partner.featured_image && !partner.featured_image.startsWith('data:')) {
        imagePreview.value = `/storage/${partner.featured_image}`;
    } else {
        imagePreview.value = partner.featured_image;
    }
    dialogOpen.value = true;
};

const resetForm = () => {
    form.type = 'partner';
    form.category = 'other';
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


// Submit form
const handleSubmit = async () => {
    form.loading = true;
    
    try {
        const url = editingPartner.value 
            ? route('content.partners.update', editingPartner.value.id)
            : route('content.partners.store');
        
        const method = editingPartner.value ? 'PUT' : 'POST';
        
        const formData = new FormData();
        
        // Add form fields (excluding loading and featured_image)
        Object.keys(form).forEach(key => {
            if (key !== 'loading' && key !== 'featured_image') {
                // Skip empty URLs to avoid validation errors
                if (key === 'source_url' && !form[key]) {
                    return;
                }
                formData.append(key, form[key]);
            }
        });
        
        // Add file if exists (new upload)
        if (imageFile.value) {
            formData.append('featured_image', imageFile.value);
        } else if (editingPartner.value && editingPartner.value.featured_image && !editingPartner.value.featured_image.startsWith('data:')) {
            // For existing partners with existing image path, include the existing path
            formData.append('featured_image', editingPartner.value.featured_image);
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
            loadPartners();
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

// Delete partner
const confirmDelete = (partnerId: number) => {
    confirmDialog.value = {
        open: true,
        partnerId: partnerId,
        loading: false
    };
};

const handleDelete = async () => {
    confirmDialog.value.loading = true;
    
    try {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        
        const response = await fetch(route('content.partners.destroy', confirmDialog.value.partnerId), {
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
            confirmDialog.value.open = false;
            loadPartners();
        } else {
            toast({
                title: 'Error',
                description: data.message || 'Failed to delete partner',
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

// Toggle featured status
const toggleFeatured = async (partner: Partner) => {
    try {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('is_featured', (!partner.is_featured).toString());
        
        const response = await fetch(route('content.partners.update', partner.id), {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': getCsrfToken(),
            },
            credentials: 'include',
            body: formData,
        });
        
        if (response.ok) {
            toast({
                title: 'Success',
                description: `Partner ${!partner.is_featured ? 'featured' : 'unfeatured'} successfully`,
                variant: 'success'
            });
            loadPartners();
        }
    } catch (error) {
        toast({
            title: 'Error',
            description: 'Failed to update partner status',
            variant: 'error'
        });
    }
};

const hasFilters = computed(() => {
    return filters.search || filters.category || filters.status || filters.featured;
});

// Pagination function
const goToPage = (page: number) => {
    filters.page = page;
    loadPartners();
};

// Initialize data
onMounted(() => {
    // Use initial data from props
    partnersData.value = props.contents;
});

</script>

<template>
    <Head title="Our Partners" />
    
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Our Partners</h1>
                        <p class="text-muted-foreground">
                            Manage company partners and strategic alliances
                        </p>
                    </div>
                    <Button @click="openCreateDialog">
                        <Plus class="w-4 h-4 mr-2" />
                        Add Partner
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
                            v-model="filters.search"
                            placeholder="Search partners..."
                            @keyup.enter="applyFilters"
                        />
                    </div>
                    
                    <div class="space-y-2">
                        <Label>Category</Label>
                        <Select v-model="filters.category">
                            <SelectTrigger>
                                <SelectValue placeholder="All Categories" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">All Categories</SelectItem>
                                <SelectItem v-for="(label, value) in categories" :key="value" :value="value">
                                    {{ label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    
                    <div class="space-y-2">
                        <Label>Status</Label>
                        <Select v-model="filters.status">
                            <SelectTrigger>
                                <SelectValue placeholder="All Statuses" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="">All Statuses</SelectItem>
                                <SelectItem v-for="(label, value) in statuses" :key="value" :value="value">
                                    {{ label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>
                    
                    <div class="space-y-2">
                        <Label>Featured Only</Label>
                        <div class="flex items-center space-x-2 h-10">
                            <Switch 
                                :checked="filters.featured" 
                                @update:checked="filters.featured = $event" 
                            />
                            <span class="text-sm">Show featured partners only</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2 mt-4">
                    <Button @click="applyFilters" size="sm">
                        <Search class="w-4 h-4 mr-2" />
                        Apply Filters
                    </Button>
                    <Button @click="clearFilters" variant="outline" size="sm">
                        Clear Filters
                    </Button>
                </div>
                </CardContent>
                    </Card>
                </div>

        <!-- Partners Grid -->
        <div v-if="loading" class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <PlaceholderPattern />
            <Card class="relative z-10 border-0 shadow-none bg-transparent">
                <CardContent class="flex justify-center py-8">
                    <div class="text-muted-foreground">Loading partners...</div>
                </CardContent>
            </Card>
        </div>
        <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div 
                v-for="partner in partnersData.data"
                :key="partner.id"
                class="group relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border"
            >
                <PlaceholderPattern />
                <Card class="relative z-10 border-0 shadow-none bg-transparent h-full transition-all duration-200 hover:shadow-lg">
                    <CardContent class="p-6">
                        <!-- Partner Logo -->
                        <div class="aspect-video bg-gray-50 dark:bg-gray-800 rounded-lg mb-4 flex items-center justify-center overflow-hidden p-4">
                            <img 
                                v-if="getPartnerImageUrl(partner)" 
                                :src="getPartnerImageUrl(partner)" 
                                :alt="getLocalizedTitle(partner)"
                                class="max-w-full max-h-full object-contain"
                            />
                            <div v-else class="text-gray-400 text-sm text-center">
                                <div class="text-2xl mb-2">🏢</div>
                                <div>No Logo</div>
                            </div>
                        </div>
                        
                        <!-- Partner Info -->
                        <div class="space-y-3">
                            <h3 class="font-semibold text-base truncate" :title="getLocalizedTitle(partner)">
                                {{ getLocalizedTitle(partner) }}
                            </h3>
                            
                            <div class="flex flex-wrap items-center gap-2">
                                <Badge :variant="getCategoryVariant(partner.category)" class="text-xs">
                                    {{ categories[partner.category] || partner.category }}
                                </Badge>
                                <Badge :variant="getStatusVariant(partner.status)" class="text-xs">
                                    {{ statuses[partner.status] || partner.status }}
                                </Badge>
                                <Badge v-if="partner.is_featured" variant="default" class="text-xs">
                                    ⭐ Featured
                                </Badge>
                            </div>
                            
                            <!-- Partner URL -->
                            <div v-if="partner.source_url" class="text-xs text-gray-500 truncate" :title="partner.source_url">
                                🌐 {{ partner.source_url }}
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <div class="flex gap-1 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-1">
                                <Button 
                                    v-if="partner.source_url" 
                                    :href="partner.source_url" 
                                    target="_blank"
                                    variant="ghost" 
                                    size="sm" 
                                    class="h-8 w-8 p-0 hover:bg-blue-50 dark:hover:bg-blue-900"
                                >
                                    <ExternalLink class="w-3 h-3" />
                                </Button>
                                <Button 
                                    @click="openEditDialog(partner)"
                                    variant="ghost" 
                                    size="sm" 
                                    class="h-8 w-8 p-0 hover:bg-gray-50 dark:hover:bg-gray-700"
                                >
                                    <Edit class="w-3 h-3" />
                                </Button>
                                <Button 
                                    @click="confirmDelete(partner.id)" 
                                    variant="ghost" 
                                    size="sm" 
                                    class="h-8 w-8 p-0 hover:bg-red-50 dark:hover:bg-red-900 text-red-600 dark:text-red-400"
                                >
                                    <Trash2 class="w-3 h-3" />
                                </Button>
                            </div>
                        </div>

                        <!-- Featured Toggle -->
                        <div class="absolute bottom-3 right-3">
                            <Button 
                                @click="toggleFeatured(partner)"
                                :variant="partner.is_featured ? 'default' : 'outline'"
                                size="sm" 
                                class="h-7 text-xs px-3 shadow-sm"
                            >
                                {{ partner.is_featured ? '⭐ Featured' : 'Feature' }}
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!partnersData.data?.length && !loading" class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <PlaceholderPattern />
            <Card class="relative z-10 border-0 shadow-none bg-transparent text-center py-16">
                <CardContent class="py-8">
                    <div class="mx-auto max-w-md">
                        <div class="text-gray-400 text-6xl mb-6">🤝</div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">No partners found</h3>
                        <p class="text-gray-500 mb-8 text-base">
                            {{ hasFilters ? 'No partners match your current filters.' : 'Get started by adding your first partner.' }}
                        </p>
                        <div class="flex gap-3 justify-center">
                            <Button v-if="hasFilters" @click="clearFilters" variant="outline">
                                <Filter class="w-4 h-4 mr-2" />
                                Clear Filters
                            </Button>
                            <Button @click="openCreateDialog">
                                <Plus class="w-4 h-4 mr-2" />
                                Add First Partner
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Pagination -->
        <div v-if="partnersData.links && partnersData.data?.length" class="mt-8 relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
            <PlaceholderPattern />
            <Card class="relative z-10 border-0 shadow-none bg-transparent">
                <CardContent class="py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            Showing <span class="font-medium">{{ partnersData.from || 0 }}</span> to <span class="font-medium">{{ partnersData.to || 0 }}</span> of <span class="font-medium">{{ partnersData.total || 0 }}</span> partners
                        </div>
                        <div class="flex gap-1">
                            <template v-for="link in partnersData.links" :key="link.label">
                                <Button 
                                    v-if="link.url"
                                    @click="goToPage(new URL(link.url).searchParams.get('page'))"
                                    variant="outline"
                                    size="sm"
                                    :disabled="loading"
                                    :class="{ 'bg-primary text-primary-foreground': link.active }"
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
        </div>

        <!-- Modal Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>
                        {{ editingPartner ? 'Edit' : 'Add' }} Partner
                    </DialogTitle>
                    <DialogDescription>
                        {{ editingPartner ? 'Update partner information' : 'Add a new strategic partner' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="handleSubmit" class="space-y-6">
                    <!-- Partner Logo -->
                    <div class="space-y-2">
                        <Label>Partner Logo *</Label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-6 transition-colors hover:border-gray-400 dark:hover:border-gray-500">
                            <div v-if="imagePreview" class="space-y-4">
                                <div class="relative inline-block">
                                    <img 
                                        :src="imagePreview" 
                                        alt="Partner Logo Preview" 
                                        class="w-32 h-32 object-contain border rounded-lg bg-white"
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
                                <Upload class="mx-auto h-12 w-12 text-gray-400 mb-3" />
                                <Button variant="outline" @click="$refs.imageInput.click()">
                                    <Upload class="w-4 h-4 mr-2" />
                                    Upload Partner Logo
                                </Button>
                                <p class="text-sm text-gray-500 mt-2">
                                    PNG, JPG, GIF up to 2MB
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
                    </div>

                    <!-- Partner Name -->
                    <div class="grid lg:grid-cols-2 gap-6" v-if="bilingualEnabled">
                        <div class="space-y-2">
                            <Label for="title_id">Partner Name (Indonesian) *</Label>
                            <Input
                                id="title_id"
                                v-model="form.title_id"
                                placeholder="Enter partner name in Indonesian"
                                :disabled="form.loading"
                                required
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="title_en">Partner Name (English) *</Label>
                            <Input
                                id="title_en"
                                v-model="form.title_en"
                                placeholder="Enter partner name in English"
                                :disabled="form.loading"
                                required
                            />
                        </div>
                    </div>
                    <div v-else class="space-y-2">
                        <Label for="title_id">Partner Name *</Label>
                        <Input
                            id="title_id"
                            v-model="form.title_id"
                            placeholder="Enter partner name"
                            :disabled="form.loading"
                            required
                        />
                    </div>

                    <!-- Category & Status -->
                    <div class="grid lg:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="category">Category</Label>
                            <Select v-model="form.category">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select category" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(label, value) in categories" :key="value" :value="value">
                                        {{ label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="status">Status</Label>
                            <Select v-model="form.status">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(label, value) in statuses" :key="value" :value="value">
                                        {{ label }}
                                    </SelectItem>
                                </SelectContent>
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
                                placeholder="Brief description about the partnership"
                                :rows="3"
                                :disabled="form.loading"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="excerpt_en">Description (English)</Label>
                            <Textarea
                                id="excerpt_en"
                                v-model="form.excerpt_en"
                                placeholder="Brief description about the partnership"
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
                            placeholder="Brief description about the partnership"
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

                    <!-- Settings -->
                    <div class="space-y-4 border-t pt-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <Label class="text-base font-medium">Published</Label>
                                <p class="text-sm text-gray-500">Partner will be visible on the website</p>
                            </div>
                            <Switch 
                                :checked="form.is_published" 
                                @update:checked="form.is_published = $event"
                                :disabled="form.loading"
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
                                :disabled="form.loading"
                            />
                        </div>
                    </div>
                </form>

                <DialogFooter class="mt-6">
                    <Button type="button" variant="outline" @click="dialogOpen = false" :disabled="form.loading">
                        Cancel
                    </Button>
                    <Button type="button" @click="handleSubmit" :disabled="form.loading">
                        <Save v-if="!form.loading" class="w-4 h-4 mr-2" />
                        {{ form.loading ? 'Saving...' : (editingPartner ? 'Update Partner' : 'Create Partner') }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

                <!-- Confirm Delete Dialog -->
                <ConfirmDialog
                    v-model:open="confirmDialog.open"
                    title="Delete Partner"
                    description="Are you sure you want to delete this partner? This action cannot be undone."
                    :loading="confirmDialog.loading"
                    @confirm="handleDelete"
                />
            </div>
        </div>
    </AppLayout>
</template>