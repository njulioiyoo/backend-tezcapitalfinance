<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive, computed } from 'vue';
import { route } from 'ziggy-js';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import { Textarea } from '@/components/ui/textarea';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import { toast } from '@/components/ui/toast';
import { Search, Plus, Filter, Edit, Trash2, Save } from 'lucide-vue-next';

interface Department {
    id: number;
    name_id: string;
    name_en: string;
    slug: string;
    description_id: string;
    description_en: string;
    is_active: boolean;
    sort_order: number;
    created_at: string;
    updated_at: string;
}

interface PaginatedData {
    data?: Department[];
    links?: any[];
    meta?: any;
    current_page?: number;
    per_page?: number;
    total?: number;
}

// State
const departments = ref<PaginatedData>({});
const loading = ref(false);
const search = ref('');
const selectedStatus = ref('');

// Modal state
const dialogOpen = ref(false);
const editingDepartment = ref<Department | null>(null);
const confirmDialog = ref({
    open: false,
    departmentId: 0,
    loading: false
});

// Form state
const form = reactive({
    name_id: '',
    name_en: '',
    slug: '',
    description_id: '',
    description_en: '',
    is_active: true,
    sort_order: 0,
    loading: false
});

// Fetch departments data
const fetchDepartments = async () => {
    loading.value = true;
    try {
        const params = new URLSearchParams();
        if (search.value) params.append('search', search.value);
        if (selectedStatus.value) params.append('is_active', selectedStatus.value);
        
        const response = await fetch(route('master.departments.data') + '?' + params.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include',
        });
        
        const data = await response.json();
        departments.value = data;
    } catch (error) {
        toast({
            title: 'Error',
            description: 'Failed to fetch departments',
            variant: 'error'
        });
    } finally {
        loading.value = false;
    }
};

// Initialize
fetchDepartments();

// Filter and search
const applyFilters = () => {
    fetchDepartments();
};

const clearFilters = () => {
    search.value = '';
    selectedStatus.value = '';
    fetchDepartments();
};

// Modal functions  
const openCreateDialog = () => {
    resetForm();
    editingDepartment.value = null;
    dialogOpen.value = true;
};

const openEditDialog = (department: Department) => {
    resetForm();
    editingDepartment.value = department;
    
    form.name_id = department.name_id;
    form.name_en = department.name_en || '';
    form.slug = department.slug;
    form.description_id = department.description_id || '';
    form.description_en = department.description_en || '';
    form.is_active = department.is_active;
    form.sort_order = department.sort_order;
    
    dialogOpen.value = true;
};

const resetForm = () => {
    form.name_id = '';
    form.name_en = '';
    form.slug = '';
    form.description_id = '';
    form.description_en = '';
    form.is_active = true;
    form.sort_order = 0;
    form.loading = false;
};

// Get CSRF token
import { useCsrfToken } from '@/composables/useCsrfToken';
const { getCsrfToken, refreshCsrfToken } = useCsrfToken();

// Helper function to make requests with CSRF token retry
const makeRequest = async (url: string, options: RequestInit, retryOnCsrf = true): Promise<Response> => {
    const csrfToken = getCsrfToken();
    if (!csrfToken) {
        throw new Error('CSRF token not found. Please refresh the page.');
    }
    
    const requestOptions = {
        ...options,
        headers: {
            ...options.headers,
            'X-CSRF-TOKEN': csrfToken,
        },
    };
    
    let response = await fetch(url, requestOptions);
    
    // If CSRF token mismatch and retry is enabled, try to refresh token and retry once
    if (response.status === 419 && retryOnCsrf) {
        try {
            const newToken = await refreshCsrfToken();
            if (newToken) {
                const retryOptions = {
                    ...requestOptions,
                    headers: {
                        ...requestOptions.headers,
                        'X-CSRF-TOKEN': newToken,
                    },
                };
                response = await fetch(url, retryOptions);
            }
        } catch (refreshError) {
            console.warn('Failed to refresh CSRF token:', refreshError);
        }
    }
    
    return response;
};

// Submit form
const handleSubmit = async () => {
    form.loading = true;
    
    try {
        const url = editingDepartment.value 
            ? route('master.departments.update', editingDepartment.value.id)
            : route('master.departments.store');
        
        const method = editingDepartment.value ? 'PUT' : 'POST';
        
        const formData = new FormData();
        
        // Add form fields with proper boolean handling
        Object.keys(form).forEach(key => {
            if (key !== 'loading') {
                // Convert boolean to 1/0 for Laravel
                if (typeof form[key] === 'boolean') {
                    formData.append(key, form[key] ? '1' : '0');
                } else {
                    formData.append(key, form[key]);
                }
            }
        });
        
        // Add method for Laravel method spoofing
        if (method === 'PUT') {
            formData.append('_method', 'PUT');
        }
        
        const response = await makeRequest(url, {
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
                description: data.message,
                variant: 'success'
            });
            dialogOpen.value = false;
            fetchDepartments();
        } else if (response.status === 419) {
            toast({
                title: 'Session Expired',
                description: 'Your session has expired. Please refresh the page and try again.',
                variant: 'error'
            });
            // Optionally refresh the page automatically after a delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
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

// Delete department
const confirmDelete = (departmentId: number) => {
    confirmDialog.value = {
        open: true,
        departmentId: departmentId,
        loading: false
    };
};

const handleDelete = async () => {
    confirmDialog.value.loading = true;
    
    try {
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        
        const response = await makeRequest(route('master.departments.destroy', confirmDialog.value.departmentId), {
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
                description: data.message,
                variant: 'success'
            });
            confirmDialog.value.open = false;
            fetchDepartments();
        } else if (response.status === 419) {
            toast({
                title: 'Session Expired',
                description: 'Your session has expired. Please refresh the page and try again.',
                variant: 'error'
            });
            confirmDialog.value.open = false;
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            toast({
                title: 'Error',
                description: data.message || 'Failed to delete department',
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

const hasFilters = computed(() => {
    return search.value || selectedStatus.value;
});

// Handle pagination click
const handlePaginationClick = (url: string) => {
    if (!url) return;
    
    // Extract page number from URL
    const urlObj = new URL(url);
    const page = urlObj.searchParams.get('page');
    
    if (page) {
        // Update search params and refetch
        const params = new URLSearchParams();
        if (search.value) params.append('search', search.value);
        if (selectedStatus.value) params.append('is_active', selectedStatus.value);
        params.append('page', page);
        
        // Fetch with new page
        loading.value = true;
        fetch(route('master.departments.data') + '?' + params.toString(), {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'include',
        })
        .then(response => response.json())
        .then(data => {
            departments.value = data;
        })
        .catch(error => {
            toast({
                title: 'Error',
                description: 'Failed to fetch departments',
                variant: 'error'
            });
        })
        .finally(() => {
            loading.value = false;
        });
    }
};

</script>

<template>
    <Head title="Departments" />
    
    <AppLayout>
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Departments</h1>
                        <p class="text-muted-foreground">
                            Manage master data for departments
                        </p>
                    </div>
                    <Button @click="openCreateDialog">
                        <Plus class="w-4 h-4 mr-2" />
                        Add Department
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
                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="space-y-2">
                                <Label>Search</Label>
                                <Input
                                    v-model="search"
                                    placeholder="Search departments..."
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                            
                            <div class="space-y-2">
                                <Label>Status</Label>
                                <select v-model="selectedStatus" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                    <option value="">All Status</option>
                                    <option value="true">Active</option>
                                    <option value="false">Inactive</option>
                                </select>
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

                <!-- Departments Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <Card v-for="department in departments.data" :key="department.id" class="group hover:shadow-lg transition-shadow">
                        <CardHeader class="pb-3">
                            <div class="flex items-start justify-between">
                                <div class="space-y-1 flex-1">
                                    <div class="flex items-center gap-2">
                                        <Badge :variant="department.is_active ? 'default' : 'secondary'">
                                            {{ department.is_active ? 'Active' : 'Inactive' }}
                                        </Badge>
                                    </div>
                                    <CardTitle class="line-clamp-2">{{ department.name_id }}</CardTitle>
                                    <p v-if="department.name_en" class="text-sm text-muted-foreground line-clamp-1">
                                        {{ department.name_en }}
                                    </p>
                                </div>
                                <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <Button @click="openEditDialog(department)" size="sm" variant="outline">
                                        <Edit class="h-4 w-4" />
                                    </Button>
                                    <Button @click="confirmDelete(department.id)" size="sm" variant="outline" class="text-red-600 hover:text-red-700">
                                        <Trash2 class="h-4 w-4" />
                                    </Button>
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <!-- Slug -->
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-muted-foreground">Slug:</span>
                                    <code class="text-xs bg-muted px-2 py-1 rounded">{{ department.slug }}</code>
                                </div>
                                
                                <!-- Description -->
                                <p v-if="department.description_id" class="text-sm text-muted-foreground line-clamp-2">
                                    {{ department.description_id }}
                                </p>
                                
                                <!-- Sort Order -->
                                <div class="flex items-center justify-between pt-2 border-t">
                                    <div class="text-xs text-muted-foreground">
                                        Sort Order: <span class="font-medium text-foreground">{{ department.sort_order }}</span>
                                    </div>
                                    <div class="text-xs text-muted-foreground">
                                        ID: {{ department.id }}
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                    
                    <!-- Empty State -->
                    <div v-if="!departments.data?.length && !loading" class="col-span-full">
                        <Card class="text-center py-12">
                            <CardContent>
                                <div class="text-6xl mb-4">📋</div>
                                <h3 class="text-lg font-medium mb-2">No departments found</h3>
                                <p class="text-muted-foreground mb-4">Get started by creating your first department.</p>
                                <Button @click="openCreateDialog">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Add Department
                                </Button>
                            </CardContent>
                        </Card>
                    </div>
                    
                    <!-- Loading State -->
                    <div v-if="loading" class="col-span-full">
                        <Card class="text-center py-12">
                            <CardContent>
                                <div class="text-muted-foreground">Loading...</div>
                            </CardContent>
                        </Card>
                    </div>
                </div>

                <!-- Pagination -->
                <Card v-if="departments.links && departments.data?.length">
                    <CardContent class="py-4">
                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span class="font-medium">{{ departments.meta?.from || 0 }}</span> to <span class="font-medium">{{ departments.meta?.to || 0 }}</span> of <span class="font-medium">{{ departments.meta?.total || 0 }}</span> departments
                            </div>
                            <nav v-if="departments.links && departments.links.length > 3" class="flex items-center gap-1">
                                <template v-for="(link, index) in departments.links" :key="index">
                                    <button
                                        v-if="link.url"
                                        @click="handlePaginationClick(link.url)"
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
                    </CardContent>
                </Card>

                <!-- Modal Dialog -->
                <Dialog v-model:open="dialogOpen">
                    <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                        <DialogHeader>
                            <DialogTitle>
                                {{ editingDepartment ? 'Edit' : 'Add' }} Department
                            </DialogTitle>
                            <DialogDescription>
                                {{ editingDepartment ? 'Update department information' : 'Add a new department' }}
                            </DialogDescription>
                        </DialogHeader>

                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <!-- Department Name -->
                            <div class="grid lg:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="name_id">Name (Indonesian) *</Label>
                                    <Input
                                        id="name_id"
                                        v-model="form.name_id"
                                        placeholder="Enter department name in Indonesian"
                                        :disabled="form.loading"
                                        required
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label for="name_en">Name (English)</Label>
                                    <Input
                                        id="name_en"
                                        v-model="form.name_en"
                                        placeholder="Enter department name in English"
                                        :disabled="form.loading"
                                    />
                                </div>
                            </div>

                            <!-- Slug -->
                            <div class="space-y-2">
                                <Label for="slug">Slug</Label>
                                <Input
                                    id="slug"
                                    v-model="form.slug"
                                    placeholder="department-slug (leave empty to auto-generate)"
                                    :disabled="form.loading"
                                />
                                <p class="text-sm text-gray-500">URL-friendly identifier (auto-generated if empty)</p>
                            </div>

                            <!-- Description -->
                            <div class="grid lg:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <Label for="description_id">Description (Indonesian)</Label>
                                    <Textarea
                                        id="description_id"
                                        v-model="form.description_id"
                                        placeholder="Department description"
                                        :rows="3"
                                        :disabled="form.loading"
                                    />
                                </div>
                                <div class="space-y-2">
                                    <Label for="description_en">Description (English)</Label>
                                    <Textarea
                                        id="description_en"
                                        v-model="form.description_en"
                                        placeholder="Department description"
                                        :rows="3"
                                        :disabled="form.loading"
                                    />
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
                                    :disabled="form.loading"
                                />
                                <p class="text-sm text-gray-500">Lower numbers appear first</p>
                            </div>

                            <!-- Settings -->
                            <div class="space-y-4 border-t pt-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <Label class="text-base font-medium">Active Status</Label>
                                        <p class="text-sm text-gray-500">Department will be available for selection</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm text-muted-foreground">
                                            {{ form.is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        <button
                                            type="button"
                                            @click.prevent="form.is_active = !form.is_active"
                                            :disabled="form.loading"
                                            class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                            :class="form.is_active ? 'bg-primary' : 'bg-gray-200'"
                                        >
                                            <span
                                                class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform"
                                                :class="form.is_active ? 'translate-x-6' : 'translate-x-1'"
                                            />
                                        </button>
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
                                {{ form.loading ? 'Saving...' : (editingDepartment ? 'Update Department' : 'Create Department') }}
                            </Button>
                        </DialogFooter>
                    </DialogContent>
                </Dialog>

                <!-- Confirm Delete Dialog -->
                <ConfirmDialog
                    v-model:open="confirmDialog.open"
                    title="Delete Department"
                    description="Are you sure you want to delete this department? This action cannot be undone."
                    :loading="confirmDialog.loading"
                    @confirm="handleDelete"
                />
            </div>
        </div>
    </AppLayout>
</template>

