<script setup lang="ts">
import { ref, reactive, watch } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Badge } from '@/components/ui/badge';
import { Upload, Save, Loader2, Image, X, Plus, Trash2 } from 'lucide-vue-next';

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
const emit = defineEmits<{
    save: [group: string, key: string, value: any, type: string];
    update: [configId: number, group: string, key: string, value: any, type: string];
    bulkSave: [group: string, changes: Array<{key: string, value: any, type: string}>];
}>();

// Reactive state for OJK images
const ojkImages = ref<Array<{
    id?: number;
    file?: File;
    preview: string;
    url?: string;
    alt: string;
    isNew: boolean;
}>>([]);

const ojkTitle = ref<string>('');
const ojkDescription = ref<string>('');

// Watch for configuration changes
watch(() => props.configurations, (newConfigs) => {
    console.log('🔄 OJK configurations updated:', newConfigs);
    
    if (newConfigs) {
        // Load OJK title and description
        ojkTitle.value = newConfigs.ojk_title?.value || 'Berizin dan Diawasi oleh Otoritas Jasa Keuangan';
        ojkDescription.value = newConfigs.ojk_description?.value || '© 2025 PT TEZ Capital and Finance. All Rights Reserved';
        
        // Load existing OJK images
        const existingImages = newConfigs.ojk_images?.value;
        console.log('🖼️ Existing OJK images data:', existingImages, 'Type:', typeof existingImages);
        
        if (existingImages) {
            let parsedImages = null;
            
            // Handle different data types - Model already decodes JSON, so it should be an array
            if (Array.isArray(existingImages)) {
                parsedImages = existingImages;
                console.log('📋 Already an array:', parsedImages);
            } else if (typeof existingImages === 'string') {
                try {
                    parsedImages = JSON.parse(existingImages);
                    console.log('📋 Parsed from JSON string:', parsedImages);
                } catch (e) {
                    console.error('❌ Failed to parse JSON string:', e);
                }
            } else if (typeof existingImages === 'object' && existingImages !== null) {
                // Handle object case
                parsedImages = Object.values(existingImages);
                console.log('📋 Converted from object:', parsedImages);
            }
            
            if (Array.isArray(parsedImages) && parsedImages.length > 0) {
                // Filter out empty or invalid images
                const validImages = parsedImages.filter(img => 
                    img && (img.url || img.preview) && img.url !== '' && img.preview !== ''
                );
                
                if (validImages.length > 0) {
                    ojkImages.value = validImages.map((img: any, index: number) => ({
                        id: index,
                        preview: img.url || img.preview || '',
                        url: img.url || img.preview || '',
                        alt: img.alt || `OJK Image ${index + 1}`,
                        isNew: false
                    }));
                    console.log('✅ OJK images loaded:', ojkImages.value);
                    return; // Don't initialize with defaults if we have valid data
                }
            }
        }
        
        // If no images exist, initialize empty
        console.log('⚠️ No OJK images found, initializing empty');
        ojkImages.value = [];
    }
}, { immediate: true, deep: true });

const handleImageUpload = (event: Event, index: number) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            ojkImages.value[index] = {
                ...ojkImages.value[index],
                file: file,
                preview: e.target?.result as string,
                isNew: true
            };
        };
        reader.readAsDataURL(file);
    }
};

const addNewImage = () => {
    ojkImages.value.push({
        preview: '',
        alt: `OJK Image ${ojkImages.value.length + 1}`,
        isNew: true
    });
};

const removeImage = (index: number) => {
    ojkImages.value.splice(index, 1);
};

const clearImage = (index: number) => {
    ojkImages.value[index] = {
        ...ojkImages.value[index],
        file: undefined,
        preview: '',
        url: '',
        isNew: true
    };
};

const triggerImageUpload = (index: number) => {
    const input = document.getElementById(`imageInput${index}`) as HTMLInputElement;
    if (input) {
        input.click();
    }
};

const saveBulkChanges = async () => {
    const changes = [];
    
    // Save title and description
    changes.push({
        key: 'ojk_title',
        value: ojkTitle.value,
        type: 'text'
    });
    
    changes.push({
        key: 'ojk_description', 
        value: ojkDescription.value,
        type: 'text'
    });
    
    // Handle image uploads
    let fileUploadsNeeded = false;
    const imageData = [];
    
    for (let i = 0; i < ojkImages.value.length; i++) {
        const img = ojkImages.value[i];
        
        if (img.file) {
            // This is a new file upload
            fileUploadsNeeded = true;
            changes.push({
                key: `ojk_image_${i}`,
                value: img.file,
                type: 'file',
                metadata: {
                    alt: img.alt,
                    index: i,
                    originalKey: 'ojk_images'
                }
            });
            
            // Keep reference for final JSON structure
            imageData.push({
                url: '', // Will be filled by server after upload
                alt: img.alt,
                index: i
            });
        } else if (img.url || img.preview) {
            // Existing image
            imageData.push({
                url: img.url || img.preview,
                alt: img.alt,
                index: i
            });
        }
    }
    
    // Always save the JSON structure (even if files are uploaded, this maintains the array structure)
    changes.push({
        key: 'ojk_images',
        value: JSON.stringify(imageData),
        type: 'json'
    });
    
    emit('bulkSave', 'ojk', changes);
};

const hasChanges = () => {
    return ojkImages.value.some(img => img.isNew) || 
           ojkTitle.value !== (props.configurations.ojk_title?.value || 'Berizin dan Diawasi oleh Otoritas Jasa Keuangan') ||
           ojkDescription.value !== (props.configurations.ojk_description?.value || '© 2025 PT TEZ Capital and Finance. All Rights Reserved');
};
</script>

<template>
    <div class="space-y-6">
        <!-- OJK Text Settings -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <Image class="w-5 h-5" />
                    OJK Text Configuration
                </CardTitle>
                <CardDescription>
                    Configure the text displayed in the footer OJK section
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="space-y-2">
                    <Label for="ojk_title">OJK Title</Label>
                    <Input
                        id="ojk_title"
                        v-model="ojkTitle"
                        placeholder="Berizin dan Diawasi oleh Otoritas Jasa Keuangan"
                        :disabled="isLoading || isSaving"
                    />
                </div>
                
                <div class="space-y-2">
                    <Label for="ojk_description">Copyright Text</Label>
                    <Input
                        id="ojk_description"
                        v-model="ojkDescription"
                        placeholder="© 2025 PT TEZ Capital and Finance. All Rights Reserved"
                        :disabled="isLoading || isSaving"
                    />
                </div>
            </CardContent>
        </Card>

        <!-- OJK Images Settings -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    <Image class="w-5 h-5" />
                    OJK Images Configuration
                </CardTitle>
                <CardDescription>
                    Manage OJK certification and regulatory logos displayed in the footer
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-4">
                <div class="grid gap-4">
                    <div 
                        v-for="(image, index) in ojkImages" 
                        :key="index"
                        class="border rounded-lg p-4 space-y-3"
                    >
                        <div class="flex justify-between items-center">
                            <Label>OJK Image {{ index + 1 }}</Label>
                            <div class="flex gap-2">
                                <Button 
                                    v-if="ojkImages.length > 1"
                                    @click="removeImage(index)"
                                    variant="outline"
                                    size="sm"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </Button>
                            </div>
                        </div>
                        
                        <!-- Image Preview -->
                        <div class="space-y-2">
                            <div v-if="image.preview" class="relative inline-block">
                                <img 
                                    :src="image.preview" 
                                    :alt="image.alt"
                                    class="w-20 h-10 object-contain border rounded"
                                />
                                <Button 
                                    @click="clearImage(index)"
                                    variant="destructive"
                                    size="sm"
                                    class="absolute -top-2 -right-2 h-6 w-6 p-0 rounded-full"
                                >
                                    <X class="w-3 h-3" />
                                </Button>
                            </div>
                            
                            <!-- Upload Button -->
                            <div class="flex gap-2">
                                <Button 
                                    variant="outline" 
                                    @click="triggerImageUpload(index)"
                                    :disabled="isLoading || isSaving"
                                >
                                    <Upload class="w-4 h-4 mr-2" />
                                    {{ image.preview ? 'Change Image' : 'Upload Image' }}
                                </Button>
                                <input
                                    :id="`imageInput${index}`"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleImageUpload($event, index)"
                                />
                            </div>
                        </div>
                        
                        <!-- Alt Text -->
                        <div class="space-y-2">
                            <Label>Alt Text</Label>
                            <Input
                                v-model="image.alt"
                                placeholder="OJK certification logo"
                                :disabled="isLoading || isSaving"
                            />
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="flex gap-2">
                            <Badge v-if="image.isNew" variant="secondary">Modified</Badge>
                            <Badge v-else variant="outline">Current</Badge>
                        </div>
                    </div>
                </div>
                
                <!-- Add New Image Button -->
                <Button 
                    @click="addNewImage"
                    variant="outline"
                    class="w-full"
                    :disabled="isLoading || isSaving"
                >
                    <Plus class="w-4 h-4 mr-2" />
                    Add New OJK Image
                </Button>
            </CardContent>
        </Card>

        <!-- Save Button -->
        <div class="flex justify-end">
            <Button 
                @click="saveBulkChanges"
                :disabled="!hasChanges() || isLoading || isSaving"
                class="min-w-[120px]"
            >
                <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
                <Save v-else class="w-4 h-4 mr-2" />
                {{ isSaving ? 'Saving...' : 'Save Changes' }}
            </Button>
        </div>

        <!-- Preview Section -->
        <Card>
            <CardHeader>
                <CardTitle>Preview</CardTitle>
                <CardDescription>
                    Preview how the OJK section will appear in the footer
                </CardDescription>
            </CardHeader>
            <CardContent>
                <div class="border rounded-lg p-6 bg-gray-50">
                    <!-- OJK Images Preview -->
                    <div class="flex gap-3 justify-center items-center mb-4">
                        <img
                            v-for="(image, index) in ojkImages.filter(img => img.preview)"
                            :key="index"
                            :src="image.preview"
                            :alt="image.alt"
                            class="w-18 xl:w-20 h-10 object-contain"
                        />
                    </div>
                    
                    <!-- Text Preview -->
                    <div class="text-center space-y-1">
                        <p class="font-bold text-sm">{{ ojkTitle }}</p>
                        <p class="text-sm text-gray-600">{{ ojkDescription }}</p>
                    </div>
                </div>
            </CardContent>
        </Card>
    </div>
</template>