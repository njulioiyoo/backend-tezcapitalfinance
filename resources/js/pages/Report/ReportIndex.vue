<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { route } from 'ziggy-js';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select } from '@/components/ui/select';
import { Switch } from '@/components/ui/switch';
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import { Plus, Edit, Trash2, Download, FileText, Calendar, Eye, EyeOff, Save, Upload, X } from 'lucide-vue-next';

interface Report {
    id: number;
    type: string;
    title_id: string;
    title_en: string;
    description_id: string;
    description_en: string;
    year: number;
    period: string;
    month: number | null;
    quarter: number | null;
    file_path: string | null;
    file_name: string | null;
    file_size: number | null;
    is_published: boolean;
    published_at: string | null;
    created_at: string;
    updated_at: string;
    type_name: string;
    period_name: string;
    month_name: string | null;
    quarter_name: string | null;
    full_title: string;
    formatted_file_size: string | null;
    file_url: string | null;
}

interface Props {
    reports: {
        data: Report[];
        links: any[];
        meta: any;
    };
    filters: {
        search?: string;
        year?: number;
        type: string;
    };
    years: number[];
    types: Record<string, string>;
    periods: Record<string, string>;
    months: Record<number, string>;
    quarters: Record<number, string>;
    currentType: string;
    currentTypeName: string;
}

const props = defineProps<Props>();

// Dialog state
const isDialogOpen = ref(false);
const editingReport = ref<Report | null>(null);
const fileInput = ref<HTMLInputElement>();

// Form handling
const form = useForm({
    type: props.currentType,
    title_id: '',
    title_en: '',
    description_id: '',
    description_en: '',
    year: new Date().getFullYear(),
    period: 'yearly',
    month: null as number | null,
    quarter: null as number | null,
    file: null as File | null,
    is_published: true,
});

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: route('dashboard') },
    { title: `Report - ${props.currentTypeName}`, href: '' },
];

// Methods
const openAddDialog = () => {
    editingReport.value = null;
    form.reset();
    form.type = props.currentType;
    form.year = new Date().getFullYear();
    form.period = 'yearly';
    isDialogOpen.value = true;
};

const openEditDialog = (report: Report) => {
    editingReport.value = report;
    form.clearErrors();
    Object.assign(form, {
        type: report.type,
        title_id: report.title_id,
        title_en: report.title_en || '',
        description_id: report.description_id,
        description_en: report.description_en || '',
        year: report.year,
        period: report.period,
        month: report.month,
        quarter: report.quarter,
        file: null,
        is_published: report.is_published,
    });
    isDialogOpen.value = true;
};

const handleFileUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        // Check file size (10MB = 10 * 1024 * 1024 bytes)
        const maxSize = 10 * 1024 * 1024;
        if (file.size > maxSize) {
            alert('File terlalu besar! Maksimal 10MB. File Anda: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB');
            // Clear the input
            target.value = '';
            return;
        }
        form.file = file;
    }
};

const removeFile = () => {
    form.file = null;
    if (fileInput.value) {
        fileInput.value.value = '';
    }
};

const saveReport = () => {
    const submitForm = () => {
        if (editingReport.value) {
            form.put(route('reports.update', editingReport.value.id), {
                onSuccess: () => {
                    isDialogOpen.value = false;
                    form.reset();
                }
            });
        } else {
            form.post(route('reports.store'), {
                onSuccess: () => {
                    isDialogOpen.value = false;
                    form.reset();
                }
            });
        }
    };
    
    submitForm();
};

const deleteReport = (report: Report) => {
    if (confirm(`Are you sure you want to delete "${report.title_id}"?`)) {
        form.delete(route('reports.destroy', report.id));
    }
};

// Get status badge variant
const getStatusBadge = (report: Report) => {
    return report.is_published 
        ? { variant: 'default', label: 'Published' }
        : { variant: 'secondary', label: 'Draft' };
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
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
                        <p class="text-muted-foreground">Kelola {{ currentTypeName.toLowerCase() }} perusahaan</p>
                    </div>
                    <Dialog v-model:open="isDialogOpen">
                        <DialogTrigger as-child>
                            <Button @click="openAddDialog">
                                <Plus class="w-4 h-4 mr-2" />
                                Tambah Report
                            </Button>
                        </DialogTrigger>
                        <DialogContent class="sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] max-h-[95vh] overflow-y-auto">
                            <DialogHeader>
                                <DialogTitle>{{ editingReport ? 'Edit' : 'Tambah' }} Report</DialogTitle>
                                <DialogDescription>
                                    {{ editingReport ? 'Update informasi report' : 'Buat report baru' }}
                                </DialogDescription>
                            </DialogHeader>
                            
                            <div class="space-y-4 py-4">
                                <!-- Type Selection -->
                                <div class="space-y-2">
                                    <Label for="type">Tipe Report</Label>
                                    <Select v-model="form.type" :disabled="!!editingReport">
                                        <option v-for="(label, key) in types" :key="key" :value="key">
                                            {{ label }}
                                        </option>
                                    </Select>
                                    <span v-if="form.errors.type" class="text-sm text-destructive">{{ form.errors.type }}</span>
                                </div>

                                <!-- Title ID -->
                                <div class="space-y-2">
                                    <Label for="title_id">Judul (Indonesia) *</Label>
                                    <Input 
                                        id="title_id" 
                                        v-model="form.title_id" 
                                        placeholder="Masukkan judul report dalam bahasa Indonesia" 
                                        required 
                                        :class="form.errors.title_id ? 'border-destructive' : ''"
                                    />
                                    <span v-if="form.errors.title_id" class="text-sm text-destructive">{{ form.errors.title_id }}</span>
                                </div>

                                <!-- Title EN -->
                                <div class="space-y-2">
                                    <Label for="title_en">Judul (English)</Label>
                                    <Input 
                                        id="title_en" 
                                        v-model="form.title_en" 
                                        placeholder="Masukkan judul report dalam bahasa Inggris" 
                                        :class="form.errors.title_en ? 'border-destructive' : ''"
                                    />
                                    <span v-if="form.errors.title_en" class="text-sm text-destructive">{{ form.errors.title_en }}</span>
                                </div>

                                <!-- Description ID -->
                                <div class="space-y-2">
                                    <Label for="description_id">Deskripsi (Indonesia)</Label>
                                    <Textarea 
                                        id="description_id" 
                                        v-model="form.description_id" 
                                        placeholder="Masukkan deskripsi report dalam bahasa Indonesia" 
                                        rows="3"
                                        :class="form.errors.description_id ? 'border-destructive' : ''"
                                    />
                                    <span v-if="form.errors.description_id" class="text-sm text-destructive">{{ form.errors.description_id }}</span>
                                </div>

                                <!-- Description EN -->
                                <div class="space-y-2">
                                    <Label for="description_en">Deskripsi (English)</Label>
                                    <Textarea 
                                        id="description_en" 
                                        v-model="form.description_en" 
                                        placeholder="Masukkan deskripsi report dalam bahasa Inggris" 
                                        rows="3"
                                        :class="form.errors.description_en ? 'border-destructive' : ''"
                                    />
                                    <span v-if="form.errors.description_en" class="text-sm text-destructive">{{ form.errors.description_en }}</span>
                                </div>

                                <!-- Year and Period -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label for="year">Tahun *</Label>
                                        <Input 
                                            id="year" 
                                            type="number" 
                                            v-model="form.year" 
                                            :min="1900" 
                                            :max="2100" 
                                            required 
                                            :class="form.errors.year ? 'border-destructive' : ''"
                                        />
                                        <span v-if="form.errors.year" class="text-sm text-destructive">{{ form.errors.year }}</span>
                                    </div>

                                    <div class="space-y-2">
                                        <Label for="period">Periode *</Label>
                                        <Select v-model="form.period">
                                            <option v-for="(label, key) in periods" :key="key" :value="key">
                                                {{ label }}
                                            </option>
                                        </Select>
                                        <span v-if="form.errors.period" class="text-sm text-destructive">{{ form.errors.period }}</span>
                                    </div>
                                </div>

                                <!-- Month (if monthly) -->
                                <div v-if="form.period === 'monthly'" class="space-y-2">
                                    <Label for="month">Bulan</Label>
                                    <Select v-model="form.month">
                                        <option value="">Pilih Bulan</option>
                                        <option v-for="(label, key) in months" :key="key" :value="key">
                                            {{ label }}
                                        </option>
                                    </Select>
                                    <span v-if="form.errors.month" class="text-sm text-destructive">{{ form.errors.month }}</span>
                                </div>

                                <!-- Quarter (if quarterly) -->
                                <div v-if="form.period === 'quarterly'" class="space-y-2">
                                    <Label for="quarter">Triwulan</Label>
                                    <Select v-model="form.quarter">
                                        <option value="">Pilih Triwulan</option>
                                        <option v-for="(label, key) in quarters" :key="key" :value="key">
                                            {{ label }}
                                        </option>
                                    </Select>
                                    <span v-if="form.errors.quarter" class="text-sm text-destructive">{{ form.errors.quarter }}</span>
                                </div>

                                <!-- File Upload -->
                                <div class="space-y-2">
                                    <Label for="file">File PDF {{ !editingReport ? '*' : '' }}</Label>
                                    <div class="space-y-3">
                                        <div class="relative">
                                            <Input 
                                                ref="fileInput"
                                                id="file"
                                                type="file" 
                                                accept=".pdf"
                                                @change="handleFileUpload"
                                                :class="form.errors.file ? 'border-destructive' : ''"
                                                class="file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-primary file:text-primary-foreground hover:file:bg-primary/80 file:cursor-pointer"
                                            />
                                        </div>
                                        
                                        <!-- File info display -->
                                        <div v-if="form.file" class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-900 rounded-md border">
                                            <FileText class="w-5 h-5 text-green-600" />
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ form.file.name }}</p>
                                                <p class="text-xs text-gray-500">{{ (form.file.size / 1024 / 1024).toFixed(2) }} MB</p>
                                            </div>
                                            <Button type="button" variant="ghost" size="sm" @click="removeFile" class="h-8 w-8 p-0 text-gray-500 hover:text-red-600">
                                                <X class="w-4 h-4" />
                                            </Button>
                                        </div>
                                        
                                        <!-- Current file info for edit mode -->
                                        <div v-else-if="editingReport && editingReport.file_name" class="flex items-center gap-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-md border border-blue-200">
                                            <FileText class="w-5 h-5 text-blue-600" />
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-blue-900 dark:text-blue-100">{{ editingReport.file_name }}</p>
                                                <p class="text-xs text-blue-600">{{ editingReport.formatted_file_size }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-muted-foreground">
                                        <strong>Upload file PDF maksimal 10MB</strong> {{ !editingReport ? '(Wajib)' : '(Opsional - biarkan kosong jika tidak ingin mengubah file)' }}
                                        <br>
                                        <span class="text-green-600">✅ Server mendukung upload file sampai 10MB.</span>
                                    </p>
                                    <span v-if="form.errors.file" class="text-sm text-destructive">{{ form.errors.file }}</span>
                                </div>

                                <!-- Published Toggle -->
                                <div class="flex items-center space-x-2">
                                    <Switch v-model:checked="form.is_published" id="is_published" />
                                    <Label for="is_published">Publikasikan report</Label>
                                </div>
                                <span v-if="form.errors.is_published" class="text-sm text-destructive">{{ form.errors.is_published }}</span>
                            </div>

                            <div class="flex justify-end gap-3">
                                <Button type="button" variant="outline" @click="isDialogOpen = false">
                                    Batal
                                </Button>
                                <Button type="button" @click="saveReport" :disabled="form.processing">
                                    <Save class="w-4 h-4 mr-2" />
                                    {{ form.processing ? 'Menyimpan...' : (editingReport ? 'Update' : 'Simpan') }}
                                </Button>
                            </div>
                        </DialogContent>
                    </Dialog>
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

                <!-- Reports List -->
                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <FileText class="w-4 h-4" />
                            Reports - {{ currentTypeName }}
                        </CardTitle>
                        <CardDescription>
                            {{ reports.meta?.total || 0 }} reports total
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div v-if="!reports.data.length" class="text-center py-12">
                            <FileText class="h-12 w-12 mx-auto text-muted-foreground mb-4" />
                            <p class="text-muted-foreground">Belum ada report {{ currentTypeName.toLowerCase() }}.</p>
                        </div>
                        
                        <div v-else class="space-y-4">
                            <div 
                                v-for="report in reports.data" 
                                :key="report.id" 
                                class="border rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-2">
                                            <h3 class="text-lg font-semibold truncate">
                                                {{ report.title_id }}
                                            </h3>
                                            <Badge :variant="getStatusBadge(report).variant" class="text-xs">
                                                <Eye v-if="report.is_published" class="w-3 h-3 mr-1" />
                                                <EyeOff v-else class="w-3 h-3 mr-1" />
                                                {{ getStatusBadge(report).label }}
                                            </Badge>
                                        </div>
                                        
                                        <p v-if="report.description_id" class="text-muted-foreground text-sm mb-3 line-clamp-2">
                                            {{ report.description_id }}
                                        </p>
                                        
                                        <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                                            <div class="flex items-center gap-1">
                                                <Calendar class="h-4 w-4" />
                                                <span>{{ report.year }} - {{ report.period }}</span>
                                            </div>
                                            
                                            <div v-if="report.file_name" class="flex items-center gap-1">
                                                <FileText class="h-4 w-4" />
                                                <span>{{ report.formatted_file_size || 'PDF' }}</span>
                                            </div>
                                            
                                            <div class="flex items-center gap-1">
                                                <span>Dibuat: {{ formatDate(report.created_at) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center gap-2 flex-shrink-0">
                                        <Button
                                            v-if="report.file_path"
                                            variant="outline"
                                            size="sm"
                                            :href="route('reports.download', report.id)"
                                            as="a"
                                            target="_blank"
                                        >
                                            <Download class="h-4 w-4" />
                                        </Button>
                                        
                                        <Button variant="outline" size="sm" @click="openEditDialog(report)">
                                            <Edit class="h-4 w-4" />
                                        </Button>

                                        <Button variant="outline" size="sm" @click="deleteReport(report)">
                                            <Trash2 class="h-4 w-4" />
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Pagination -->
                        <div v-if="reports.links" class="flex justify-center mt-6">
                            <div class="flex gap-2">
                                <template v-for="(link, index) in reports.links" :key="index">
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
    </AppLayout>
</template>