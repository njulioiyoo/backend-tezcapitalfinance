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
                                @input="updateConfig('banner_about_title_id', $event.target.value)"
                                placeholder="Tentang Kami"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_about_title_en"
                                @input="updateConfig('banner_about_title_en', $event.target.value)"
                                placeholder="About Us"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_about_description_id"
                                @input="updateConfig('banner_about_description_id', $event.target.value)"
                                placeholder="Pelajari lebih lanjut tentang TEZ Capital & Finance"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_about_description_en"
                                @input="updateConfig('banner_about_description_en', $event.target.value)"
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
                                @input="updateConfig('banner_services_title_id', $event.target.value)"
                                placeholder="Layanan Kami"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_services_title_en"
                                @input="updateConfig('banner_services_title_en', $event.target.value)"
                                placeholder="Our Services"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_services_description_id"
                                @input="updateConfig('banner_services_description_id', $event.target.value)"
                                placeholder="Solusi pembiayaan terpercaya untuk kebutuhan Anda"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_services_description_en"
                                @input="updateConfig('banner_services_description_en', $event.target.value)"
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
                                @input="updateConfig('banner_news_title_id', $event.target.value)"
                                placeholder="Berita & Acara"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_news_title_en"
                                @input="updateConfig('banner_news_title_en', $event.target.value)"
                                placeholder="News & Events"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_news_description_id"
                                @input="updateConfig('banner_news_description_id', $event.target.value)"
                                placeholder="Temukan berita terbaru mengenai TEZ Capital di sini"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_news_description_en"
                                @input="updateConfig('banner_news_description_en', $event.target.value)"
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
                                @input="updateConfig('banner_contact_title_id', $event.target.value)"
                                placeholder="Hubungi Kami"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_contact_title_en"
                                @input="updateConfig('banner_contact_title_en', $event.target.value)"
                                placeholder="Contact Us"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_contact_description_id"
                                @input="updateConfig('banner_contact_description_id', $event.target.value)"
                                placeholder="Siap membantu dengan layanan terbaik untuk Anda"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_contact_description_en"
                                @input="updateConfig('banner_contact_description_en', $event.target.value)"
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
                                @input="updateConfig('banner_corporate_title_id', $event.target.value)"
                                placeholder="Korporat"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Title (English)</Label>
                            <Input 
                                v-model="localConfigs.banner_corporate_title_en"
                                @input="updateConfig('banner_corporate_title_en', $event.target.value)"
                                placeholder="Corporate"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (Indonesian)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_corporate_description_id"
                                @input="updateConfig('banner_corporate_description_id', $event.target.value)"
                                placeholder="Informasi korporat dan laporan keuangan terkini"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label>Description (English)</Label>
                            <Textarea 
                                v-model="localConfigs.banner_corporate_description_en"
                                @input="updateConfig('banner_corporate_description_en', $event.target.value)"
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
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import { Image, Upload } from 'lucide-vue-next';

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

const emit = defineEmits(['save', 'update', 'bulk-save']);

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
    banner_corporate_image: ''
});

const aboutBannerPreview = ref('');
const servicesBannerPreview = ref('');
const newsBannerPreview = ref('');
const contactBannerPreview = ref('');
const corporateBannerPreview = ref('');

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
        }
    };
    reader.readAsDataURL(file);

    const formData = new FormData();
    formData.append('configurations[' + configKey + ']', file);
    formData.append('group', 'banners');
    
    emit('bulk-save', formData);
};

const updateConfig = (key, value) => {
    emit('update', { key, value, group: 'banners' });
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