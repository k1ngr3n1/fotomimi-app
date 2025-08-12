<script setup lang="ts">
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import LanguageSwitcher from '@/components/LanguageSwitcher.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { useTranslation } from '@/composables/useTranslation';

defineProps<{
    title?: string;
    description?: string;
}>();

const { initLanguage } = useTranslation();

onMounted(() => {
    initLanguage();
});
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
        <!-- Language Switcher positioned at top right -->
        <div class="absolute top-4 right-4">
            <LanguageSwitcher />
        </div>
        
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link :href="route('home')" class="flex flex-col items-center gap-2 font-medium">
                        <div class="mb-1 flex h-64 w-64 items-center justify-center rounded-md">
                            <AppLogoIcon class="h-64 w-64 object-contain" />
                        </div>
                        <span class="sr-only">{{ title }}</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">{{ description }}</p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
