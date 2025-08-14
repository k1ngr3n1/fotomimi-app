<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { 
    Home, 
    Image, 
    Upload, 
    Menu, 
    X,
    LogOut,
    User,
    Eye,
    Users
} from 'lucide-vue-next';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import ToastContainer from '@/components/ToastContainer.vue';
import { useTranslation } from '@/composables/useTranslation';

const { t, initLanguage } = useTranslation();

const $page = usePage();

const isSidebarOpen = ref(false);

// Get user data from the page props
const user = computed(() => $page.props.auth?.user);

// Always use dark logo for admin
const logoUrl = computed(() => '/output-onlinepngtools.png');

const navigation = [
    { nameKey: 'admin.navigation.dashboard', href: route('admin.dashboard'), icon: Home },
    { nameKey: 'admin.navigation.users', href: route('admin.users'), icon: Users },
    { nameKey: 'admin.navigation.mediaLibrary', href: route('admin.media'), icon: Image },
    { nameKey: 'admin.navigation.uploadMedia', href: route('admin.upload'), icon: Upload },
];

// Add admin-page class to body when component mounts
onMounted(() => {
    document.body.classList.add('admin-page');
    // Force dark mode for admin
    document.documentElement.classList.add('dark');
    initLanguage();
});

// Remove admin-page class when component unmounts
onUnmounted(() => {
    document.body.classList.remove('admin-page');
});
</script>

<template>
    <div class="min-h-screen bg-black flex transition-colors duration-300">
        <!-- Mobile sidebar overlay -->
        <div 
            v-if="isSidebarOpen" 
            class="fixed inset-0 z-40 bg-black bg-opacity-75 lg:hidden"
            @click="isSidebarOpen = false"
        ></div>

        <!-- Sidebar -->
        <div 
            :class="[
                'lg:w-1/8 w-64 h-screen bg-black shadow-lg transform transition-transform duration-300 ease-in-out flex flex-col flex-shrink-0',
                'fixed inset-y-0 left-0 z-50',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'lg:static lg:translate-x-0 lg:inset-0',
            ]"
        >

            <div class="flex items-center space-x-2 border-b p-2">
                <img :src="logoUrl" alt="FOTOMIMI" class="h-12 w-auto ml-3" />
            </div>

            <!-- Navigation -->
            <nav class="flex-1 mt-6 px-3 overflow-y-auto">
                <div class="space-y-1">
                    <Link
                        v-for="item in navigation"
                        :key="item.nameKey"
                        :href="item.href"
                        :class="[
                            'group flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200',
                            $page.url.startsWith(item.href)
                                ? 'bg-red-900/30 text-red-400 shadow-sm border border-red-800'
                                : 'text-gray-300 hover:bg-gray-900 hover:text-white hover:shadow-sm'
                        ]"
                    >
                        <component 
                            :is="item.icon" 
                            :class="[
                                'mr-3 h-5 w-5 flex-shrink-0',
                                $page.url.startsWith(item.href)
                                    ? 'text-red-400'
                                    : 'text-gray-400 group-hover:text-gray-300'
                            ]"
                        />
                        <span class="truncate">{{ t(item.nameKey) }}</span>
                    </Link>
                </div>
            </nav>

            <!-- User section -->
            <div class="flex-shrink-0 p-4 border-t border-gray-800 bg-black">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                        <User class="w-4 h-4 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ user?.name || 'Admin User' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ user?.email || 'admin@photostudio.com' }}</p>
                    </div>
                    <Link 
                        :href="route('logout')" 
                        method="post" 
                        as="button"
                        class="text-gray-400 hover:text-red-400 flex-shrink-0 p-1 rounded-lg hover:bg-red-900/20 transition-all duration-200"
                    >
                        <LogOut class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 lg:w-3/4 ml-0 lg:ml-0 flex flex-col min-h-screen max-h-screen overflow-hidden">
            <!-- Top bar -->
            <div class="sticky top-0 z-10 bg-black shadow-sm border-b border-gray-800">
                <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
                    <button 
                        @click="isSidebarOpen = true"
                        class="lg:hidden text-gray-400 hover:text-white p-2 rounded-lg hover:bg-gray-900 transition-colors"
                    >
                        <Menu class="w-6 h-6" />
                    </button>
                    
                    <div class="flex-1 lg:hidden"></div>
                    
                    <div class="flex items-center space-x-4">
                        <Link 
                            :href="route('home')" 
                            class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-300 bg-gray-900 rounded-lg hover:bg-gray-800 hover:text-white transition-colors"
                        >
                            <Eye class="w-4 h-4 mr-2" />
                            {{ t('admin.navigation.viewSite') }}
                        </Link>
                        <LanguageSwitcher />
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 overflow-y-auto py-6 bg-black">
                <div class="px-4 sm:px-6 lg:px-8">
                    <slot />
                </div>
            </main>
        </div>
        
        <!-- Toast Container -->
        <ToastContainer />
    </div>
</template> 