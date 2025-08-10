<script setup lang="ts">
import { useTranslation } from '@/composables/useTranslation';
import { onMounted, ref, computed } from 'vue';
import { ChevronDown } from 'lucide-vue-next';

const { t, setLanguage, getCurrentLanguage, getAvailableLanguages, initLanguage } = useTranslation();

const languages = getAvailableLanguages();
const isDropdownOpen = ref(false);

// Make currentLang reactive
const currentLang = computed(() => getCurrentLanguage());

onMounted(() => {
    initLanguage();
});

const toggleDropdown = () => {
    isDropdownOpen.value = !isDropdownOpen.value;
};

const closeDropdown = () => {
    isDropdownOpen.value = false;
};

const switchLanguage = (langCode: string) => {
    setLanguage(langCode);
    closeDropdown();
    // Reload the page to apply language changes
    window.location.reload();
};

// Close dropdown when clicking outside
const handleClickOutside = (event: Event) => {
    const target = event.target as HTMLElement;
    if (!target.closest('.language-switcher')) {
        closeDropdown();
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

// Cleanup event listener
import { onUnmounted } from 'vue';
onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <div class="relative language-switcher">
        <button
            @click="toggleDropdown"
            class="flex items-center gap-2 px-3 py-2 rounded-lg bg-white dark:bg-black border border-black/10 dark:border-white/10 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors"
        >
            <span class="text-lg">{{ currentLang === 'en' ? '🇺🇸' : '🇭🇷' }}</span>
            <ChevronDown class="w-4 h-4 text-black dark:text-white transition-transform duration-200" :class="{ 'rotate-180': isDropdownOpen }" />
        </button>
        
        <div 
            v-if="isDropdownOpen"
            class="absolute top-full right-0 mt-2 bg-white dark:bg-black border border-black/10 dark:border-white/10 rounded-lg shadow-lg z-50 min-w-[120px]"
        >
            <div class="py-1">
                <button
                    v-for="language in languages"
                    :key="language.code"
                    @click="switchLanguage(language.code)"
                    :class="[
                        'w-full flex items-center gap-3 px-4 py-2 text-left hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors',
                        currentLang === language.code ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'text-black dark:text-white'
                    ]"
                >
                    <span class="text-lg">{{ language.code === 'en' ? '🇺🇸' : '🇭🇷' }}</span>
                    <span class="text-sm font-medium">{{ language.name }}</span>
                </button>
            </div>
        </div>
    </div>
</template> 