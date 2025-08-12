<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Home, 
    Image, 
    Upload, 
    Settings, 
    Menu, 
    X,
    LogOut,
    User,
    Eye,
    Users
} from 'lucide-vue-next';
import ThemeToggle from '@/components/ThemeToggle.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';

const $page = usePage();

const isSidebarOpen = ref(false);

const navigation = [
    { name: 'Dashboard', href: route('admin.dashboard'), icon: Home },
    { name: 'Users', href: route('admin.users'), icon: Users },
    { name: 'Media Library', href: route('admin.media'), icon: Image },
    { name: 'Upload Media', href: route('admin.upload'), icon: Upload },
    { name: 'Settings', href: route('admin.settings'), icon: Settings },
];
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex transition-colors duration-300">
        <!-- Mobile sidebar overlay -->
        <div 
            v-if="isSidebarOpen" 
            class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 dark:bg-black dark:bg-opacity-75 lg:hidden"
            @click="isSidebarOpen = false"
        ></div>

        <!-- Sidebar -->
        <div 
            :class="[
                'lg:w-1/8 w-64 h-screen bg-white dark:bg-gray-800 shadow-lg transform transition-transform duration-300 ease-in-out flex flex-col flex-shrink-0',
                'fixed inset-y-0 left-0 z-50',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'lg:static lg:translate-x-0 lg:inset-0',
            ]"
        >
            <!-- Header -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-600 to-pink-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-sm">A</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Admin Panel</span>
                </div>
                <button 
                    @click="isSidebarOpen = false"
                    class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200"
                >
                    <X class="w-6 h-6" />
                </button>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 mt-6 px-3 overflow-y-auto">
                <div class="space-y-1">
                    <Link
                        v-for="item in navigation"
                        :key="item.name"
                        :href="item.href"
                        :class="[
                            'group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                            $page.url.startsWith(item.href)
                                ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 shadow-sm'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white hover:shadow-sm'
                        ]"
                    >
                        <component 
                            :is="item.icon" 
                            :class="[
                                'mr-3 h-5 w-5 flex-shrink-0',
                                $page.url.startsWith(item.href)
                                    ? 'text-purple-500 dark:text-purple-400'
                                    : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400'
                            ]"
                        />
                        <span class="truncate">{{ item.name }}</span>
                    </Link>
                </div>
            </nav>

            <!-- User section -->
            <div class="flex-shrink-0 p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                        <User class="w-4 h-4 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">Admin User</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">admin@photostudio.com</p>
                    </div>
                    <Link 
                        :href="route('logout')" 
                        method="post" 
                        as="button"
                        class="text-gray-400 dark:text-gray-500 hover:text-red-500 dark:hover:text-red-400 flex-shrink-0 p-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200"
                    >
                        <LogOut class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 lg:w-3/4 ml-0 lg:ml-0 flex flex-col min-h-screen max-h-screen overflow-hidden">
            <!-- Top bar -->
            <div class="sticky top-0 z-10 bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <button 
                        @click="isSidebarOpen = true"
                        class="lg:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        <Menu class="w-6 h-6" />
                    </button>
                    
                    <div class="flex-1 lg:hidden"></div>
                    
                    <div class="flex items-center space-x-4">
                        <LanguageSwitcher />
                        <ThemeToggle />
                        <Link 
                            :href="route('home')" 
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                        >
                            <Eye class="w-4 h-4 mr-2" />
                            View Site
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto py-6 bg-gray-50 dark:bg-gray-900">
                <div class="px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template> 