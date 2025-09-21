<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { route } from 'ziggy-js';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { 
    MessageSquare, 
    Eye, 
    User, 
    Mail, 
    Phone, 
    Calendar, 
    Clock,
    CheckCircle,
    XCircle,
    AlertCircle,
    Search,
    Filter,
    Reply
} from 'lucide-vue-next';

interface Complaint {
    id: number;
    name: string;
    email: string;
    phone: string;
    subject: string;
    message: string;
    status: string;
    admin_response: string | null;
    responded_at: string | null;
    responded_by: number | null;
    created_at: string;
    updated_at: string;
    status_label: string;
    responded_by_user?: {
        id: number;
        name: string;
        email: string;
    };
}

interface Props {
    complaints: {
        data: Complaint[];
        links: any[];
        meta?: any;
        current_page?: number;
        first_page_url?: string;
        from?: number;
        last_page?: number;
        last_page_url?: string;
        next_page_url?: string;
        path?: string;
        per_page?: number;
        prev_page_url?: string;
        to?: number;
        total?: number;
    };
    totalComplaints: number;
    filters: {
        search?: string;
        status?: string;
        type: string;
    };
    statuses: Record<string, string>;
    types: Record<string, string>;
    currentType: string;
    currentTypeName: string;
}

const props = defineProps<Props>();

// Dialog state
const isViewDialogOpen = ref(false);
const isResponseDialogOpen = ref(false);
const viewingComplaint = ref<Complaint | null>(null);
const respondingComplaint = ref<Complaint | null>(null);

// Form handling
const responseForm = useForm({
    admin_response: '',
    status: 'resolved',
});

const filters = ref({
    search: props.filters.search || '',
    status: props.filters.status || '',
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: `Report - ${props.currentTypeName}`, href: '' },
];

// Methods
const openViewDialog = (complaint: Complaint) => {
    viewingComplaint.value = complaint;
    isViewDialogOpen.value = true;
};

const openResponseDialog = (complaint: Complaint) => {
    respondingComplaint.value = complaint;
    responseForm.admin_response = complaint.admin_response || '';
    responseForm.status = complaint.status;
    isResponseDialogOpen.value = true;
};

const submitResponse = () => {
    if (!respondingComplaint.value) return;
    
    responseForm.put(route('complaints.respond', respondingComplaint.value.id), {
        onSuccess: () => {
            isResponseDialogOpen.value = false;
            respondingComplaint.value = null;
            responseForm.reset();
        }
    });
};

const applyFilters = () => {
    const params = new URLSearchParams();
    params.set('type', props.currentType);
    
    if (filters.value.search) params.set('search', filters.value.search);
    if (filters.value.status) params.set('status', filters.value.status);
    
    const queryString = params.toString();
    const url = `/reports?${queryString}`;
    
    window.location.href = url;
};

const clearFilters = () => {
    filters.value = { search: '', status: '' };
    window.location.href = `/reports?type=${props.currentType}`;
};

// Get status badge variant
const getStatusBadge = (status: string) => {
    const variants = {
        'pending': { variant: 'secondary', icon: AlertCircle, class: 'text-yellow-600' },
        'in_review': { variant: 'outline', icon: Eye, class: 'text-blue-600' },
        'resolved': { variant: 'default', icon: CheckCircle, class: 'text-green-600' },
        'rejected': { variant: 'destructive', icon: XCircle, class: 'text-red-600' },
    };
    return variants[status] || variants['pending'];
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const truncateText = (text: string, limit: number = 100) => {
    return text.length > limit ? text.substring(0, limit) + '...' : text;
};
</script>

<template>
    <Head :title="`Report - ${currentTypeName}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">Report - {{ currentTypeName }}</h1>
                        <p class="text-muted-foreground">Kelola {{ currentTypeName.toLowerCase() }} dari pengguna</p>
                    </div>
                </div>

                <!-- Content Type Tabs -->
                <div class="border-b">
                    <nav class="-mb-px flex space-x-8">
                        <a
                            v-for="(typeName, typeKey) in types"
                            :key="typeKey"
                            :href="route('reports.index', { type: typeKey })"
                            :class="[
                                'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm',
                                currentType === typeKey
                                    ? 'border-primary text-primary'
                                    : 'border-transparent text-muted-foreground hover:text-foreground hover:border-gray-300'
                            ]"
                        >
                            {{ typeName }}
                        </a>
                    </nav>
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
                                    placeholder="Search by name, email, subject..."
                                    @keyup.enter="applyFilters"
                                />
                            </div>
                            
                            <div class="space-y-2">
                                <Label>Status</Label>
                                <select v-model="filters.status" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-background dark:text-foreground dark:border-input">
                                    <option value="">All Status</option>
                                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                                        {{ label }}
                                    </option>
                                </select>
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

                <!-- Complaints Table -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <MessageSquare class="w-4 h-4" />
                            Laporan Pengaduan
                        </CardTitle>
                        <CardDescription>
                            {{ totalComplaints }} pengaduan total
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="!complaints.data.length" class="text-center py-12">
                            <MessageSquare class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                            <p class="text-muted-foreground">Belum ada pengaduan.</p>
                        </div>
                        
                        <div v-else class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left p-4 text-sm font-medium">Pengadu</th>
                                        <th class="text-left p-4 text-sm font-medium">Judul</th>
                                        <th class="text-left p-4 text-sm font-medium">Pesan</th>
                                        <th class="text-left p-4 text-sm font-medium">Status</th>
                                        <th class="text-left p-4 text-sm font-medium">Tanggal</th>
                                        <th class="text-left p-4 text-sm font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="complaint in complaints.data" :key="complaint.id" class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="p-4">
                                            <div class="space-y-1">
                                                <div class="font-medium">{{ complaint.name }}</div>
                                                <div class="text-sm text-muted-foreground flex items-center gap-1">
                                                    <Mail class="h-3 w-3" />
                                                    {{ complaint.email }}
                                                </div>
                                                <div v-if="complaint.phone" class="text-sm text-muted-foreground flex items-center gap-1">
                                                    <Phone class="h-3 w-3" />
                                                    {{ complaint.phone }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div class="font-medium">{{ complaint.subject }}</div>
                                        </td>
                                        <td class="p-4">
                                            <div class="max-w-xs">
                                                {{ truncateText(complaint.message, 80) }}
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <Badge :variant="getStatusBadge(complaint.status).variant" class="text-xs">
                                                <component :is="getStatusBadge(complaint.status).icon" class="w-3 h-3 mr-1" />
                                                {{ complaint.status_label }}
                                            </Badge>
                                        </td>
                                        <td class="p-4">
                                            <div class="text-sm">{{ formatDate(complaint.created_at) }}</div>
                                            <div v-if="complaint.responded_at" class="text-xs text-muted-foreground">
                                                Direspon: {{ formatDate(complaint.responded_at) }}
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div class="flex items-center gap-2">
                                                <Button @click="openViewDialog(complaint)" variant="outline" size="sm">
                                                    <Eye class="h-4 w-4" />
                                                </Button>
                                                <Button @click="openResponseDialog(complaint)" variant="outline" size="sm">
                                                    <Reply class="h-4 w-4" />
                                                </Button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div v-if="complaints.links" class="flex justify-center mt-6">
                            <div class="flex gap-2">
                                <template v-for="(link, index) in complaints.links" :key="index">
                                    <Button
                                        v-if="link.url"
                                        :variant="link.active ? 'default' : 'outline'"
                                        size="sm"
                                        @click="$inertia.visit(link.url)"
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

        <!-- View Complaint Dialog -->
        <Dialog v-model:open="isViewDialogOpen">
            <DialogContent class="sm:max-w-[600px] max-h-[95vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Detail Pengaduan</DialogTitle>
                    <DialogDescription>
                        Informasi lengkap pengaduan dari pengguna
                    </DialogDescription>
                </DialogHeader>
                
                <div v-if="viewingComplaint" class="space-y-4 py-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Nama Pengadu</Label>
                            <div class="p-2 bg-muted rounded">{{ viewingComplaint.name }}</div>
                        </div>
                        <div class="space-y-2">
                            <Label>Email</Label>
                            <div class="p-2 bg-muted rounded">{{ viewingComplaint.email }}</div>
                        </div>
                    </div>
                    
                    <div v-if="viewingComplaint.phone" class="space-y-2">
                        <Label>Nomor Telepon</Label>
                        <div class="p-2 bg-muted rounded">{{ viewingComplaint.phone }}</div>
                    </div>
                    
                    <div class="space-y-2">
                        <Label>Judul Pengaduan</Label>
                        <div class="p-2 bg-muted rounded">{{ viewingComplaint.subject }}</div>
                    </div>
                    
                    <div class="space-y-2">
                        <Label>Pesan</Label>
                        <div class="p-3 bg-muted rounded whitespace-pre-wrap">{{ viewingComplaint.message }}</div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <Label>Status</Label>
                            <Badge :variant="getStatusBadge(viewingComplaint.status).variant">
                                <component :is="getStatusBadge(viewingComplaint.status).icon" class="w-3 h-3 mr-1" />
                                {{ viewingComplaint.status_label }}
                            </Badge>
                        </div>
                        <div class="space-y-2">
                            <Label>Tanggal Pengaduan</Label>
                            <div class="p-2 bg-muted rounded text-sm">{{ formatDate(viewingComplaint.created_at) }}</div>
                        </div>
                    </div>
                    
                    <div v-if="viewingComplaint.admin_response" class="space-y-2">
                        <Label>Respon Admin</Label>
                        <div class="p-3 bg-muted rounded whitespace-pre-wrap">{{ viewingComplaint.admin_response }}</div>
                        <div v-if="viewingComplaint.responded_at" class="text-xs text-muted-foreground">
                            Direspon pada: {{ formatDate(viewingComplaint.responded_at) }}
                            <span v-if="viewingComplaint.responded_by_user">
                                oleh {{ viewingComplaint.responded_by_user.name }}
                            </span>
                        </div>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

        <!-- Response Dialog -->
        <Dialog v-model:open="isResponseDialogOpen">
            <DialogContent class="sm:max-w-[600px] max-h-[95vh] overflow-y-auto">
                <DialogHeader>
                    <DialogTitle>Respon Pengaduan</DialogTitle>
                    <DialogDescription>
                        Berikan respon untuk pengaduan ini
                    </DialogDescription>
                </DialogHeader>
                
                <div v-if="respondingComplaint" class="space-y-4 py-4">
                    <div class="space-y-2">
                        <Label>Pengaduan dari: {{ respondingComplaint.name }}</Label>
                        <div class="p-3 bg-muted rounded">
                            <div class="font-medium">{{ respondingComplaint.subject }}</div>
                            <div class="text-sm mt-1">{{ truncateText(respondingComplaint.message, 200) }}</div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="status">Status</Label>
                        <select 
                            id="status" 
                            v-model="responseForm.status" 
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-background dark:text-foreground dark:border-input"
                        >
                            <option v-for="(label, value) in statuses" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="admin_response">Respon</Label>
                        <Textarea 
                            id="admin_response" 
                            v-model="responseForm.admin_response" 
                            placeholder="Tulis respon Anda untuk pengaduan ini..." 
                            rows="5"
                            required
                        />
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <Button type="button" variant="outline" @click="isResponseDialogOpen = false">
                        Batal
                    </Button>
                    <Button type="button" @click="submitResponse" :disabled="responseForm.processing">
                        {{ responseForm.processing ? 'Menyimpan...' : 'Kirim Respon' }}
                    </Button>
                </div>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>