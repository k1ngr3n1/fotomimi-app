<script setup lang="ts">
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { useTranslation } from '@/composables/useTranslation';
import { useToast } from '@/composables/useToast';
import { 
    Image, 
    Video, 
    Search, 
    Filter, 
    Grid, 
    List,
    Edit,
    Trash2,
    Eye,
    Star,
    Download,
    MoreVertical,
    X,
    Check
} from 'lucide-vue-next';

const { t, initLanguage } = useTranslation();
const { success, error } = useToast();

onMounted(() => {
    initLanguage();
});

const props = defineProps({
    media: Array,
    categories: [Array, Object], // Allow both Array and Object
    filters: Object
});

const form = useForm({
    search: '',
    category: '',
});

// Create reactive refs for filters to ensure proper reactivity
const searchQuery = ref('');
const selectedCategory = ref('');

const viewMode = ref('grid'); // 'grid' or 'list'
const selectedItems = ref([]);
const isDeleting = ref(false);

// Modal states
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const editingItem = ref(null);
const deletingItem = ref(null);

// Edit form
const editForm = useForm({
    title: '',
    description: '',
    alt_text: '',
});

const categories = [
    { value: '', labelKey: 'admin.media.filters.categories.all' },
    { value: 'wedding', labelKey: 'admin.media.filters.categories.wedding' },
    { value: 'baptism', labelKey: 'admin.media.filters.categories.baptism' },
    { value: 'concert', labelKey: 'admin.media.filters.categories.concert' },
    { value: 'on-set', labelKey: 'admin.media.filters.categories.onSet' },
    { value: 'studio', labelKey: 'admin.media.filters.categories.studio' },
    { value: 'modelling', labelKey: 'admin.media.filters.categories.modelling' },
    { value: 'travel', labelKey: 'admin.media.filters.categories.travel' },
    { value: 'video', labelKey: 'admin.media.filters.categories.video' },
    { value: 'other', labelKey: 'admin.media.filters.categories.other' }
];

const filteredMedia = computed(() => {
    let filtered = props.media || [];
    
    if (searchQuery.value) {
        const search = searchQuery.value.toLowerCase();
        filtered = filtered.filter(item => 
            item.title.toLowerCase().includes(search) ||
            (item.description && item.description.toLowerCase().includes(search))
        );
    }
    
    if (selectedCategory.value) {
        filtered = filtered.filter(item => item.category === selectedCategory.value);
    }
    
    return filtered;
});

const toggleSelection = (itemId) => {
    const index = selectedItems.value.indexOf(itemId);
    if (index > -1) {
        selectedItems.value.splice(index, 1);
    } else {
        selectedItems.value.push(itemId);
    }
};

const selectAll = () => {
    if (selectedItems.value.length === filteredMedia.value.length) {
        selectedItems.value = [];
    } else {
        selectedItems.value = filteredMedia.value.map(item => item.id);
    }
};

// Edit modal functions
const openEditModal = (item) => {
    console.log('Opening edit modal for:', item);
    editingItem.value = item;
    editForm.title = item.title || '';
    editForm.description = item.description || '';
    editForm.alt_text = item.alt_text || '';
    showEditModal.value = true;
    
    console.log('Form values set to:', {
        title: editForm.title,
        description: editForm.description,
        alt_text: editForm.alt_text
    });
    console.log('Edit modal state:', showEditModal.value);
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingItem.value = null;
    editForm.reset();
};

const saveEdit = () => {
    const formData = {
        title: editForm.title,
        description: editForm.description,
        alt_text: editForm.alt_text
    };
    
    console.log('Saving edit with form data:', formData);
    console.log('Original item data:', editingItem.value);
    
    // Try sending data explicitly
    router.put(route('admin.media.update', editingItem.value.id), formData, {
        onSuccess: () => {
            console.log('Update successful');
            closeEditModal();
        },
        onError: (errors) => {
            console.error('Update failed:', errors);
        }
    });
};

// Delete modal functions
const openDeleteModal = (item = null) => {
    console.log('Opening delete modal for:', item);
    if (item) {
        deletingItem.value = item;
    } else {
        deletingItem.value = null; // For bulk delete
    }
    showDeleteModal.value = true;
    console.log('Delete modal state:', showDeleteModal.value);
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deletingItem.value = null;
};

const confirmDelete = () => {
    console.log('Confirm delete called');
    console.log('Deleting item:', deletingItem.value);
    console.log('Selected items:', selectedItems.value);
    
    if (deletingItem.value) {
        // Single item delete
        console.log('Deleting single item:', deletingItem.value.id);
        
        try {
            const deleteUrl = route('admin.media.destroy', deletingItem.value.id);
            console.log('Delete URL:', deleteUrl);
            
            router.delete(deleteUrl, {
                onSuccess: () => {
                    console.log('Single delete successful');
                    success('Media item deleted successfully');
                    closeDeleteModal();
                },
                onError: (errors) => {
                    console.error('Single delete failed:', errors);
                    error('Failed to delete media item. Please try again.');
                }
            });
        } catch (error) {
            console.error('Route generation failed:', error);
            // Fallback: construct URL manually
            const fallbackUrl = `/admin/media/${deletingItem.value.id}`;
            console.log('Using fallback URL:', fallbackUrl);
            
            router.delete(fallbackUrl, {
                onSuccess: () => {
                    console.log('Single delete successful (fallback)');
                    success('Media item deleted successfully');
                    closeDeleteModal();
                },
                onError: (errors) => {
                    console.error('Single delete failed (fallback):', errors);
                    error('Failed to delete media item. Please check the console for details.');
                }
            });
        }
    } else {
        // Bulk delete - use the new bulk endpoint
        console.log('Deleting multiple items:', selectedItems.value);
        
        if (selectedItems.value.length === 0) {
            console.log('No items selected for deletion');
            closeDeleteModal();
            return;
        }
        
        // Use bulk delete endpoint
        router.delete(route('admin.media.bulk-destroy'), {
            data: {
                ids: selectedItems.value
            },
            onSuccess: () => {
                console.log('Bulk delete successful');
                success(`Successfully deleted ${selectedItems.value.length} items`);
                selectedItems.value = [];
                closeDeleteModal();
            },
            onError: (errors) => {
                console.error('Bulk delete failed:', errors);
                error('Failed to delete some items. Please try again.');
            }
        });
    }
};

// Legacy function - now opens delete modal
const deleteItem = (item) => {
    console.log('Delete item called with:', item);
    openDeleteModal(item);
};

// Test function to verify button clicks work
const testFunction = () => {
    console.log('Test function called - buttons are working!');
    alert('Button click works!');
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};
</script>

<template>
    <Head title="Media Library - Admin">
        <meta name="description" content="Manage your photography media library." />
    </Head>

    <AdminLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">{{ t('admin.media.title') }}</h1>
                        <p class="mt-2 text-gray-300">
                            {{ t('admin.media.subtitle') }}
                        </p>
                    </div>
                    <Link
                        :href="route('admin.upload')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        <Image class="w-4 h-4 mr-2" />
                        {{ t('admin.media.uploadButton') }}
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-gray-900 rounded-lg shadow mb-6 border border-gray-800">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Search -->
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input
                                v-model="searchQuery"
                                type="text"
                                :placeholder="t('admin.media.search.placeholder')"
                                class="w-full pl-10 pr-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-black text-white placeholder-gray-500"
                            />
                        </div>

                        <!-- Category Filter -->
                        <select
                            v-model="selectedCategory"
                            class="px-4 py-2 border border-gray-600 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent bg-black text-white"
                        >
                            <option
                                v-for="category in categories"
                                :key="category.value"
                                :value="category.value"
                            >
                                {{ t(category.labelKey) }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Results Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <p class="text-sm text-gray-400">
                        {{ filteredMedia.length }} {{ t('admin.media.results.itemsFound', filteredMedia.length) }}
                    </p>
                    
                    <div v-if="selectedItems.length > 0" class="flex items-center space-x-2">
                        <span class="text-sm text-gray-400">
                            {{ selectedItems.length }} selected
                        </span>
                        <button
                            @click="selectAll"
                            class="text-sm text-red-400 hover:text-red-300"
                        >
                            {{ selectedItems.length === filteredMedia.length ? 'Select None' : 'Select All' }}
                        </button>
                        <button
                            @click="openDeleteModal()"
                            class="text-sm text-red-400 hover:text-red-300 ml-4"
                        >
                            Delete Selected
                        </button>
                    </div>
                </div>

                <!-- View Mode Toggle -->
                <div class="flex items-center space-x-2">
                    <button
                        @click="viewMode = 'grid'"
                        :class="[
                            'p-2 rounded-lg transition-colors',
                            viewMode === 'grid' 
                                ? 'bg-red-900/30 text-red-400 border border-red-800' 
                                : 'text-gray-400 hover:text-gray-300'
                        ]"
                    >
                        <Grid class="w-5 h-5" />
                    </button>
                    <button
                        @click="viewMode = 'list'"
                        :class="[
                            'p-2 rounded-lg transition-colors',
                            viewMode === 'list' 
                                ? 'bg-red-900/30 text-red-400 border border-red-800' 
                                : 'text-gray-400 hover:text-gray-300'
                        ]"
                    >
                        <List class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- Media Grid -->
            <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div
                    v-for="item in filteredMedia"
                    :key="item.id"
                    class="bg-gray-900 rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow border border-gray-800"
                >
                    <!-- Media Preview -->
                    <div class="relative aspect-square bg-gray-800">
                        <!-- Checkbox -->
                        <div class="absolute top-2 left-2 z-10">
                            <input
                                type="checkbox"
                                :checked="selectedItems.includes(item.id)"
                                @change="toggleSelection(item.id)"
                                class="w-4 h-4 text-red-600 border-gray-600 rounded focus:ring-red-500 bg-black"
                            />
                        </div>
                        <img
                            v-if="item.type === 'photo'"
                            :src="item.url"
                            :alt="item.title"
                            class="w-full h-full object-cover"
                        />
                        <div
                            v-else
                            class="w-full h-full flex items-center justify-center relative"
                        >
                            <video
                                :src="item.url"
                                class="w-full h-full object-cover"
                                preload="metadata"
                                muted
                                @click.stop
                            >
                                <source :src="item.url" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-20">
                                <Video class="w-8 h-8 text-white" />
                            </div>
                        </div>
                        
                        <!-- Overlay Actions -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 hover:opacity-100">
                            <div class="flex items-center space-x-2">
                                                             <button
                                            @click="openEditModal(item)"
                                            class="p-2 bg-blue-500 hover:bg-blue-600 rounded-full text-white transition-colors"
                                            title="Edit"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                <button
                                    @click="deleteItem(item)"
                                    class="p-2 bg-red-500 hover:bg-red-600 rounded-full text-white transition-colors"
                                    title="Delete"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    

                        <!-- Type Badge -->
                        <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded-full text-xs font-medium">
                            {{ item.type }}
                        </div>
                    </div>

                    <!-- Media Info -->
                    <div class="p-4">
                        <h3 class="font-medium text-white truncate">{{ item.title }}</h3>
                        <p class="text-sm text-gray-400 mt-1">{{ item.category }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-500">{{ formatDate(item.created_at) }}</span>
                            <span class="text-xs text-gray-500">{{ formatFileSize(item.file_size) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media List -->
            <div v-else class="bg-gray-900 rounded-lg shadow overflow-hidden border border-gray-800">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-800">
                        <thead class="bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    <input
                                        type="checkbox"
                                        :checked="selectedItems.length === filteredMedia.length && filteredMedia.length > 0"
                                        @change="selectAll"
                                        class="w-4 h-4 text-red-600 border-gray-600 rounded focus:ring-red-500 bg-black"
                                    />
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    {{ t('admin.media.table.headers.media') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    {{ t('admin.media.table.headers.title') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    {{ t('admin.media.table.headers.category') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    {{ t('admin.media.table.headers.type') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    {{ t('admin.media.table.headers.size') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    {{ t('admin.media.table.headers.uploaded') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">
                                    {{ t('admin.media.table.headers.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-900 divide-y divide-gray-800">
                            <tr
                                v-for="item in filteredMedia"
                                :key="item.id"
                                class="hover:bg-gray-800"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input
                                        type="checkbox"
                                        :checked="selectedItems.includes(item.id)"
                                        @change="toggleSelection(item.id)"
                                        class="w-4 h-4 text-red-600 border-gray-600 rounded focus:ring-red-500 bg-black"
                                    />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-16 h-16 bg-gray-800 rounded-lg overflow-hidden">
                                        <img
                                            v-if="item.type === 'photo'"
                                            :src="item.url"
                                            :alt="item.title"
                                            class="w-full h-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="w-full h-full flex items-center justify-center relative"
                                        >
                                            <video
                                                :src="item.url"
                                                class="w-full h-full object-cover"
                                                preload="metadata"
                                                muted
                                                @click.stop
                                            >
                                                <source :src="item.url" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                            <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-20">
                                                <Video class="w-6 h-6 text-white" />
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-white">{{ item.title }}</div>
                                        <div class="text-sm text-gray-400">{{ item.description }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-800 text-gray-300 border border-gray-700">
                                        {{ item.category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            item.type === 'photo' 
                                                ? 'bg-red-900/30 text-red-400 border border-red-800' 
                                                : 'bg-red-900/30 text-red-400 border border-red-800'
                                        ]"
                                    >
                                        {{ item.type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ formatFileSize(item.file_size) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                    {{ formatDate(item.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button
                                            @click="openEditModal(item)"
                                            class="text-red-400 hover:text-red-300"
                                            title="Edit"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </button>
                                        <button
                                            @click="deleteItem(item)"
                                            class="text-red-400 hover:text-red-300"
                                            title="Delete"
                                        >
                                            <Trash2 class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="filteredMedia.length === 0" class="text-center py-12">
                <Image class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-white">{{ t('admin.media.emptyState.title') }}</h3>
                <p class="mt-1 text-sm text-gray-400">
                    {{ t('admin.media.emptyState.description') }}
                </p>
                <div class="mt-6">
                    <Link
                        :href="route('admin.upload')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors"
                    >
                        <Image class="w-4 h-4 mr-2" />
                        {{ t('admin.media.uploadButton') }}
                    </Link>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-gray-900 rounded-lg p-6 w-full max-w-md mx-4 border border-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">Edit Media</h3>
                    <button @click="closeEditModal" class="text-gray-400 hover:text-gray-300">
                        <X class="w-6 h-6" />
                    </button>
                </div>
                
                <form @submit.prevent="saveEdit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            Title
                        </label>
                        <input
                            v-model="editForm.title"
                            type="text"
                            placeholder="Enter media title"
                            class="w-full px-3 py-2 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 bg-black text-white placeholder-gray-500"
                        />
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            Description
                        </label>
                        <textarea
                            v-model="editForm.description"
                            rows="3"
                            placeholder="Enter media description"
                            class="w-full px-3 py-2 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 bg-black text-white placeholder-gray-500"
                        ></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-1">
                            Alt Text
                        </label>
                        <input
                            v-model="editForm.alt_text"
                            type="text"
                            placeholder="Enter alt text for accessibility"
                            class="w-full px-3 py-2 border border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 bg-black text-white placeholder-gray-500"
                        />
                    </div>
                    
                    <div class="flex justify-end space-x-3 pt-4">
                        <button
                            type="button"
                            @click="closeEditModal"
                            class="px-4 py-2 text-sm text-gray-300 bg-gray-800 border border-gray-600 rounded-md hover:bg-gray-700"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="editForm.processing"
                            class="px-4 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50"
                        >
                            {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-gray-900 rounded-lg p-6 w-full max-w-md mx-4 border border-gray-800">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-white">Delete Media</h3>
                    <button @click="closeDeleteModal" class="text-gray-400 hover:text-gray-300">
                        <X class="w-6 h-6" />
                    </button>
                </div>
                
                <div class="mb-6">
                    <p class="text-sm text-gray-400">
                        <span v-if="deletingItem">
                            Are you sure you want to delete "<strong class="text-white">{{ deletingItem.title }}</strong>"?
                        </span>
                        <span v-else>
                            Are you sure you want to delete {{ selectedItems.length }} selected items?
                        </span>
                    </p>
                    <p class="text-sm text-red-400 mt-2">
                        This action cannot be undone.
                    </p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button
                        @click="closeDeleteModal"
                        class="px-4 py-2 text-sm text-gray-300 bg-gray-800 border border-gray-600 rounded-md hover:bg-gray-700"
                    >
                        Cancel
                    </button>
                    <button
                        @click="confirmDelete"
                        class="px-4 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700"
                    >
                        Delete
                    </button>
                </div>
            </div>
        </div>

    </AdminLayout>
</template> 