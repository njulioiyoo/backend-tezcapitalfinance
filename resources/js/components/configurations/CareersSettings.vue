<script setup lang="ts">
import { ref, reactive, watch, computed } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Badge } from '@/components/ui/badge';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogFooter } from '@/components/ui/dialog';
import { Save, Loader2, Plus, Trash2, Edit } from 'lucide-vue-next';
import axios from 'axios';

interface Career {
    id: number;
    title_id: string;
    title_en: string;
    category: string;
    location_id: string;
    location_en: string;
    department_id?: string;
    department_en?: string;
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
}

interface Props {
    careers: Career[];
    isLoading: boolean;
    isSaving: boolean;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    refresh: [];
}>();

// Career form for adding/editing
const careerForm = reactive({
    id: null,
    title_id: '',
    title_en: '',
    category: '',
    content_id: '',
    content_en: '',
    requirements_id: [],
    requirements_en: [],
    benefits_id: [],
    benefits_en: [],
    location_id: '',
    location_en: '',
    department_id: '',
    department_en: '',
    tags: [],
    start_date: '',
    end_date: '',
    status: 'published',
    is_published: true
});

const isModalOpen = ref(false);
const editingCareer = ref<Career | null>(null);

// Department and location options
const departments = ['Finance', 'People & Operation', 'Technology', 'Marketing', 'Sales'];
const locations = ['Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Yogyakarta'];
const jobTypes = [
    { value: 'full-time', label: 'Full Time' },
    { value: 'part-time', label: 'Part Time' },
    { value: 'contract', label: 'Contract' },
    { value: 'internship', label: 'Internship' }
];
const experienceLevels = [
    { value: 'entry', label: 'Entry Level' },
    { value: 'mid', label: 'Mid Level' },
    { value: 'senior', label: 'Senior Level' },
    { value: 'lead', label: 'Lead/Manager' }
];

const openModal = (career: Career | null = null) => {
    if (career) {
        editingCareer.value = career;
        Object.assign(careerForm, {
            ...career,
            requirements_id: Array.isArray(career.requirements_id) ? career.requirements_id.join('\n') : '',
            requirements_en: Array.isArray(career.requirements_en) ? career.requirements_en.join('\n') : '',
            benefits_id: Array.isArray(career.benefits_id) ? career.benefits_id.join('\n') : '',
            benefits_en: Array.isArray(career.benefits_en) ? career.benefits_en.join('\n') : '',
        });
    } else {
        resetCareerForm();
        editingCareer.value = null;
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    resetCareerForm();
    editingCareer.value = null;
};

const resetCareerForm = () => {
    Object.assign(careerForm, {
        id: null,
        title_id: '',
        title_en: '',
        category: '',
        content_id: '',
        content_en: '',
        requirements_id: [],
        requirements_en: [],
        benefits_id: [],
        benefits_en: [],
        location_id: '',
        location_en: '',
        department_id: '',
        department_en: '',
        tags: [],
        start_date: '',
        end_date: '',
        status: 'published',
        is_published: true
    });
};

const saveCareer = async () => {
    try {
        const careerData = {
            ...careerForm,
            type: 'career',
            department_en: careerForm.department_id, // Auto-fill department_en with same value as department_id
            requirements_id: typeof careerForm.requirements_id === 'string' 
                ? careerForm.requirements_id.split('\n').filter(item => item.trim())
                : careerForm.requirements_id,
            requirements_en: typeof careerForm.requirements_en === 'string'
                ? careerForm.requirements_en.split('\n').filter(item => item.trim()) 
                : careerForm.requirements_en,
            benefits_id: typeof careerForm.benefits_id === 'string'
                ? careerForm.benefits_id.split('\n').filter(item => item.trim())
                : careerForm.benefits_id,
            benefits_en: typeof careerForm.benefits_en === 'string'
                ? careerForm.benefits_en.split('\n').filter(item => item.trim())
                : careerForm.benefits_en
        };

        if (editingCareer.value) {
            await axios.put(`/content/careers/${editingCareer.value.id}`, careerData);
        } else {
            await axios.post('/content/careers', careerData);
        }
        
        closeModal();
        emit('refresh');
    } catch (error) {
        console.error('Error saving career:', error);
    }
};

const deleteCareer = async (career: Career) => {
    try {
        await axios.delete(`/content/careers/${career.id}`);
        emit('refresh');
    } catch (error) {
        console.error('Error deleting career:', error);
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- Careers Management -->
        <Card>
            <CardHeader>
                <div class="flex items-center justify-between">
                    <div>
                        <CardTitle class="flex items-center gap-2">
                            Careers Management
                        </CardTitle>
                        <CardDescription>
                            Manage job postings and career opportunities
                        </CardDescription>
                    </div>
                    <Button @click="openModal()" class="flex items-center gap-2">
                        <Plus class="w-4 h-4" />
                        Add New Job
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div v-if="props.careers.length === 0" class="text-center py-8 text-muted-foreground">
                    No careers posted yet. Click "Add New Job" to create your first job posting.
                </div>
                
                <div v-else class="space-y-4">
                    <div 
                        v-for="career in props.careers" 
                        :key="career.id"
                        class="border border-border rounded-lg p-4"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="font-semibold text-lg">{{ career.title_en }}</h3>
                                    <Badge variant="secondary">{{ career.category }}</Badge>
                                    <Badge variant="outline">{{ career.status }}</Badge>
                                </div>
                                <p class="text-sm text-muted-foreground mb-2">
                                    {{ career.location_en }}
                                </p>
                                <p class="text-sm text-muted-foreground line-clamp-2">
                                    {{ career.content_en }}
                                </p>
                                <div class="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                                    <span>Posted: {{ new Date(career.start_date).toLocaleDateString() }}</span>
                                    <span>Deadline: {{ new Date(career.end_date).toLocaleDateString() }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 ml-4">
                                <Button 
                                    variant="outline" 
                                    size="sm" 
                                    @click="openModal(career)"
                                >
                                    <Edit class="w-4 h-4" />
                                </Button>
                                <Button 
                                    variant="destructive" 
                                    size="sm" 
                                    @click="deleteCareer(career)"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            </CardContent>
        </Card>


        <!-- Add/Edit Career Modal -->
        <Dialog :open="isModalOpen" @update:open="(value) => { if (!value) closeModal() }">
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

                        <!-- Category and Department -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="category">Category *</Label>
                                <Input
                                    id="category"
                                    v-model="careerForm.category"
                                    placeholder="e.g., Technology, Human Resources"
                                    required
                                />
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="department_id">Department *</Label>
                                <Select v-model="careerForm.department_id">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select department" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="dept in departments" :key="dept" :value="dept">
                                            {{ dept }}
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

                        <!-- Job Type, Experience Level, Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="space-y-2">
                                <Label for="type">Job Type *</Label>
                                <Select v-model="careerForm.type">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select type" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="type in jobTypes" :key="type.value" :value="type.value">
                                            {{ type.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="experience_level">Experience Level *</Label>
                                <Select v-model="careerForm.experience_level">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select level" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem v-for="level in experienceLevels" :key="level.value" :value="level.value">
                                            {{ level.label }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            
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
                                <Textarea
                                    id="content_id"
                                    v-model="careerForm.content_id"
                                    placeholder="Describe the role in Indonesian..."
                                    rows="4"
                                    required
                                />
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="content_en">Job Description (English) *</Label>
                                <Textarea
                                    id="content_en"
                                    v-model="careerForm.content_en"
                                    placeholder="Describe the role in English..."
                                    rows="4"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Responsibilities -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="responsibilities_id">Responsibilities (Indonesian) *</Label>
                                <Textarea
                                    id="responsibilities_id"
                                    v-model="careerForm.responsibilities_id"
                                    placeholder="Enter responsibilities (one per line)..."
                                    rows="6"
                                    required
                                />
                                <p class="text-sm text-muted-foreground">One responsibility per line</p>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="responsibilities_en">Responsibilities (English) *</Label>
                                <Textarea
                                    id="responsibilities_en"
                                    v-model="careerForm.responsibilities_en"
                                    placeholder="Enter responsibilities (one per line)..."
                                    rows="6"
                                    required
                                />
                                <p class="text-sm text-muted-foreground">One responsibility per line</p>
                            </div>
                        </div>

                        <!-- Requirements -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="requirements_id">Requirements (Indonesian) *</Label>
                                <Textarea
                                    id="requirements_id"
                                    v-model="careerForm.requirements_id"
                                    placeholder="Enter requirements (one per line)..."
                                    rows="6"
                                    required
                                />
                                <p class="text-sm text-muted-foreground">One requirement per line</p>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="requirements_en">Requirements (English) *</Label>
                                <Textarea
                                    id="requirements_en"
                                    v-model="careerForm.requirements_en"
                                    placeholder="Enter requirements (one per line)..."
                                    rows="6"
                                    required
                                />
                                <p class="text-sm text-muted-foreground">One requirement per line</p>
                            </div>
                        </div>

                        <!-- Benefits -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <Label for="benefits_id">Benefits (Indonesian) *</Label>
                                <Textarea
                                    id="benefits_id"
                                    v-model="careerForm.benefits_id"
                                    placeholder="Enter benefits (one per line)..."
                                    rows="4"
                                    required
                                />
                                <p class="text-sm text-muted-foreground">One benefit per line</p>
                            </div>
                            
                            <div class="space-y-2">
                                <Label for="benefits_en">Benefits (English) *</Label>
                                <Textarea
                                    id="benefits_en"
                                    v-model="careerForm.benefits_en"
                                    placeholder="Enter benefits (one per line)..."
                                    rows="4"
                                    required
                                />
                                <p class="text-sm text-muted-foreground">One benefit per line</p>
                            </div>
                        </div>
                </div>

                <DialogFooter>
                    <Button variant="outline" @click="closeModal">Cancel</Button>
                    <Button @click="saveCareer">
                        {{ editingCareer ? 'Update Career' : 'Add Career' }}
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>