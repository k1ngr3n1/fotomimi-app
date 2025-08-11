<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { Menu, X } from 'lucide-vue-next';
import { useTranslation } from '@/composables/useTranslation';
import LanguageSwitcher from './LanguageSwitcher.vue';
import ThemeToggle from './ThemeToggle.vue';

const { t, initLanguage } = useTranslation();
const isMenuOpen = ref(false);

const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
};

onMounted(() => {
    initLanguage();
});
</script>

<template>
    <nav class="bg-white/95 dark:bg-black/95 backdrop-blur-sm border-b border-black/10 dark:border-white/10 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <Link :href="route('home')" class="flex items-center space-x-2">
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-white dark:bg-black">
                        <img src="/fotomimi-logo.jpg" alt="Mimi Logo" class="object-contain w-8 h-8" />
                    </div>
                    <span class="text-xl font-bold text-black dark:text-white">Fotomimi</span>
                </Link>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <Link 
                        :href="route('home')" 
                        class="text-black dark:text-white hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200"
                    >
                        {{ t('navigation.home') }}
                    </Link>
                    <Link 
                        :href="route('gallery')" 
                        class="text-black dark:text-white hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200"
                    >
                        {{ t('navigation.gallery') }}
                    </Link>
                    <Link 
                        :href="route('about')" 
                        class="text-black dark:text-white hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200"
                    >
                        {{ t('navigation.about') }}
                    </Link>
                    <Link 
                        :href="route('contact')" 
                        class="bg-red-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-black hover:text-red-600 dark:bg-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-200"
                    >
                        {{ t('navigation.contact') }}
                    </Link>
                    <LanguageSwitcher />
                    <ThemeToggle />
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center gap-2">
                    <ThemeToggle />
                    <button
                        @click="toggleMenu"
                        class="text-black dark:text-white hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200"
                    >
                        <Menu v-if="!isMenuOpen" class="h-6 w-6" />
                        <X v-else class="h-6 w-6" />
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div v-if="isMenuOpen" class="md:hidden">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white dark:bg-black border-t border-black/10 dark:border-white/10">
                    <Link 
                        :href="route('home')" 
                        class="block px-3 py-2 text-black dark:text-white hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200"
                        @click="isMenuOpen = false"
                    >
                        {{ t('navigation.home') }}
                    </Link>
                    <Link 
                        :href="route('gallery')" 
                        class="block px-3 py-2 text-black dark:text-white hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200"
                        @click="isMenuOpen = false"
                    >
                        {{ t('navigation.gallery') }}
                    </Link>
                    <Link 
                        :href="route('about')" 
                        class="block px-3 py-2 text-black dark:text-white hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200"
                        @click="isMenuOpen = false"
                    >
                        {{ t('navigation.about') }}
                    </Link>
                    <Link 
                        :href="route('contact')" 
                        class="block px-3 py-2 text-black dark:text-white hover:text-red-600 dark:hover:text-red-400 transition-colors duration-200"
                        @click="isMenuOpen = false"
                    >
                        {{ t('navigation.contact') }}
                    </Link>
                    <Link 
                        :href="route('booking')" 
                        class="block px-3 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-black hover:text-red-600 dark:bg-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-200"
                        @click="isMenuOpen = false"
                    >
                        {{ t('navigation.booking') }}
                    </Link>
                    <div class="px-3 py-2">
                        <LanguageSwitcher />
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template> 