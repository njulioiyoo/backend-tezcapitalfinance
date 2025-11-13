<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Briefcase, RefreshCw, Plus, Edit, Trash2, Search, Filter } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import RichTextEditor from '@/components/ui/RichTextEditor.vue';
import axios from 'axios';

interface Career {
    id: number;
    title_id: string;
    title_en: string;
    department_id?: string;
    department_en?: string;
    location_id: string;
    location_en: string;
    content_id: string;
    content_en: string;
    requirements_id: string[];
    requirements_en: string[];
    benefits_id: string[];
    benefits_en: string[];
    tags: string[];
    status: string;
    start_date: string;
    end_date: string;
    is_published: boolean;
    created_at: string;
}

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Content', href: '/content' },
    { label: 'Careers Management', href: '/content/careers' }
];

const careers = ref<{ data: Career[], links: any[], from?: number, to?: number, total?: number }>({ data: [], links: [], from: 0, to: 0, total: 0 });
const allDepartments = ref<{id: number, name_id: string, name_en: string}[]>([]);
const isLoading = ref(false);
const dialogOpen = ref(false);
const editingCareer = ref<Career | null>(null);
const confirmDialog = ref({
    open: false,
    itemId: 0,
    loading: false
});

const filters = reactive({
    search: '',
    department: '',
    status: '',
    location: '',
    per_page: 10,
    page: 1
});

const careerForm = reactive({
    id: null,
    title_id: '',
    title_en: '',
    department_id: '',
    department_en: '',
    content_id: '',
    content_en: '',
    requirements_id: '',
    requirements_en: '',
    benefits_id: '',
    benefits_en: '',
    location_id: '',
    location_en: '',
    tags: [],
    start_date: '',
    end_date: '',
    status: 'published',
    is_published: true
});

const loadCareers = async (params = {}) => {
    isLoading.value = true;
    try {
        const response = await axios.get('/content/careers/data', { 
            params: { ...filters, ...params }
        });
        careers.value = response.data;
    } catch (error) {
        console.error('Error loading careers:', error);
    } finally {
        isLoading.value = false;
    }
};

const loadAllDepartments = async () => {
    try {
        const response = await axios.get('/master/departments/data');
        allDepartments.value = response.data.data || response.data || [];
    } catch (error) {
        console.error('Error loading departments:', error);
    }
};

const openDialog = (career: Career | null = null) => {
    if (career) {
        editingCareer.value = career;
        Object.assign(careerForm, {
            ...career,
            requirements_id: career.requirements_id || '',
            requirements_en: career.requirements_en || '',
            benefits_id: career.benefits_id || '',
            benefits_en: career.benefits_en || '',
        });
    } else {
        resetForm();
        editingCareer.value = null;
    }
    dialogOpen.value = true;
};

const closeDialog = () => {
    dialogOpen.value = false;
    resetForm();
    editingCareer.value = null;
};

const resetForm = () => {
    Object.assign(careerForm, {
        id: null,
        title_id: '',
        title_en: '',
        department_id: '',
        department_en: '',
        content_id: '',
        content_en: '',
        requirements_id: '',
        requirements_en: '',
        benefits_id: '',
        benefits_en: '',
        location_id: '',
        location_en: '',
        tags: [],
        start_date: '',
        end_date: '',
        status: 'published',
        is_published: true
    });
};

import { toast } from '@/components/ui/toast';

const saveCareer = async () => {
    try {
        const careerData = {
            ...careerForm,
            type: 'career',
            // Keep as HTML string for rich text content
            requirements_id: careerForm.requirements_id,
            requirements_en: careerForm.requirements_en,
            benefits_id: careerForm.benefits_id,
            benefits_en: careerForm.benefits_en
        };

        if (editingCareer.value) {
            await axios.put(`/content/careers/${editingCareer.value.id}`, careerData);
            toast({ title: 'Success', description: 'Career updated successfully.', variant: 'success' });
        } else {
            await axios.post('/content/careers', careerData);
            toast({ title: 'Success', description: 'Career created successfully.', variant: 'success' });
        }
        
        closeDialog();
        loadCareers();
        loadAllDepartments();
    } catch (error) {
        if (error.response && error.response.data && error.response.data.errors) {
            const firstError = Object.values(error.response.data.errors)[0][0];
            toast({ title: 'Validation Error', description: firstError, variant: 'error' });
        } else {
            toast({ title: 'Error', description: 'An unexpected error occurred.', variant: 'error' });
        }
        console.error('Error saving career:', error);
    }
};

const deleteCareer = async (careerId: number) => {
    confirmDialog.value.loading = true;
    try {
        await axios.delete(`/content/careers/${careerId}`);
        confirmDialog.value.open = false;
        loadCareers();
        loadAllDepartments();
    } catch (error) {
        console.error('Error deleting career:', error);
    } finally {
        confirmDialog.value.loading = false;
    }
};

const openDeleteConfirm = (careerId: number) => {
    confirmDialog.value.itemId = careerId;
    confirmDialog.value.open = true;
};

watch(() => [filters.search, filters.department, filters.status, filters.location], 
    () => {
        filters.page = 1;
        loadCareers();
    },
    { deep: true }
);

onMounted(() => {
    loadCareers();
    loadAllDepartments();
});
</script>

<template>
    <Head title="Careers Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Briefcase class="w-8 h-8 text-primary" />
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Careers Management</h1>
                            <p class="text-muted-foreground">
                                Manage job postings, career opportunities, and recruitment content
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Button @click="openDialog()">
                            <Plus class="w-4 h-4 mr-2" />
                            Add New Career
                        </Button>
                        <Button 
                            @click="loadCareers" 
                            variant="outline" 
                            size="sm"
                            :disabled="isLoading"
                        >
                            <RefreshCw :class="{ 'animate-spin': isLoading }" class="w-4 h-4 mr-2" />
                            Refresh
                        </Button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent">
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Filter class="w-4 h-4" />
                            Filters
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="space-y-2">
                                <Label>Search</Label>
                                <Input
                                    v-model="filters.search"
                                    placeholder="Search careers..."
                                    class="w-full"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Department</Label>
                                <Select v-model="filters.department">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Departments" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Departments</SelectItem>
                                        <SelectItem 
                                            v-for="department in allDepartments" 
                                            :key="department.id" 
                                            :value="department.name_id"
                                        >
                                            {{ department.name_id }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Status</Label>
                                <Select v-model="filters.status">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Status" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Status</SelectItem>
                                        <SelectItem value="published">Published</SelectItem>
                                        <SelectItem value="draft">Draft</SelectItem>
                                        <SelectItem value="archived">Archived</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div class="space-y-2">
                                <Label>Location</Label>
                                <Select v-model="filters.location">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Locations" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Locations</SelectItem>
                                        <SelectItem value="Jakarta">Jakarta</SelectItem>
                                        <SelectItem value="Surabaya">Surabaya</SelectItem>
                                        <SelectItem value="Bandung">Bandung</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>
                    </CardContent>
                    </Card>
                </div>

                <!-- Content Table -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent">
                    <CardHeader>
                        <CardTitle>Careers List</CardTitle>
                        <CardDescription>
                            Showing {{ careers.from || 0 }}-{{ careers.to || 0 }} of {{ careers.total || 0 }} careers
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="isLoading" class="flex justify-center py-8">
                            <div class="text-muted-foreground">Loading careers...</div>
                        </div>
                        <div v-else-if="careers.data?.length === 0" class="text-center py-8">
                            <div class="text-muted-foreground mb-4">
                                No careers found. Create your first job posting.
                            </div>
                            <Button @click="openDialog()">
                                <Plus class="w-4 h-4 mr-2" />
                                Add New Career
                            </Button>
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left p-4 font-medium">Title</th>
                                        <th class="text-left p-4 font-medium">Department</th>
                                        <th class="text-left p-4 font-medium">Location</th>
                                        <th class="text-left p-4 font-medium">Status</th>
                                        <th class="text-left p-4 font-medium">Posted Date</th>
                                        <th class="text-left p-4 font-medium">Deadline</th>
                                        <th class="text-right p-4 font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr 
                                        v-for="career in careers.data" 
                                        :key="career.id"
                                        class="border-b hover:bg-muted/50"
                                    >
                                        <td class="p-4">
                                            <div>
                                                <div class="font-medium">{{ career.title_en }}</div>
                                                <div class="text-sm text-muted-foreground">{{ career.title_id }}</div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <Badge variant="secondary">{{ career.department_id || '-' }}</Badge>
                                        </td>
                                        <td class="p-4">{{ career.location_en }}</td>
                                        <td class="p-4">
                                            <Badge :variant="career.status === 'published' ? 'default' : 'outline'">
                                                {{ career.status }}
                                            </Badge>
                                        </td>
                                        <td class="p-4">{{ new Date(career.start_date).toLocaleDateString() }}</td>
                                        <td class="p-4">{{ new Date(career.end_date).toLocaleDateString() }}</td>
                                        <td class="p-4">
                                            <div class="flex items-center justify-end gap-2">
                                                <Button 
                                                    variant="outline" 
                                                    size="sm" 
                                                    @click="openDialog(career)"
                                                >
                                                    <Edit class="w-4 h-4" />
                                                </Button>
                                                <Button 
                                                    variant="destructive" 
                                                    size="sm" 
                                                    @click="openDeleteConfirm(career.id)"
                                                >
                                                    <Trash2 class="w-4 h-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div v-if="careers.links && careers.links.length > 3" class="flex items-center justify-between mt-6">
                            <div class="text-sm text-muted-foreground">
                                Showing {{ careers.meta.from }}-{{ careers.meta.to }} of {{ careers.meta.total }} results
                            </div>
                            <div class="flex items-center gap-1">
                                <Button
                                    v-for="link in careers.links"
                                    :key="link.label"
                                    variant="outline"
                                    size="sm"
                                    :disabled="!link.url || isLoading"
                                    :class="{ 'bg-primary text-primary-foreground': link.active }"
                                    @click="link.url && loadCareers({ page: new URL(link.url).searchParams.get('page') })"
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

        <!-- Add/Edit Career Dialog -->
        <Dialog :open="dialogOpen" @update:open="(value) => { if (!value) closeDialog() }">
            <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>
                        {{ editingCareer ? 'Edit Career' : 'Add New Career' }}
                    </DialogTitle>
                </DialogHeader>

                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="title_id">Job Title (Indonesian) *</Label>
                            <Input
                                id="title_id"
                                v-model="careerForm.title_id"
                                placeholder="e.g., Supervisor Procurement"
                                required
                            />
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="title_en">Job Title (English) *</Label>
                            <Input
                                id="title_en"
                                v-model="careerForm.title_en"
                                placeholder="e.g., Procurement Supervisor"
                                required
                            />
                        </div>
                    </div>

                    <!-- Department -->
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <div class="space-y-2">
                            <Label for="department_id">Department *</Label>
                            <Select v-model="careerForm.department_id">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select department" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem 
                                        v-for="department in allDepartments" 
                                        :key="department.id" 
                                        :value="department.name_id"
                                    >
                                        {{ department.name_id }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <!-- Location Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="location_id">Location (Indonesian) *</Label>
                            <Input
                                id="location_id"
                                v-model="careerForm.location_id"
                                placeholder="e.g., Jakarta"
                                required
                            />
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="location_en">Location (English) *</Label>
                            <Input
                                id="location_en"
                                v-model="careerForm.location_en"
                                placeholder="e.g., Jakarta"
                                required
                            />
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="start_date">Posted Date *</Label>
                            <Input
                                id="start_date"
                                v-model="careerForm.start_date"
                                type="date"
                                required
                            />
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="end_date">Application Deadline *</Label>
                            <Input
                                id="end_date"
                                v-model="careerForm.end_date"
                                type="date"
                                required
                            />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="content_id">Job Description (Indonesian) *</Label>
                            <RichTextEditor
                                v-model="careerForm.content_id"
                                placeholder="Describe the role in Indonesian..."
                                :height="200"
                            />
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="content_en">Job Description (English) *</Label>
                            <RichTextEditor
                                v-model="careerForm.content_en"
                                placeholder="Describe the role in English..."
                                :height="200"
                            />
                        </div>
                    </div>

                    <!-- Requirements -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="requirements_id">Requirements (Indonesian) *</Label>
                            <RichTextEditor
                                v-model="careerForm.requirements_id"
                                placeholder="Enter requirements in Indonesian..."
                                :height="150"
                            />
                            <p class="text-sm text-muted-foreground">Use bullet points or numbered lists for requirements</p>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="requirements_en">Requirements (English) *</Label>
                            <RichTextEditor
                                v-model="careerForm.requirements_en"
                                placeholder="Enter requirements in English..."
                                :height="150"
                            />
                            <p class="text-sm text-muted-foreground">Use bullet points or numbered lists for requirements</p>
                        </div>
                    </div>

                    <!-- Benefits -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="benefits_id">Benefits (Indonesian) *</Label>
                            <RichTextEditor
                                v-model="careerForm.benefits_id"
                                placeholder="Enter benefits in Indonesian..."
                                :height="150"
                            />
                            <p class="text-sm text-muted-foreground">Use bullet points or numbered lists for benefits</p>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="benefits_en">Benefits (English) *</Label>
                            <RichTextEditor
                                v-model="careerForm.benefits_en"
                                placeholder="Enter benefits in English..."
                                :height="150"
                            />
                            <p class="text-sm text-muted-foreground">Use bullet points or numbered lists for benefits</p>
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="closeDialog">Cancel</Button>
                    <Button @click="saveCareer">
                        {{ editingCareer ? 'Update Career' : 'Add Career' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            :open="confirmDialog.open"
            title="Delete Career"
            description="Are you sure you want to delete this career? This action cannot be undone."
            :loading="confirmDialog.loading"
            @confirm="deleteCareer(confirmDialog.itemId)"
            @cancel="confirmDialog.open = false"
        />
    </AppLayout>
</template>