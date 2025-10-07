<template>
    <div class="space-y-6">
        <div class="border rounded-lg p-6">
            <div class="flex items-center gap-2 mb-4">
                <h3 class="text-lg font-semibold">Page Banners Configuration</h3>
            </div>
            <p class="text-sm text-muted-foreground mb-6">
                Configure banner images, titles, and descriptions for each page on your website
            </p>

            <!-- About Page Banner -->
            <div class="mb-8 p-4 border rounded">
                <h4 class="font-medium mb-4">About Page Banner</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Title (Indonesian)</Label>
                            <Input 
                                v-model="localConfigs.banner_about_title_id"
                                placeholder="Tentang Kami"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_about_title_en"
                                placeholder="About Us"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_about_description_id"
                                placeholder="Pelajari lebih lanjut tentang TEZ Capital & Finance"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_about_description_en"
                                placeholder="Learn more about TEZ Capital & Finance"
                            />
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Banner Image</Label>
                            <div class="space-y-3">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                    <div v-if="aboutBannerPreview || getImageUrl(localConfigs.banner_about_image)" class="mb-4">
                                        <img 
                                            :src="aboutBannerPreview || getImageUrl(localConfigs.banner_about_image)" 
                                            alt="About Banner Preview" 
                                            class="w-full h-32 object-cover rounded-md border mx-auto"
                                        />
                                    </div>
                                    <div v-else class="mb-4">
                                        <Image class="mx-auto h-8 w-8 text-gray-400" />
                                    </div>
                                    <Button size="sm" variant="outline" @click="$refs.aboutBannerInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Upload Banner Image
                                    </Button>
                                    <input 
                                        ref="aboutBannerInput"
                                        type="file" 
                                        @change="handleImageUpload('banner_about_image', $event)"
                                        accept="image/*"
                                        class="hidden"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Page Banner -->
            <div class="mb-8 p-4 border rounded">
                <h4 class="font-medium mb-4">Services Page Banner</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Title (Indonesian)</Label>
                            <Input 
                                v-model="localConfigs.banner_services_title_id"
                                
                                placeholder="Layanan Kami"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_services_title_en"
                                
                                placeholder="Our Services"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_services_description_id"
                                
                                placeholder="Solusi pembiayaan terpercaya untuk kebutuhan Anda"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_services_description_en"
                                
                                placeholder="Trusted financing solutions for your needs"
                            />
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Banner Image</Label>
                            <div class="space-y-3">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                    <div v-if="servicesBannerPreview || getImageUrl(localConfigs.banner_services_image)" class="mb-4">
                                        <img 
                                            :src="servicesBannerPreview || getImageUrl(localConfigs.banner_services_image)" 
                                            alt="Services Banner Preview" 
                                            class="w-full h-32 object-cover rounded-md border mx-auto"
                                        />
                                    </div>
                                    <div v-else class="mb-4">
                                        <Image class="mx-auto h-8 w-8 text-gray-400" />
                                    </div>
                                    <Button size="sm" variant="outline" @click="$refs.servicesBannerInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Upload Banner Image
                                    </Button>
                                    <input 
                                        ref="servicesBannerInput"
                                        type="file" 
                                        @change="handleImageUpload('banner_services_image', $event)"
                                        accept="image/*"
                                        class="hidden"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- News Page Banner -->
            <div class="mb-8 p-4 border rounded">
                <h4 class="font-medium mb-4">News & Events Page Banner</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Title (Indonesian)</Label>
                            <Input 
                                v-model="localConfigs.banner_news_title_id"
                                
                                placeholder="Berita & Acara"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_news_title_en"
                                
                                placeholder="News & Events"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_news_description_id"
                                
                                placeholder="Temukan berita terbaru mengenai TEZ Capital di sini"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_news_description_en"
                                
                                placeholder="Find the latest news about TEZ Capital here"
                            />
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Banner Image</Label>
                            <div class="space-y-3">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                    <div v-if="newsBannerPreview || getImageUrl(localConfigs.banner_news_image)" class="mb-4">
                                        <img 
                                            :src="newsBannerPreview || getImageUrl(localConfigs.banner_news_image)" 
                                            alt="News Banner Preview" 
                                            class="w-full h-32 object-cover rounded-md border mx-auto"
                                        />
                                    </div>
                                    <div v-else class="mb-4">
                                        <Image class="mx-auto h-8 w-8 text-gray-400" />
                                    </div>
                                    <Button size="sm" variant="outline" @click="$refs.newsBannerInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Upload Banner Image
                                    </Button>
                                    <input 
                                        ref="newsBannerInput"
                                        type="file" 
                                        @change="handleImageUpload('banner_news_image', $event)"
                                        accept="image/*"
                                        class="hidden"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Page Banner -->
            <div class="mb-8 p-4 border rounded">
                <h4 class="font-medium mb-4">Contact Page Banner</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Title (Indonesian)</Label>
                            <Input 
                                v-model="localConfigs.banner_contact_title_id"
                                
                                placeholder="Hubungi Kami"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_contact_title_en"
                                
                                placeholder="Contact Us"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_contact_description_id"
                                
                                placeholder="Siap membantu dengan layanan terbaik untuk Anda"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_contact_description_en"
                                
                                placeholder="Ready to help with the best service for you"
                            />
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Banner Image</Label>
                            <div class="space-y-3">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                    <div v-if="contactBannerPreview || getImageUrl(localConfigs.banner_contact_image)" class="mb-4">
                                        <img 
                                            :src="contactBannerPreview || getImageUrl(localConfigs.banner_contact_image)" 
                                            alt="Contact Banner Preview" 
                                            class="w-full h-32 object-cover rounded-md border mx-auto"
                                        />
                                    </div>
                                    <div v-else class="mb-4">
                                        <Image class="mx-auto h-8 w-8 text-gray-400" />
                                    </div>
                                    <Button size="sm" variant="outline" @click="$refs.contactBannerInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Upload Banner Image
                                    </Button>
                                    <input 
                                        ref="contactBannerInput"
                                        type="file" 
                                        @change="handleImageUpload('banner_contact_image', $event)"
                                        accept="image/*"
                                        class="hidden"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Corporate Page Banner -->
            <div class="mb-8 p-4 border rounded">
                <h4 class="font-medium mb-4">Corporate Page Banner</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Title (Indonesian)</Label>
                            <Input 
                                v-model="localConfigs.banner_corporate_title_id"
                                
                                placeholder="Korporat"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_corporate_title_en"
                                
                                placeholder="Corporate"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_corporate_description_id"
                                
                                placeholder="Informasi korporat dan laporan keuangan terkini"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_corporate_description_en"
                                
                                placeholder="Corporate information and latest financial reports"
                            />
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Banner Image</Label>
                            <div class="space-y-3">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                    <div v-if="corporateBannerPreview || getImageUrl(localConfigs.banner_corporate_image)" class="mb-4">
                                        <img 
                                            :src="corporateBannerPreview || getImageUrl(localConfigs.banner_corporate_image)" 
                                            alt="Corporate Banner Preview" 
                                            class="w-full h-32 object-cover rounded-md border mx-auto"
                                        />
                                    </div>
                                    <div v-else class="mb-4">
                                        <Image class="mx-auto h-8 w-8 text-gray-400" />
                                    </div>
                                    <Button size="sm" variant="outline" @click="$refs.corporateBannerInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Upload Banner Image
                                    </Button>
                                    <input 
                                        ref="corporateBannerInput"
                                        type="file" 
                                        @change="handleImageUpload('banner_corporate_image', $event)"
                                        accept="image/*"
                                        class="hidden"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Join Us Page Banner -->
            <div class="mb-8 p-4 border rounded">
                <h4 class="font-medium mb-4">Join Us Page Banner</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Title (Indonesian)</Label>
                            <Input 
                                v-model="localConfigs.banner_join_us_title_id"
                                placeholder="Bergabunglah dengan Tim Kami"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_join_us_title_en"
                                placeholder="Join Our Team"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_join_us_description_id"
                                placeholder="Temukan peluang karir yang tepat untuk Anda di TEZ Capital. Bergabunglah dengan tim profesional kami dan wujudkan potensi terbaik Anda."
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_join_us_description_en"
                                placeholder="Discover the right career opportunities for you at TEZ Capital. Join our professional team and realize your full potential."
                            />
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <Label>Banner Image</Label>
                            <div class="space-y-3">
                                <div class="relative border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                                    <div v-if="joinUsBannerPreview || getImageUrl(localConfigs.banner_join_us_image)" class="mb-4">
                                        <img 
                                            :src="joinUsBannerPreview || getImageUrl(localConfigs.banner_join_us_image)" 
                                            alt="Join Us Banner Preview" 
                                            class="w-full h-32 object-cover rounded-md border mx-auto"
                                        />
                                    </div>
                                    <div v-else class="mb-4">
                                        <Image class="mx-auto h-8 w-8 text-gray-400" />
                                    </div>
                                    <Button size="sm" variant="outline" @click="$refs.joinUsBannerInput.click()">
                                        <Upload class="w-4 h-4 mr-2" />
                                        Upload Banner Image
                                    </Button>
                                    <input 
                                        ref="joinUsBannerInput"
                                        type="file" 
                                        @change="handleImageUpload('banner_join_us_image', $event)"
                                        accept="image/*"
                                        class="hidden"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end pt-4">
                <Button 
                    @click="saveSettings"
                    :disabled="!hasChanges() || isSaving || isLoading"
                    class="min-w-[120px]"
                >
                    <Loader2 v-if="isSaving" class="w-4 h-4 mr-2 animate-spin" />
                    <Save v-else class="w-4 h-4 mr-2" />
                    {{ isSaving ? 'Saving...' : 'Save Changes' }}
                </Button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Image, Upload, Save, Loader2 } from 'lucide-vue-next';

const props = defineProps({
    configurations: {
        type: Object,
        default: () => ({})
    },
    isLoading: {
        type: Boolean,
        default: false
    },
    isSaving: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['save', 'update', 'bulkSave']);

const localConfigs = reactive({
    banner_about_title_id: '',
    banner_about_title_en: '',
    banner_about_description_id: '',
    banner_about_description_en: '',
    banner_about_image: '',
    banner_services_title_id: '',
    banner_services_title_en: '',
    banner_services_description_id: '',
    banner_services_description_en: '',
    banner_services_image: '',
    banner_news_title_id: '',
    banner_news_title_en: '',
    banner_news_description_id: '',
    banner_news_description_en: '',
    banner_news_image: '',
    banner_contact_title_id: '',
    banner_contact_title_en: '',
    banner_contact_description_id: '',
    banner_contact_description_en: '',
    banner_contact_image: '',
    banner_corporate_title_id: '',
    banner_corporate_title_en: '',
    banner_corporate_description_id: '',
    banner_corporate_description_en: '',
    banner_corporate_image: '',
    banner_join_us_title_id: '',
    banner_join_us_title_en: '',
    banner_join_us_description_id: '',
    banner_join_us_description_en: '',
    banner_join_us_image: ''
});

const aboutBannerPreview = ref('');
const servicesBannerPreview = ref('');
const newsBannerPreview = ref('');
const contactBannerPreview = ref('');
const corporateBannerPreview = ref('');
const joinUsBannerPreview = ref('');

// Check if there are changes to save
const hasChanges = () => {
    return Object.keys(localConfigs).some(key => {
        const originalValue = props.configurations[key]?.value || '';
        return localConfigs[key] !== originalValue;
    });
};

const getImageUrl = (path) => {
    if (!path) return '';
    if (path.startsWith('data:') || path.startsWith('http://') || path.startsWith('https://')) {
        return path;
    }
    const timestamp = new Date().getTime();
    return `/storage/${path}?t=${timestamp}`;
};

const handleImageUpload = (configKey, event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        const preview = e.target.result;
        
        if (configKey === 'banner_about_image') {
            aboutBannerPreview.value = preview;
        } else if (configKey === 'banner_services_image') {
            servicesBannerPreview.value = preview;
        } else if (configKey === 'banner_news_image') {
            newsBannerPreview.value = preview;
        } else if (configKey === 'banner_contact_image') {
            contactBannerPreview.value = preview;
        } else if (configKey === 'banner_corporate_image') {
            corporateBannerPreview.value = preview;
        } else if (configKey === 'banner_join_us_image') {
            joinUsBannerPreview.value = preview;
        }
    };
    reader.readAsDataURL(file);

    // Immediately save the image file
    const changes = [{
        key: configKey,
        value: file,
        type: 'file'
    }];
    
    emit('bulkSave', 'banners', changes);
};

const saveSettings = () => {
    const changes = [];

    // Check for changes and collect them
    Object.keys(localConfigs).forEach(key => {
        const originalValue = props.configurations[key]?.value || '';
        if (localConfigs[key] !== originalValue && !key.includes('_image')) {
            // Determine the type based on key name
            let type = 'string';
            if (key.includes('description')) {
                type = 'text';
            }
            
            changes.push({
                key: key,
                value: localConfigs[key],
                type: type
            });
        }
    });

    // Only save if there are changes
    if (changes.length > 0) {
        emit('bulkSave', 'banners', changes);
    }
};

watch(() => props.configurations, (newConfigs) => {
    if (newConfigs) {
        Object.keys(localConfigs).forEach(key => {
            if (newConfigs[key] !== undefined) {
                if (newConfigs[key]?.value !== undefined) {
                    localConfigs[key] = newConfigs[key].value;
                } else {
                    localConfigs[key] = newConfigs[key];
                }
            }
        });
    }
}, { immediate: true, deep: true });
</script>