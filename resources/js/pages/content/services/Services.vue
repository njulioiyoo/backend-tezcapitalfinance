<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, reactive, computed, onMounted, nextTick, getCurrentInstance, watch } from 'vue';
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
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
import ConfirmDialog from '@/components/ui/ConfirmDialog.vue';
import RichTextEditor from '@/components/ui/RichTextEditor.vue';
import { toast } from '@/components/ui/toast';
import { Search, Plus, Filter, Edit, Trash2, ExternalLink, Upload, Image, X, Save } from 'lucide-vue-next';

interface DynamicTable {
    headers: string[];
    rows: string[][];
}

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
    interest_list: DynamicTable;
    document_list: DynamicTable;
    fees_list: DynamicTable;
    interest_list_id?: DynamicTable;
    interest_list_en?: DynamicTable;
    document_list_id?: DynamicTable;
    document_list_en?: DynamicTable;
    fees_list_id?: DynamicTable;
    fees_list_en?: DynamicTable;
    status: string;
    is_published: boolean;
    is_featured: boolean;
    show_credit_simulation: boolean;
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

// Computed
const hasCategoriesData = computed(() => Object.keys(props.categories).length > 0);

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
    interest_list: {
        headers: ['Jenis Interest', 'Pribadi/Perorangan (%)', 'Badan Usaha/PT (%)'],
        rows: [['', '', '']]
    } as DynamicTable,
    document_list: {
        headers: ['Document Type', 'Description'],
        rows: [['', '']]
    } as DynamicTable,
    fees_list: {
        headers: ['Jenis Biaya', 'Pribadi/Perorangan', 'Badan Usaha/PT'],
        rows: [['', '', '']]
    } as DynamicTable,
    interest_list_id: {
        headers: ['Jenis Interest', 'Pribadi/Perorangan (%)', 'Badan Usaha/PT (%)'],
        rows: [['', '', '']]
    } as DynamicTable,
    interest_list_en: {
        headers: ['Interest Type', 'Individual (%)', 'Corporate (%)'],
        rows: [['', '', '']]
    } as DynamicTable,
    document_list_id: {
        headers: ['Jenis Dokumen', 'Deskripsi'],
        rows: [['', '']]
    } as DynamicTable,
    document_list_en: {
        headers: ['Document Type', 'Description'],
        rows: [['', '']]
    } as DynamicTable,
    fees_list_id: {
        headers: ['Jenis Biaya', 'Pribadi/Perorangan', 'Badan Usaha/PT'],
        rows: [['', '', '']]
    } as DynamicTable,
    fees_list_en: {
        headers: ['Fee Type', 'Individual', 'Corporate'],
        rows: [['', '', '']]
    } as DynamicTable,
    status: 'draft',
    is_published: false,
    is_featured: false,
    show_credit_simulation: false,
    sort_order: 0,
    meta_title_id: '',
    meta_title_en: '',
    meta_description_id: '',
    meta_description_en: ''
});

// Featured image preview
const featuredImagePreview = ref<string | null>(null);

// Tab states for bilingual dynamic tables
const activeInterestTab = ref('id');
const activeDocumentTab = ref('id');
const activeFeesTab = ref('id');

// Watch for preview changes
watch(featuredImagePreview, (newValue, oldValue) => {
    console.log('Featured image preview changed:', { oldValue, newValue });
}, { immediate: true });

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
        interest_list: {
            headers: ['Jenis Interest', 'Pribadi/Perorangan (%)', 'Badan Usaha/PT (%)'],
            rows: [['', '', '']]
        },
        document_list: {
            headers: ['Document Type', 'Description'],
            rows: [['', '']]
        },
        fees_list: {
            headers: ['Jenis Biaya', 'Pribadi/Perorangan', 'Badan Usaha/PT'],
            rows: [['', '', '']]
        },
        interest_list_id: {
            headers: ['Jenis Interest', 'Pribadi/Perorangan (%)', 'Badan Usaha/PT (%)'],
            rows: [['', '', '']]
        },
        interest_list_en: {
            headers: ['Interest Type', 'Individual (%)', 'Corporate (%)'],
            rows: [['', '', '']]
        },
        document_list_id: {
            headers: ['Jenis Dokumen', 'Deskripsi'],
            rows: [['', '']]
        },
        document_list_en: {
            headers: ['Document Type', 'Description'],
            rows: [['', '']]
        },
        fees_list_id: {
            headers: ['Jenis Biaya', 'Pribadi/Perorangan', 'Badan Usaha/PT'],
            rows: [['', '', '']]
        },
        fees_list_en: {
            headers: ['Fee Type', 'Individual', 'Corporate'],
            rows: [['', '', '']]
        },
        status: 'draft',
        is_published: false,
        is_featured: false,
        show_credit_simulation: false,
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
    
    // Debug: Log the service data structure
    console.log('Service data for edit:', service);
    console.log('Interest table (old):', service.interest_list);
    console.log('Interest table ID:', service.interest_list_id);
    console.log('Interest table EN:', service.interest_list_en);
    console.log('Document table (old):', service.document_list);
    console.log('Document table ID:', service.document_list_id);
    console.log('Document table EN:', service.document_list_en);
    console.log('Fees table (old):', service.fees_list);
    console.log('Fees table ID:', service.fees_list_id);
    console.log('Fees table EN:', service.fees_list_en);
    
    // Clear any previous preview
    featuredImagePreview.value = null;
    
    // Helper function to ensure table data is in correct format
    const ensureTableFormat = (tableData, fallbackData = null) => {
        console.log('ensureTableFormat called with:', tableData, 'fallback:', fallbackData);
        
        // If it's already in the correct format, return as is
        if (tableData && Array.isArray(tableData.headers) && Array.isArray(tableData.rows)) {
            console.log('Using provided tableData');
            return {
                headers: tableData.headers.map(h => String(h || '')),
                rows: tableData.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            };
        }
        
        // If tableData is null/empty but we have fallback data, use that
        if (fallbackData && Array.isArray(fallbackData.headers) && Array.isArray(fallbackData.rows)) {
            console.log('Using fallback data');
            return {
                headers: fallbackData.headers.map(h => String(h || '')),
                rows: fallbackData.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            };
        }
        
        // If both are null or empty, return default
        console.log('Using default empty data');
        return {
            headers: ['Column 1', 'Column 2'],
            rows: [['', '']]
        };
    };
    
    // Process table data and log results
    const processedInterestTable = ensureTableFormat(service.interest_list);
    const processedDocumentTable = ensureTableFormat(service.document_list);
    const processedFeesTable = ensureTableFormat(service.fees_list);
    
    console.log('Processed Interest Table:', processedInterestTable);
    console.log('Processed Document Table:', processedDocumentTable);
    console.log('Processed Fees Table:', processedFeesTable);

    Object.assign(form, {
        title_id: service.title_id || '',
        title_en: service.title_en || '',
        excerpt_id: service.excerpt_id || '',
        excerpt_en: service.excerpt_en || '',
        content_id: service.content_id || '',
        content_en: service.content_en || '',
        category: service.category || '',
        featured_image: null,
        interest_list: processedInterestTable,
        document_list: processedDocumentTable, 
        fees_list: processedFeesTable,
        // Bilingual dynamic tables - use existing data as fallback if bilingual data is empty
        interest_list_id: ensureTableFormat(service.interest_list_id, service.interest_list),
        interest_list_en: ensureTableFormat(service.interest_list_en, service.interest_list),
        document_list_id: ensureTableFormat(service.document_list_id, service.document_list),
        document_list_en: ensureTableFormat(service.document_list_en, service.document_list),
        fees_list_id: ensureTableFormat(service.fees_list_id, service.fees_list),
        fees_list_en: ensureTableFormat(service.fees_list_en, service.fees_list),
        status: service.status || 'draft',
        is_published: service.is_published || false,
        is_featured: service.is_featured || false,
        show_credit_simulation: service.show_credit_simulation || false,
        sort_order: service.sort_order || 0,
        meta_title_id: service.meta_title_id || '',
        meta_title_en: service.meta_title_en || '',
        meta_description_id: service.meta_description_id || '',
        meta_description_en: service.meta_description_en || ''
    });
    
    console.log('Edit modal opened for service:', service.title_id);
    console.log('Current service image:', service.featured_image);
    console.log('Preview cleared:', featuredImagePreview.value);
    console.log('Form after assignment:', {
        interest_list_id: form.interest_list_id,
        interest_list_en: form.interest_list_en,
        document_list_id: form.document_list_id,
        document_list_en: form.document_list_en,
        fees_list_id: form.fees_list_id,
        fees_list_en: form.fees_list_en
    });
    
    console.log('=== DEBUGGING DATA LOADING ===');
    console.log('Service title:', service.title_id);
    console.log('Raw interest_list_id from service:', service.interest_list_id);
    console.log('Type of interest_list_id:', typeof service.interest_list_id);
    console.log('Raw interest_list from service:', service.interest_list);
    console.log('Type of interest_list:', typeof service.interest_list);
    
    showEditModal.value = true;
};

const handleImageUpload = (event: Event) => {
    const input = event.target as HTMLInputElement;
    console.log('Image upload triggered', input.files);
    console.log('Number of files:', input.files?.length);
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        console.log('Selected file:', file.name, file.size, file.type);
        
        form.featured_image = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            const result = e.target?.result as string;
            console.log('FileReader loaded, result length:', result?.length);
            featuredImagePreview.value = result;
            console.log('Preview updated:', !!featuredImagePreview.value);
        };
        reader.onerror = (e) => {
            console.error('FileReader error:', e);
        };
        console.log('Starting FileReader...');
        reader.readAsDataURL(file);
    } else {
        console.log('No file selected');
    }
    
    // Reset input value to allow same file to be selected again
    input.value = '';
};

// Template refs for file inputs
const fileInputEdit = ref<HTMLInputElement>();
const fileInputCreate = ref<HTMLInputElement>();

const triggerImageUpload = () => {
    console.log('Button clicked - triggering file input');
    console.log('Current preview value:', featuredImagePreview.value);
    console.log('fileInputEdit.value:', fileInputEdit.value);
    console.log('typeof fileInputEdit.value:', typeof fileInputEdit.value);
    
    if (fileInputEdit.value) {
        console.log('Calling click() on file input');
        console.log('File input current value:', fileInputEdit.value.value);
        console.log('File input files:', fileInputEdit.value.files);
        
        // Clear the input first to ensure change event fires
        fileInputEdit.value.value = '';
        
        try {
            fileInputEdit.value.click();
            console.log('Click method called successfully');
        } catch (error) {
            console.error('Error calling click():', error);
        }
    } else {
        console.error('File input ref not found');
        console.error('Available refs:', Object.keys(getCurrentInstance()?.refs || {}));
    }
};

const triggerImageUploadCreate = () => {
    console.log('Create button clicked - triggering file input');
    if (fileInputCreate.value) {
        fileInputCreate.value.click();
    } else {
        console.error('Create file input ref not found');
    }
};

const removeImage = () => {
    form.featured_image = null;
    featuredImagePreview.value = null;
};

// Helper functions for dynamic tables
const addTableColumn = (tableType: 'interest' | 'fees', lang?: 'id' | 'en') => {
    if (lang) {
        // Add column to specific language version
        const table = tableType === 'interest' 
            ? (lang === 'id' ? form.interest_list_id : form.interest_list_en)
            : (lang === 'id' ? form.fees_list_id : form.fees_list_en);
        
        table.headers.push('');
        table.rows.forEach(row => {
            row.push('');
        });
    } else {
        // Update both ID and EN versions (backward compatibility)
        const tableId = tableType === 'interest' ? form.interest_list_id : form.fees_list_id;
        const tableEn = tableType === 'interest' ? form.interest_list_en : form.fees_list_en;
        
        tableId.headers.push('');
        tableId.rows.forEach(row => {
            row.push('');
        });
        
        tableEn.headers.push('');
        tableEn.rows.forEach(row => {
            row.push('');
        });
    }
};

const removeTableColumn = (tableType: 'interest' | 'fees', langOrIndex: 'id' | 'en' | number, columnIndex?: number) => {
    if (typeof langOrIndex === 'string' && columnIndex !== undefined) {
        // Remove column from specific language version
        const table = tableType === 'interest' 
            ? (langOrIndex === 'id' ? form.interest_list_id : form.interest_list_en)
            : (langOrIndex === 'id' ? form.fees_list_id : form.fees_list_en);
        
        table.headers.splice(columnIndex, 1);
        table.rows.forEach(row => {
            row.splice(columnIndex, 1);
        });
        
        // If no headers left, reset to empty state
        if (table.headers.length === 0) {
            table.rows = [];
        }
    } else {
        // Backward compatibility: update both versions
        const index = typeof langOrIndex === 'number' ? langOrIndex : columnIndex || 0;
        const tableId = tableType === 'interest' ? form.interest_list_id : form.fees_list_id;
        const tableEn = tableType === 'interest' ? form.interest_list_en : form.fees_list_en;
        
        tableId.headers.splice(index, 1);
        tableId.rows.forEach(row => {
            row.splice(index, 1);
        });
        
        tableEn.headers.splice(index, 1);
        tableEn.rows.forEach(row => {
            row.splice(index, 1);
        });
        
        // If no headers left, reset to empty state
        if (tableId.headers.length === 0) {
            tableId.rows = [];
            tableEn.rows = [];
        }
    }
};

const addTableRow = (tableType: 'interest' | 'fees', lang?: 'id' | 'en') => {
    if (lang) {
        // Add row to specific language version
        const table = tableType === 'interest' 
            ? (lang === 'id' ? form.interest_list_id : form.interest_list_en)
            : (lang === 'id' ? form.fees_list_id : form.fees_list_en);
        
        // Don't add row if there are no headers
        if (table.headers.length === 0) {
            return;
        }
        
        const newRow = new Array(table.headers.length).fill('');
        table.rows.push(newRow);
    } else {
        // Update both ID and EN versions (backward compatibility)
        const tableId = tableType === 'interest' ? form.interest_list_id : form.fees_list_id;
        const tableEn = tableType === 'interest' ? form.interest_list_en : form.fees_list_en;
        
        // Don't add row if there are no headers
        if (tableId.headers.length === 0) {
            return;
        }
        
        const newRowId = new Array(tableId.headers.length).fill('');
        const newRowEn = new Array(tableEn.headers.length).fill('');
        
        tableId.rows.push(newRowId);
        tableEn.rows.push(newRowEn);
    }
};

const removeTableRow = (tableType: 'interest' | 'fees', langOrIndex: 'id' | 'en' | number, rowIndex?: number) => {
    if (typeof langOrIndex === 'string' && rowIndex !== undefined) {
        // Remove row from specific language version
        const table = tableType === 'interest' 
            ? (langOrIndex === 'id' ? form.interest_list_id : form.interest_list_en)
            : (langOrIndex === 'id' ? form.fees_list_id : form.fees_list_en);
        
        if (table.rows.length > 1) {
            table.rows.splice(rowIndex, 1);
        } else {
            table.rows[0] = new Array(table.headers.length).fill('');
        }
    } else {
        // Backward compatibility: update both versions
        const index = typeof langOrIndex === 'number' ? langOrIndex : rowIndex || 0;
        const tableId = tableType === 'interest' ? form.interest_list_id : form.fees_list_id;
        const tableEn = tableType === 'interest' ? form.interest_list_en : form.fees_list_en;
        
        if (tableId.rows.length > 1) {
            tableId.rows.splice(index, 1);
            tableEn.rows.splice(index, 1);
        } else {
            tableId.rows[0] = new Array(tableId.headers.length).fill('');
            tableEn.rows[0] = new Array(tableEn.headers.length).fill('');
        }
    }
};

// Helper functions for document table  
const addDocumentColumn = (lang?: 'id' | 'en') => {
    if (lang) {
        // Add column to specific language version
        const table = lang === 'id' ? form.document_list_id : form.document_list_en;
        table.headers.push('');
        table.rows.forEach(row => {
            row.push('');
        });
    } else {
        // Update both ID and EN versions (backward compatibility)
        form.document_list_id.headers.push('');
        form.document_list_id.rows.forEach(row => {
            row.push('');
        });
        
        form.document_list_en.headers.push('');
        form.document_list_en.rows.forEach(row => {
            row.push('');
        });
    }
};

const removeDocumentColumn = (langOrIndex: 'id' | 'en' | number, columnIndex?: number) => {
    if (typeof langOrIndex === 'string' && columnIndex !== undefined) {
        // Remove column from specific language version
        const table = langOrIndex === 'id' ? form.document_list_id : form.document_list_en;
        
        table.headers.splice(columnIndex, 1);
        table.rows.forEach(row => {
            row.splice(columnIndex, 1);
        });
        
        // If no headers left, reset to empty state
        if (table.headers.length === 0) {
            table.rows = [];
        }
    } else {
        // Backward compatibility: update both versions
        const index = typeof langOrIndex === 'number' ? langOrIndex : columnIndex || 0;
        
        form.document_list_id.headers.splice(index, 1);
        form.document_list_id.rows.forEach(row => {
            row.splice(index, 1);
        });
        
        form.document_list_en.headers.splice(index, 1);
        form.document_list_en.rows.forEach(row => {
            row.splice(index, 1);
        });
        
        // If no headers left, reset to empty state
        if (form.document_list_id.headers.length === 0) {
            form.document_list_id.rows = [];
            form.document_list_en.rows = [];
        }
    }
};

const addDocumentRow = (lang?: 'id' | 'en') => {
    if (lang) {
        // Add row to specific language version
        const table = lang === 'id' ? form.document_list_id : form.document_list_en;
        
        // Don't add row if there are no headers
        if (table.headers.length === 0) {
            return;
        }
        
        const newRow = new Array(table.headers.length).fill('');
        table.rows.push(newRow);
    } else {
        // Don't add row if there are no headers (backward compatibility)
        if (form.document_list_id.headers.length === 0) {
            return;
        }
        
        const newRowId = new Array(form.document_list_id.headers.length).fill('');
        const newRowEn = new Array(form.document_list_en.headers.length).fill('');
        
        form.document_list_id.rows.push(newRowId);
        form.document_list_en.rows.push(newRowEn);
    }
};

const removeDocumentRow = (langOrIndex: 'id' | 'en' | number, rowIndex?: number) => {
    if (typeof langOrIndex === 'string' && rowIndex !== undefined) {
        // Remove row from specific language version
        const table = langOrIndex === 'id' ? form.document_list_id : form.document_list_en;
        
        if (table.rows.length > 1) {
            table.rows.splice(rowIndex, 1);
        } else {
            table.rows[0] = new Array(table.headers.length).fill('');
        }
    } else {
        // Backward compatibility: update both versions
        const index = typeof langOrIndex === 'number' ? langOrIndex : rowIndex || 0;
        
        if (form.document_list_id.rows.length > 1) {
            form.document_list_id.rows.splice(index, 1);
            form.document_list_en.rows.splice(index, 1);
        } else {
            form.document_list_id.rows[0] = new Array(form.document_list_id.headers.length).fill('');
            form.document_list_en.rows[0] = new Array(form.document_list_en.headers.length).fill('');
        }
    }
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
        console.log('Original form data:', form);
        const cleanFormData = {
            type: 'service',
            title_id: form.title_id.trim(),
            title_en: form.title_en.trim(),
            excerpt_id: form.excerpt_id?.trim() || '',
            excerpt_en: form.excerpt_en?.trim() || '',
            content_id: form.content_id?.trim() || '',
            content_en: form.content_en?.trim() || '',
            category: form.category || '',
            interest_list: {
                headers: form.interest_list.headers.map(h => String(h || '')),
                rows: form.interest_list.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            },
            document_list: {
                headers: form.document_list.headers.map(h => String(h || '')),
                rows: form.document_list.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            },
            fees_list: {
                headers: form.fees_list.headers.map(h => String(h || '')),
                rows: form.fees_list.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            },
            // Bilingual dynamic tables
            interest_list_id: {
                headers: form.interest_list_id.headers.map(h => String(h || '')),
                rows: form.interest_list_id.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            },
            interest_list_en: {
                headers: form.interest_list_en.headers.map(h => String(h || '')),
                rows: form.interest_list_en.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            },
            document_list_id: {
                headers: form.document_list_id.headers.map(h => String(h || '')),
                rows: form.document_list_id.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            },
            document_list_en: {
                headers: form.document_list_en.headers.map(h => String(h || '')),
                rows: form.document_list_en.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            },
            fees_list_id: {
                headers: form.fees_list_id.headers.map(h => String(h || '')),
                rows: form.fees_list_id.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            },
            fees_list_en: {
                headers: form.fees_list_en.headers.map(h => String(h || '')),
                rows: form.fees_list_en.rows.map(row => 
                    Array.isArray(row) ? row.map(cell => String(cell || '')) : ['']
                )
            },
            status: form.status || 'draft',
            is_published: form.is_published ? '1' : '0',
            is_featured: form.is_featured ? '1' : '0',
            show_credit_simulation: form.show_credit_simulation ? '1' : '0',
            sort_order: String(parseInt(form.sort_order) || 0),
            meta_title_id: form.meta_title_id?.trim() || '',
            meta_title_en: form.meta_title_en?.trim() || '',
            meta_description_id: form.meta_description_id?.trim() || '',
            meta_description_en: form.meta_description_en?.trim() || ''
        };
        
        console.log('Clean form data:', cleanFormData);

        // Ensure all table data is properly formatted as strings
        const processTableData = (tableData) => {
            if (!tableData || !tableData.headers || !tableData.rows) {
                return { headers: [], rows: [] };
            }
            
            return {
                headers: (tableData.headers || []).map(h => String(h || '')),
                rows: (tableData.rows || []).map(row => {
                    if (!Array.isArray(row)) return [''];
                    return row.map(cell => String(cell || ''));
                })
            };
        };

        // Override table data with processed versions
        cleanFormData.interest_list = processTableData(cleanFormData.interest_list);
        cleanFormData.document_list = processTableData(cleanFormData.document_list);
        cleanFormData.fees_list = processTableData(cleanFormData.fees_list);
        // Process bilingual dynamic tables
        cleanFormData.interest_list_id = processTableData(cleanFormData.interest_list_id);
        cleanFormData.interest_list_en = processTableData(cleanFormData.interest_list_en);
        cleanFormData.document_list_id = processTableData(cleanFormData.document_list_id);
        cleanFormData.document_list_en = processTableData(cleanFormData.document_list_en);
        cleanFormData.fees_list_id = processTableData(cleanFormData.fees_list_id);
        cleanFormData.fees_list_en = processTableData(cleanFormData.fees_list_en);

        console.log('Final processed form data:', cleanFormData);
        
        // Convert table data to JSON strings to avoid Inertia.js issues
        cleanFormData.interest_list = JSON.stringify(cleanFormData.interest_list);
        cleanFormData.document_list = JSON.stringify(cleanFormData.document_list);
        cleanFormData.fees_list = JSON.stringify(cleanFormData.fees_list);
        // Convert bilingual dynamic tables to JSON strings
        cleanFormData.interest_list_id = JSON.stringify(cleanFormData.interest_list_id);
        cleanFormData.interest_list_en = JSON.stringify(cleanFormData.interest_list_en);
        cleanFormData.document_list_id = JSON.stringify(cleanFormData.document_list_id);
        cleanFormData.document_list_en = JSON.stringify(cleanFormData.document_list_en);
        cleanFormData.fees_list_id = JSON.stringify(cleanFormData.fees_list_id);
        cleanFormData.fees_list_en = JSON.stringify(cleanFormData.fees_list_en);
        
        console.log('JSON stringified form data:', cleanFormData);

        // Add image if present
        if (form.featured_image) {
            cleanFormData.featured_image = form.featured_image;
        }


        if (currentService.value) {
            // Update existing service
            if (form.featured_image) {
                // Create custom FormData to ensure all fields are included
                const formData = new FormData();
                
                console.log('Using FormData for update with file');
                
                // Add all text fields explicitly
                Object.keys(cleanFormData).forEach(key => {
                    if (key !== 'featured_image') {
                        if (key === 'interest_list' || key === 'document_list' || key === 'fees_list') {
                            // Handle dynamic table objects
                            const tableData = cleanFormData[key];
                            if (tableData && typeof tableData === 'object') {
                                // Add headers
                                if (tableData.headers && Array.isArray(tableData.headers)) {
                                    tableData.headers.forEach((header, index) => {
                                        formData.append(`${key}[headers][${index}]`, String(header));
                                    });
                                }
                                // Add rows
                                if (tableData.rows && Array.isArray(tableData.rows)) {
                                    tableData.rows.forEach((row, rowIndex) => {
                                        if (Array.isArray(row)) {
                                            row.forEach((cell, cellIndex) => {
                                                formData.append(`${key}[rows][${rowIndex}][${cellIndex}]`, String(cell));
                                            });
                                        }
                                    });
                                }
                            }
                        } else if (Array.isArray(cleanFormData[key])) {
                            // Handle other arrays
                            cleanFormData[key].forEach((item, index) => {
                                formData.append(`${key}[${index}]`, String(item));
                            });
                        } else {
                            formData.append(key, String(cleanFormData[key] || ''));
                        }
                    }
                });
                
                // Add the file
                formData.append('featured_image', form.featured_image);
                formData.append('_method', 'PUT');
                
                router.post(route('content.services.update', currentService.value.id), formData, {
                    onSuccess: (page) => {
                        console.log('Update success response:', page);
                        toast({
                            title: 'Success',
                            description: 'Service updated successfully',
                            variant: 'success'
                        });
                        
                        showEditModal.value = false;
                        resetForm();
                        
                        // Refresh data to show changes
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
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
                console.log('Using JSON for update without file');
                console.log('cleanFormData to send:', JSON.stringify(cleanFormData, null, 2));
                router.put(route('content.services.update', currentService.value.id), cleanFormData, {
                    onSuccess: (page) => {
                        console.log('Update success response (no file):', page);
                        toast({
                            title: 'Success',
                            description: 'Service updated successfully',
                            variant: 'success'
                        });
                        
                        showEditModal.value = false;
                        resetForm();
                        
                        // Refresh data to show changes
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
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
                        if (key === 'interest_list' || key === 'document_list' || key === 'fees_list') {
                            // Handle dynamic table objects
                            const tableData = cleanFormData[key];
                            if (tableData && typeof tableData === 'object') {
                                // Add headers
                                if (tableData.headers && Array.isArray(tableData.headers)) {
                                    tableData.headers.forEach((header, index) => {
                                        formData.append(`${key}[headers][${index}]`, String(header));
                                    });
                                }
                                // Add rows
                                if (tableData.rows && Array.isArray(tableData.rows)) {
                                    tableData.rows.forEach((row, rowIndex) => {
                                        if (Array.isArray(row)) {
                                            row.forEach((cell, cellIndex) => {
                                                formData.append(`${key}[rows][${rowIndex}][${cellIndex}]`, String(cell));
                                            });
                                        }
                                    });
                                }
                            }
                        } else if (Array.isArray(cleanFormData[key])) {
                            // Handle other arrays
                            cleanFormData[key].forEach((item, index) => {
                                formData.append(`${key}[${index}]`, String(item));
                            });
                        } else {
                            formData.append(key, String(cleanFormData[key] || ''));
                        }
                    }
                });
                
                // Add the file
                formData.append('featured_image', form.featured_image);
                
                router.post(route('content.services.store'), formData, {
                    onSuccess: (page) => {
                        console.log('Create success response:', page);
                        toast({
                            title: 'Success',
                            description: 'Service created successfully',
                            variant: 'success'
                        });
                        showCreateModal.value = false;
                        resetForm();
                        
                        // Refresh data to show new service
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
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
                console.log('Using JSON for create without file');
                console.log('cleanFormData to send:', JSON.stringify(cleanFormData, null, 2));
                router.post(route('content.services.store'), cleanFormData, {
                    onSuccess: (page) => {
                        console.log('Create success response (no file):', page);
                        toast({
                            title: 'Success',
                            description: 'Service created successfully',
                            variant: 'success'
                        });
                        showCreateModal.value = false;
                        resetForm();
                        
                        // Refresh data to show new service
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
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

// Separate submit function for edit modal to prevent conflicts
const submitEditForm = async () => {
    // Just call the main submitForm function but ensure we're in edit mode
    if (!currentService.value) {
        toast({
            title: 'Error',
            description: 'No service selected for editing',
            variant: 'error'
        });
        return;
    }
    
    await submitForm();
};

const confirmDelete = (service: Service) => {
    currentService.value = service;
    showDeleteConfirm.value = true;
};

const deleteService = async () => {
    if (!currentService.value || isSubmitting.value) return;
    
    const serviceIdToDelete = currentService.value.id;
    isSubmitting.value = true;
    
    try {
        router.delete(route('content.services.destroy', serviceIdToDelete), {
            onSuccess: (page) => {
                // Show success message
                toast({
                    title: 'Success',
                    description: 'Service deleted successfully',
                    variant: 'success'
                });
                
                // Close dialog and reset state
                showDeleteConfirm.value = false;
                currentService.value = null;
                
                // Simple page refresh to show updated data
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            },
            onError: (errors) => {
                console.log('Delete errors:', errors);
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
        console.log('Error deleting service:', error);
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
    if (filters.category && Object.keys(props.categories).length > 0) params.set('category', filters.category);
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
                            
                            <div v-if="Object.keys(categories).length > 0" class="space-y-2">
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
                                    <Badge v-if="service.category" :variant="service.is_featured ? 'default' : 'secondary'">
                                        {{ service.category }}
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

                    <!-- Status -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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


                    <!-- Interest Rate Table -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <Label>Interest Rate Table</Label>
                            <div class="flex gap-2">
                                <Button type="button" size="sm" variant="outline" @click="addTableColumn('interest')">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Add Column
                                </Button>
                                <Button type="button" size="sm" variant="outline" @click="addTableRow('interest')">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Add Row
                                </Button>
                            </div>
                        </div>
                        
                        <!-- Language Tabs -->
                        <Tabs v-model:modelValue="activeInterestTab" class="w-full">
                            <TabsList class="grid w-full grid-cols-2">
                                <TabsTrigger value="id">Bahasa Indonesia</TabsTrigger>
                                <TabsTrigger value="en">English</TabsTrigger>
                            </TabsList>
                        </Tabs>
                        
                        <!-- Indonesian Table -->
                        <div v-show="activeInterestTab === 'id'" class="space-y-2">
                            <h4 class="font-medium text-sm text-gray-700">Tabel Interest Rate (Bahasa Indonesia)</h4>
                            <!-- Table Headers -->
                            <div v-if="form.interest_list_id.headers.length > 0" class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.interest_list_id.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.interest_list_id.headers" :key="`interest-id-header-${headerIndex}`">
                                    {{ header || `Column ${headerIndex + 1}` }}
                                </div>
                            </div>
                            
                            <!-- Header Edit Row -->
                            <div v-if="form.interest_list_id.headers.length > 0" class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.interest_list_id.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.interest_list_id.headers" :key="`interest-id-header-${headerIndex}`" class="space-y-1">
                                    <Input
                                        v-model="form.interest_list_id.headers[headerIndex]"
                                        :placeholder="`Header ${headerIndex + 1} (ID)`"
                                        class="text-xs"
                                    />
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="outline"
                                        @click="removeTableColumn('interest', headerIndex)"
                                        class="w-full text-red-600 hover:text-red-700 text-xs"
                                    >
                                        Remove Column
                                    </Button>
                                </div>
                            </div>
                            
                            <!-- Table Rows -->
                            <div v-for="(row, rowIndex) in form.interest_list_id.rows" :key="`interest-id-row-${rowIndex}`" class="flex gap-2 items-center">
                                <div class="grid gap-2 items-center flex-1" 
                                     :style="{ gridTemplateColumns: `repeat(${form.interest_list_id.headers.length}, 1fr)` }">
                                    <Input
                                        v-for="(cell, cellIndex) in row"
                                        :key="`interest-id-cell-${rowIndex}-${cellIndex}`"
                                        v-model="form.interest_list_id.rows[rowIndex][cellIndex]"
                                        :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                    />
                                </div>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="removeTableRow('interest', rowIndex)"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                    title="Remove Row"
                                >
                                    <X class="w-4 h-4" />
                                </Button>
                            </div>
                            
                            <p v-if="form.interest_list_id.headers.length === 0 || form.interest_list_id.rows.length === 0" class="text-sm text-muted-foreground">
                                No interest items added yet.
                            </p>
                        </div>

                        <!-- English Table -->
                        <div v-show="activeInterestTab === 'en'" class="space-y-2">
                            <h4 class="font-medium text-sm text-gray-700">Interest Rate Table (English)</h4>
                            <!-- Table Headers -->
                            <div v-if="form.interest_list_en.headers.length > 0" class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.interest_list_en.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.interest_list_en.headers" :key="`interest-en-header-${headerIndex}`">
                                    {{ header || `Column ${headerIndex + 1}` }}
                                </div>
                            </div>
                            
                            <!-- Header Edit Row -->
                            <div v-if="form.interest_list_en.headers.length > 0" class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.interest_list_en.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.interest_list_en.headers" :key="`interest-en-header-${headerIndex}`" class="space-y-1">
                                    <Input
                                        v-model="form.interest_list_en.headers[headerIndex]"
                                        :placeholder="`Header ${headerIndex + 1} (EN)`"
                                        class="text-xs"
                                    />
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="outline"
                                        @click="removeTableColumn('interest', headerIndex)"
                                        class="w-full text-red-600 hover:text-red-700 text-xs"
                                    >
                                        Remove Column
                                    </Button>
                                </div>
                            </div>
                            
                            <!-- Table Rows -->
                            <div v-for="(row, rowIndex) in form.interest_list_en.rows" :key="`interest-en-row-${rowIndex}`" class="flex gap-2 items-center">
                                <div class="grid gap-2 items-center flex-1" 
                                     :style="{ gridTemplateColumns: `repeat(${form.interest_list_en.headers.length}, 1fr)` }">
                                    <Input
                                        v-for="(cell, cellIndex) in row"
                                        :key="`interest-en-cell-${rowIndex}-${cellIndex}`"
                                        v-model="form.interest_list_en.rows[rowIndex][cellIndex]"
                                        :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                    />
                                </div>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="removeTableRow('interest', rowIndex)"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                    title="Remove Row"
                                >
                                    <X class="w-4 h-4" />
                                </Button>
                            </div>
                            
                            <p v-if="form.interest_list_en.headers.length === 0 || form.interest_list_en.rows.length === 0" class="text-sm text-muted-foreground">
                                No interest items added yet.
                            </p>
                        </div>
                    </div>

                    <!-- Required Documents Table -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <Label>Required Documents Table</Label>
                            <div class="flex gap-2">
                                <Button type="button" size="sm" variant="outline" @click="addDocumentColumn">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Add Column
                                </Button>
                                <Button type="button" size="sm" variant="outline" @click="addDocumentRow">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Add Row
                                </Button>
                            </div>
                        </div>
                        
                        <!-- Language Tabs -->
                        <Tabs v-model:modelValue="activeDocumentTab" class="w-full">
                            <TabsList class="grid w-full grid-cols-2">
                                <TabsTrigger value="id">Bahasa Indonesia</TabsTrigger>
                                <TabsTrigger value="en">English</TabsTrigger>
                            </TabsList>
                        </Tabs>
                        
                        <!-- Indonesian Table -->
                        <div v-show="!bilingualEnabled || activeDocumentTab === 'id'" class="space-y-2">
                            <h4 v-if="bilingualEnabled" class="font-medium text-sm text-gray-700">Tabel Dokumen Persyaratan (Bahasa Indonesia)</h4>
                            <!-- Table Headers -->
                            <div v-if="form.document_list_id.headers.length > 0" class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.document_list_id.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.document_list_id.headers" :key="`document-id-header-${headerIndex}`">
                                    {{ header || `Column ${headerIndex + 1}` }}
                                </div>
                            </div>
                            
                            <!-- Header Edit Row -->
                            <div v-if="form.document_list_id.headers.length > 0" class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.document_list_id.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.document_list_id.headers" :key="`document-id-header-${headerIndex}`" class="space-y-1">
                                    <Input
                                        v-model="form.document_list_id.headers[headerIndex]"
                                        :placeholder="`Header ${headerIndex + 1} (ID)`"
                                        class="text-xs"
                                    />
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="outline"
                                        @click="removeDocumentColumn(headerIndex)"
                                        class="w-full text-red-600 hover:text-red-700 text-xs"
                                    >
                                        Remove Column
                                    </Button>
                                </div>
                            </div>
                            
                            <!-- Table Rows -->
                            <div v-for="(row, rowIndex) in form.document_list_id.rows" :key="`document-id-row-${rowIndex}`" class="flex gap-2 items-center">
                                <div class="grid gap-2 items-center flex-1" 
                                     :style="{ gridTemplateColumns: `repeat(${form.document_list_id.headers.length}, 1fr)` }">
                                    <Input
                                        v-for="(cell, cellIndex) in row"
                                        :key="`document-id-cell-${rowIndex}-${cellIndex}`"
                                        v-model="form.document_list_id.rows[rowIndex][cellIndex]"
                                        :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                    />
                                </div>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="removeDocumentRow(rowIndex)"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                    title="Remove Row"
                                >
                                    <X class="w-4 h-4" />
                                </Button>
                            </div>
                            
                            <p v-if="form.document_list_id.headers.length === 0 || form.document_list_id.rows.length === 0" class="text-sm text-muted-foreground">
                                No document items added yet.
                            </p>
                        </div>

                        <!-- English Table -->
                        <div v-if="bilingualEnabled" v-show="activeDocumentTab === 'en'" class="space-y-2">
                            <h4 class="font-medium text-sm text-gray-700">Required Documents Table (English)</h4>
                            <!-- Table Headers -->
                            <div v-if="form.document_list_en.headers.length > 0" class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.document_list_en.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.document_list_en.headers" :key="`document-en-header-${headerIndex}`">
                                    {{ header || `Column ${headerIndex + 1}` }}
                                </div>
                            </div>
                            
                            <!-- Header Edit Row -->
                            <div v-if="form.document_list_en.headers.length > 0" class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.document_list_en.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.document_list_en.headers" :key="`document-en-header-${headerIndex}`" class="space-y-1">
                                    <Input
                                        v-model="form.document_list_en.headers[headerIndex]"
                                        :placeholder="`Header ${headerIndex + 1} (EN)`"
                                        class="text-xs"
                                    />
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="outline"
                                        @click="removeDocumentColumn(headerIndex)"
                                        class="w-full text-red-600 hover:text-red-700 text-xs"
                                    >
                                        Remove Column
                                    </Button>
                                </div>
                            </div>
                            
                            <!-- Table Rows -->
                            <div v-for="(row, rowIndex) in form.document_list_en.rows" :key="`document-en-row-${rowIndex}`" class="flex gap-2 items-center">
                                <div class="grid gap-2 items-center flex-1" 
                                     :style="{ gridTemplateColumns: `repeat(${form.document_list_en.headers.length}, 1fr)` }">
                                    <Input
                                        v-for="(cell, cellIndex) in row"
                                        :key="`document-en-cell-${rowIndex}-${cellIndex}`"
                                        v-model="form.document_list_en.rows[rowIndex][cellIndex]"
                                        :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                    />
                                </div>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="removeDocumentRow(rowIndex)"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                    title="Remove Row"
                                >
                                    <X class="w-4 h-4" />
                                </Button>
                            </div>
                            
                            <p v-if="form.document_list_en.headers.length === 0 || form.document_list_en.rows.length === 0" class="text-sm text-muted-foreground">
                                No document items added yet.
                            </p>
                        </div>
                    </div>

                    <!-- Fees List Table -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <Label>Fees List Table</Label>
                            <div class="flex gap-2">
                                <Button type="button" size="sm" variant="outline" @click="addTableColumn('fees')">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Add Column
                                </Button>
                                <Button type="button" size="sm" variant="outline" @click="addTableRow('fees')">
                                    <Plus class="h-4 w-4 mr-2" />
                                    Add Row
                                </Button>
                            </div>
                        </div>
                        
                        <!-- Language Tabs -->
                        <Tabs v-model:modelValue="activeFeesTab" class="w-full">
                            <TabsList class="grid w-full grid-cols-2">
                                <TabsTrigger value="id">Bahasa Indonesia</TabsTrigger>
                                <TabsTrigger value="en">English</TabsTrigger>
                            </TabsList>
                        </Tabs>
                        
                        <!-- Indonesian Table -->
                        <div v-show="!bilingualEnabled || activeFeesTab === 'id'" class="space-y-2">
                            <h4 v-if="bilingualEnabled" class="font-medium text-sm text-gray-700">Tabel Biaya (Bahasa Indonesia)</h4>
                            <!-- Table Headers -->
                            <div v-if="form.fees_list_id.headers.length > 0" class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.fees_list_id.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.fees_list_id.headers" :key="`fees-id-header-${headerIndex}`">
                                    {{ header || `Column ${headerIndex + 1}` }}
                                </div>
                            </div>
                            
                            <!-- Header Edit Row -->
                            <div v-if="form.fees_list_id.headers.length > 0" class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.fees_list_id.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.fees_list_id.headers" :key="`fees-id-header-${headerIndex}`" class="space-y-1">
                                    <Input
                                        v-model="form.fees_list_id.headers[headerIndex]"
                                        :placeholder="`Header ${headerIndex + 1} (ID)`"
                                        class="text-xs"
                                    />
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="outline"
                                        @click="removeTableColumn('fees', headerIndex)"
                                        class="w-full text-red-600 hover:text-red-700 text-xs"
                                    >
                                        Remove Column
                                    </Button>
                                </div>
                            </div>
                            
                            <!-- Table Rows -->
                            <div v-for="(row, rowIndex) in form.fees_list_id.rows" :key="`fees-id-row-${rowIndex}`" class="flex gap-2 items-center">
                                <div class="grid gap-2 items-center flex-1" 
                                     :style="{ gridTemplateColumns: `repeat(${form.fees_list_id.headers.length}, 1fr)` }">
                                    <Input
                                        v-for="(cell, cellIndex) in row"
                                        :key="`fees-id-cell-${rowIndex}-${cellIndex}`"
                                        v-model="form.fees_list_id.rows[rowIndex][cellIndex]"
                                        :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                    />
                                </div>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="removeTableRow('fees', rowIndex)"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                    title="Remove Row"
                                >
                                    <X class="w-4 h-4" />
                                </Button>
                            </div>
                            
                            <p v-if="form.fees_list_id.headers.length === 0 || form.fees_list_id.rows.length === 0" class="text-sm text-muted-foreground">
                                No fee items added yet.
                            </p>
                        </div>

                        <!-- English Table -->
                        <div v-if="bilingualEnabled" v-show="activeFeesTab === 'en'" class="space-y-2">
                            <h4 class="font-medium text-sm text-gray-700">Fees List Table (English)</h4>
                            <!-- Table Headers -->
                            <div v-if="form.fees_list_en.headers.length > 0" class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.fees_list_en.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.fees_list_en.headers" :key="`fees-en-header-${headerIndex}`">
                                    {{ header || `Column ${headerIndex + 1}` }}
                                </div>
                            </div>
                            
                            <!-- Header Edit Row -->
                            <div v-if="form.fees_list_en.headers.length > 0" class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.fees_list_en.headers.length}, 1fr)` }">
                                <div v-for="(header, headerIndex) in form.fees_list_en.headers" :key="`fees-en-header-${headerIndex}`" class="space-y-1">
                                    <Input
                                        v-model="form.fees_list_en.headers[headerIndex]"
                                        :placeholder="`Header ${headerIndex + 1} (EN)`"
                                        class="text-xs"
                                    />
                                    <Button
                                        type="button"
                                        size="sm"
                                        variant="outline"
                                        @click="removeTableColumn('fees', headerIndex)"
                                        class="w-full text-red-600 hover:text-red-700 text-xs"
                                    >
                                        Remove Column
                                    </Button>
                                </div>
                            </div>
                            
                            <!-- Table Rows -->
                            <div v-for="(row, rowIndex) in form.fees_list_en.rows" :key="`fees-en-row-${rowIndex}`" class="flex gap-2 items-center">
                                <div class="grid gap-2 items-center flex-1" 
                                     :style="{ gridTemplateColumns: `repeat(${form.fees_list_en.headers.length}, 1fr)` }">
                                    <Input
                                        v-for="(cell, cellIndex) in row"
                                        :key="`fees-en-cell-${rowIndex}-${cellIndex}`"
                                        v-model="form.fees_list_en.rows[rowIndex][cellIndex]"
                                        :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                    />
                                </div>
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="removeTableRow('fees', rowIndex)"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                    title="Remove Row"
                                >
                                    <X class="w-4 h-4" />
                                </Button>
                            </div>
                            
                            <p v-if="form.fees_list_en.headers.length === 0 || form.fees_list_en.rows.length === 0" class="text-sm text-muted-foreground">
                                No fee items added yet.
                            </p>
                        </div>
                    </div>


                    <!-- Featured Image -->
                    <div class="space-y-2">
                        <Label>Featured Image</Label>
                        <div class="space-y-4">
                            <!-- Custom Upload Button -->
                            <div class="flex items-center gap-4">
                                <input
                                    ref="fileInputCreate"
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageUpload"
                                    class="hidden"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="triggerImageUploadCreate"
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
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" v-model="form.is_published" id="is_published" class="rounded border-gray-300" />
                            <Label for="is_published">Published</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" v-model="form.is_featured" id="is_featured" class="rounded border-gray-300" />
                            <Label for="is_featured">Featured</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" v-model="form.show_credit_simulation" id="show_credit_simulation" class="rounded border-gray-300" />
                            <Label for="show_credit_simulation">Show Credit Simulation</Label>
                        </div>
                    </div>
                </form>
                
                <DialogFooter>
                    <Button type="button" variant="outline" @click="showCreateModal = false">
                        Cancel
                    </Button>
                    <Button type="button" @click="submitForm" :disabled="isSubmitting">
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
                
                <form @submit.prevent="submitEditForm" class="space-y-6">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                    <!-- Interest Rate Table -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label>Interest Rate Table</Label>
                        </div>
                        
                        <!-- Language Tabs -->
                        <Tabs v-model:modelValue="activeInterestTab" class="w-full">
                            <TabsList class="grid w-full grid-cols-2">
                                <TabsTrigger value="id">Bahasa Indonesia</TabsTrigger>
                                <TabsTrigger value="en">English</TabsTrigger>
                            </TabsList>
                            
                            <!-- Indonesian Version -->
                            <TabsContent value="id" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm text-muted-foreground">Indonesian Version</Label>
                                    <div class="flex gap-2">
                                        <Button type="button" size="sm" variant="outline" @click="addTableColumn('interest', 'id')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Column
                                        </Button>
                                        <Button type="button" size="sm" variant="outline" @click="addTableRow('interest', 'id')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Row
                                        </Button>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <!-- Table Headers -->
                                    <div class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.interest_list_id.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.interest_list_id.headers" :key="`edit-interest-id-header-${headerIndex}`">
                                            {{ header || `Column ${headerIndex + 1}` }}
                                        </div>
                                    </div>
                                    
                                    <!-- Header Edit Row -->
                                    <div class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.interest_list_id.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.interest_list_id.headers" :key="`edit-interest-id-header-${headerIndex}`" class="space-y-1">
                                            <Input
                                                v-model="form.interest_list_id.headers[headerIndex]"
                                                :placeholder="`Header ${headerIndex + 1}`"
                                                class="text-xs"
                                            />
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="outline"
                                                @click="removeTableColumn('interest', 'id', headerIndex)"
                                                class="w-full text-red-600 hover:text-red-700 text-xs"
                                            >
                                                Remove Column
                                            </Button>
                                        </div>
                                    </div>
                                    
                                    <!-- Table Rows -->
                                    <div v-for="(row, rowIndex) in form.interest_list_id.rows" :key="`edit-interest-id-row-${rowIndex}`" class="flex gap-2 items-center">
                                        <div class="grid gap-2 items-center flex-1" 
                                             :style="{ gridTemplateColumns: `repeat(${form.interest_list_id.headers.length}, 1fr)` }">
                                            <Input
                                                v-for="(cell, cellIndex) in row"
                                                :key="`edit-interest-id-cell-${rowIndex}-${cellIndex}`"
                                                v-model="form.interest_list_id.rows[rowIndex][cellIndex]"
                                                :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                            />
                                        </div>
                                        <Button
                                            type="button"
                                            size="sm"
                                            variant="outline"
                                            @click="removeTableRow('interest', 'id', rowIndex)"
                                            class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                            title="Remove Row"
                                        >
                                            <X class="w-4 h-4" />
                                        </Button>
                                    </div>
                                    
                                    <p v-if="form.interest_list_id.headers.length === 0 || form.interest_list_id.rows.length === 0" class="text-sm text-muted-foreground">
                                        No interest items added yet.
                                    </p>
                                </div>
                            </TabsContent>
                            
                            <!-- English Version -->
                            <TabsContent value="en" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm text-muted-foreground">English Version</Label>
                                    <div class="flex gap-2">
                                        <Button type="button" size="sm" variant="outline" @click="addTableColumn('interest', 'en')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Column
                                        </Button>
                                        <Button type="button" size="sm" variant="outline" @click="addTableRow('interest', 'en')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Row
                                        </Button>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <!-- Table Headers -->
                                    <div class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.interest_list_en.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.interest_list_en.headers" :key="`edit-interest-en-header-${headerIndex}`">
                                            {{ header || `Column ${headerIndex + 1}` }}
                                        </div>
                                    </div>
                                    
                                    <!-- Header Edit Row -->
                                    <div class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.interest_list_en.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.interest_list_en.headers" :key="`edit-interest-en-header-${headerIndex}`" class="space-y-1">
                                            <Input
                                                v-model="form.interest_list_en.headers[headerIndex]"
                                                :placeholder="`Header ${headerIndex + 1}`"
                                                class="text-xs"
                                            />
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="outline"
                                                @click="removeTableColumn('interest', 'en', headerIndex)"
                                                class="w-full text-red-600 hover:text-red-700 text-xs"
                                            >
                                                Remove Column
                                            </Button>
                                        </div>
                                    </div>
                                    
                                    <!-- Table Rows -->
                                    <div v-for="(row, rowIndex) in form.interest_list_en.rows" :key="`edit-interest-en-row-${rowIndex}`" class="flex gap-2 items-center">
                                        <div class="grid gap-2 items-center flex-1" 
                                             :style="{ gridTemplateColumns: `repeat(${form.interest_list_en.headers.length}, 1fr)` }">
                                            <Input
                                                v-for="(cell, cellIndex) in row"
                                                :key="`edit-interest-en-cell-${rowIndex}-${cellIndex}`"
                                                v-model="form.interest_list_en.rows[rowIndex][cellIndex]"
                                                :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                            />
                                        </div>
                                        <Button
                                            type="button"
                                            size="sm"
                                            variant="outline"
                                            @click="removeTableRow('interest', 'en', rowIndex)"
                                            class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                            title="Remove Row"
                                        >
                                            <X class="w-4 h-4" />
                                        </Button>
                                    </div>
                                    
                                    <p v-if="form.interest_list_en.headers.length === 0 || form.interest_list_en.rows.length === 0" class="text-sm text-muted-foreground">
                                        No interest items added yet.
                                    </p>
                                </div>
                            </TabsContent>
                        </Tabs>
                    </div>

                    <!-- Required Documents Table -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label>Required Documents Table</Label>
                        </div>
                        
                        <!-- Language Tabs -->
                        <Tabs v-model:modelValue="activeDocumentTab" class="w-full">
                            <TabsList class="grid w-full grid-cols-2">
                                <TabsTrigger value="id">Bahasa Indonesia</TabsTrigger>
                                <TabsTrigger value="en">English</TabsTrigger>
                            </TabsList>
                            
                            <!-- Indonesian Version -->
                            <TabsContent value="id" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm text-muted-foreground">Indonesian Version</Label>
                                    <div class="flex gap-2">
                                        <Button type="button" size="sm" variant="outline" @click="addDocumentColumn('id')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Column
                                        </Button>
                                        <Button type="button" size="sm" variant="outline" @click="addDocumentRow('id')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Row
                                        </Button>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <!-- Table Headers -->
                                    <div class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.document_list_id.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.document_list_id.headers" :key="`edit-document-id-header-${headerIndex}`">
                                            {{ header || `Column ${headerIndex + 1}` }}
                                        </div>
                                    </div>
                                    
                                    <!-- Header Edit Row -->
                                    <div class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.document_list_id.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.document_list_id.headers" :key="`edit-document-id-header-${headerIndex}`" class="space-y-1">
                                            <Input
                                                v-model="form.document_list_id.headers[headerIndex]"
                                                :placeholder="`Header ${headerIndex + 1}`"
                                                class="text-xs"
                                            />
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="outline"
                                                @click="removeDocumentColumn('id', headerIndex)"
                                                class="w-full text-red-600 hover:text-red-700 text-xs"
                                            >
                                                Remove Column
                                            </Button>
                                        </div>
                                    </div>
                                    
                                    <!-- Table Rows -->
                                    <div v-for="(row, rowIndex) in form.document_list_id.rows" :key="`edit-document-id-row-${rowIndex}`" class="flex gap-2 items-center">
                                        <div class="grid gap-2 items-center flex-1" 
                                             :style="{ gridTemplateColumns: `repeat(${form.document_list_id.headers.length}, 1fr)` }">
                                            <Input
                                                v-for="(cell, cellIndex) in row"
                                                :key="`edit-document-id-cell-${rowIndex}-${cellIndex}`"
                                                v-model="form.document_list_id.rows[rowIndex][cellIndex]"
                                                :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                            />
                                        </div>
                                        <Button
                                            type="button"
                                            size="sm"
                                            variant="outline"
                                            @click="removeDocumentRow('id', rowIndex)"
                                            class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                            title="Remove Row"
                                        >
                                            <X class="w-4 h-4" />
                                        </Button>
                                    </div>
                                    
                                    <p v-if="form.document_list_id.headers.length === 0 || form.document_list_id.rows.length === 0" class="text-sm text-muted-foreground">
                                        No document items added yet.
                                    </p>
                                </div>
                            </TabsContent>
                            
                            <!-- English Version -->
                            <TabsContent value="en" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm text-muted-foreground">English Version</Label>
                                    <div class="flex gap-2">
                                        <Button type="button" size="sm" variant="outline" @click="addDocumentColumn('en')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Column
                                        </Button>
                                        <Button type="button" size="sm" variant="outline" @click="addDocumentRow('en')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Row
                                        </Button>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <!-- Table Headers -->
                                    <div class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.document_list_en.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.document_list_en.headers" :key="`edit-document-en-header-${headerIndex}`">
                                            {{ header || `Column ${headerIndex + 1}` }}
                                        </div>
                                    </div>
                                    
                                    <!-- Header Edit Row -->
                                    <div class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.document_list_en.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.document_list_en.headers" :key="`edit-document-en-header-${headerIndex}`" class="space-y-1">
                                            <Input
                                                v-model="form.document_list_en.headers[headerIndex]"
                                                :placeholder="`Header ${headerIndex + 1}`"
                                                class="text-xs"
                                            />
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="outline"
                                                @click="removeDocumentColumn('en', headerIndex)"
                                                class="w-full text-red-600 hover:text-red-700 text-xs"
                                            >
                                                Remove Column
                                            </Button>
                                        </div>
                                    </div>
                                    
                                    <!-- Table Rows -->
                                    <div v-for="(row, rowIndex) in form.document_list_en.rows" :key="`edit-document-en-row-${rowIndex}`" class="flex gap-2 items-center">
                                        <div class="grid gap-2 items-center flex-1" 
                                             :style="{ gridTemplateColumns: `repeat(${form.document_list_en.headers.length}, 1fr)` }">
                                            <Input
                                                v-for="(cell, cellIndex) in row"
                                                :key="`edit-document-en-cell-${rowIndex}-${cellIndex}`"
                                                v-model="form.document_list_en.rows[rowIndex][cellIndex]"
                                                :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                            />
                                        </div>
                                        <Button
                                            type="button"
                                            size="sm"
                                            variant="outline"
                                            @click="removeDocumentRow('en', rowIndex)"
                                            class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                            title="Remove Row"
                                        >
                                            <X class="w-4 h-4" />
                                        </Button>
                                    </div>
                                    
                                    <p v-if="form.document_list_en.headers.length === 0 || form.document_list_en.rows.length === 0" class="text-sm text-muted-foreground">
                                        No document items added yet.
                                    </p>
                                </div>
                            </TabsContent>
                        </Tabs>
                    </div>

                    <!-- Fees List Table -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label>Fees List Table</Label>
                        </div>
                        
                        <!-- Language Tabs -->
                        <Tabs v-model:modelValue="activeFeesTab" class="w-full">
                            <TabsList class="grid w-full grid-cols-2">
                                <TabsTrigger value="id">Bahasa Indonesia</TabsTrigger>
                                <TabsTrigger value="en">English</TabsTrigger>
                            </TabsList>
                            
                            <!-- Indonesian Version -->
                            <TabsContent value="id" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm text-muted-foreground">Indonesian Version</Label>
                                    <div class="flex gap-2">
                                        <Button type="button" size="sm" variant="outline" @click="addTableColumn('fees', 'id')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Column
                                        </Button>
                                        <Button type="button" size="sm" variant="outline" @click="addTableRow('fees', 'id')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Row
                                        </Button>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <!-- Table Headers -->
                                    <div class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.fees_list_id.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.fees_list_id.headers" :key="`edit-fees-id-header-${headerIndex}`">
                                            {{ header || `Column ${headerIndex + 1}` }}
                                        </div>
                                    </div>
                                    
                                    <!-- Header Edit Row -->
                                    <div class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.fees_list_id.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.fees_list_id.headers" :key="`edit-fees-id-header-${headerIndex}`" class="space-y-1">
                                            <Input
                                                v-model="form.fees_list_id.headers[headerIndex]"
                                                :placeholder="`Header ${headerIndex + 1}`"
                                                class="text-xs"
                                            />
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="outline"
                                                @click="removeTableColumn('fees', 'id', headerIndex)"
                                                class="w-full text-red-600 hover:text-red-700 text-xs"
                                            >
                                                Remove Column
                                            </Button>
                                        </div>
                                    </div>
                                    
                                    <!-- Table Rows -->
                                    <div v-for="(row, rowIndex) in form.fees_list_id.rows" :key="`edit-fees-id-row-${rowIndex}`" class="flex gap-2 items-center">
                                        <div class="grid gap-2 items-center flex-1" 
                                             :style="{ gridTemplateColumns: `repeat(${form.fees_list_id.headers.length}, 1fr)` }">
                                            <Input
                                                v-for="(cell, cellIndex) in row"
                                                :key="`edit-fees-id-cell-${rowIndex}-${cellIndex}`"
                                                v-model="form.fees_list_id.rows[rowIndex][cellIndex]"
                                                :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                            />
                                        </div>
                                        <Button
                                            type="button"
                                            size="sm"
                                            variant="outline"
                                            @click="removeTableRow('fees', 'id', rowIndex)"
                                            class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                            title="Remove Row"
                                        >
                                            <X class="w-4 h-4" />
                                        </Button>
                                    </div>
                                    
                                    <p v-if="form.fees_list_id.headers.length === 0 || form.fees_list_id.rows.length === 0" class="text-sm text-muted-foreground">
                                        No fee items added yet.
                                    </p>
                                </div>
                            </TabsContent>
                            
                            <!-- English Version -->
                            <TabsContent value="en" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <Label class="text-sm text-muted-foreground">English Version</Label>
                                    <div class="flex gap-2">
                                        <Button type="button" size="sm" variant="outline" @click="addTableColumn('fees', 'en')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Column
                                        </Button>
                                        <Button type="button" size="sm" variant="outline" @click="addTableRow('fees', 'en')">
                                            <Plus class="h-4 w-4 mr-2" />
                                            Add Row
                                        </Button>
                                    </div>
                                </div>
                                
                                <div class="space-y-2">
                                    <!-- Table Headers -->
                                    <div class="grid gap-2 items-center font-medium text-sm text-muted-foreground border-b pb-2" :style="{ gridTemplateColumns: `repeat(${form.fees_list_en.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.fees_list_en.headers" :key="`edit-fees-en-header-${headerIndex}`">
                                            {{ header || `Column ${headerIndex + 1}` }}
                                        </div>
                                    </div>
                                    
                                    <!-- Header Edit Row -->
                                    <div class="grid gap-2 items-center mb-4" :style="{ gridTemplateColumns: `repeat(${form.fees_list_en.headers.length}, 1fr)` }">
                                        <div v-for="(header, headerIndex) in form.fees_list_en.headers" :key="`edit-fees-en-header-${headerIndex}`" class="space-y-1">
                                            <Input
                                                v-model="form.fees_list_en.headers[headerIndex]"
                                                :placeholder="`Header ${headerIndex + 1}`"
                                                class="text-xs"
                                            />
                                            <Button
                                                type="button"
                                                size="sm"
                                                variant="outline"
                                                @click="removeTableColumn('fees', 'en', headerIndex)"
                                                class="w-full text-red-600 hover:text-red-700 text-xs"
                                            >
                                                Remove Column
                                            </Button>
                                        </div>
                                    </div>
                                    
                                    <!-- Table Rows -->
                                    <div v-for="(row, rowIndex) in form.fees_list_en.rows" :key="`edit-fees-en-row-${rowIndex}`" class="flex gap-2 items-center">
                                        <div class="grid gap-2 items-center flex-1" 
                                             :style="{ gridTemplateColumns: `repeat(${form.fees_list_en.headers.length}, 1fr)` }">
                                            <Input
                                                v-for="(cell, cellIndex) in row"
                                                :key="`edit-fees-en-cell-${rowIndex}-${cellIndex}`"
                                                v-model="form.fees_list_en.rows[rowIndex][cellIndex]"
                                                :placeholder="`Row ${rowIndex + 1}, Col ${cellIndex + 1}`"
                                            />
                                        </div>
                                        <Button
                                            type="button"
                                            size="sm"
                                            variant="outline"
                                            @click="removeTableRow('fees', 'en', rowIndex)"
                                            class="text-red-600 hover:text-red-700 hover:bg-red-50 p-2 shrink-0"
                                            title="Remove Row"
                                        >
                                            <X class="w-4 h-4" />
                                        </Button>
                                    </div>
                                    
                                    <p v-if="form.fees_list_en.headers.length === 0 || form.fees_list_en.rows.length === 0" class="text-sm text-muted-foreground">
                                        No fee items added yet.
                                    </p>
                                </div>
                            </TabsContent>
                        </Tabs>
                    </div>


                    <!-- Featured Image -->
                    <div class="space-y-2">
                        <Label>Featured Image</Label>
                        <div class="space-y-4">
                            <!-- Custom Upload Button -->
                            <div class="flex items-center gap-4">
                                <input
                                    ref="fileInputEdit"
                                    type="file"
                                    accept="image/*"
                                    @change="handleImageUpload"
                                    class="hidden"
                                />
                                <Button
                                    type="button"
                                    variant="outline"
                                    @click="triggerImageUpload"
                                    class="gap-2"
                                    data-testid="replace-image-button"
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
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" v-model="form.is_published" id="is_published" class="rounded border-gray-300" />
                            <Label for="is_published">Published</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" v-model="form.is_featured" id="is_featured" class="rounded border-gray-300" />
                            <Label for="is_featured">Featured</Label>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="checkbox" v-model="form.show_credit_simulation" id="show_credit_simulation" class="rounded border-gray-300" />
                            <Label for="show_credit_simulation">Show Credit Simulation</Label>
                        </div>
                    </div>
                </form>
                
                <DialogFooter>
                    <Button type="button" variant="outline" @click="showEditModal = false">
                        Cancel
                    </Button>
                    <Button type="button" @click="submitEditForm" :disabled="isSubmitting">
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