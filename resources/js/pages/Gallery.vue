<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import Navigation from '@/components/Navigation.vue';
import { Filter, X, Maximize2, Minimize2 } from 'lucide-vue-next';
import { useTranslation } from '@/composables/useTranslation';

const { t, initLanguage } = useTranslation();

onMounted(() => {
    initLanguage();
});

interface Props {
    category?: string;
    media?: any[];
    categories?: Record<string, string>;
}

const props = defineProps<Props>();

const selectedCategory = ref(props.category || 'all');
const media = ref(props.media || []);
const categories = ref(props.categories || {});
const isLightboxOpen = ref(false);
const selectedImage = ref(null);
const isFullSize = ref(false);

// Use real media data from props or fallback to empty array
const galleryImages = computed(() => {
    if (media.value && media.value.length > 0) {
        return media.value.map(item => ({
            id: item.id,
            category: item.category,
            src: item.url,
            alt: item.alt_text || item.title,
            title: item.title,
            type: item.type
        }));
    }
    return [];
});

// Reactive category list with computed counts
const categoryList = computed(() => [
    { id: 'all', nameKey: 'gallery.filter.allPhotos', count: galleryImages.value.length },
    { id: 'wedding', nameKey: 'gallery.filter.weddings', count: galleryImages.value.filter(img => img.category === 'wedding').length },
    { id: 'video', nameKey: 'gallery.filter.video', count: galleryImages.value.filter(img => img.category === 'video').length },
    { id: 'on-set', nameKey: 'gallery.filter.onSet', count: galleryImages.value.filter(img => img.category === 'on-set').length },
    { id: 'studio', nameKey: 'gallery.filter.studio', count: galleryImages.value.filter(img => img.category === 'studio').length },
    { id: 'modelling', nameKey: 'gallery.filter.modelling', count: galleryImages.value.filter(img => img.category === 'modelling').length },
    { id: 'concert', nameKey: 'gallery.filter.concerts', count: galleryImages.value.filter(img => img.category === 'concert').length },
    { id: 'baptism', nameKey: 'gallery.filter.baptism', count: galleryImages.value.filter(img => img.category === 'baptism').length }
]);

const filteredImages = computed(() => {
    if (selectedCategory.value === 'all') {
        return galleryImages.value;
    }
    return galleryImages.value.filter(image => image.category === selectedCategory.value);
});

const openLightbox = (image: any) => {
    selectedImage.value = image;
    isLightboxOpen.value = true;
    isFullSize.value = false;
    document.body.style.overflow = 'hidden';
};

const closeLightbox = () => {
    isLightboxOpen.value = false;
    selectedImage.value = null;
    isFullSize.value = false;
    document.body.style.overflow = 'auto';
};

const toggleFullSize = () => {
    isFullSize.value = !isFullSize.value;
};

const selectCategory = (categoryId: string) => {
    selectedCategory.value = categoryId;
};

// Handle keyboard events for lightbox
const handleKeydown = (event: KeyboardEvent) => {
    if (!isLightboxOpen.value) return;
    
    if (event.key === 'Escape') {
        closeLightbox();
    } else if (event.key === 'f' || event.key === 'F') {
        toggleFullSize();
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
});

import { onUnmounted } from 'vue';
onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
});
</script>

<template>
    <Head :title="t('gallery.title')">
        <meta name="description" :content="t('gallery.subtitle')" />
    </Head>

    <div class="min-h-screen bg-white dark:bg-black transition-colors duration-300">
        <Navigation />

        <!-- Header -->
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-7xl mx-auto text-center text-black dark:text-white">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">{{ t('gallery.title') }}</h1>
                <p class="text-xl text-red-600 dark:text-red-500 max-w-3xl mx-auto">
                    {{ t('gallery.subtitle') }}
                </p>
            </div>
        </section>

        <!-- Category Filter -->
        <section class="py-8 px-4 bg-white dark:bg-black border-b border-black/10 dark:border-white/10 transition-colors duration-300">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center gap-4 mb-6">
                    <Filter class="w-5 h-5 text-red-600 dark:text-red-500" />
                    <h2 class="text-lg font-semibold text-black dark:text-white">{{ t('gallery.filter.title') }}</h2>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button
                        v-for="category in categoryList"
                        :key="category.id"
                        @click="selectCategory(category.id)"
                        :class="[
                            'px-4 py-2 rounded-lg font-medium transition-all duration-200',
                            selectedCategory === category.id
                                ? 'bg-red-600 text-white dark:bg-red-600 dark:text-white'
                                : 'bg-white dark:bg-black text-black dark:text-white border border-black/10 dark:border-white/10 hover:bg-red-50 dark:hover:bg-red-900 hover:text-red-600 dark:hover:text-red-400'
                        ]"
                    >
                        {{ t(category.nameKey) }}
                        <span class="ml-2 text-sm opacity-75">({{ category.count }})</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- Gallery Grid -->
        <section class="py-12 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 gallery-grid">
                    <div
                        v-for="image in filteredImages"
                        :key="image.id"
                        @click="openLightbox(image)"
                        class="group relative overflow-hidden rounded-xl bg-white dark:bg-black shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer border border-black/10 dark:border-white/10 gallery-item lightbox-trigger"
                    >
                        <div class="aspect-[4/3] overflow-hidden">
                            <img
                                v-if="image.type === 'photo'"
                                :src="image.src"
                                :alt="image.alt"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                            />
                            <div
                                v-else
                                class="w-full h-full relative group-hover:scale-110 transition-transform duration-300"
                            >
                                <video
                                    :src="image.src"
                                    class="w-full h-full object-cover"
                                    preload="metadata"
                                    muted
                                    @click.stop
                                >
                                    <source :src="image.src" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-20">
                                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/60 dark:group-hover:bg-white/60 transition-all duration-300 flex items-end">
                            <div class="p-4 text-white dark:text-black opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <h3 class="font-semibold">{{ image.title }}</h3>
                                <p class="text-sm text-red-600 dark:text-red-500">
                                    {{ t(`gallery.filter.${image.category === 'on-set' ? 'onSet' : image.category}`) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredImages.length === 0" class="text-center py-20">
                    <div class="text-red-600 dark:text-red-500 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-black dark:text-white mb-2">{{ t('gallery.emptyState.title') }}</h3>
                    <p class="text-black/70 dark:text-white/70">{{ t('gallery.emptyState.description') }}</p>
                </div>
            </div>
        </section>

        <!-- Enhanced Lightbox -->
        <div
            v-if="isLightboxOpen"
            class="fixed inset-0 bg-black/95 dark:bg-white/95 z-50 flex items-center justify-center p-4"
            @click="closeLightbox"
        >
            <div 
                :class="[
                    'relative transition-all duration-300',
                    isFullSize 
                        ? 'w-full h-full overflow-auto' 
                        : 'max-w-4xl max-h-full'
                ]"
            >
                <!-- Close Button -->
                <button
                    @click="closeLightbox"
                    class="absolute -top-12 right-0 text-white dark:text-black hover:text-red-600 dark:hover:text-red-500 transition-colors z-10"
                >
                    <X class="w-8 h-8" />
                </button>

                <!-- Full Size Toggle Button -->
                <button
                    @click="toggleFullSize"
                    class="absolute -top-12 right-12 text-white dark:text-black hover:text-red-600 dark:hover:text-red-500 transition-colors z-10"
                    :title="isFullSize ? 'Fit to screen' : 'Show full size'"
                >
                    <Maximize2 v-if="!isFullSize" class="w-8 h-8" />
                    <Minimize2 v-else class="w-8 h-8" />
                </button>

                <!-- Media Container -->
                <div 
                    :class="[
                        'transition-all duration-300',
                        isFullSize 
                            ? 'w-full h-full flex items-center justify-center' 
                            : 'flex items-center justify-center'
                    ]"
                >
                    <img
                        v-if="selectedImage && selectedImage.type === 'photo'"
                        :src="selectedImage.src"
                        :alt="selectedImage.alt"
                        :class="[
                            'transition-all duration-300',
                            isFullSize 
                                ? 'w-auto h-auto max-w-none max-h-none' 
                                : 'max-w-full max-h-full object-contain rounded-lg'
                        ]"
                        @click.stop
                    />
                    <video
                        v-else-if="selectedImage && selectedImage.type === 'video'"
                        :src="selectedImage.src"
                        :class="[
                            'transition-all duration-300',
                            isFullSize 
                                ? 'w-auto h-auto max-w-none max-h-none' 
                                : 'max-w-full max-h-full object-contain rounded-lg'
                        ]"
                        controls
                        autoplay
                        @click.stop
                    >
                        <source :src="selectedImage.src" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <!-- Media Info -->
                <div 
                    v-if="selectedImage" 
                    :class="[
                        'absolute transition-all duration-300',
                        isFullSize 
                            ? 'bottom-4 left-4 bg-black/50 dark:bg-white/50 backdrop-blur-sm rounded-lg p-4' 
                            : 'bottom-4 left-4',
                        isFullSize ? 'text-white dark:text-black' : 'text-white dark:text-black'
                    ]"
                >
                    <h3 class="text-xl font-semibold">{{ selectedImage.title }}</h3>
                    <p class="text-red-600 dark:text-red-500">
                        {{ t(`gallery.filter.${selectedImage.category === 'on-set' ? 'onSet' : selectedImage.category}`) }}
                    </p>
                </div>

                <!-- Full Size Instructions -->
                <div 
                    v-if="!isFullSize" 
                    class="absolute bottom-4 right-4 text-white dark:text-black text-sm opacity-75"
                >
                    Press 'F' or click the button to view full size
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-4xl mx-auto text-center text-black dark:text-white">
                <h2 class="text-3xl md:text-4xl font-bold mb-6">
                    {{ t('gallery.cta.title') }}
                </h2>
                <p class="text-xl text-red-600 dark:text-red-500 mb-8">
                    {{ t('gallery.cta.subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        :href="route('booking')"
                        class="border-2 border-red-600 text-black dark:text-white px-8 py-4 rounded-lg font-semibold hover:bg-red-600 hover:text-black dark:hover:bg-red-600 dark:hover:text-white transition-all duration-200 btn-camera"
                    >
                        {{ t('gallery.cta.bookingButton') }}
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template> 