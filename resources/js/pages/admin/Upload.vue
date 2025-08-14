<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { 
    Upload, 
    X, 
    Image, 
    Video, 
    File, 
    Trash2,
    Eye,
    EyeOff
} from 'lucide-vue-next';
import { useTranslation } from '@/composables/useTranslation';
import { useToast } from '@/composables/useToast';

const { t, initLanguage } = useTranslation();
const { success, error } = useToast();

onMounted(() => {
    initLanguage();
});

const form = useForm({
    files: [],
    category: '',
    titles: {},
    descriptions: {},
    alt_texts: {},
    is_featured: {},
    sort_orders: {}
});

const isUploading = ref(false);
const dragOver = ref(false);
const selectedFiles = ref([]);
const previews = ref({});

// Toast functionality is now handled by useToast composable

const categories = [
    { value: 'wedding', labelKey: 'admin.media.upload.categorySelection.categories.wedding', icon: '💒' },
    { value: 'baptism', labelKey: 'admin.media.upload.categorySelection.categories.baptism', icon: '👶' },
    { value: 'concert', labelKey: 'admin.media.upload.categorySelection.categories.concert', icon: '🎵' },
    { value: 'on-set', labelKey: 'admin.media.upload.categorySelection.categories.onSet', icon: '🎬' },
    { value: 'studio', labelKey: 'admin.media.upload.categorySelection.categories.studio', icon: '📸' },
    { value: 'modelling', labelKey: 'admin.media.upload.categorySelection.categories.modelling', icon: '👗' },
    { value: 'travel', labelKey: 'admin.media.upload.categorySelection.categories.travel', icon: '✈️' },
    { value: 'other', labelKey: 'admin.media.upload.categorySelection.categories.other', icon: '📁' }
];

const supportedTypes = {
    images: ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'image/tiff'],
    videos: ['video/mp4', 'video/avi', 'video/mov', 'video/wmv', 'video/flv', 'video/webm', 'video/mkv']
};

const handleFileSelect = (event) => {
    const files = Array.from(event.target.files);
    addFiles(files);
};

const handleDrop = (event) => {
    event.preventDefault();
    dragOver.value = false;
    
    const files = Array.from(event.dataTransfer.files);
    addFiles(files);
};

const handleDragOver = (event) => {
    event.preventDefault();
    dragOver.value = true;
};

const handleDragLeave = (event) => {
    event.preventDefault();
    dragOver.value = false;
};

const addFiles = (files) => {
    files.forEach(file => {
        if (isValidFile(file)) {
            const fileId = generateFileId();
            selectedFiles.value.push({
                id: fileId,
                file: file,
                name: file.name,
                size: file.size,
                type: getFileType(file),
                preview: null
            });
            
            // Generate preview for images
            if (supportedTypes.images.includes(file.type)) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previews.value[fileId] = e.target.result;
                };
                reader.readAsDataURL(file);
            }
            
            // Initialize form data for this file
            form.titles[fileId] = generateTitleFromFilename(file.name);
            form.descriptions[fileId] = '';
            form.alt_texts[fileId] = generateTitleFromFilename(file.name);
            form.is_featured[fileId] = false;
            form.sort_orders[fileId] = selectedFiles.value.length;
        }
    });
};

const isValidFile = (file) => {
    const allSupportedTypes = [...supportedTypes.images, ...supportedTypes.videos];
    return allSupportedTypes.includes(file.type);
};

const getFileType = (file) => {
    if (supportedTypes.images.includes(file.type)) return 'image';
    if (supportedTypes.videos.includes(file.type)) return 'video';
    return 'unknown';
};

const generateFileId = () => {
    return 'file_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
};

const generateTitleFromFilename = (filename) => {
    return filename
        .replace(/\.[^/.]+$/, '') // Remove extension
        .replace(/[-_]/g, ' ') // Replace dashes and underscores with spaces
        .replace(/\b\w/g, l => l.toUpperCase()); // Capitalize first letter of each word
};

const removeFile = (fileId) => {
    const index = selectedFiles.value.findIndex(f => f.id === fileId);
    if (index > -1) {
        selectedFiles.value.splice(index, 1);
        delete previews.value[fileId];
        delete form.titles[fileId];
        delete form.descriptions[fileId];
        delete form.alt_texts[fileId];
        delete form.is_featured[fileId];
        delete form.sort_orders[fileId];
    }
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const submitUpload = () => {
    if (!form.category) {
        alert(t('admin.media.upload.alerts.selectCategory'));
        return;
    }
    
    if (selectedFiles.value.length === 0) {
        alert(t('admin.media.upload.alerts.selectFiles'));
        return;
    }
    
    isUploading.value = true;
    
    // Create a new form instance for file upload
    const uploadForm = useForm({
        category: form.category,
        files: [],
        titles: {},
        descriptions: {},
        alt_texts: {},
        is_featured: {},
        sort_orders: {}
    });
    
    // Add files to the form
    selectedFiles.value.forEach((fileObj, index) => {
        uploadForm.files.push(fileObj.file);
        uploadForm.titles[index] = form.titles[fileObj.id] || '';
        uploadForm.descriptions[index] = form.descriptions[fileObj.id] || '';
        uploadForm.alt_texts[index] = form.alt_texts[fileObj.id] || '';
        uploadForm.is_featured[index] = form.is_featured[fileObj.id] ? '1' : '0';
        uploadForm.sort_orders[index] = form.sort_orders[fileObj.id] || index;
    });
    
    // Submit using Inertia form
    uploadForm.post(route('admin.media.store'), {
        onSuccess: () => {
            selectedFiles.value = [];
            previews.value = {};
            form.reset();
            isUploading.value = false;
            success('Files uploaded successfully!');
        },
        onError: (errors) => {
            isUploading.value = false;
            error('Upload failed. Please try again.');
        }
    });
};

const clearAll = () => {
    selectedFiles.value = [];
    previews.value = {};
    form.reset();
};
</script>

<template>
    <Head :title="t('admin.media.upload.title') + ' - Admin'">
        <meta name="description" content="Upload photos and videos to your photography website." />
    </Head>

    <AdminLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white">{{ t('admin.media.upload.title') }}</h1>
                <p class="mt-2 text-gray-300">
                    {{ t('admin.media.upload.subtitle') }}
                </p>
            </div>

            <!-- File Upload Area (First) -->
            <div class="bg-gray-900 rounded-lg shadow p-6 mb-6 border border-gray-800">
                <h2 class="text-lg font-semibold text-white mb-4">{{ t('admin.media.upload.fileUpload.title') }}</h2>
                
                <!-- Drag & Drop Zone -->
                <div
                    @drop="handleDrop"
                    @dragover="handleDragOver"
                    @dragleave="handleDragLeave"
                    :class="[
                        'border-2 border-dashed rounded-lg p-8 text-center transition-colors duration-200',
                        dragOver
                            ? 'border-red-500 bg-red-900/20'
                            : 'border-gray-600 hover:border-gray-500'
                    ]"
                >
                    <Upload class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                    <p class="text-lg font-medium text-white mb-2">
                        {{ t('admin.media.upload.fileUpload.dropZone.title') }}
                    </p>
                    <p class="text-gray-400 mb-4">
                        {{ t('admin.media.upload.fileUpload.dropZone.description') }}
                    </p>
                    <input
                        type="file"
                        multiple
                        accept="image/*,video/*"
                        @change="handleFileSelect"
                        class="hidden"
                        id="file-input"
                    />
                    <label
                        for="file-input"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors cursor-pointer"
                    >
                        {{ t('admin.media.upload.fileUpload.dropZone.chooseFiles') }}
                    </label>
                </div>

                <!-- Selected Files -->
                <div v-if="selectedFiles.length > 0" class="mt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-white">
                            {{ t('admin.media.upload.fileUpload.selectedFiles.title') }} ({{ selectedFiles.length }})
                        </h3>
                        <button
                            @click="clearAll"
                            class="text-red-400 hover:text-red-300 text-sm font-medium"
                        >
                            {{ t('admin.media.upload.fileUpload.selectedFiles.clearAll') }}
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div
                            v-for="fileObj in selectedFiles"
                            :key="fileObj.id"
                            class="border border-gray-800 rounded-lg p-4 bg-gray-800"
                        >
                            <!-- File Preview -->
                            <div class="relative mb-4">
                                <div class="aspect-square bg-gray-700 rounded-lg overflow-hidden">
                                    <img
                                        v-if="previews[fileObj.id]"
                                        :src="previews[fileObj.id]"
                                        :alt="fileObj.name"
                                        class="w-full h-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="w-full h-full flex items-center justify-center"
                                    >
                                        <Video v-if="fileObj.type === 'video'" class="w-12 h-12 text-gray-400" />
                                        <File v-else class="w-12 h-12 text-gray-400" />
                                    </div>
                                </div>
                                <button
                                    @click="removeFile(fileObj.id)"
                                    class="absolute top-2 right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center hover:bg-red-600 transition-colors"
                                >
                                    <X class="w-4 h-4" />
                                </button>
                            </div>

                            <!-- File Info -->
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">
                                        {{ t('admin.media.upload.fileUpload.selectedFiles.titleLabel') }}
                                    </label>
                                    <input
                                        v-model="form.titles[fileObj.id]"
                                        type="text"
                                        class="w-full px-3 py-2 border border-gray-600 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent bg-black text-white placeholder-gray-500"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">
                                        {{ t('admin.media.upload.fileUpload.selectedFiles.descriptionLabel') }}
                                    </label>
                                    <textarea
                                        v-model="form.descriptions[fileObj.id]"
                                        rows="2"
                                        class="w-full px-3 py-2 border border-gray-600 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none bg-black text-white placeholder-gray-500"
                                    ></textarea>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-300 mb-1">
                                        {{ t('admin.media.upload.fileUpload.selectedFiles.altTextLabel') }}
                                    </label>
                                    <input
                                        v-model="form.alt_texts[fileObj.id]"
                                        type="text"
                                        class="w-full px-3 py-2 border border-gray-600 rounded-md focus:ring-2 focus:ring-red-500 focus:border-transparent bg-black text-white placeholder-gray-500"
                                    />
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4">
                                        <label class="flex items-center">
                                            <input
                                                v-model="form.is_featured[fileObj.id]"
                                                type="checkbox"
                                                class="rounded border-gray-600 text-red-600 focus:ring-red-500 bg-black"
                                            />
                                            <span class="ml-2 text-sm text-gray-300">{{ t('admin.media.upload.fileUpload.selectedFiles.featured') }}</span>
                                        </label>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ formatFileSize(fileObj.size) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Selection (Second) -->
            <div class="bg-gray-900 rounded-lg shadow p-6 mb-6 border border-gray-800">
                <h2 class="text-lg font-semibold text-white mb-4">{{ t('admin.media.upload.categorySelection.title') }}</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <button
                        v-for="category in categories"
                        :key="category.value"
                        @click="form.category = category.value"
                        :class="[
                            'flex flex-col items-center p-4 rounded-lg border-2 transition-all duration-200',
                            form.category === category.value
                                ? 'border-red-500 bg-red-900/20 text-red-400'
                                : 'border-gray-700 hover:border-gray-600 text-gray-300 hover:bg-gray-800'
                        ]"
                    >
                        <span class="text-2xl mb-2">{{ category.icon }}</span>
                        <span class="text-sm font-medium">{{ t(category.labelKey) }}</span>
                    </button>
                </div>
            </div>

            <!-- Upload Button -->
            <div class="bg-gray-900 rounded-lg shadow p-6 border border-gray-800">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-400">
                            <span v-if="selectedFiles.length > 0">
                                {{ t('admin.media.upload.fileUpload.selectedFiles.readyToUpload') }} {{ selectedFiles.length }} {{ t('admin.media.upload.fileUpload.selectedFiles.files', selectedFiles.length) }}
                            </span>
                            <span v-else>{{ t('admin.media.upload.fileUpload.selectedFiles.noFilesSelected') }}</span>
                        </p>
                    </div>
                    <button
                        @click="submitUpload"
                        :disabled="!form.category || selectedFiles.length === 0 || isUploading"
                        class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    >
                        <Upload v-if="!isUploading" class="w-5 h-5 mr-2" />
                        <span v-if="isUploading">{{ t('admin.media.upload.uploadButton.uploading') }}</span>
                        <span v-else>{{ t('admin.media.upload.uploadButton.uploadFiles') }}</span>
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template> 