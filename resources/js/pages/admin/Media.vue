<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AdminLayout from '@/layouts/AdminLayout.vue';
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
    MoreVertical
} from 'lucide-vue-next';

const props = defineProps({
    media: Array,
    categories: Array,
    filters: Object
});

const form = useForm({
    search: '',
    category: '',
    type: '',
    featured: ''
});

const viewMode = ref('grid'); // 'grid' or 'list'
const selectedItems = ref([]);
const showDeleteModal = ref(false);
const itemToDelete = ref(null);

const categories = [
    { value: '', label: 'All Categories' },
    { value: 'wedding', label: 'Weddings' },
    { value: 'baptism', label: 'Baptisms' },
    { value: 'concert', label: 'Concerts' },
    { value: 'studio', label: 'Studio' },
    { value: 'modelling', label: 'Modelling' },
    { value: 'other', label: 'Other' }
];

const typeFilters = [
    { value: '', label: 'All Types' },
    { value: 'image', label: 'Images' },
    { value: 'video', label: 'Videos' }
];

const featuredFilters = [
    { value: '', label: 'All Items' },
    { value: '1', label: 'Featured Only' },
    { value: '0', label: 'Not Featured' }
];

const filteredMedia = computed(() => {
    let filtered = props.media || [];
    
    if (form.search) {
        const search = form.search.toLowerCase();
        filtered = filtered.filter(item => 
            item.title.toLowerCase().includes(search) ||
            item.description.toLowerCase().includes(search)
        );
    }
    
    if (form.category) {
        filtered = filtered.filter(item => item.category === form.category);
    }
    
    if (form.type) {
        filtered = filtered.filter(item => item.type === form.type);
    }
    
    if (form.featured !== '') {
        filtered = filtered.filter(item => item.is_featured == form.featured);
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

const deleteItem = (item) => {
    itemToDelete.value = item;
    showDeleteModal.value = true;
};

const confirmDelete = () => {
    if (itemToDelete.value) {
        form.delete(route('admin.media.destroy', itemToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false;
                itemToDelete.value = null;
            }
        });
    }
};

const toggleFeatured = (item) => {
    form.patch(route('admin.media.update', item.id), {
        data: {
            is_featured: !item.is_featured
        }
    });
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
                        <h1 class="text-3xl font-bold text-gray-900">Media Library</h1>
                        <p class="mt-2 text-gray-600">
                            Manage your photos and videos. Upload, edit, and organize your media files.
                        </p>
                    </div>
                    <Link
                        :href="route('admin.upload')"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                    >
                        <Image class="w-4 h-4 mr-2" />
                        Upload Media
                    </Link>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="relative">
                            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" />
                            <input
                                v-model="form.search"
                                type="text"
                                placeholder="Search media..."
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900"
                            />
                        </div>

                        <!-- Category Filter -->
                        <select
                            v-model="form.category"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900"
                        >
                            <option
                                v-for="category in categories"
                                :key="category.value"
                                :value="category.value"
                            >
                                {{ category.label }}
                            </option>
                        </select>

                        <!-- Type Filter -->
                        <select
                            v-model="form.type"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900"
                        >
                            <option
                                v-for="type in typeFilters"
                                :key="type.value"
                                :value="type.value"
                            >
                                {{ type.label }}
                            </option>
                        </select>

                        <!-- Featured Filter -->
                        <select
                            v-model="form.featured"
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-900"
                        >
                            <option
                                v-for="filter in featuredFilters"
                                :key="filter.value"
                                :value="filter.value"
                            >
                                {{ filter.label }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Results Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center space-x-4">
                    <p class="text-sm text-gray-600">
                        {{ filteredMedia.length }} item{{ filteredMedia.length !== 1 ? 's' : '' }} found
                    </p>
                    
                    <div v-if="selectedItems.length > 0" class="flex items-center space-x-2">
                        <span class="text-sm text-gray-600">
                            {{ selectedItems.length }} selected
                        </span>
                        <button
                            @click="selectAll"
                            class="text-sm text-purple-600 hover:text-purple-700"
                        >
                            {{ selectedItems.length === filteredMedia.length ? 'Deselect All' : 'Select All' }}
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
                                ? 'bg-purple-100 text-purple-600' 
                                : 'text-gray-400 hover:text-gray-600'
                        ]"
                    >
                        <Grid class="w-5 h-5" />
                    </button>
                    <button
                        @click="viewMode = 'list'"
                        :class="[
                            'p-2 rounded-lg transition-colors',
                            viewMode === 'list' 
                                ? 'bg-purple-100 text-purple-600' 
                                : 'text-gray-400 hover:text-gray-600'
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
                    class="bg-white rounded-lg shadow overflow-hidden hover:shadow-lg transition-shadow"
                >
                    <!-- Media Preview -->
                    <div class="relative aspect-square bg-gray-100">
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
                                    @click="toggleFeatured(item)"
                                    :class="[
                                        'p-2 rounded-full text-white transition-colors',
                                        item.is_featured ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-600 hover:bg-gray-700'
                                    ]"
                                >
                                    <Star :class="['w-4 h-4', item.is_featured ? 'fill-current' : '']" />
                                </button>
                                <Link
                                    :href="route('admin.media.edit', item.id)"
                                    class="p-2 bg-blue-500 hover:bg-blue-600 rounded-full text-white transition-colors"
                                >
                                    <Edit class="w-4 h-4" />
                                </Link>
                                <button
                                    @click="deleteItem(item)"
                                    class="p-2 bg-red-500 hover:bg-red-600 rounded-full text-white transition-colors"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Featured Badge -->
                        <div
                            v-if="item.is_featured"
                            class="absolute top-2 left-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-medium"
                        >
                            Featured
                        </div>

                        <!-- Type Badge -->
                        <div class="absolute top-2 right-2 bg-black bg-opacity-75 text-white px-2 py-1 rounded-full text-xs font-medium">
                            {{ item.type }}
                        </div>
                    </div>

                    <!-- Media Info -->
                    <div class="p-4">
                        <h3 class="font-medium text-gray-900 truncate">{{ item.title }}</h3>
                        <p class="text-sm text-gray-500 mt-1">{{ item.category }}</p>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-400">{{ formatDate(item.created_at) }}</span>
                            <span class="text-xs text-gray-400">{{ formatFileSize(item.file_size) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media List -->
            <div v-else class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Media
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Title
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Size
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Uploaded
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="item in filteredMedia"
                                :key="item.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden">
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
                                        <div class="text-sm font-medium text-gray-900">{{ item.title }}</div>
                                        <div class="text-sm text-gray-500">{{ item.description }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ item.category }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span 
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            item.type === 'photo' 
                                                ? 'bg-blue-100 text-blue-800' 
                                                : 'bg-purple-100 text-purple-800'
                                        ]"
                                    >
                                        {{ item.type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatFileSize(item.file_size) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ formatDate(item.created_at) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <button
                                            @click="toggleFeatured(item)"
                                            :class="[
                                                'p-1 rounded transition-colors',
                                                item.is_featured ? 'text-yellow-600 hover:text-yellow-700' : 'text-gray-400 hover:text-gray-600'
                                            ]"
                                        >
                                            <Star :class="['w-4 h-4', item.is_featured ? 'fill-current' : '']" />
                                        </button>
                                        <Link
                                            :href="route('admin.media.edit', item.id)"
                                            class="text-blue-600 hover:text-blue-700"
                                        >
                                            <Edit class="w-4 h-4" />
                                        </Link>
                                        <button
                                            @click="deleteItem(item)"
                                            class="text-red-600 hover:text-red-700"
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
                <h3 class="mt-2 text-sm font-medium text-gray-900">No media found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Try adjusting your search or filter criteria.
                </p>
                <div class="mt-6">
                    <Link
                        :href="route('admin.upload')"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors"
                    >
                        <Image class="w-4 h-4 mr-2" />
                        Upload Media
                    </Link>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                    @click="showDeleteModal = false"
                ></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <Trash2 class="h-6 w-6 text-red-600" />
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Delete Media
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete "{{ itemToDelete?.title }}"? This action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button
                            @click="confirmDelete"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Delete
                        </button>
                        <button
                            @click="showDeleteModal = false"
                            type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template> 