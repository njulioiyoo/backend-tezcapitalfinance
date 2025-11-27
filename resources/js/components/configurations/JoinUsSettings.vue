<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Save, Loader2, Upload, X, Image, Plus, Trash2, ChevronDown, ChevronUp } from 'lucide-vue-next';

interface Configuration {
    [key: string]: {
        value: any;
        type: string;
        description: string;
        is_public: boolean;
    };
}

interface Props {
    configurations: Configuration;
    isLoading: boolean;
    isSaving: boolean;
}

const props = defineProps<Props>();

// Debug props on mount (removed to reduce reactivity)
const emit = defineEmits<{
    save: [group: string, key: string, value: any, type: string];
    update: [configId: number, group: string, key: string, value: any, type: string];
    bulkSave: [group: string, changes: Array<{key: string, value: any, type: string}>];
}>();

const form = reactive({
    hero_title_id: '',
    hero_title_en: '',
    ceo_message_title_id: '',
    ceo_message_title_en: '',
    ceo_message_content_id: '',
    ceo_message_content_en: '',
    ceo_image: '',
    
    // Our Business Section
    our_business_title_id: '',
    our_business_title_en: '',
    our_business_content_id: '',
    our_business_content_en: '',
    our_business_image: '',
    
    career_application_email: '',
    button_join_us_enabled: false, // Default to false
    
    // Explore Our Workplace - Working Environment
    workplace_working_environment_title_id: '',
    workplace_working_environment_title_en: '',
    workplace_working_environment_description_id: '',
    workplace_working_environment_description_en: '',
    workplace_working_environment_image: '',
    workplace_working_environment_slug: '',
    
    // Explore Our Workplace - Employee Benefits  
    workplace_employee_benefits_title_id: '',
    workplace_employee_benefits_title_en: '',
    workplace_employee_benefits_description_id: '',
    workplace_employee_benefits_description_en: '',
    workplace_employee_benefits_image: '',
    workplace_employee_benefits_slug: '',
    
    // Employee Benefits Items
    employee_benefits_items: [],
});

const ceoImageFile = ref<File | null>(null);
const ceoImagePreview = ref<string>('');

// Our Business image uploads
const ourBusinessImageFile = ref<File | null>(null);
const ourBusinessImagePreview = ref<string>('');

// Workplace image uploads
const workingEnvironmentImageFile = ref<File | null>(null);
const workingEnvironmentImagePreview = ref<string>('');
const employeeBenefitsImageFile = ref<File | null>(null);
const employeeBenefitsImagePreview = ref<string>('');

// Template refs
const ceoImageInput = ref<HTMLInputElement | null>(null);
const ourBusinessImageInput = ref<HTMLInputElement | null>(null);
const workingEnvironmentImageInput = ref<HTMLInputElement | null>(null);
const employeeBenefitsImageInput = ref<HTMLInputElement | null>(null);

// Collapse states for all sections
const isHeroExpanded = ref(false);
const isCeoMessageExpanded = ref(false);
const isOurBusinessExpanded = ref(false);
const isButtonSettingsExpanded = ref(false);
const isCareerApplicationExpanded = ref(false);
const isWorkplaceExpanded = ref(false);
const isEmployeeBenefitsExpanded = ref(false);

// Track collapse state for each category
const categoryCollapseStates = ref<Record<number, boolean>>({});

// Track if employee benefits have been manually changed
const employeeBenefitsChanged = ref(false);

// Flag to prevent form updates when user is typing
const isUserEditing = ref(false);

// Debounced function to mark benefits as changed
const markBenefitsChanged = () => {
    isUserEditing.value = true;
    employeeBenefitsChanged.value = true;
};

// Track uploaded icon files for employee benefits items
const iconFiles = ref<Record<string, File>>({});

// Value types for employee benefits
const valueTypes = [
    { value: '%', label: '%' },
    { value: 'Days', label: 'Days' },
    { value: 'Hours', label: 'Hours' },
    { value: 'Months', label: 'Months' },
    { value: 'Years', label: 'Years' },
    { value: 'Times', label: 'Times' },
    { value: 'Week', label: 'Week' },
    { value: 'Weeks', label: 'Weeks' }
];


// Watch for configuration changes and update form
watch(() => props.configurations, (newConfigs) => {
    // Only update if we have actual configuration data (not empty object) and user is not editing
    if (newConfigs && Object.keys(newConfigs).length > 0 && !isUserEditing.value) {
        
        form.hero_title_id = newConfigs.hero_title_id?.value || 'Bagian dari TEZ Capital';
        form.hero_title_en = newConfigs.hero_title_en?.value || 'Be Part of TEZ Capital';
        form.ceo_message_title_id = newConfigs.ceo_message_title_id?.value || 'Pesan dari CEO';
        form.ceo_message_title_en = newConfigs.ceo_message_title_en?.value || 'Message from CEO';
        form.ceo_message_content_id = newConfigs.ceo_message_content_id?.value || '';
        form.ceo_message_content_en = newConfigs.ceo_message_content_en?.value || '';
        form.ceo_image = newConfigs.ceo_image?.value || '/img/profile/1.png';
        
        // Our Business configurations
        form.our_business_title_id = newConfigs.our_business_title_id?.value || 'Bisnis Kami';
        form.our_business_title_en = newConfigs.our_business_title_en?.value || 'Our Business';
        form.our_business_content_id = newConfigs.our_business_content_id?.value || '';
        form.our_business_content_en = newConfigs.our_business_content_en?.value || '';
        form.our_business_image = newConfigs.our_business_image?.value || '/img/dummy3.jpg';
        
        form.career_application_email = newConfigs.career_application_email?.value || 'hr@tez-capital.com';
        
        // Simple boolean handling like LanguageSettings
        const dbValue = newConfigs.button_join_us_enabled?.value;
        
        // Simple assignment with fallback (like LanguageSettings)
        form.button_join_us_enabled = dbValue || false;
        
        ceoImagePreview.value = newConfigs.ceo_image?.value || '';
        ourBusinessImagePreview.value = newConfigs.our_business_image?.value || '';
        
        // Workplace configurations
        form.workplace_working_environment_title_id = newConfigs.workplace_working_environment_title_id?.value || 'Lingkungan Kerja';
        form.workplace_working_environment_title_en = newConfigs.workplace_working_environment_title_en?.value || 'Working Environment';
        form.workplace_working_environment_description_id = newConfigs.workplace_working_environment_description_id?.value || '';
        form.workplace_working_environment_description_en = newConfigs.workplace_working_environment_description_en?.value || '';
        form.workplace_working_environment_image = newConfigs.workplace_working_environment_image?.value || '/img/workplace/working-environment.jpg';
        form.workplace_working_environment_slug = newConfigs.workplace_working_environment_slug?.value || '#working-environment';
        
        form.workplace_employee_benefits_title_id = newConfigs.workplace_employee_benefits_title_id?.value || 'Benefit Karyawan';
        form.workplace_employee_benefits_title_en = newConfigs.workplace_employee_benefits_title_en?.value || 'Employee Benefits';
        form.workplace_employee_benefits_description_id = newConfigs.workplace_employee_benefits_description_id?.value || '';
        form.workplace_employee_benefits_description_en = newConfigs.workplace_employee_benefits_description_en?.value || '';
        form.workplace_employee_benefits_image = newConfigs.workplace_employee_benefits_image?.value || '/img/workplace/employee-benefits.jpg';
        form.workplace_employee_benefits_slug = newConfigs.workplace_employee_benefits_slug?.value || '#employee-benefits';
        
        // Set image previews
        workingEnvironmentImagePreview.value = newConfigs.workplace_working_environment_image?.value || '';
        employeeBenefitsImagePreview.value = newConfigs.workplace_employee_benefits_image?.value || '';
        
        // Employee Benefits Items
        if (newConfigs.employee_benefits_items?.value) {
            try {
                const items = typeof newConfigs.employee_benefits_items.value === 'string' 
                    ? JSON.parse(newConfigs.employee_benefits_items.value)
                    : newConfigs.employee_benefits_items.value;
                form.employee_benefits_items = Array.isArray(items) ? items : [];
                
                // Initialize collapse states for loaded categories (preserve existing states)
                const newStates: Record<number, boolean> = { ...categoryCollapseStates.value };
                form.employee_benefits_items.forEach((_, index) => {
                    if (newStates[index] === undefined) {
                        newStates[index] = false; // Start collapsed by default for new categories
                    }
                });
                categoryCollapseStates.value = newStates;
            } catch (e) {
                form.employee_benefits_items = [];
                categoryCollapseStates.value = {};
            }
        } else {
            // Default structure with 3 categories from screenshot
            form.employee_benefits_items = [
                {
                    category_title_id: 'Informasi Dasar',
                    category_title_en: 'Basic Information',
                    items: [
                        { title_id: 'Annual Holiday', title_en: 'Annual Holiday', icon: '', value: '12', value_type: 'Days', description_id: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', description_en: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
                        { title_id: 'Paid Leave Utilization Rate', title_en: 'Paid Leave Utilization Rate', icon: '', value: '100', value_type: '%', description_id: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', description_en: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
                        { title_id: 'Average Turnover Rate', title_en: 'Average Turnover Rate', icon: '', value: '10', value_type: '%', description_id: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', description_en: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' }
                    ]
                },
                {
                    category_title_id: 'Subsidi dan Tunjangan',
                    category_title_en: 'Subsidies and Allowances',
                    items: [
                        { title_id: 'Transportation Allowance', title_en: 'Transportation Allowance', icon: '', value: '', value_type: '', description_id: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', description_en: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
                        { title_id: 'Meal Allowance', title_en: 'Meal Allowance', icon: '', value: '', value_type: '', description_id: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', description_en: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
                        { title_id: 'Health Insurance', title_en: 'Health Insurance', icon: '', value: '', value_type: '', description_id: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', description_en: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' }
                    ]
                },
                {
                    category_title_id: 'Pernikahan dan Kelahiran',
                    category_title_en: 'Marriage and Childbirth',
                    items: [
                        { title_id: 'Wedding Gift Money', title_en: 'Wedding Gift Money', icon: '', value: '', value_type: '', description_id: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', description_en: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
                        { title_id: 'Birth Gift Money', title_en: 'Birth Gift Money', icon: '', value: '', value_type: '', description_id: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', description_en: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' },
                        { title_id: 'Maternity Leave', title_en: 'Maternity Leave', icon: '', value: '90', value_type: 'Days', description_id: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', description_en: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.' }
                    ]
                }
            ];
            
            // Initialize collapse states for default categories (preserve existing states)
            const newStates: Record<number, boolean> = { ...categoryCollapseStates.value };
            [0, 1, 2].forEach(index => {
                if (newStates[index] === undefined) {
                    newStates[index] = false; // Start collapsed by default
                }
            });
            categoryCollapseStates.value = newStates;
        }
    }
}, { immediate: true });

const handleBulkSave = () => {
    const changes = [];

    // Check for changes and collect them (like LanguageSettings)
    if (props.configurations.hero_title_id?.value !== form.hero_title_id) {
        changes.push({ key: 'hero_title_id', value: form.hero_title_id, type: 'text' });
    }

    if (props.configurations.hero_title_en?.value !== form.hero_title_en) {
        changes.push({ key: 'hero_title_en', value: form.hero_title_en, type: 'text' });
    }

    if (props.configurations.ceo_message_title_id?.value !== form.ceo_message_title_id) {
        changes.push({ key: 'ceo_message_title_id', value: form.ceo_message_title_id, type: 'text' });
    }

    if (props.configurations.ceo_message_title_en?.value !== form.ceo_message_title_en) {
        changes.push({ key: 'ceo_message_title_en', value: form.ceo_message_title_en, type: 'text' });
    }

    if (props.configurations.ceo_message_content_id?.value !== form.ceo_message_content_id) {
        changes.push({ key: 'ceo_message_content_id', value: form.ceo_message_content_id, type: 'textarea' });
    }

    if (props.configurations.ceo_message_content_en?.value !== form.ceo_message_content_en) {
        changes.push({ key: 'ceo_message_content_en', value: form.ceo_message_content_en, type: 'textarea' });
    }

    if (props.configurations.career_application_email?.value !== form.career_application_email) {
        changes.push({ key: 'career_application_email', value: form.career_application_email, type: 'email' });
    }

    // Simple boolean comparison like LanguageSettings
    const currentButtonValue = props.configurations.button_join_us_enabled?.value || false;
    
    if (currentButtonValue !== form.button_join_us_enabled) {
        changes.push({ key: 'button_join_us_enabled', value: form.button_join_us_enabled, type: 'boolean' });
    }
    
    if (ceoImageFile.value) {
        changes.push({ key: 'ceo_image', value: ceoImageFile.value, type: 'file' });
    }
    
    // Our Business changes
    if (props.configurations.our_business_title_id?.value !== form.our_business_title_id) {
        changes.push({ key: 'our_business_title_id', value: form.our_business_title_id, type: 'text' });
    }
    if (props.configurations.our_business_title_en?.value !== form.our_business_title_en) {
        changes.push({ key: 'our_business_title_en', value: form.our_business_title_en, type: 'text' });
    }
    if (props.configurations.our_business_content_id?.value !== form.our_business_content_id) {
        changes.push({ key: 'our_business_content_id', value: form.our_business_content_id, type: 'textarea' });
    }
    if (props.configurations.our_business_content_en?.value !== form.our_business_content_en) {
        changes.push({ key: 'our_business_content_en', value: form.our_business_content_en, type: 'textarea' });
    }
    if (ourBusinessImageFile.value) {
        changes.push({ key: 'our_business_image', value: ourBusinessImageFile.value, type: 'file' });
    }
    
    // Workplace - Working Environment changes
    if (props.configurations.workplace_working_environment_title_id?.value !== form.workplace_working_environment_title_id) {
        changes.push({ key: 'workplace_working_environment_title_id', value: form.workplace_working_environment_title_id, type: 'text' });
    }
    if (props.configurations.workplace_working_environment_title_en?.value !== form.workplace_working_environment_title_en) {
        changes.push({ key: 'workplace_working_environment_title_en', value: form.workplace_working_environment_title_en, type: 'text' });
    }
    if (props.configurations.workplace_working_environment_description_id?.value !== form.workplace_working_environment_description_id) {
        changes.push({ key: 'workplace_working_environment_description_id', value: form.workplace_working_environment_description_id, type: 'textarea' });
    }
    if (props.configurations.workplace_working_environment_description_en?.value !== form.workplace_working_environment_description_en) {
        changes.push({ key: 'workplace_working_environment_description_en', value: form.workplace_working_environment_description_en, type: 'textarea' });
    }
    if (props.configurations.workplace_working_environment_slug?.value !== form.workplace_working_environment_slug) {
        changes.push({ key: 'workplace_working_environment_slug', value: form.workplace_working_environment_slug, type: 'text' });
    }
    if (workingEnvironmentImageFile.value) {
        changes.push({ key: 'workplace_working_environment_image', value: workingEnvironmentImageFile.value, type: 'file' });
    }
    
    // Workplace - Employee Benefits changes
    if (props.configurations.workplace_employee_benefits_title_id?.value !== form.workplace_employee_benefits_title_id) {
        changes.push({ key: 'workplace_employee_benefits_title_id', value: form.workplace_employee_benefits_title_id, type: 'text' });
    }
    if (props.configurations.workplace_employee_benefits_title_en?.value !== form.workplace_employee_benefits_title_en) {
        changes.push({ key: 'workplace_employee_benefits_title_en', value: form.workplace_employee_benefits_title_en, type: 'text' });
    }
    if (props.configurations.workplace_employee_benefits_description_id?.value !== form.workplace_employee_benefits_description_id) {
        changes.push({ key: 'workplace_employee_benefits_description_id', value: form.workplace_employee_benefits_description_id, type: 'textarea' });
    }
    if (props.configurations.workplace_employee_benefits_description_en?.value !== form.workplace_employee_benefits_description_en) {
        changes.push({ key: 'workplace_employee_benefits_description_en', value: form.workplace_employee_benefits_description_en, type: 'textarea' });
    }
    if (props.configurations.workplace_employee_benefits_slug?.value !== form.workplace_employee_benefits_slug) {
        changes.push({ key: 'workplace_employee_benefits_slug', value: form.workplace_employee_benefits_slug, type: 'text' });
    }
    if (employeeBenefitsImageFile.value) {
        changes.push({ key: 'workplace_employee_benefits_image', value: employeeBenefitsImageFile.value, type: 'file' });
    }
    
    // Employee Benefits Items changes - improved comparison
    const currentItems = props.configurations.employee_benefits_items?.value || [];
    let currentItemsParsed;
    
    // Parse current items properly
    if (typeof currentItems === 'string') {
        try {
            currentItemsParsed = JSON.parse(currentItems);
        } catch (e) {
            currentItemsParsed = [];
        }
    } else {
        currentItemsParsed = currentItems;
    }
    
    // Normalize both to strings for comparison
    const currentItemsString = JSON.stringify(currentItemsParsed);
    const formItemsString = JSON.stringify(form.employee_benefits_items);
    
    if (currentItemsString !== formItemsString || employeeBenefitsChanged.value) {
        changes.push({ key: 'employee_benefits_items', value: form.employee_benefits_items, type: 'json' });
    }
    
    // Add icon files for employee benefits items
    console.log('🖼️ Processing icon files:', iconFiles.value);
    Object.keys(iconFiles.value).forEach(fileKey => {
        const file = iconFiles.value[fileKey];
        const [categoryIndex, itemIndex] = fileKey.split('-').map(Number);
        const change = { 
            key: `employee_benefits_icon_${categoryIndex}_${itemIndex}`, 
            value: file, 
            type: 'file',
            metadata: {
                arrayKey: 'employee_benefits_items',
                categoryIndex: categoryIndex,
                itemIndex: itemIndex,
                arrayField: 'icon'
            }
        };
        console.log('📁 Adding icon file change:', change);
        changes.push(change);
    });
    
    // Only save if there are changes
    if (changes.length > 0) {
        console.log('🚀 Emitting bulkSave with changes:', changes);
        emit('bulkSave', 'join_us', changes);
        
        // Clear file refs after successful save initiation
        ceoImageFile.value = null;
        ourBusinessImageFile.value = null;
        workingEnvironmentImageFile.value = null;
        employeeBenefitsImageFile.value = null;
        iconFiles.value = {}; // Clear icon files
        
        // Reset change flag and editing state
        employeeBenefitsChanged.value = false;
        isUserEditing.value = false;
    }
};

// Image upload handling
const handleCeoImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        ceoImageFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            ceoImagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const clearCeoImage = () => {
    ceoImageFile.value = null;
    ceoImagePreview.value = '';
};

// Our Business image upload handling
const handleOurBusinessImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        ourBusinessImageFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            ourBusinessImagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const clearOurBusinessImage = () => {
    ourBusinessImageFile.value = null;
    ourBusinessImagePreview.value = '';
};

// Working Environment image upload handling
const handleWorkingEnvironmentImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        workingEnvironmentImageFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            workingEnvironmentImagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const clearWorkingEnvironmentImage = () => {
    workingEnvironmentImageFile.value = null;
    workingEnvironmentImagePreview.value = '';
};

// Employee Benefits image upload handling
const handleEmployeeBenefitsImageUpload = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        employeeBenefitsImageFile.value = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            employeeBenefitsImagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const clearEmployeeBenefitsImage = () => {
    employeeBenefitsImageFile.value = null;
    employeeBenefitsImagePreview.value = '';
};



// Toggle collapse state for categories
const toggleCategoryCollapse = (categoryIndex: number) => {
    categoryCollapseStates.value[categoryIndex] = !categoryCollapseStates.value[categoryIndex];
};

// Employee Benefits Items Management
const addCategory = () => {
    const newIndex = form.employee_benefits_items.length;
    form.employee_benefits_items.push({
        category_title_id: '',
        category_title_en: '',
        items: []
    });
    // Set new category to expanded by default
    categoryCollapseStates.value[newIndex] = true;
    markBenefitsChanged();
};

const removeCategory = (index: number) => {
    form.employee_benefits_items.splice(index, 1);
    // Reorganize collapse states after removal
    const newStates: Record<number, boolean> = {};
    Object.keys(categoryCollapseStates.value).forEach((key) => {
        const keyIndex = parseInt(key);
        if (keyIndex < index) {
            newStates[keyIndex] = categoryCollapseStates.value[keyIndex];
        } else if (keyIndex > index) {
            newStates[keyIndex - 1] = categoryCollapseStates.value[keyIndex];
        }
    });
    categoryCollapseStates.value = newStates;
    markBenefitsChanged();
};

const addItem = (categoryIndex: number) => {
    const newItem = {
        _id: Date.now() + Math.random(),
        title_id: '',
        title_en: '',
        icon: '',
        value: '',
        value_type: '',
        description_id: '',
        description_en: ''
    };
    
    form.employee_benefits_items[categoryIndex].items.push(newItem);
    
    // Ensure category is expanded
    categoryCollapseStates.value[categoryIndex] = true;
    
    markBenefitsChanged();
    
    // Focus on the new item after DOM update
    nextTick(() => {
        const newItemIndex = form.employee_benefits_items[categoryIndex].items.length - 1;
        const newItemElement = document.querySelector(`[data-item-key="item-${categoryIndex}-${newItemIndex}"]`);
        if (newItemElement) {
            newItemElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            // Focus on first input in the new item
            const firstInput = newItemElement.querySelector('input, textarea') as HTMLElement;
            if (firstInput) {
                setTimeout(() => firstInput.focus(), 200);
            }
        }
    });
};

const removeItem = (categoryIndex: number, itemIndex: number) => {
    form.employee_benefits_items[categoryIndex].items.splice(itemIndex, 1);
    markBenefitsChanged();
};

const handleIconUpload = (event: Event, categoryIndex: number, itemIndex: number) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        // Store the actual file for upload
        const fileKey = `${categoryIndex}-${itemIndex}`;
        iconFiles.value[fileKey] = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            // Store preview path temporarily (will be replaced with server path after upload)
            form.employee_benefits_items[categoryIndex].items[itemIndex].icon = e.target?.result as string;
            markBenefitsChanged();
        };
        reader.readAsDataURL(file);
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- Hero Section Settings -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div>
                        <CardTitle class="flex items-center gap-2">
                            Hero Section
                        </CardTitle>
                        <CardDescription>
                            Configure the hero section content for the Join Us page
                        </CardDescription>
                    </div>
                    <Button 
                        @click="isHeroExpanded = !isHeroExpanded"
                        size="sm" 
                        variant="ghost"
                    >
                        <ChevronDown v-if="!isHeroExpanded" class="w-4 h-4" />
                        <ChevronUp v-else class="w-4 h-4" />
                    </Button>
                </div>
            </CardHeader>
            <CardContent v-if="isHeroExpanded" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <Label for="hero_title_id">Hero Title (Indonesian) *</Label>
                        <Input
                            id="hero_title_id"
                            v-model="form.hero_title_id"
                            placeholder="Bagian dari TEZ Capital"
                            :disabled="isLoading"
                        />
                        <p class="text-sm text-muted-foreground">
                            Main title displayed in the hero section (Indonesian)
                        </p>
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="hero_title_en">Hero Title (English) *</Label>
                        <Input
                            id="hero_title_en"
                            v-model="form.hero_title_en"
                            placeholder="Be Part of TEZ Capital"
                            :disabled="isLoading"
                        />
                        <p class="text-sm text-muted-foreground">
                            Main title displayed in the hero section (English)
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- CEO Message Settings -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div>
                        <CardTitle class="flex items-center gap-2">
                            CEO Message Section
                        </CardTitle>
                        <CardDescription>
                            Configure the CEO message content and image
                        </CardDescription>
                    </div>
                    <Button 
                        @click="isCeoMessageExpanded = !isCeoMessageExpanded"
                        size="sm" 
                        variant="ghost"
                    >
                        <ChevronDown v-if="!isCeoMessageExpanded" class="w-4 h-4" />
                        <ChevronUp v-else class="w-4 h-4" />
                    </Button>
                </div>
            </CardHeader>
            <CardContent v-if="isCeoMessageExpanded" class="space-y-6">
                <!-- CEO Message Titles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <Label for="ceo_message_title_id">CEO Message Title (Indonesian) *</Label>
                        <Input
                            id="ceo_message_title_id"
                            v-model="form.ceo_message_title_id"
                            placeholder="Pesan dari CEO"
                            :disabled="isLoading"
                        />
                        <p class="text-sm text-muted-foreground">
                            Title for the CEO message section (Indonesian)
                        </p>
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="ceo_message_title_en">CEO Message Title (English) *</Label>
                        <Input
                            id="ceo_message_title_en"
                            v-model="form.ceo_message_title_en"
                            placeholder="Message from CEO"
                            :disabled="isLoading"
                        />
                        <p class="text-sm text-muted-foreground">
                            Title for the CEO message section (English)
                        </p>
                    </div>
                </div>

                <!-- CEO Message Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <Label for="ceo_message_content_id">CEO Message Content (Indonesian) *</Label>
                        <Textarea
                            id="ceo_message_content_id"
                            v-model="form.ceo_message_content_id"
                            placeholder="Masukkan konten pesan CEO dalam bahasa Indonesia..."
                            rows="8"
                            :disabled="isLoading"
                        />
                        <p class="text-sm text-muted-foreground">
                            Main content of the CEO message in Indonesian. Use line breaks to separate paragraphs.
                        </p>
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="ceo_message_content_en">CEO Message Content (English) *</Label>
                        <Textarea
                            id="ceo_message_content_en"
                            v-model="form.ceo_message_content_en"
                            placeholder="Enter the CEO message content in English..."
                            rows="8"
                            :disabled="isLoading"
                        />
                        <p class="text-sm text-muted-foreground">
                            Main content of the CEO message in English. Use line breaks to separate paragraphs.
                        </p>
                    </div>
                </div>

                <!-- CEO Image -->
                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label>CEO Image</Label>
                        <div class="border-2 border-dashed border-input rounded-lg p-6 dark:border-input">
                            <div v-if="ceoImagePreview" class="space-y-4">
                                <div class="flex items-center justify-center">
                                    <img 
                                        :src="ceoImagePreview.startsWith('data:') || ceoImagePreview.startsWith('http') ? ceoImagePreview : `/storage/${ceoImagePreview}`" 
                                        alt="CEO Image Preview" 
                                        class="max-h-48 max-w-64 object-contain rounded-lg"
                                    />
                                </div>
                                <div class="flex justify-center gap-2">
                                    <Button size="sm" variant="outline" @click="clearCeoImage">
                                        <X class="w-4 h-4 mr-2" />
                                        Remove
                                    </Button>
                                    <Button size="sm" variant="outline" @click="ceoImageInput?.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Change
                                    </Button>
                                </div>
                            </div>
                            <div v-else class="text-center">
                                <Image class="mx-auto h-12 w-12 text-muted-foreground" />
                                <div class="mt-2">
                                    <Button variant="outline" @click="ceoImageInput?.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Upload CEO Image
                                    </Button>
                                </div>
                                <p class="text-sm text-muted-foreground mt-2">PNG, JPG up to 5MB</p>
                            </div>
                            <input
                                ref="ceoImageInput"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleCeoImageUpload"
                            />
                        </div>
                        <p class="text-sm text-muted-foreground">
                            CEO image displayed in the message section (recommended size: 400x400px)
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Our Business Section Settings -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div>
                        <CardTitle class="flex items-center gap-2">
                            Our Business Section
                        </CardTitle>
                        <CardDescription>
                            Configure the Our Business section content, image and description
                        </CardDescription>
                    </div>
                    <Button 
                        @click="isOurBusinessExpanded = !isOurBusinessExpanded"
                        size="sm" 
                        variant="ghost"
                    >
                        <ChevronDown v-if="!isOurBusinessExpanded" class="w-4 h-4" />
                        <ChevronUp v-else class="w-4 h-4" />
                    </Button>
                </div>
            </CardHeader>
            <CardContent v-if="isOurBusinessExpanded" class="space-y-6">
                <!-- Our Business Titles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <Label for="our_business_title_id">Title (Indonesian) *</Label>
                        <Input
                            id="our_business_title_id"
                            v-model="form.our_business_title_id"
                            placeholder="Bisnis Kami"
                            maxlength="40"
                            :disabled="isLoading"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ form.our_business_title_id?.length || 0 }}/40 characters
                        </p>
                        <p class="text-sm text-muted-foreground">
                            Title for the Our Business section (Indonesian)
                        </p>
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="our_business_title_en">Title (English) *</Label>
                        <Input
                            id="our_business_title_en"
                            v-model="form.our_business_title_en"
                            placeholder="Our Business"
                            maxlength="40"
                            :disabled="isLoading"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ form.our_business_title_en?.length || 0 }}/40 characters
                        </p>
                        <p class="text-sm text-muted-foreground">
                            Title for the Our Business section (English)
                        </p>
                    </div>
                </div>

                <!-- Our Business Content -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <Label for="our_business_content_id">Content (Indonesian) *</Label>
                        <Textarea
                            id="our_business_content_id"
                            v-model="form.our_business_content_id"
                            placeholder="Masukkan konten bisnis kami dalam bahasa Indonesia..."
                            rows="8"
                            maxlength="700"
                            :disabled="isLoading"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ form.our_business_content_id?.length || 0 }}/700 characters
                        </p>
                        <p class="text-sm text-muted-foreground">
                            Main content of the Our Business section in Indonesian. Use line breaks to separate paragraphs.
                        </p>
                    </div>
                    
                    <div class="space-y-2">
                        <Label for="our_business_content_en">Content (English) *</Label>
                        <Textarea
                            id="our_business_content_en"
                            v-model="form.our_business_content_en"
                            placeholder="Enter the Our Business content in English..."
                            rows="8"
                            maxlength="700"
                            :disabled="isLoading"
                        />
                        <p class="text-xs text-muted-foreground">
                            {{ form.our_business_content_en?.length || 0 }}/700 characters
                        </p>
                        <p class="text-sm text-muted-foreground">
                            Main content of the Our Business section in English. Use line breaks to separate paragraphs.
                        </p>
                    </div>
                </div>

                <!-- Our Business Image -->
                <div class="space-y-4">
                    <div class="space-y-2">
                        <Label>Image</Label>
                        <div class="border-2 border-dashed border-input rounded-lg p-6 dark:border-input">
                            <div v-if="ourBusinessImagePreview" class="space-y-4">
                                <div class="flex items-center justify-center">
                                    <img 
                                        :src="ourBusinessImagePreview.startsWith('data:') || ourBusinessImagePreview.startsWith('http') ? ourBusinessImagePreview : `/storage/${ourBusinessImagePreview}`" 
                                        alt="Our Business Image Preview" 
                                        class="max-h-48 max-w-64 object-contain rounded-lg"
                                    />
                                </div>
                                <div class="flex justify-center gap-2">
                                    <Button size="sm" variant="outline" @click="clearOurBusinessImage">
                                        <X class="w-4 h-4 mr-2" />
                                        Remove
                                    </Button>
                                    <Button size="sm" variant="outline" @click="ourBusinessImageInput?.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Change
                                    </Button>
                                </div>
                            </div>
                            <div v-else class="text-center">
                                <Image class="mx-auto h-12 w-12 text-muted-foreground" />
                                <div class="mt-2">
                                    <Button variant="outline" @click="ourBusinessImageInput?.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Upload Image
                                    </Button>
                                </div>
                                <p class="text-sm text-muted-foreground mt-2">PNG, JPG up to 5MB</p>
                            </div>
                            <input
                                ref="ourBusinessImageInput"
                                type="file"
                                accept="image/*"
                                class="hidden"
                                @change="handleOurBusinessImageUpload"
                            />
                        </div>
                        <p class="text-sm text-muted-foreground">
Image displayed in the Our Business section (recommended size: 400x500px)
                        </p>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Button Settings -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div>
                        <CardTitle class="flex items-center gap-2">
                            Button Settings
                        </CardTitle>
                        <CardDescription>
                            Configure visibility of Join Us button in website header
                        </CardDescription>
                    </div>
                    <Button 
                        @click="isButtonSettingsExpanded = !isButtonSettingsExpanded"
                        size="sm" 
                        variant="ghost"
                    >
                        <ChevronDown v-if="!isButtonSettingsExpanded" class="w-4 h-4" />
                        <ChevronUp v-else class="w-4 h-4" />
                    </Button>
                </div>
            </CardHeader>
            <CardContent v-if="isButtonSettingsExpanded" class="space-y-6">
                <div class="flex items-center justify-between p-4 rounded-lg border">
                    <div class="space-y-1">
                        <Label for="button_join_us_enabled" class="text-sm font-medium">
                            Show Join Us Button
                        </Label>
                        <p class="text-sm text-muted-foreground">
                            Enable or disable the Join Us button in the website header (both desktop and mobile)
                        </p>
                    </div>
                    <Switch
                        id="button_join_us_enabled"
                        v-model:checked="form.button_join_us_enabled"
                        :disabled="isLoading"
                    />
                </div>
            </CardContent>
        </Card>

        <!-- Career Application Settings -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div>
                        <CardTitle class="flex items-center gap-2">
                            Career Application Settings
                        </CardTitle>
                        <CardDescription>
                            Configure email settings for career applications
                        </CardDescription>
                    </div>
                    <Button 
                        @click="isCareerApplicationExpanded = !isCareerApplicationExpanded"
                        size="sm" 
                        variant="ghost"
                    >
                        <ChevronDown v-if="!isCareerApplicationExpanded" class="w-4 h-4" />
                        <ChevronUp v-else class="w-4 h-4" />
                    </Button>
                </div>
            </CardHeader>
            <CardContent v-if="isCareerApplicationExpanded" class="space-y-6">
                <div class="space-y-2">
                    <Label for="career_application_email">Application Destination Email *</Label>
                    <Input
                        id="career_application_email"
                        v-model="form.career_application_email"
                        type="email"
                        placeholder="hr@tez-capital.com"
                        :disabled="isLoading"
                    />
                    <p class="text-sm text-muted-foreground">
                        Email address where career applications will be sent. This email will be used when users submit job applications through the career detail pages.
                    </p>
                </div>
            </CardContent>
        </Card>

        <!-- Explore Our Workplace Settings -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div>
                        <CardTitle class="flex items-center gap-2">
                            Explore Our Workplace Settings
                        </CardTitle>
                        <CardDescription>
                            Configure the static cards for Working Environment and Employee Benefits section
                        </CardDescription>
                    </div>
                    <Button 
                        @click="isWorkplaceExpanded = !isWorkplaceExpanded"
                        size="sm" 
                        variant="ghost"
                    >
                        <ChevronDown v-if="!isWorkplaceExpanded" class="w-4 h-4" />
                        <ChevronUp v-else class="w-4 h-4" />
                    </Button>
                </div>
            </CardHeader>
            <CardContent v-if="isWorkplaceExpanded" class="space-y-8">
                <!-- Working Environment Card -->
                <div class="space-y-6 p-6 border rounded-lg">
                    <h3 class="text-lg font-semibold">Working Environment Card</h3>
                    
                    <!-- Titles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="workplace_working_environment_title_id">Title (Indonesian) *</Label>
                            <Input
                                id="workplace_working_environment_title_id"
                                v-model="form.workplace_working_environment_title_id"
                                placeholder="Lingkungan Kerja"
                                :disabled="isLoading"
                            />
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="workplace_working_environment_title_en">Title (English) *</Label>
                            <Input
                                id="workplace_working_environment_title_en"
                                v-model="form.workplace_working_environment_title_en"
                                placeholder="Working Environment"
                                :disabled="isLoading"
                            />
                        </div>
                    </div>

                    <!-- Descriptions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="workplace_working_environment_description_id">Description (Indonesian) *</Label>
                            <Textarea
                                id="workplace_working_environment_description_id"
                                v-model="form.workplace_working_environment_description_id"
                                placeholder="Deskripsi tentang lingkungan kerja..."
                                rows="4"
                                :disabled="isLoading"
                            />
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="workplace_working_environment_description_en">Description (English) *</Label>
                            <Textarea
                                id="workplace_working_environment_description_en"
                                v-model="form.workplace_working_environment_description_en"
                                placeholder="Description about working environment..."
                                rows="4"
                                :disabled="isLoading"
                            />
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="space-y-2">
                        <Label for="workplace_working_environment_slug">Link/Slug *</Label>
                        <Input
                            id="workplace_working_environment_slug"
                            v-model="form.workplace_working_environment_slug"
                            placeholder="#working-environment"
                            :disabled="isLoading"
                        />
                        <p class="text-sm text-muted-foreground">
                            Link URL or anchor when the card is clicked
                        </p>
                    </div>

                    <!-- Image -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Working Environment Image</Label>
                            <div class="border-2 border-dashed border-input rounded-lg p-6 dark:border-input">
                                <div v-if="workingEnvironmentImagePreview" class="space-y-4">
                                    <div class="flex items-center justify-center">
                                        <img 
                                            :src="workingEnvironmentImagePreview.startsWith('data:') || workingEnvironmentImagePreview.startsWith('http') ? workingEnvironmentImagePreview : `/storage/${workingEnvironmentImagePreview}`" 
                                            alt="Working Environment Image Preview" 
                                            class="max-h-48 max-w-64 object-contain rounded-lg"
                                        />
                                    </div>
                                    <div class="flex justify-center gap-2">
                                        <Button size="sm" variant="outline" @click="clearWorkingEnvironmentImage">
                                            <X class="w-4 h-4 mr-2" />
                                            Remove
                                        </Button>
                                        <Button size="sm" variant="outline" @click="workingEnvironmentImageInput?.click()">
                                            <Upload class="w-4 h-4 mr-2" />
                                            Change
                                        </Button>
                                    </div>
                                </div>
                                <div v-else class="text-center">
                                    <Image class="mx-auto h-12 w-12 text-muted-foreground" />
                                    <div class="mt-2">
                                        <Button variant="outline" @click="workingEnvironmentImageInput?.click()">
                                            <Upload class="w-4 h-4 mr-2" />
                                            Upload Image
                                        </Button>
                                    </div>
                                    <p class="text-sm text-muted-foreground mt-2">PNG, JPG up to 5MB</p>
                                </div>
                                <input
                                    ref="workingEnvironmentImageInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleWorkingEnvironmentImageUpload"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employee Benefits Card -->
                <div class="space-y-6 p-6 border rounded-lg">
                    <h3 class="text-lg font-semibold">Employee Benefits Card</h3>
                    
                    <!-- Titles -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="workplace_employee_benefits_title_id">Title (Indonesian) *</Label>
                            <Input
                                id="workplace_employee_benefits_title_id"
                                v-model="form.workplace_employee_benefits_title_id"
                                placeholder="Benefit Karyawan"
                                maxlength="45"
                                :disabled="isLoading"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ form.workplace_employee_benefits_title_id?.length || 0 }}/45 characters
                            </p>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="workplace_employee_benefits_title_en">Title (English) *</Label>
                            <Input
                                id="workplace_employee_benefits_title_en"
                                v-model="form.workplace_employee_benefits_title_en"
                                placeholder="Employee Benefits"
                                maxlength="45"
                                :disabled="isLoading"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ form.workplace_employee_benefits_title_en?.length || 0 }}/45 characters
                            </p>
                        </div>
                    </div>

                    <!-- Descriptions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <Label for="workplace_employee_benefits_description_id">Description (Indonesian) *</Label>
                            <Textarea
                                id="workplace_employee_benefits_description_id"
                                v-model="form.workplace_employee_benefits_description_id"
                                placeholder="Deskripsi tentang benefit karyawan..."
                                rows="4"
                                maxlength="260"
                                :disabled="isLoading"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ form.workplace_employee_benefits_description_id?.length || 0 }}/260 characters
                            </p>
                        </div>
                        
                        <div class="space-y-2">
                            <Label for="workplace_employee_benefits_description_en">Description (English) *</Label>
                            <Textarea
                                id="workplace_employee_benefits_description_en"
                                v-model="form.workplace_employee_benefits_description_en"
                                placeholder="Description about employee benefits..."
                                rows="4"
                                maxlength="260"
                                :disabled="isLoading"
                            />
                            <p class="text-xs text-muted-foreground">
                                {{ form.workplace_employee_benefits_description_en?.length || 0 }}/260 characters
                            </p>
                        </div>
                    </div>

                    <!-- Slug -->
                    <div class="space-y-2">
                        <Label for="workplace_employee_benefits_slug">Link/Slug *</Label>
                        <Input
                            id="workplace_employee_benefits_slug"
                            v-model="form.workplace_employee_benefits_slug"
                            placeholder="#employee-benefits"
                            :disabled="isLoading"
                        />
                        <p class="text-sm text-muted-foreground">
                            Link URL or anchor when the card is clicked
                        </p>
                    </div>

                    <!-- Image -->
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Employee Benefits Image</Label>
                            <div class="border-2 border-dashed border-input rounded-lg p-6 dark:border-input">
                                <div v-if="employeeBenefitsImagePreview" class="space-y-4">
                                    <div class="flex items-center justify-center">
                                        <img 
                                            :src="employeeBenefitsImagePreview.startsWith('data:') || employeeBenefitsImagePreview.startsWith('http') ? employeeBenefitsImagePreview : `/storage/${employeeBenefitsImagePreview}`" 
                                            alt="Employee Benefits Image Preview" 
                                            class="max-h-48 max-w-64 object-contain rounded-lg"
                                        />
                                    </div>
                                    <div class="flex justify-center gap-2">
                                        <Button size="sm" variant="outline" @click="clearEmployeeBenefitsImage">
                                            <X class="w-4 h-4 mr-2" />
                                            Remove
                                        </Button>
                                        <Button size="sm" variant="outline" @click="employeeBenefitsImageInput?.click()">
                                            <Upload class="w-4 h-4 mr-2" />
                                            Change
                                        </Button>
                                    </div>
                                </div>
                                <div v-else class="text-center">
                                    <Image class="mx-auto h-12 w-12 text-muted-foreground" />
                                    <div class="mt-2">
                                        <Button variant="outline" @click="employeeBenefitsImageInput?.click()">
                                            <Upload class="w-4 h-4 mr-2" />
                                            Upload Image
                                        </Button>
                                    </div>
                                    <p class="text-sm text-muted-foreground mt-2">PNG, JPG up to 5MB</p>
                                </div>
                                <input
                                    ref="employeeBenefitsImageInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleEmployeeBenefitsImageUpload"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Employee Benefits Items Section -->
                    <div class="space-y-6 p-6 border rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold">Employee Benefits Items</h3>
                                <p class="text-sm text-muted-foreground mt-1">
                                    Configure the detailed employee benefits that will be displayed on the Employee Benefits page.
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <Button @click="addCategory" size="sm" variant="outline">
                                    <Plus class="w-4 h-4 mr-2" />
                                    Add Category
                                </Button>
                                <Button 
                                    @click="isEmployeeBenefitsExpanded = !isEmployeeBenefitsExpanded"
                                    size="sm" 
                                    variant="ghost"
                                >
                                    <ChevronDown v-if="!isEmployeeBenefitsExpanded" class="w-4 h-4" />
                                    <ChevronUp v-else class="w-4 h-4" />
                                </Button>
                            </div>
                        </div>

                        <!-- Categories -->
                        <div v-if="isEmployeeBenefitsExpanded" class="space-y-6">
                            <div 
                                v-for="(category, categoryIndex) in form.employee_benefits_items" 
                                :key="`category-${categoryIndex}-${category.category_title_id || categoryIndex}`"
                                class="p-6 border rounded-lg space-y-4"
                            >
                                <!-- Category Header -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-md">{{ category.category_title_id || category.category_title_en || `Category ${categoryIndex + 1}` }}</h4>
                                        <Button 
                                            @click="toggleCategoryCollapse(categoryIndex)"
                                            size="sm" 
                                            variant="ghost"
                                        >
                                            <ChevronDown v-if="!categoryCollapseStates[categoryIndex]" class="w-4 h-4" />
                                            <ChevronUp v-else class="w-4 h-4" />
                                        </Button>
                                    </div>
                                    <Button 
                                        @click="removeCategory(categoryIndex)" 
                                        size="sm" 
                                        variant="outline"
                                        class="text-red-600 hover:text-red-700"
                                    >
                                        <Trash2 class="w-4 h-4" />
                                    </Button>
                                </div>

                                <!-- Category Content (Collapsible) -->
                                <div v-if="categoryCollapseStates[categoryIndex]" class="space-y-4">
                                    <!-- Category Titles -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-2">
                                        <Label>Category Title (Indonesian)</Label>
                                        <Input
                                            v-model="category.category_title_id"
                                            placeholder="e.g., Informasi Dasar"
                                            :disabled="isLoading"
                                            @input="markBenefitsChanged"
                                        />
                                    </div>
                                    <div class="space-y-2">
                                        <Label>Category Title (English)</Label>
                                        <Input
                                            v-model="category.category_title_en"
                                            placeholder="e.g., Basic Information"
                                            :disabled="isLoading"
                                            @input="markBenefitsChanged"
                                        />
                                    </div>
                                </div>

                                <!-- Category Items -->
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h5 class="font-medium text-sm">Items in this category</h5>
                                        <Button 
                                            @click="addItem(categoryIndex)" 
                                            size="sm" 
                                            variant="outline"
                                        >
                                            <Plus class="w-4 h-4 mr-2" />
                                            Add Item
                                        </Button>
                                    </div>

                                    <div class="space-y-4">
                                        <div 
                                            v-for="(item, itemIndex) in category.items" 
                                            :key="`item-${categoryIndex}-${itemIndex}-${item._id || itemIndex}`"
                                            :data-item-key="`item-${categoryIndex}-${itemIndex}`"
                                            class="p-4 border border-dashed rounded-lg space-y-4"
                                        >
                                            <!-- Item Header -->
                                            <div class="flex items-center justify-between">
                                                <span class="text-sm font-medium">Item {{ itemIndex + 1 }}</span>
                                                <Button 
                                                    @click="removeItem(categoryIndex, itemIndex)" 
                                                    size="sm" 
                                                    variant="ghost"
                                                    class="text-red-600 hover:text-red-700"
                                                >
                                                    <Trash2 class="w-4 h-4" />
                                                </Button>
                                            </div>

                                            <!-- Item Titles -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <Label>Title (Indonesian)</Label>
                                                    <Input
                                                        v-model="item.title_id"
                                                        placeholder="e.g., Annual Holiday"
                                                        maxlength="25"
                                                        :disabled="isLoading"
                                                        @input="markBenefitsChanged"
                                                    />
                                                    <p class="text-xs text-muted-foreground">
                                                        {{ item.title_id?.length || 0 }}/25 characters
                                                    </p>
                                                </div>
                                                <div class="space-y-2">
                                                    <Label>Title (English)</Label>
                                                    <Input
                                                        v-model="item.title_en"
                                                        placeholder="e.g., Annual Holiday"
                                                        maxlength="25"
                                                        :disabled="isLoading"
                                                        @input="markBenefitsChanged"
                                                    />
                                                    <p class="text-xs text-muted-foreground">
                                                        {{ item.title_en?.length || 0 }}/25 characters
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Icon & Percentage -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <Label>Icon</Label>
                                                    <div class="border-2 border-dashed border-input rounded-lg p-4">
                                                        <div v-if="item.icon" class="space-y-3">
                                                            <div class="flex items-center justify-center">
                                                                <img 
                                                                    :src="item.icon.startsWith('data:') || item.icon.startsWith('http') ? item.icon : `/storage/${item.icon}`" 
                                                                    alt="Icon preview" 
                                                                    class="w-16 h-16 object-contain rounded"
                                                                />
                                                            </div>
                                                            <div class="flex justify-center gap-2">
                                                                <Button size="sm" variant="outline" @click="() => { item.icon = ''; markBenefitsChanged(); }">
                                                                    <X class="w-3 h-3 mr-1" />
                                                                    Remove
                                                                </Button>
                                                                <Button size="sm" variant="outline" @click="(e) => e.target.closest('.border-dashed').querySelector('input[type=file]').click()">
                                                                    <Upload class="w-3 h-3 mr-1" />
                                                                    Change
                                                                </Button>
                                                            </div>
                                                        </div>
                                                        <div v-else class="text-center">
                                                            <Image class="mx-auto h-8 w-8 text-muted-foreground mb-2" />
                                                            <Button size="sm" variant="outline" @click="(e) => e.target.closest('.border-dashed').querySelector('input[type=file]').click()">
                                                                <Upload class="w-3 h-3 mr-1" />
                                                                Upload Icon
                                                            </Button>
                                                            <p class="text-xs text-muted-foreground mt-1">PNG, JPG, SVG</p>
                                                        </div>
                                                        <input
                                                            type="file"
                                                            accept="image/*"
                                                            @change="handleIconUpload($event, categoryIndex, itemIndex)"
                                                            class="hidden"
                                                        />
                                                    </div>
                                                </div>
                                                <div class="space-y-2">
                                                    <Label>Value (Optional)</Label>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <Input
                                                            v-model="item.value"
                                                            placeholder="e.g., 100, 12, 8"
                                                            :disabled="isLoading"
                                                            @input="markBenefitsChanged"
                                                        />
                                                        <div class="relative">
                                                            <select
                                                                v-model="item.value_type"
                                                                :disabled="isLoading"
                                                                @change="markBenefitsChanged"
                                                                class="flex h-10 w-full rounded-md border border-input bg-background pl-3 pr-8 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 appearance-none"
                                                            >
                                                                <option value="">Type</option>
                                                                <option v-for="type in valueTypes" :key="type.value" :value="type.value">
                                                                    {{ type.label }}
                                                                </option>
                                                            </select>
                                                            <ChevronDown class="absolute right-2 top-1/2 transform -translate-y-1/2 h-4 w-4 text-muted-foreground pointer-events-none" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Descriptions -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div class="space-y-2">
                                                    <Label>Description (Indonesian)</Label>
                                                    <Textarea
                                                        v-model="item.description_id"
                                                        placeholder="Description in Indonesian"
                                                        rows="3"
                                                        maxlength="60"
                                                        :disabled="isLoading"
                                                        @input="markBenefitsChanged"
                                                    />
                                                    <p class="text-xs text-muted-foreground">
                                                        {{ item.description_id?.length || 0 }}/60 characters
                                                    </p>
                                                </div>
                                                <div class="space-y-2">
                                                    <Label>Description (English)</Label>
                                                    <Textarea
                                                        v-model="item.description_en"
                                                        placeholder="Description in English"
                                                        rows="3"
                                                        maxlength="60"
                                                        :disabled="isLoading"
                                                        @input="markBenefitsChanged"
                                                    />
                                                    <p class="text-xs text-muted-foreground">
                                                        {{ item.description_en?.length || 0 }}/60 characters
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                </div> <!-- End Category Content (Collapsible) -->
                            </div>
                        </div>

                        <!-- Add Category Button (if no categories exist) -->
                        <div v-if="isEmployeeBenefitsExpanded && form.employee_benefits_items.length === 0" class="text-center py-8">
                            <p class="text-muted-foreground mb-4">No employee benefit categories configured yet.</p>
                            <Button @click="addCategory" variant="outline">
                                <Plus class="w-4 h-4 mr-2" />
                                Add First Category
                            </Button>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Save Button -->
        <Card>
            <CardContent class="pt-6">
                <Button 
                    @click="handleBulkSave" 
                    :disabled="isLoading || isSaving"
                    class="w-full"
                >
                    <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
                    <Save v-else class="w-4 h-4 mr-2" />
                    {{ isSaving ? 'Saving...' : 'Save Join Us Settings' }}
                </Button>
            </CardContent>
        </Card>
    </div>
</template>