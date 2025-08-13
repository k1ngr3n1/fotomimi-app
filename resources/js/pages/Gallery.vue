<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import Navigation from '@/components/Navigation.vue';
import { Filter, X, ChevronLeft, ChevronRight, Download, Play, Pause, Volume2, VolumeX, Maximize, Minimize, Loader2 } from 'lucide-vue-next';
import { useTranslation } from '@/composables/useTranslation';

const { t, initLanguage } = useTranslation();

onMounted(() => {
    initLanguage();
});

interface MediaItem {
    id: number;
    category: string;
    src: string;
    alt: string;
    title: string;
    type: 'photo' | 'video';
}

interface Props {
    category?: string;
    media?: any[];
    categories?: Record<string, string>;
}

const props = defineProps<Props>();

const selectedCategory = ref(props.category || 'all');
const media = ref(props.media || []);
const categories = ref(props.categories || {});

// Preview Modal State
const isPreviewOpen = ref(false);
const currentIndex = ref(0);
const isLoading = ref(false);
const isFullscreen = ref(false);

// Video Controls State
const isPlaying = ref(false);
const isMuted = ref(false);
const currentTime = ref(0);
const duration = ref(0);
const volume = ref(1);

// References
const videoRef = ref<HTMLVideoElement | null>(null);
const previewRef = ref<HTMLDivElement | null>(null);

// Use real media data from props or fallback to empty array
const galleryImages = computed((): MediaItem[] => {
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
    { id: 'wedding', nameKey: 'gallery.filter.wedding', count: galleryImages.value.filter(img => img.category === 'wedding').length },
    { id: 'video', nameKey: 'gallery.filter.video', count: galleryImages.value.filter(img => img.category === 'video').length },
    { id: 'on-set', nameKey: 'gallery.filter.onSet', count: galleryImages.value.filter(img => img.category === 'on-set').length },
    { id: 'studio', nameKey: 'gallery.filter.studio', count: galleryImages.value.filter(img => img.category === 'studio').length },
    { id: 'modelling', nameKey: 'gallery.filter.modelling', count: galleryImages.value.filter(img => img.category === 'modelling').length },
    { id: 'travel', nameKey: 'gallery.filter.travel', count: galleryImages.value.filter(img => img.category === 'travel').length },
    { id: 'concert', nameKey: 'gallery.filter.concert', count: galleryImages.value.filter(img => img.category === 'concert').length },
    { id: 'baptism', nameKey: 'gallery.filter.baptism', count: galleryImages.value.filter(img => img.category === 'baptism').length },
    { id: 'other', nameKey: 'gallery.filter.other', count: galleryImages.value.filter(img => img.category === 'other').length }
]);

const filteredImages = computed((): MediaItem[] => {
    if (selectedCategory.value === 'all') {
        return galleryImages.value;
    }
    return galleryImages.value.filter(image => image.category === selectedCategory.value);
});

const currentMedia = computed((): MediaItem | null => {
    return filteredImages.value[currentIndex.value] || null;
});

// Navigation Functions
const openPreview = (image: MediaItem) => {
    const index = filteredImages.value.findIndex(item => item.id === image.id);
    if (index !== -1) {
        currentIndex.value = index;
        isPreviewOpen.value = true;
        isLoading.value = true;
        document.body.style.overflow = 'hidden';
        
        // Reset video state
        isPlaying.value = false;
        isMuted.value = false;
        currentTime.value = 0;
        duration.value = 0;
    }
};

const closePreview = () => {
    isPreviewOpen.value = false;
    isFullscreen.value = false;
    isLoading.value = false;
    document.body.style.overflow = 'auto';
    
    // Stop video if playing
    if (videoRef.value) {
        videoRef.value.pause();
        isPlaying.value = false;
    }
};

const goToPrevious = () => {
    if (currentIndex.value > 0) {
        currentIndex.value--;
        isLoading.value = true;
        resetVideoState();
    }
};

const goToNext = () => {
    if (currentIndex.value < filteredImages.value.length - 1) {
        currentIndex.value++;
        isLoading.value = true;
        resetVideoState();
    }
};

const resetVideoState = () => {
    isPlaying.value = false;
    currentTime.value = 0;
    duration.value = 0;
};

// Video Controls
const togglePlay = () => {
    if (videoRef.value) {
        if (isPlaying.value) {
            videoRef.value.pause();
        } else {
            videoRef.value.play();
        }
    }
};

const toggleMute = () => {
    if (videoRef.value) {
        videoRef.value.muted = !videoRef.value.muted;
        isMuted.value = videoRef.value.muted;
    }
};

const onVideoLoad = () => {
    isLoading.value = false;
    if (videoRef.value) {
        duration.value = videoRef.value.duration;
    }
};

const onVideoTimeUpdate = () => {
    if (videoRef.value) {
        currentTime.value = videoRef.value.currentTime;
    }
};

const onVideoPlay = () => {
    isPlaying.value = true;
};

const onVideoPause = () => {
    isPlaying.value = false;
};

const seekTo = (event: MouseEvent) => {
    if (videoRef.value && duration.value > 0) {
        const rect = (event.currentTarget as HTMLElement).getBoundingClientRect();
        const percent = (event.clientX - rect.left) / rect.width;
        videoRef.value.currentTime = duration.value * percent;
    }
};

const toggleFullscreen = async () => {
    if (!previewRef.value) return;
    
    try {
        if (!document.fullscreenElement) {
            await previewRef.value.requestFullscreen();
            isFullscreen.value = true;
        } else {
            await document.exitFullscreen();
            isFullscreen.value = false;
        }
    } catch (error) {
        console.error('Fullscreen error:', error);
    }
};

// Image loading
const onImageLoad = () => {
    isLoading.value = false;
};

const selectCategory = (categoryId: string) => {
    selectedCategory.value = categoryId;
};

// Generate video thumbnail by seeking to first frame
const seekToFirstFrame = (event: Event) => {
    const video = event.target as HTMLVideoElement;
    if (video && video.duration) {
        const thumbnailTime = Math.min(0.5, video.duration * 0.1);
        video.currentTime = thumbnailTime;
        
        video.addEventListener('seeked', () => {
            video.pause();
        }, { once: true });
    }
};

// Keyboard Navigation
const handleKeydown = (event: KeyboardEvent) => {
    if (!isPreviewOpen.value) return;
    
    switch (event.key) {
        case 'Escape':
            closePreview();
            break;
        case 'ArrowLeft':
            event.preventDefault();
            goToPrevious();
            break;
        case 'ArrowRight':
            event.preventDefault();
            goToNext();
            break;
        case ' ':
            event.preventDefault();
            if (currentMedia.value?.type === 'video') {
                togglePlay();
            }
            break;
        case 'f':
        case 'F':
            event.preventDefault();
            toggleFullscreen();
            break;
        case 'm':
        case 'M':
            event.preventDefault();
            if (currentMedia.value?.type === 'video') {
                toggleMute();
            }
            break;
    }
};

// Format time for video controls
const formatTime = (seconds: number): string => {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return `${mins}:${secs.toString().padStart(2, '0')}`;
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
    document.addEventListener('fullscreenchange', () => {
        isFullscreen.value = !!document.fullscreenElement;
    });
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = 'auto';
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
                        @click="openPreview(image)"
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
                                <!-- Video thumbnail -->
                                <video
                                    :src="image.src"
                                    class="w-full h-full object-cover"
                                    preload="metadata"
                                    muted
                                    playsinline
                                    @loadedmetadata="seekToFirstFrame"
                                    @canplay="seekToFirstFrame"
                                    @click.stop
                                >
                                    <source :src="image.src" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                <!-- Play button overlay -->
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-30">
                                    <div class="bg-white bg-opacity-95 rounded-full p-4 shadow-lg group-hover:scale-110 transition-transform duration-200">
                                        <svg class="w-10 h-10 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Video indicator badge -->
                                <div class="absolute top-2 right-2 bg-red-600 text-white px-2 py-1 rounded-md text-xs font-medium">
                                    VIDEO
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

        <!-- Modern Preview Modal -->
        <Transition name="modal" appear>
            <div
                v-if="isPreviewOpen"
                ref="previewRef"
                class="fixed inset-0 bg-black/95 z-50 flex items-center justify-center"
                @click="closePreview"
            >
                <!-- Loading Spinner -->
                <div
                    v-if="isLoading"
                    class="absolute inset-0 flex items-center justify-center z-10"
                >
                    <Loader2 class="w-12 h-12 text-white animate-spin" />
                </div>

                <!-- Top Controls -->
                <div class="absolute top-0 left-0 right-0 z-20 p-4">
                    <div class="flex items-center justify-between">
                        <!-- Media Info -->
                        <div v-if="currentMedia && !isLoading" class="text-white">
                            <h3 class="text-lg font-semibold">{{ currentMedia.title }}</h3>
                            <p class="text-sm text-gray-300">
                                {{ currentIndex + 1 }} / {{ filteredImages.length }} - 
                                {{ t(`gallery.filter.${currentMedia.category === 'on-set' ? 'onSet' : currentMedia.category}`) }}
                            </p>
                        </div>
                        <div v-else class="w-48"></div>

                        <!-- Top Right Controls -->
                        <div class="flex items-center gap-2">
                            <button
                                v-if="currentMedia?.type === 'video'"
                                @click.stop="toggleFullscreen"
                                class="p-2 text-white hover:text-red-500 transition-colors rounded-lg hover:bg-white/10"
                                :title="isFullscreen ? 'Exit fullscreen' : 'Enter fullscreen'"
                            >
                                <Minimize v-if="isFullscreen" class="w-6 h-6" />
                                <Maximize v-else class="w-6 h-6" />
                            </button>
                            <button
                                @click.stop="closePreview"
                                class="p-2 text-white hover:text-red-500 transition-colors rounded-lg hover:bg-white/10"
                                title="Close (Esc)"
                            >
                                <X class="w-6 h-6" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Navigation Arrows -->
                <button
                    v-if="currentIndex > 0"
                    @click.stop="goToPrevious"
                    class="absolute left-4 top-1/2 -translate-y-1/2 z-20 p-3 text-white hover:text-red-500 transition-colors rounded-full hover:bg-white/10"
                    title="Previous (←)"
                >
                    <ChevronLeft class="w-8 h-8" />
                </button>

                <button
                    v-if="currentIndex < filteredImages.length - 1"
                    @click.stop="goToNext"
                    class="absolute right-4 top-1/2 -translate-y-1/2 z-20 p-3 text-white hover:text-red-500 transition-colors rounded-full hover:bg-white/10"
                    title="Next (→)"
                >
                    <ChevronRight class="w-8 h-8" />
                </button>

                <!-- Media Container -->
                <div class="w-full h-full flex items-center justify-center p-16">
                    <!-- Image -->
                    <img
                        v-if="currentMedia?.type === 'photo'"
                        :src="currentMedia.src"
                        :alt="currentMedia.alt"
                        class="max-w-full max-h-full object-contain rounded-lg shadow-2xl"
                        @click.stop
                        @load="onImageLoad"
                    />

                    <!-- Video -->
                    <div
                        v-else-if="currentMedia?.type === 'video'"
                        class="relative max-w-full max-h-full"
                        @click.stop
                    >
                        <video
                            ref="videoRef"
                            :src="currentMedia.src"
                            class="max-w-full max-h-full object-contain rounded-lg shadow-2xl"
                            @loadeddata="onVideoLoad"
                            @timeupdate="onVideoTimeUpdate"
                            @play="onVideoPlay"
                            @pause="onVideoPause"
                            @click="togglePlay"
                        >
                            <source :src="currentMedia.src" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>

                        <!-- Video Controls Overlay -->
                        <div class="absolute bottom-4 left-4 right-4 bg-black/60 backdrop-blur-sm rounded-lg p-3">
                            <!-- Progress Bar -->
                            <div class="mb-3">
                                <div class="relative bg-white/20 rounded-full h-1 cursor-pointer" @click="seekTo($event)">
                                    <div
                                        class="absolute left-0 top-0 h-full bg-red-500 rounded-full transition-all"
                                        :style="{ width: duration > 0 ? `${(currentTime / duration) * 100}%` : '0%' }"
                                    ></div>
                                </div>
                            </div>

                            <!-- Control Buttons -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <button
                                        @click="togglePlay"
                                        class="p-2 text-white hover:text-red-500 transition-colors rounded-lg hover:bg-white/10"
                                    >
                                        <Play v-if="!isPlaying" class="w-5 h-5" />
                                        <Pause v-else class="w-5 h-5" />
                                    </button>

                                    <button
                                        @click="toggleMute"
                                        class="p-2 text-white hover:text-red-500 transition-colors rounded-lg hover:bg-white/10"
                                    >
                                        <Volume2 v-if="!isMuted" class="w-5 h-5" />
                                        <VolumeX v-else class="w-5 h-5" />
                                    </button>

                                    <span class="text-sm text-white">
                                        {{ formatTime(currentTime) }} / {{ formatTime(duration) }}
                                    </span>
                                </div>

                                <button
                                    @click="toggleFullscreen"
                                    class="p-2 text-white hover:text-red-500 transition-colors rounded-lg hover:bg-white/10"
                                >
                                    <Minimize v-if="isFullscreen" class="w-5 h-5" />
                                    <Maximize v-else class="w-5 h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Keyboard Shortcuts -->
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20">
                    <div class="bg-black/60 backdrop-blur-sm rounded-lg px-4 py-2 text-sm text-gray-300">
                        <span class="hidden sm:inline">
                            Press <kbd class="bg-white/20 px-1 rounded">←</kbd> <kbd class="bg-white/20 px-1 rounded">→</kbd> to navigate, 
                            <kbd class="bg-white/20 px-1 rounded">Space</kbd> to play/pause, 
                            <kbd class="bg-white/20 px-1 rounded">Esc</kbd> to close
                        </span>
                        <span class="sm:hidden">Swipe or tap arrows to navigate</span>
                    </div>
                </div>
            </div>
        </Transition>

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
                        :href="route('contact')"
                        class="border-2 border-red-600 text-black dark:text-white px-8 py-4 rounded-lg font-semibold hover:bg-red-600 hover:text-black dark:hover:bg-red-600 dark:hover:text-white transition-all duration-200 btn-camera"
                    >
                        {{ t('gallery.cta.contactButton') }}
                    </Link>
                </div>
            </div>
        </section>
    </div>
</template>

<style scoped>
/* Modal Transitions */
.modal-enter-active, .modal-leave-active {
    transition: all 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
    opacity: 0;
    transform: scale(0.95);
}

.modal-enter-to, .modal-leave-from {
    opacity: 1;
    transform: scale(1);
}

/* Keyboard hint styling */
kbd {
    display: inline-block;
    padding: 0.2rem 0.4rem;
    font-size: 0.75rem;
    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    border-radius: 0.25rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Video controls animations */
.video-controls {
    transition: opacity 0.3s ease;
}

.video-controls:hover {
    opacity: 1;
}

/* Loading animation improvements */
.loading-backdrop {
    backdrop-filter: blur(4px);
}

/* Progress bar interactive styling */
.progress-bar:hover .progress-handle {
    opacity: 1;
    transform: scale(1.2);
}

.progress-handle {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 12px;
    height: 12px;
    background: #ef4444;
    border-radius: 50%;
    opacity: 0;
    transition: all 0.2s ease;
    cursor: pointer;
}
</style> 