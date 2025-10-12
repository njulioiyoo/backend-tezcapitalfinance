<script setup lang="ts">
import { ref, reactive, watch, nextTick } from 'vue';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Switch } from '@/components/ui/switch';
import { Save, Loader2, Upload, X, Image } from 'lucide-vue-next';

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

// Debug props on mount
watch(() => props.configurations, (newConfigs) => {
    console.log('🔧 JoinUsSettings - Props changed:', newConfigs);
}, { immediate: true });
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
    career_application_email: '',
    button_join_us_enabled: false, // Default to false
});

const ceoImageFile = ref<File | null>(null);
const ceoImagePreview = ref<string>('');

// Watch for configuration changes and update form
watch(() => props.configurations, (newConfigs) => {
    console.log('🔧 JoinUsSettings - Watch triggered with:', newConfigs);
    // Only update if we have actual configuration data (not empty object)
    if (newConfigs && Object.keys(newConfigs).length > 0) {
        
        form.hero_title_id = newConfigs.hero_title_id?.value || 'Bagian dari TEZ Capital';
        form.hero_title_en = newConfigs.hero_title_en?.value || 'Be Part of TEZ Capital';
        form.ceo_message_title_id = newConfigs.ceo_message_title_id?.value || 'Pesan dari CEO';
        form.ceo_message_title_en = newConfigs.ceo_message_title_en?.value || 'Message from CEO';
        form.ceo_message_content_id = newConfigs.ceo_message_content_id?.value || '';
        form.ceo_message_content_en = newConfigs.ceo_message_content_en?.value || '';
        form.ceo_image = newConfigs.ceo_image?.value || '/img/profile/1.png';
        form.career_application_email = newConfigs.career_application_email?.value || 'hr@tez-capital.com';
        
        // Simple boolean handling like LanguageSettings
        const dbValue = newConfigs.button_join_us_enabled?.value;
        console.log('🔧 JoinUsSettings - DB Value:', dbValue, 'Type:', typeof dbValue);
        
        // Simple assignment with fallback (like LanguageSettings)
        form.button_join_us_enabled = dbValue || false;
        
        console.log('🔧 JoinUsSettings - Form Value:', form.button_join_us_enabled, 'Type:', typeof form.button_join_us_enabled);
        
        ceoImagePreview.value = newConfigs.ceo_image?.value || '';
        
        // Force DOM update
        nextTick(() => {
            console.log('🔧 JoinUsSettings - After nextTick, form.button_join_us_enabled:', form.button_join_us_enabled);
        });
    }
}, { immediate: true, deep: true });

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
    
    console.log('🔧 JoinUsSettings - Save: Current DB:', currentButtonValue, 'Form:', form.button_join_us_enabled, 'Changed:', currentButtonValue !== form.button_join_us_enabled);
    if (currentButtonValue !== form.button_join_us_enabled) {
        changes.push({ key: 'button_join_us_enabled', value: form.button_join_us_enabled, type: 'boolean' });
        console.log('🔧 JoinUsSettings - Saving value:', form.button_join_us_enabled);
    }
    
    if (ceoImageFile.value) {
        changes.push({ key: 'ceo_image', value: ceoImageFile.value, type: 'file' });
    }
    
    // Only save if there are changes
    if (changes.length > 0) {
        console.log('🔧 JoinUsSettings - Emitting bulkSave with changes:', changes);
        emit('bulkSave', 'join_us', changes);
        
        // Clear file ref after successful save initiation
        ceoImageFile.value = null;
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
</script>

<template>
    <div class="space-y-6">
        <!-- Hero Section Settings -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    Hero Section
                </CardTitle>
                <CardDescription>
                    Configure the hero section content for the Join Us page
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
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
                <CardTitle class="flex items-center gap-2">
                    CEO Message Section
                </CardTitle>
                <CardDescription>
                    Configure the CEO message content and image
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
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
                                    <Button size="sm" variant="outline" @click="$refs.ceoImageInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Change
                                    </Button>
                                </div>
                            </div>
                            <div v-else class="text-center">
                                <Image class="mx-auto h-12 w-12 text-muted-foreground" />
                                <div class="mt-2">
                                    <Button variant="outline" @click="$refs.ceoImageInput.click()">
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

        <!-- Button Settings -->
        <Card>
            <CardHeader>
                <CardTitle class="flex items-center gap-2">
                    Button Settings
                </CardTitle>
                <CardDescription>
                    Configure visibility of Join Us button in website header
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
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
                <CardTitle class="flex items-center gap-2">
                    Career Application Settings
                </CardTitle>
                <CardDescription>
                    Configure email settings for career applications
                </CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
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