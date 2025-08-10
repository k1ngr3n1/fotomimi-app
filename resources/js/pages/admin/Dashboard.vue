<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { 
    Image, 
    Video, 
    Upload, 
    Eye, 
    Star,
    TrendingUp,
    Calendar,
    Users
} from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    recentUploads: Array
});

const stats = [
    {
        name: 'Total Images',
        value: props.stats?.total_images?.toLocaleString() || '0',
        change: '+12%',
        changeType: 'positive',
        icon: Image
    },
    {
        name: 'Total Videos',
        value: props.stats?.total_videos?.toLocaleString() || '0',
        change: '+5%',
        changeType: 'positive',
        icon: Video
    },
    {
        name: 'Featured Media',
        value: props.stats?.featured_media?.toLocaleString() || '0',
        change: '+8%',
        changeType: 'positive',
        icon: Star
    },
    {
        name: 'Total Media',
        value: props.stats?.total_media?.toLocaleString() || '0',
        change: '+23%',
        changeType: 'positive',
        icon: Eye
    }
];

const recentUploads = props.recentUploads || [];

const quickActions = [
    {
        name: 'Upload Media',
        description: 'Add new photos and videos',
        href: route('admin.upload'),
        icon: Upload,
        color: 'bg-purple-500'
    },
    {
        name: 'View Gallery',
        description: 'Browse all media files',
        href: route('admin.media'),
        icon: Image,
        color: 'bg-blue-500'
    },
    {
        name: 'Analytics',
        description: 'View site statistics',
        href: '#',
        icon: TrendingUp,
        color: 'bg-green-500'
    }
];
</script>

<template>
    <Head title="Admin Dashboard">
        <meta name="description" content="Admin dashboard for photography website." />
    </Head>

    <AdminLayout>
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
                <p class="mt-2 text-gray-600">
                    Welcome back! Here's an overview of your photography website.
                </p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div
                    v-for="stat in stats"
                    :key="stat.name"
                    class="bg-white rounded-lg shadow p-6"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <component 
                                    :is="stat.icon" 
                                    class="w-5 h-5 text-purple-600"
                                />
                            </div>
                        </div>
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-medium text-gray-500">{{ stat.name }}</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stat.value }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span 
                            :class="[
                                'inline-flex items-center text-sm font-medium',
                                stat.changeType === 'positive' ? 'text-green-600' : 'text-red-600'
                            ]"
                        >
                            {{ stat.change }}
                        </span>
                        <span class="text-sm text-gray-500 ml-1">from last month</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow mb-8">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Link
                            v-for="action in quickActions"
                            :key="action.name"
                            :href="action.href"
                            class="group block p-6 border border-gray-200 rounded-lg hover:border-purple-300 hover:shadow-md transition-all duration-200"
                        >
                            <div class="flex items-center">
                                <div 
                                    :class="[
                                        'w-10 h-10 rounded-lg flex items-center justify-center text-white',
                                        action.color
                                    ]"
                                >
                                    <component 
                                        :is="action.icon" 
                                        class="w-5 h-5"
                                    />
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg font-medium text-gray-900 group-hover:text-purple-600 transition-colors">
                                        {{ action.name }}
                                    </h3>
                                    <p class="text-sm text-gray-500">{{ action.description }}</p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Recent Uploads -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Uploads</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div
                            v-for="upload in recentUploads"
                            :key="upload.id"
                            class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                        >
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden">
                                    <img
                                        v-if="upload.type === 'image'"
                                        :src="upload.thumbnail"
                                        :alt="upload.title"
                                        class="w-full h-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="w-full h-full flex items-center justify-center"
                                    >
                                        <Video class="w-8 h-8 text-gray-400" />
                                    </div>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-medium text-gray-900 truncate">
                                    {{ upload.title }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ upload.category.charAt(0).toUpperCase() + upload.category.slice(1) }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ upload.uploaded_at }}
                                </p>
                            </div>
                            <div class="flex-shrink-0">
                                <span 
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        upload.type === 'image' 
                                            ? 'bg-blue-100 text-blue-800' 
                                            : 'bg-purple-100 text-purple-800'
                                    ]"
                                >
                                    {{ upload.type }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6 text-center">
                        <Link
                            :href="route('admin.media')"
                            class="text-purple-600 hover:text-purple-700 text-sm font-medium"
                        >
                            View all media →
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template> 