<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import Navigation from '@/components/Navigation.vue';
import { Filter, X, ChevronLeft, ChevronRight, Download, Play, Pause, Volume2, VolumeX, Maximize, Minimize, Loader2, Heart, Baby, Video, Camera, Users, Drum, Plane, Image } from 'lucide-vue-next';
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

// Watch for changes in props.category and update selectedCategory
watch(() => props.category, (newCategory) => {
    if (newCategory) {
        selectedCategory.value = newCategory;
    }
}, { immediate: true });

// Category configuration with icons and preview images
const categoryConfig = {
    'all': {
        icon: Image,
        image: '/hero.jpg'
    },
    'wedding': {
        icon: Heart,
        image: '/v_services.jpg'
    },
    'video': {
        icon: Video,
        image: '/video.jpg'
    },
    'on-set': {
        icon: Camera,
        image: '/onSet.jpg'
    },
    'studio': {
        icon: Camera,
        image: '/studio.JPG'
    },
    'modelling': {
        icon: Users,
        image: '/modeling.jpg'
    },
    'travel': {
        icon: Plane,
        image: '/t_services.jpg'
    },
    'concert': {
        icon: Drum,
        image: '/koncerti.jpg'
    },
    'baptism': {
        icon: Baby,
        image: '/k_services.jpg'
    },
    'other': {
        icon: Image,
        image: '/o_services.jpg'
    }
};

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

// Auto-hide controls state
const showControls = ref(true);
const showVideoControls = ref(true);
const controlsTimeout = ref<number | null>(null);
const videoControlsTimeout = ref<number | null>(null);

// Touch/swipe state for mobile
const touchStartX = ref(0);
const touchStartY = ref(0);
const touchEndX = ref(0);
const touchEndY = ref(0);
const isMobile = ref(false);

// Theme state for stamp watermark
const isDark = ref(false);

// Carousel state for category filter
const carouselRef = ref<HTMLDivElement | null>(null);
const canScrollLeft = ref(false);
const canScrollRight = ref(true);

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

// Reactive category list with computed counts and configuration
const categoryList = computed(() => [
    { 
        id: 'wedding', 
        nameKey: 'gallery.filter.wedding', 
        count: galleryImages.value.filter(img => img.category === 'wedding').length,
        ...categoryConfig.wedding
    },
    { 
        id: 'baptism', 
        nameKey: 'gallery.filter.baptism', 
        count: galleryImages.value.filter(img => img.category === 'baptism').length,
        ...categoryConfig.baptism
    },
    { 
        id: 'video', 
        nameKey: 'gallery.filter.video', 
        count: galleryImages.value.filter(img => img.category === 'video').length,
        ...categoryConfig.video
    },
    { 
        id: 'studio', 
        nameKey: 'gallery.filter.studio', 
        count: galleryImages.value.filter(img => img.category === 'studio').length,
        ...categoryConfig.studio
    },
    { 
        id: 'modelling', 
        nameKey: 'gallery.filter.modelling', 
        count: galleryImages.value.filter(img => img.category === 'modelling').length,
        ...categoryConfig.modelling
    },
    { 
        id: 'concert', 
        nameKey: 'gallery.filter.concert', 
        count: galleryImages.value.filter(img => img.category === 'concert').length,
        ...categoryConfig.concert
    },
    { 
        id: 'on-set', 
        nameKey: 'gallery.filter.onSet', 
        count: galleryImages.value.filter(img => img.category === 'on-set').length,
        ...categoryConfig['on-set']
    },
    { 
        id: 'travel', 
        nameKey: 'gallery.filter.travel', 
        count: galleryImages.value.filter(img => img.category === 'travel').length,
        ...categoryConfig.travel
    },
    { 
        id: 'other', 
        nameKey: 'gallery.filter.other', 
        count: galleryImages.value.filter(img => img.category === 'other').length,
        ...categoryConfig.other
    },
    { 
        id: 'all', 
        nameKey: 'gallery.filter.allPhotos', 
        count: galleryImages.value.length,
        ...categoryConfig.all
    }
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
        
        // Reset controls visibility
        showControls.value = true;
        showVideoControls.value = true;
        resetControlsTimeout();
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
    
    // Clear timeouts
    if (controlsTimeout.value) {
        clearTimeout(controlsTimeout.value);
    }
    if (videoControlsTimeout.value) {
        clearTimeout(videoControlsTimeout.value);
    }
    
    // Reset controls visibility
    showControls.value = true;
    showVideoControls.value = true;
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

const toggleVideoFullscreen = async () => {
    if (!videoRef.value) return;
    
    try {
        if (!document.fullscreenElement) {
            await videoRef.value.requestFullscreen();
            isFullscreen.value = true;
        } else {
            await document.exitFullscreen();
            isFullscreen.value = false;
        }
    } catch (error) {
        console.error('Video fullscreen error:', error);
    }
};

// Image loading
const onImageLoad = () => {
    isLoading.value = false;
};

const selectCategory = (categoryId: string) => {
    selectedCategory.value = categoryId;
    // Navigate to the appropriate URL based on category without scrolling to top
    if (categoryId === 'all') {
        router.visit(route('gallery'), { 
            preserveScroll: true,
            preserveState: true 
        });
    } else if (categoryId === 'wedding') {
        router.visit(route('gallery', { category: 'wedding' }), { 
            preserveScroll: true,
            preserveState: true 
        });
    } else {
        router.visit(route('gallery', categoryId), { 
            preserveScroll: true,
            preserveState: true 
        });
    }
};

// Carousel functions
const scrollLeft = () => {
    if (carouselRef.value) {
        carouselRef.value.scrollBy({ left: -300, behavior: 'smooth' });
        updateScrollButtons();
    }
};

const scrollRight = () => {
    if (carouselRef.value) {
        carouselRef.value.scrollBy({ left: 300, behavior: 'smooth' });
        updateScrollButtons();
    }
};

const updateScrollButtons = () => {
    if (carouselRef.value) {
        const { scrollLeft, scrollWidth, clientWidth } = carouselRef.value;
        canScrollLeft.value = scrollLeft > 0;
        canScrollRight.value = scrollLeft < scrollWidth - clientWidth - 1;
    }
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

// Auto-hide controls functions
const resetControlsTimeout = () => {
    showControls.value = true;
    if (controlsTimeout.value) {
        clearTimeout(controlsTimeout.value);
    }
    controlsTimeout.value = setTimeout(() => {
        showControls.value = false;
    }, 1000);
};

const resetVideoControlsTimeout = () => {
    showVideoControls.value = true;
    if (videoControlsTimeout.value) {
        clearTimeout(videoControlsTimeout.value);
    }
    videoControlsTimeout.value = setTimeout(() => {
        showVideoControls.value = false;
    }, 1000);
};

const handleMouseMove = () => {
    resetControlsTimeout();
    if (currentMedia.value?.type === 'video') {
        resetVideoControlsTimeout();
    }
};

const handleVideoMouseMove = () => {
    resetVideoControlsTimeout();
};

// Touch gesture functions for mobile
const handleTouchStart = (event: TouchEvent) => {
    touchStartX.value = event.touches[0].clientX;
    touchStartY.value = event.touches[0].clientY;
};

const handleTouchEnd = (event: TouchEvent) => {
    touchEndX.value = event.changedTouches[0].clientX;
    touchEndY.value = event.changedTouches[0].clientY;
    handleSwipe();
};

const handleSwipe = () => {
    const diffX = touchStartX.value - touchEndX.value;
    const diffY = touchStartY.value - touchEndY.value;
    const minSwipeDistance = 50;

    // Check if it's a horizontal swipe (more horizontal than vertical)
    if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > minSwipeDistance) {
        if (diffX > 0) {
            // Swipe left - go to next
            if (currentIndex.value < filteredImages.value.length - 1) {
                goToNext();
            }
        } else {
            // Swipe right - go to previous
            if (currentIndex.value > 0) {
                goToPrevious();
            }
        }
    }
};

const checkMobile = () => {
    isMobile.value = window.innerWidth <= 768;
};

const checkTheme = () => {
    isDark.value = document.documentElement.classList.contains('dark');
};

onMounted(() => {
    document.addEventListener('keydown', handleKeydown);
    document.addEventListener('fullscreenchange', () => {
        isFullscreen.value = !!document.fullscreenElement;
    });
    
    // Check if mobile on mount
    checkMobile();
    
    // Check theme on mount
    checkTheme();
    
    // Add resize listener for mobile detection
    window.addEventListener('resize', checkMobile);
    
    // Add theme change observer
    const observer = new MutationObserver(() => {
        checkTheme();
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
    
    // Initialize carousel scroll buttons
    nextTick(() => {
        updateScrollButtons();
    });
    
    // Auto-redirect to wedding category if no category is specified
    if (!props.category || props.category === 'all') {
        selectedCategory.value = 'wedding';
        router.visit(route('gallery', 'wedding'), { 
            preserveScroll: true,
            preserveState: true 
        });
    }
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleKeydown);
    document.body.style.overflow = 'auto';
    
    // Remove resize listener
    window.removeEventListener('resize', checkMobile);
    
    // Clear timeouts
    if (controlsTimeout.value) {
        clearTimeout(controlsTimeout.value);
    }
    if (videoControlsTimeout.value) {
        clearTimeout(videoControlsTimeout.value);
    }
});
</script>

<template>
    <Head :title="t('gallery.title')">
        <meta name="description" :content="t('gallery.subtitle')" />
    </Head>

    <div class="min-h-screen bg-white dark:bg-black transition-colors duration-300 relative overflow-hidden">
        <!-- Background Stamp Watermark -->
        <div 
            class="fixed inset-0 pointer-events-none z-0 opacity-5 transition-opacity duration-300"
            :class="isDark ? 'background-stamp-dark' : 'background-stamp'"
            :style="{
                backgroundImage: `url(${isDark ? '/ZIG_BIJELI.png' : '/ZIG_CRNI.png'})`,
                backgroundSize: 'contain',
                backgroundPosition: 'center',
                backgroundRepeat: 'no-repeat'
            }"
        ></div>
        
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
        <section class="py-16 px-4 bg-white dark:bg-black border-b border-black/10 dark:border-white/10 transition-colors duration-300">
            <div class="max-w-7xl mx-auto">
                <div class="flex items-center gap-4 mb-8">
                    <Filter class="w-6 h-6 text-red-600 dark:text-red-500" />
                    <h2 class="text-2xl font-bold text-black dark:text-white">{{ t('gallery.filter.title') }}</h2>
                </div>
                
                <!-- Carousel Container -->
                <div class="relative py-6 category-carousel">
                    <!-- Left Arrow -->
                    <button
                        @click="scrollLeft"
                        class="absolute left-2 top-1/2 -translate-y-1/2 z-10 bg-white dark:bg-black border border-black/10 dark:border-white/10 rounded-full p-3 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-110 carousel-arrow"
                        :class="{ 'opacity-50 cursor-not-allowed': !canScrollLeft, 'hidden': !canScrollLeft }"
                    >
                        <ChevronLeft class="w-5 h-5 text-red-600 dark:text-red-500" />
                    </button>
                    
                    <!-- Right Arrow -->
                    <button
                        @click="scrollRight"
                        class="absolute right-2 top-1/2 -translate-y-1/2 z-10 bg-white dark:bg-black border border-black/10 dark:border-white/10 rounded-full p-3 shadow-lg hover:shadow-xl transition-all duration-200 hover:scale-110 carousel-arrow"
                        :class="{ 'opacity-50 cursor-not-allowed': !canScrollRight, 'hidden': !canScrollRight }"
                    >
                        <ChevronRight class="w-5 h-5 text-red-600 dark:text-red-500" />
                    </button>
                    
                    <!-- Carousel Content -->
                    <div 
                        ref="carouselRef"
                        class="flex p-2 gap-6 sm:gap-8 overflow-x-auto scrollbar-hide scroll-smooth"
                        @scroll="updateScrollButtons"
                        style="scrollbar-width: none; -ms-overflow-style: none;"
                    >
                        <div
                            v-for="category in categoryList"
                            :key="category.id"
                            @click="selectCategory(category.id)"
                            :class="[
                                'group relative overflow-hidden rounded-xl bg-white dark:bg-black shadow-lg transition-all duration-300 cursor-pointer border-2 hover:shadow-xl transform hover:scale-105 category-filter-card flex-shrink-0',
                                selectedCategory === category.id
                                    ? 'border-red-600 dark:border-red-500 shadow-red-500/20 selected'
                                    : 'border-black/10 dark:border-white/10 hover:border-red-300 dark:hover:border-red-700'
                            ]"
                            style="width: 220px;"
                        >
                            <div class="aspect-[4/3] overflow-hidden">
                                <img
                                    :src="category.image"
                                    :alt="t(category.nameKey)"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                    loading="lazy"
                                />
                                <!-- Overlay for selected state -->
                                <div 
                                    v-if="selectedCategory === category.id"
                                    class="absolute inset-0 bg-red-600/20 dark:bg-red-500/20 flex items-center justify-center"
                                >
                                    <div class="bg-white dark:bg-black rounded-full p-2 shadow-lg">
                                        <component 
                                            :is="category.icon" 
                                            class="w-5 h-5 text-red-600 dark:text-red-500"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="p-5">
                                <div class="flex items-center gap-2 mb-2">
                                    <component 
                                        :is="category.icon" 
                                        :class="[
                                            'w-5 h-5 transition-transform duration-300',
                                            selectedCategory === category.id
                                                ? 'text-red-600 dark:text-red-500'
                                                : 'text-black/70 dark:text-white/70 group-hover:text-red-600 dark:group-hover:text-red-500'
                                        ]"
                                    />
                                    <h3 class="font-semibold text-sm text-black dark:text-white truncate flex justify-between">
                                       {{ t(category.nameKey) }}
                                    </h3>
                                </div>
                                <div class="text-xs text-black/60 dark:text-white/60">
                                    {{ category.count }} {{ category.count === 1 ? t('gallery.filter.singleItem') : t('gallery.filter.multipleItems') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gallery Grid -->
        <section class="py-12 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-7xl mx-auto">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 sm:gap-6 gallery-grid">
                    <div
                        v-for="image in filteredImages"
                        :key="image.id"
                        @click="openPreview(image)"
                        class="group relative overflow-hidden rounded-xl bg-white dark:bg-black shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer border border-black/10 dark:border-white/10 gallery-item lightbox-trigger"
                    >
                        <div class="aspect-4-3 overflow-hidden">
                            <img
                                v-if="image.type === 'photo'"
                                :src="image.src"
                                :alt="image.alt"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                loading="lazy"
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
                                    loading="lazy"
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
                class="fixed inset-0 bg-black/95 z-50 flex items-center justify-center modal-container"
                @click="closePreview"
                @mousemove="handleMouseMove"
                @touchstart="handleTouchStart"
                @touchend="handleTouchEnd"
            >
                <!-- Loading Spinner -->
                <div
                    v-if="isLoading"
                    class="absolute inset-0 flex items-center justify-center z-10"
                >
                    <Loader2 class="w-12 h-12 text-white animate-spin" />
                </div>

                <!-- Top Controls -->
                <div 
                    class="absolute top-0 left-0 right-0 z-20 p-4 transition-opacity duration-300"
                    :class="{ 'opacity-0': !showControls }"
                >
                    <div class="flex items-center justify-between">
                        <!-- Media Info -->
                        <div v-if="currentMedia && !isLoading" class="text-white">
                            <h3 class="text-lg font-semibold truncate max-w-[200px] sm:max-w-none">{{ currentMedia.title }}</h3>
                            <p class="text-sm text-gray-300">
                                {{ currentIndex + 1 }} / {{ filteredImages.length }} - 
                                {{ t(`gallery.filter.${currentMedia.category === 'on-set' ? 'onSet' : currentMedia.category}`) }}
                            </p>
                        </div>
                        <div v-else class="w-48"></div>

                        <!-- Top Right Controls -->
                        <div class="flex items-center gap-2">
                            <button
                                @click.stop="closePreview"
                                class="p-3 sm:p-2 text-white hover:text-red-500 transition-colors rounded-lg hover:bg-white/10 touch-manipulation"
                                title="Close (Esc)"
                            >
                                <X class="w-7 h-7 sm:w-6 sm:h-6" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Navigation Arrows -->
                <button
                    v-if="currentIndex > 0"
                    @click.stop="goToPrevious"
                    class="absolute left-2 sm:left-4 top-1/2 -translate-y-1/2 z-20 p-4 sm:p-3 text-white hover:text-red-500 transition-all duration-300 rounded-full hover:bg-white/10 touch-manipulation"
                    :class="{ 'opacity-0': !showControls }"
                    title="Previous (←)"
                >
                    <ChevronLeft class="w-10 h-10 sm:w-8 sm:h-8" />
                </button>

                <button
                    v-if="currentIndex < filteredImages.length - 1"
                    @click.stop="goToNext"
                    class="absolute right-2 sm:right-4 top-1/2 -translate-y-1/2 z-20 p-4 sm:p-3 text-white hover:text-red-500 transition-all duration-300 rounded-full hover:bg-white/10 touch-manipulation"
                    :class="{ 'opacity-0': !showControls }"
                    title="Next (→)"
                >
                    <ChevronRight class="w-10 h-10 sm:w-8 sm:h-8" />
                </button>

                <!-- Media Container -->
                <div class="w-full h-full flex items-center justify-center p-2 sm:p-4 md:p-8 lg:p-12">
                    <!-- Image -->
                    <div v-if="currentMedia?.type === 'photo'" class="relative w-full h-full flex items-center justify-center">
                        <img
                            :src="currentMedia.src"
                            :alt="currentMedia.alt"
                            class="w-auto h-auto max-w-full max-h-full object-contain rounded-lg shadow-2xl touch-manipulation"
                            style="max-width: 95vw; max-height: 95vh;"
                            @click.stop
                            @load="onImageLoad"
                        />
                        <!-- Image Watermark -->
                        <div 
                            class="absolute bottom-4 right-4 pointer-events-none opacity-20 transition-opacity duration-300 stamp-watermark"
                            :class="isDark ? 'stamp-watermark-dark' : 'stamp-watermark'"
                            :style="{
                                backgroundImage: `url(${isDark ? '/ZIG_BIJELI.png' : '/ZIG_CRNI.png'})`,
                                backgroundSize: 'contain',
                                backgroundPosition: 'center',
                                backgroundRepeat: 'no-repeat',
                                width: '60px',
                                height: '60px'
                            }"
                        ></div>
                    </div>

                    <!-- Video -->
                    <div
                        v-else-if="currentMedia?.type === 'video'"
                        class="relative w-full h-full flex items-center justify-center video-container"
                        @click.stop
                        @mousemove="handleVideoMouseMove"
                    >
                        <video
                            ref="videoRef"
                            :src="currentMedia.src"
                            class="w-auto h-auto max-w-full max-h-full object-contain rounded-lg shadow-2xl touch-manipulation"
                            style="max-width: 95vw; max-height: 95vh;"
                            @loadeddata="onVideoLoad"
                            @timeupdate="onVideoTimeUpdate"
                            @play="onVideoPlay"
                            @pause="onVideoPause"
                            @click="togglePlay"
                        >
                            <source :src="currentMedia.src" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>

                        <!-- Video Watermark -->
                        <div 
                            class="absolute bottom-20 right-4 pointer-events-none opacity-20 transition-opacity duration-300 z-10 stamp-watermark"
                            :class="isDark ? 'stamp-watermark-dark' : 'stamp-watermark'"
                            :style="{
                                backgroundImage: `url(${isDark ? '/ZIG_BIJELI.png' : '/ZIG_CRNI.png'})`,
                                backgroundSize: 'contain',
                                backgroundPosition: 'center',
                                backgroundRepeat: 'no-repeat',
                                width: '60px',
                                height: '60px'
                            }"
                        ></div>

                        <!-- Video Controls Overlay -->
                        <div 
                            class="absolute bottom-2 sm:bottom-4 left-2 sm:left-4 right-2 sm:right-4 bg-black/60 backdrop-blur-sm rounded-lg p-3 transition-opacity duration-300 video-controls"
                            :class="{ 'opacity-0': !showVideoControls }"
                        >
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
                                <div class="flex items-center gap-2 sm:gap-3">
                                    <button
                                        @click="togglePlay"
                                        class="p-3 sm:p-2 text-white hover:text-red-500 transition-colors rounded-lg hover:bg-white/10 touch-manipulation"
                                    >
                                        <Play v-if="!isPlaying" class="w-6 h-6 sm:w-5 sm:h-5" />
                                        <Pause v-else class="w-6 h-6 sm:w-5 sm:h-5" />
                                    </button>

                                    <button
                                        @click="toggleMute"
                                        class="p-3 sm:p-2 text-white hover:text-red-500 transition-colors rounded-lg hover:bg-white/10 touch-manipulation"
                                    >
                                        <Volume2 v-if="!isMuted" class="w-6 h-6 sm:w-5 sm:h-5" />
                                        <VolumeX v-else class="w-6 h-6 sm:w-5 sm:h-5" />
                                    </button>

                                    <span class="text-sm text-white min-w-[80px] sm:min-w-0">
                                        {{ formatTime(currentTime) }} / {{ formatTime(duration) }}
                                    </span>
                                </div>

                                <button
                                    @click="toggleVideoFullscreen"
                                    class="p-3 sm:p-2 text-white hover:text-red-500 transition-colors rounded-lg hover:bg-white/10 touch-manipulation"
                                >
                                    <Minimize v-if="isFullscreen" class="w-6 h-6 sm:w-5 sm:h-5" />
                                    <Maximize v-else class="w-6 h-6 sm:w-5 sm:h-5" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Keyboard Shortcuts -->
                <div 
                    class="absolute bottom-2 sm:bottom-4 left-1/2 -translate-x-1/2 z-20 transition-opacity duration-300"
                    :class="{ 'opacity-0': !showControls }"
                >
                    <div class="bg-black/60 backdrop-blur-sm rounded-lg px-3 sm:px-4 py-2 text-xs sm:text-sm text-gray-300">
                        <span class="hidden sm:inline">
                            Press <kbd class="bg-white/20 px-1 rounded">←</kbd> <kbd class="bg-white/20 px-1 rounded">→</kbd> to navigate, 
                            <kbd class="bg-white/20 px-1 rounded">Space</kbd> to play/pause, 
                            <kbd class="bg-white/20 px-1 rounded">Esc</kbd> to close
                        </span>
                        <span class="sm:hidden">Swipe left/right to navigate • Tap to close</span>
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

/* Fullscreen video styling */
video:fullscreen {
    width: 100vw;
    height: 100vh;
    object-fit: contain;
}

/* Optimized image sizing for all devices */
.gallery-item img,
.gallery-item video {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}

/* Preview modal image optimization */
.modal-container img,
.modal-container video {
    max-width: 95vw !important;
    max-height: 95vh !important;
    width: auto !important;
    height: auto !important;
    object-fit: contain !important;
}

/* Ensure proper aspect ratio maintenance */
.aspect-4-3 {
    aspect-ratio: 4 / 3;
}

/* Responsive grid improvements */
@media (max-width: 640px) {
    .gallery-grid {
        gap: 0.75rem;
    }
}

@media (min-width: 1536px) {
    .gallery-grid {
        gap: 1.5rem;
    }
}

/* Category filter card improvements */
.category-filter-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.category-filter-card:hover {
    transform: translateY(-2px);
}

.category-filter-card.selected {
    box-shadow: 0 10px 25px -5px rgba(239, 68, 68, 0.3);
}

/* Ensure category images maintain aspect ratio */
.category-filter-card img {
    transition: transform 0.3s ease;
}

.category-filter-card:hover img {
    transform: scale(1.1);
}

/* Carousel scrollbar hiding */
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

/* Improved carousel spacing */
.category-carousel {
    padding: 1rem 0;
    margin: 0.5rem 0;
}

.category-carousel .category-filter-card {
    margin: 0.25rem 0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.category-carousel .category-filter-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Carousel arrow button improvements */
.carousel-arrow {
    backdrop-filter: blur(8px);
    transition: all 0.2s ease;
}

.carousel-arrow:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Smooth scrolling for carousel */
.scroll-smooth {
    scroll-behavior: smooth;
}

/* Additional image optimization for edge cases */
@media (max-width: 480px) {
    .modal-container img,
    .modal-container video {
        max-width: 98vw !important;
        max-height: 98vh !important;
    }
}

@media (min-width: 1920px) {
    .modal-container img,
    .modal-container video {
        max-width: 90vw !important;
        max-height: 90vh !important;
    }
}

/* Auto-hide controls styling */
.controls-hidden {
    pointer-events: none;
}

.controls-hidden * {
    pointer-events: none;
}

/* Video controls hover effect */
.video-container:hover .video-controls {
    opacity: 1 !important;
}

/* Mobile-specific improvements */
@media (max-width: 768px) {
    /* Prevent zoom on double tap */
    .touch-manipulation {
        touch-action: manipulation;
    }
    
    /* Larger touch targets for mobile */
    button {
        min-height: 44px;
        min-width: 44px;
    }
    
    /* Better video controls spacing on mobile */
    .video-controls {
        padding: 0.75rem;
    }
    
    /* Improve progress bar touch target */
    .video-controls .relative {
        height: 8px;
    }
    
    /* Better modal padding on mobile */
    .modal-container {
        padding: 1rem;
    }
}

/* Prevent text selection on mobile */
.modal-container {
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Smooth touch scrolling */
.modal-container {
    -webkit-overflow-scrolling: touch;
}

/* Stamp watermark effects */
.stamp-watermark {
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    mix-blend-mode: multiply;
}

.stamp-watermark-dark {
    filter: drop-shadow(0 2px 4px rgba(255, 255, 255, 0.1));
    mix-blend-mode: screen;
}

/* Background stamp effect */
.background-stamp {
    background-blend-mode: multiply;
    filter: contrast(0.8) brightness(1.1);
}

.background-stamp-dark {
    background-blend-mode: screen;
    filter: contrast(0.8) brightness(0.9);
}
</style> 