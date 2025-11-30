<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, reactive, computed, watch, onMounted } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Users, RefreshCw, Plus, Edit, Trash2, Search, Filter, Upload, X, Image } from 'lucide-vue-next';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { Switch } from '@/components/ui/switch';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import PlaceholderPattern from '@/components/PlaceholderPattern.vue';
import { useCsrfToken } from '@/composables/useCsrfToken';
import axios from 'axios';

interface TeamMember {
    id: number;
    type: string;
    slug: string;
    title_id: string;
    title_en?: string;
    testimonial_id?: string;
    testimonial_en?: string;
    position_id: string;
    position_en?: string;
    division_id?: string;
    division_en?: string;
    featured_image?: string;
    featured_image_url?: string;
    is_published: boolean;
    is_featured: boolean;
    sort_order?: number;
    status: string;
    published_at?: string;
    created_at: string;
    updated_at: string;
    view_count?: number;
}

const breadcrumbs: BreadcrumbItem[] = [
    { label: 'Content', href: '/content' },
    { label: 'Team Members Management', href: '/content/team-members' }
];

const teamMembers = ref<{ data: TeamMember[], links: any[], from?: number, to?: number, total?: number }>({ data: [], links: [], from: 0, to: 0, total: 0 });
const allPositions = ref<string[]>([]);
const allDepartments = ref<{id: number, name_id: string, name_en: string}[]>([]);
const isLoading = ref(false);
const dialogOpen = ref(false);
const editingTeamMember = ref<TeamMember | null>(null);
const confirmDialog = ref({
    open: false,
    itemId: 0,
    loading: false
});

const filters = reactive({
    search: '',
    position: '',
    status: '',
    per_page: 10,
    page: 1
});

const teamMemberForm = reactive({
    id: null,
    title_id: '',
    title_en: '',
    testimonial_id: '',
    testimonial_en: '',
    position_id: '',
    position_en: '',
    division_id: '',
    division_en: '',
    featured_image: null as string | null,
    featured_image_file: null as File | null,
    is_published: true,
    is_featured: false,
    sort_order: 0,
    status: 'published'
});

const loadTeamMembers = async (params = {}) => {
    isLoading.value = true;
    try {
        const response = await axios.get('/content/team-members/data', { 
            params: { ...filters, ...params }
        });
        teamMembers.value = response.data;
    } catch (error) {
        console.error('🔧 Error loading team members:', error);
        console.error('🔧 Error response:', error.response);
    } finally {
        isLoading.value = false;
    }
};

const loadAllPositions = async () => {
    try {
        const response = await axios.get('/content/team-members/data', { 
            params: { per_page: 1000, no_pagination: true }
        });
        const positions = new Set<string>();
        response.data.data?.forEach((member: TeamMember) => {
            if (member.position_id && member.position_id.trim()) {
                positions.add(member.position_id.trim());
            }
        });
        allPositions.value = Array.from(positions).sort();
    } catch (error) {
        console.error('Error loading positions:', error);
    }
};

const loadAllDepartments = async () => {
    try {
        const response = await axios.get('/master/departments/data');
        console.log('🔧 Departments response:', response.data);
        allDepartments.value = response.data.data || response.data || [];
    } catch (error) {
        console.error('Error loading departments:', error);
    }
};

const openDialog = (teamMember: TeamMember | null = null) => {
    if (teamMember) {
        console.log('🔧 openDialog - teamMember data:', teamMember);
        editingTeamMember.value = teamMember;
        
        // Explicitly assign each field to ensure they're set correctly
        teamMemberForm.id = teamMember.id;
        teamMemberForm.title_id = teamMember.title_id || '';
        teamMemberForm.title_en = teamMember.title_en || '';
        teamMemberForm.testimonial_id = teamMember.testimonial_id || '';
        teamMemberForm.testimonial_en = teamMember.testimonial_en || '';
        teamMemberForm.position_id = teamMember.position_id || '';
        teamMemberForm.position_en = teamMember.position_en || '';
        teamMemberForm.division_id = teamMember.division_id || '';
        teamMemberForm.division_en = teamMember.division_en || '';
        teamMemberForm.featured_image = teamMember.featured_image ? teamMember.featured_image : null;
        teamMemberForm.featured_image_file = null;
        teamMemberForm.status = teamMember.status || 'published';
        // Sync is_published with status: if status is 'published', then is_published = true
        teamMemberForm.is_published = (teamMember.status || 'published') === 'published';
        teamMemberForm.is_featured = teamMember.is_featured;
        teamMemberForm.sort_order = teamMember.sort_order || 0;
        
        console.log('🔧 openDialog - teamMemberForm after assignment:', teamMemberForm);
    } else {
        resetForm();
        editingTeamMember.value = null;
    }
    dialogOpen.value = true;
};

const closeDialog = () => {
    dialogOpen.value = false;
    resetForm();
    editingTeamMember.value = null;
};

const resetForm = () => {
    Object.assign(teamMemberForm, {
        id: null,
        title_id: '',
        title_en: '',
        testimonial_id: '',
        testimonial_en: '',
        position_id: '',
        position_en: '',
        division_id: '',
        division_en: '',
        featured_image: null,
        featured_image_file: null,
        is_published: true, // Will be synced with status automatically
        is_featured: false,
        sort_order: 0,
        status: 'published'
    });
};

import { toast } from '@/components/ui/toast';

// Get CSRF token
const { getCsrfToken } = useCsrfToken();

const saveTeamMember = async () => {
    try {
        console.log('🔧 saveTeamMember - teamMemberForm before submit:', teamMemberForm);
        
        const formData = new FormData();
        
        // Add form data
        formData.append('title_id', teamMemberForm.title_id);
        formData.append('title_en', teamMemberForm.title_en || '');
        formData.append('testimonial_id', teamMemberForm.testimonial_id || '');
        formData.append('testimonial_en', teamMemberForm.testimonial_en || '');
        formData.append('position_id', teamMemberForm.position_id);
        formData.append('position_en', teamMemberForm.position_en || '');
        formData.append('division_id', teamMemberForm.division_id || '');
        formData.append('division_en', teamMemberForm.division_en || '');
        // Sync is_published with status: if status is 'published', then is_published = true
        const isPublished = teamMemberForm.status === 'published';
        formData.append('is_published', isPublished ? '1' : '0');
        formData.append('is_featured', teamMemberForm.is_featured ? '1' : '0');
        formData.append('sort_order', teamMemberForm.sort_order.toString());
        formData.append('status', teamMemberForm.status);
        
        // Add CSRF token to FormData
        formData.append('_token', getCsrfToken());
        
        // Add image file if selected
        if (teamMemberForm.featured_image_file) {
            formData.append('featured_image', teamMemberForm.featured_image_file);
        }

        // For PUT requests with file uploads, use POST with _method field (Laravel workaround)
        if (editingTeamMember.value) {
            formData.append('_method', 'PUT');
        }

        // Debug FormData
        console.log('🔧 FormData entries:');
        for (const pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        if (editingTeamMember.value) {
            console.log('🔧 Updating team member ID:', editingTeamMember.value.id);
            // Use POST with _method=PUT for file uploads (Laravel limitation with PUT + multipart/form-data)
            await axios.post(`/content/team-members/${editingTeamMember.value.id}`, formData, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            toast({ title: 'Success', description: 'Team member updated successfully.', variant: 'success' });
        } else {
            console.log('🔧 Creating new team member');
            await axios.post('/content/team-members', formData, {
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            toast({ title: 'Success', description: 'Team member created successfully.', variant: 'success' });
        }
        
        closeDialog();
        loadTeamMembers();
        loadAllPositions();
        loadAllDepartments();
    } catch (error) {
        console.error('🔧 Error saving team member:', error);
        console.error('🔧 Error response:', error.response);
        if (error.response && error.response.data && error.response.data.errors) {
            console.error('🔧 Validation errors:', error.response.data.errors);
            
            // Log each validation error in detail
            Object.entries(error.response.data.errors).forEach(([field, messages]) => {
                console.error(`🔧 ${field}:`, messages);
            });
            
            const firstError = Object.values(error.response.data.errors)[0][0];
            toast({ title: 'Validation Error', description: firstError, variant: 'error' });
        } else {
            toast({ title: 'Error', description: 'An unexpected error occurred.', variant: 'error' });
        }
    }
};

const deleteTeamMember = async (teamMemberId: number) => {
    confirmDialog.value.loading = true;
    try {
        await axios.delete(`/content/team-members/${teamMemberId}`);
        confirmDialog.value.open = false;
        loadTeamMembers();
        loadAllPositions();
        loadAllDepartments();
        toast({ title: 'Success', description: 'Team member deleted successfully.', variant: 'success' });
    } catch (error) {
        console.error('Error deleting team member:', error);
        toast({ title: 'Error', description: 'Failed to delete team member.', variant: 'error' });
    } finally {
        confirmDialog.value.loading = false;
    }
};

const openDeleteConfirm = (teamMemberId: number) => {
    confirmDialog.value.itemId = teamMemberId;
    confirmDialog.value.open = true;
};

const handleImageUpload = (event: Event) => {
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
        
        teamMemberForm.featured_image_file = file;
        
        // Create preview URL
        const reader = new FileReader();
        reader.onload = (e) => {
            teamMemberForm.featured_image = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const clearProfileImage = () => {
    teamMemberForm.featured_image = null;
    teamMemberForm.featured_image_file = null;
    // Clear the input value
    const input = document.querySelector('input[type="file"]') as HTMLInputElement;
    if (input) {
        input.value = '';
    }
};

const getImageUrl = (member: TeamMember) => {
    if (!member.featured_image) {
        return '/img/placeholder.png'; // Default placeholder
    }
    
    // Handle case where featured_image might be an object or already have full path
    let imagePath = member.featured_image;
    if (typeof imagePath === 'object') {
        imagePath = imagePath.path || imagePath.url || '';
    }
    
    // If already has full URL or starts with /storage/, return as is
    if (imagePath.startsWith('http') || imagePath.startsWith('/storage/')) {
        return imagePath;
    }
    
    // Otherwise, prepend /storage/
    return `/storage/${imagePath}`;
};

const uniquePositions = computed(() => {
    return allPositions.value;
});

const applyFilters = () => {
    filters.page = 1;
    loadTeamMembers();
};

const clearFilters = () => {
    filters.search = '';
    filters.position = '';
    filters.status = '';
    filters.page = 1;
    loadTeamMembers();
};


// Watch status to sync is_published automatically
watch(() => teamMemberForm.status, (newStatus) => {
    // Sync is_published with status: if status is 'published', then is_published = true
    teamMemberForm.is_published = newStatus === 'published';
});

onMounted(() => {
    loadTeamMembers();
    loadAllPositions();
    loadAllDepartments();
});
</script>

<template>
    <Head title="Team Members Management" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <Users class="w-8 h-8 text-primary" />
                        <div>
                            <h1 class="text-3xl font-bold tracking-tight">Team Members Management</h1>
                            <p class="text-muted-foreground">
                                Manage team members, leadership, and company personnel
                            </p>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <Button @click="openDialog()">
                            <Plus class="w-4 h-4 mr-2" />
                            Add New Team Member
                        </Button>
                        <Button 
                            @click="loadTeamMembers" 
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
                                    placeholder="Search team members..."
                                    class="w-full"
                                />
                            </div>
                            <div class="space-y-2">
                                <Label>Position</Label>
                                <Select v-model="filters.position">
                                    <SelectTrigger>
                                        <SelectValue placeholder="All Positions" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All Positions</SelectItem>
                                        <SelectItem 
                                            v-for="position in uniquePositions" 
                                            :key="position" 
                                            :value="position"
                                        >
                                            {{ position }}
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
                </div>

                <!-- Content Table -->
                <div class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                    <PlaceholderPattern />
                    <Card class="relative z-10 border-0 shadow-none bg-transparent">
                        <CardHeader>
                            <CardTitle>Team Members List</CardTitle>
                            <CardDescription>
                                Showing {{ teamMembers.from || 0 }}-{{ teamMembers.to || 0 }} of {{ teamMembers.total || 0 }} team members
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                        <div v-if="isLoading" class="flex justify-center py-8">
                            <div class="text-muted-foreground">Loading team members...</div>
                        </div>
                        <div v-else-if="teamMembers.data?.length === 0" class="text-center py-8">
                            <div class="text-muted-foreground mb-4">
                                No team members found. Add your first team member.
                            </div>
                            <Button @click="openDialog()">
                                <Plus class="w-4 h-4 mr-2" />
                                Add New Team Member
                            </Button>
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left p-4 font-medium">Photo</th>
                                        <th class="text-left p-4 font-medium">Name</th>
                                        <th class="text-left p-4 font-medium">Position</th>
                                        <th class="text-left p-4 font-medium">Division</th>
                                        <th class="text-left p-4 font-medium">Status</th>
                                        <th class="text-left p-4 font-medium">Featured</th>
                                        <th class="text-left p-4 font-medium">Sort Order</th>
                                        <th class="text-right p-4 font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr 
                                        v-for="member in teamMembers.data" 
                                        :key="member.id"
                                        class="border-b hover:bg-muted/50"
                                    >
                                        <td class="p-4">
                                            <div class="w-12 h-12 rounded-full overflow-hidden bg-muted">
                                                <img 
                                                    :src="getImageUrl(member)" 
                                                    :alt="member.title_id"
                                                    class="w-full h-full object-cover"
                                                />
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div>
                                                <div class="font-medium">{{ member.title_en || member.title_id }}</div>
                                                <div v-if="member.title_en && member.title_id !== member.title_en" class="text-sm text-muted-foreground">{{ member.title_id }}</div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <Badge variant="secondary">{{ member.position_id || '-' }}</Badge>
                                        </td>
                                        <td class="p-4">
                                            <Badge variant="outline">{{ member.division_id || member.division_en || '-' }}</Badge>
                                        </td>
                                        <td class="p-4">
                                            <Badge :variant="member.status === 'published' ? 'default' : 'outline'">
                                                {{ member.status }}
                                            </Badge>
                                        </td>
                                        <td class="p-4">
                                            <Badge v-if="member.is_featured" variant="default" class="bg-yellow-500">
                                                Featured
                                            </Badge>
                                            <span v-else class="text-muted-foreground">-</span>
                                        </td>
                                        <td class="p-4">{{ member.sort_order || 0 }}</td>
                                        <td class="p-4">
                                            <div class="flex items-center justify-end gap-2">
                                                <Button 
                                                    variant="outline" 
                                                    size="sm" 
                                                    @click="openDialog(member)"
                                                >
                                                    <Edit class="w-4 h-4" />
                                                </Button>
                                                <Button 
                                                    variant="destructive" 
                                                    size="sm" 
                                                    @click="openDeleteConfirm(member.id)"
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
                        <div v-if="teamMembers.links && teamMembers.links.length > 3" class="flex items-center justify-between mt-6">
                            <div class="text-sm text-muted-foreground">
                                Showing {{ teamMembers.from }}-{{ teamMembers.to }} of {{ teamMembers.total }} results
                            </div>
                            <div class="flex items-center gap-1">
                                <Button
                                    v-for="link in teamMembers.links"
                                    :key="link.label"
                                    variant="outline"
                                    size="sm"
                                    :disabled="!link.url || isLoading"
                                    :class="{ 'bg-primary text-primary-foreground': link.active }"
                                    @click="link.url && loadTeamMembers({ page: new URL(link.url).searchParams.get('page') })"
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

        <!-- Add/Edit Team Member Dialog -->
        <Dialog v-model:open="dialogOpen">
            <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>
                        {{ editingTeamMember ? 'Edit Team Member' : 'Add New Team Member' }}
                    </DialogTitle>
                    <DialogDescription>
                        {{ editingTeamMember ? 'Update' : 'Create new' }} team member information
                    </DialogDescription>
                </DialogHeader>

                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="title_id">Name (Indonesian) *</Label>
                            <Input
                                id="title_id"
                                v-model="teamMemberForm.title_id"
                                placeholder="e.g., Arwin Rasyid"
                                maxlength="20"
                                required
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ teamMemberForm.title_id?.length || 0 }}/20 characters
                            </p>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="title_en">Name (English)</Label>
                            <Input
                                id="title_en"
                                v-model="teamMemberForm.title_en"
                                placeholder="e.g., Arwin Rasyid"
                                maxlength="20"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ teamMemberForm.title_en?.length || 0 }}/20 characters
                            </p>
                        </div>
                    </div>

                    <!-- Position -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="position_id">Position (Indonesian) *</Label>
                            <Input
                                id="position_id"
                                v-model="teamMemberForm.position_id"
                                placeholder="e.g., CEO"
                                maxlength="10"
                                required
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ teamMemberForm.position_id?.length || 0 }}/10 characters
                            </p>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="position_en">Position (English)</Label>
                            <Input
                                id="position_en"
                                v-model="teamMemberForm.position_en"
                                placeholder="e.g., CEO"
                                maxlength="10"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ teamMemberForm.position_en?.length || 0 }}/10 characters
                            </p>
                        </div>
                    </div>

                    <!-- Division -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="division_id">Division (Indonesian)</Label>
                            <Input
                                id="division_id"
                                v-model="teamMemberForm.division_id"
                                placeholder="e.g., Teknologi"
                                maxlength="20"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ teamMemberForm.division_id?.length || 0 }}/20 characters
                            </p>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="division_en">Division (English)</Label>
                            <Input
                                id="division_en"
                                v-model="teamMemberForm.division_en"
                                placeholder="e.g., Technology"
                                maxlength="20"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ teamMemberForm.division_en?.length || 0 }}/20 characters
                            </p>
                        </div>
                    </div>

                    <!-- Testimonial -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="testimonial_id">Testimonial (Indonesian)</Label>
                            <Textarea
                                id="testimonial_id"
                                v-model="teamMemberForm.testimonial_id"
                                placeholder="Enter testimonial or quote in Indonesian..."
                                rows="4"
                                maxlength="500"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ teamMemberForm.testimonial_id?.length || 0 }}/500 characters
                            </p>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="testimonial_en">Testimonial (English)</Label>
                            <Textarea
                                id="testimonial_en"
                                v-model="teamMemberForm.testimonial_en"
                                placeholder="Enter testimonial or quote in English..."
                                rows="4"
                                maxlength="500"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ teamMemberForm.testimonial_en?.length || 0 }}/500 characters
                            </p>
                        </div>
                    </div>

                    <!-- Profile Photo Upload -->
                    <div class="space-y-2">
                        <Label>Profile Photo</Label>
                        <div class="border-2 border-dashed border-input rounded-lg p-6">
                            <div v-if="teamMemberForm.featured_image" class="space-y-4">
                                <div class="flex items-center justify-center">
                                    <img 
                                        :src="teamMemberForm.featured_image.startsWith('data:') ? teamMemberForm.featured_image : (teamMemberForm.featured_image.startsWith('/storage/') ? teamMemberForm.featured_image : `/storage/${teamMemberForm.featured_image}`)"
                                        alt="Profile Photo Preview" 
                                        class="max-h-32 rounded-lg border bg-white object-contain"
                                    />
                                </div>
                                <div class="flex justify-center space-x-2">
                                    <Button size="sm" variant="outline" @click="clearProfileImage">
                                        <X class="w-4 h-4 mr-2" />
                                        Remove
                                    </Button>
                                    <Button size="sm" variant="outline" @click="$refs.profileImageInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Change
                                    </Button>
                                </div>
                            </div>
                            <div v-else class="text-center">
                                <Image class="mx-auto h-12 w-12 text-muted-foreground" />
                                <div class="mt-2">
                                    <Button variant="outline" @click="$refs.profileImageInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Upload Image
                                    </Button>
                                </div>
                                <p class="text-sm text-muted-foreground mt-2">JPEG, PNG, WebP up to 5MB</p>
                            </div>
                            <input
                                ref="profileImageInput"
                                type="file"
                                accept="image/jpeg,image/jpg,image/png,image/webp"
                                class="hidden"
                                @change="handleImageUpload"
                            />
                        </div>
                        <p class="text-sm text-muted-foreground">
                            Profile photo for the team member (recommended size: 250x400px)
                        </p>
                    </div>

                    <!-- Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label for="sort_order">Sort Order</Label>
                            <Input
                                id="sort_order"
                                v-model.number="teamMemberForm.sort_order"
                                type="number"
                                min="0"
                                placeholder="0"
                            />
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="status">Status</Label>
                            <Select v-model="teamMemberForm.status">
                                <SelectTrigger>
                                    <SelectValue placeholder="Select status" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="published">Published</SelectItem>
                                    <SelectItem value="draft">Draft</SelectItem>
                                    <SelectItem value="archived">Archived</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <!-- Toggles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center space-x-2">
                            <Switch 
                                id="is_featured" 
                                v-model:checked="teamMemberForm.is_featured"
                            />
                            <Label for="is_featured">Featured</Label>
                        </div>
                    </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="closeDialog">Cancel</Button>
                    <Button @click="saveTeamMember">
                        {{ editingTeamMember ? 'Update Team Member' : 'Add Team Member' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>

        <!-- Delete Confirmation Dialog -->
        <ConfirmDialog
            :open="confirmDialog.open"
            title="Delete Team Member"
            description="Are you sure you want to delete this team member? This action cannot be undone."
            :loading="confirmDialog.loading"
            @confirm="deleteTeamMember(confirmDialog.itemId)"
            @cancel="confirmDialog.open = false"
        />
    </AppLayout>
</template>
