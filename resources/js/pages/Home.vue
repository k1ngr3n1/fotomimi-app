<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Navigation from '@/components/Navigation.vue';
import { Camera, Heart, Users, Star, ArrowRight, Baby, Video, Drum} from 'lucide-vue-next';
import { useTranslation } from '@/composables/useTranslation';
import { useThemeLogo } from '@/composables/useThemeLogo';

const { t, initLanguage } = useTranslation();
const { logoSrc } = useThemeLogo();

onMounted(() => {
    initLanguage();
});



const services = [
    {
        icon: Heart,
        titleKey: 'home.services.weddings.title',
        descriptionKey: 'home.services.weddings.description',
        image: '/vjencanja.jpg'
    },
    {
        icon: Baby,
        titleKey: 'home.services.baptism.title',
        descriptionKey: 'home.services.baptism.description',
        image: '/krstenja.jpg'
    },
    {
        icon: Video,
        titleKey: 'home.services.video.title',
        descriptionKey: 'home.services.video.description',
        image: '/video.jpg'
    },
    {
        icon: Camera,
        titleKey: 'home.services.studio.title',
        descriptionKey: 'home.services.studio.description',
        image: '/studio.JPG'
    },
    {
        icon: Users,
        titleKey: 'home.services.modelling.title',
        descriptionKey: 'home.services.modelling.description',
        image: '/modeling.jpg'
    },
    {
        icon: Drum,
        titleKey: 'home.services.concerts.title',
        descriptionKey: 'home.services.concerts.description',
        image: '/koncerti.jpg'
    },
];
</script>

<template>
    <Head title="Fotomimi">
        <meta name="description" content="Professional photography services for weddings, baptisms, concerts, studio sessions, and modelling. Book your session today!" />
    </Head>

    <div class="min-h-screen bg-white dark:bg-black transition-colors duration-300">
        <Navigation />

        <!-- Hero Section -->
        <section class="relative h-screen flex items-center justify-center overflow-hidden">
            <div class="absolute inset-0">
                <img 
                    src="/hero.jpg" 
                    alt="Photography Studio" 
                    class="w-full h-full object-cover"
                />
                <div class="absolute inset-0 bg-black/70 dark:bg-black/70"></div>
            </div>
            
            <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 text-white dark:text-white">
                    {{ t('home.hero.title') }}
                    <span class="text-red-600 dark:text-red-500">{{ t('home.hero.titleHighlight') }}</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-white/80 dark:text-white/70 max-w-2xl mx-auto">
                    {{ t('home.hero.subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link 
                        :href="route('gallery')" 
                        class="hero-btn bg-red-600 text-white border-2 border-black px-8 py-4 rounded-lg font-semibold hover:bg-black hover:text-red-600 dark:bg-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-300 transform hover:scale-110 hover:shadow-xl flex items-center justify-center gap-2"
                    >
                        {{ t('home.hero.galleryButton') }}
                        <ArrowRight class="w-5 h-5 arrow-animate" />
                    </Link>
                    <Link 
                        :href="route('contact')" 
                        class="hero-btn bg-black text-white border-2 border-red-600 px-8 py-4 rounded-lg font-semibold hover:bg-red-600 hover:text-white dark:bg-white dark:text-black dark:hover:bg-red-600 dark:hover:text-white transition-all duration-300 transform hover:scale-110 hover:shadow-xl"
                    >
                        {{ t('home.hero.contactButton') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-7xl mx-auto">
                <div class="text-center mb-16">
                    <h2 class="text-4xl md:text-5xl font-bold text-red-600 dark:text-red-500 mb-4">
                        {{ t('home.services.title') }}
                    </h2>
                    <p class="text-xl text-black/70 dark:text-white/70 max-w-3xl mx-auto">
                        {{ t('home.services.subtitle') }}
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                    <div 
                        v-for="service in services" 
                        :key="service.title"
                        class="group relative overflow-hidden rounded-xl bg-white dark:bg-black shadow-lg transition-all duration-300 border border-black/10 dark:border-white/10 cursor-pointer service-card"
                    >
                        <div class="aspect-[4/3] overflow-hidden">
                            <img 
                                :src="service.image" 
                                :alt="service.title"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                            />
                        </div>
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-3">
                                <component 
                                    :is="service.icon" 
                                    class="w-6 h-6 text-red-600 dark:text-red-500 transition-transform duration-300 group-hover:scale-125"
                                />
                                <h3 class="text-xl font-semibold text-black dark:text-white">{{ t(service.titleKey) }}</h3>
                            </div>
                            <p class="text-black/70 dark:text-white/70">{{ t(service.descriptionKey) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Preview Section -->
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-7xl mx-auto">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h2 class="text-4xl md:text-5xl font-bold text-black dark:text-white mb-6">
                            {{ t('home.about.title') }}
                        </h2>
                        <p class="text-xl text-black/70 dark:text-white/70 mb-6">
                            {{ t('home.about.description1') }}
                        </p>
                        <p class="text-lg text-black/70 dark:text-white/70 mb-8">
                            {{ t('home.about.description2') }}
                        </p>
                        <Link 
                            :href="route('about')" 
                            class="btn-camera inline-flex items-center gap-2 bg-red-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-black hover:text-red-600 dark:bg-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-300 transform hover:scale-110 hover:shadow-xl group"
                        >
                            {{ t('home.about.button') }}
                            <ArrowRight class="w-5 h-5 arrow-animate" />
                        </Link>
                    </div>
                    <div class="relative">
                        <img 
                            src="/1010.jpg" 
                            alt="Professional Photographer" 
                            class="rounded-xl shadow-2xl"
                        />
                        <div class="absolute -bottom-6 -left-6 bg-white dark:bg-black p-6 rounded-xl shadow-lg border border-black/10 dark:border-white/10">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-red-600 dark:text-red-500 mb-1">2500+</div>
                                <div class="text-black/70 dark:text-white/70">{{ t('home.about.stats.clients') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-4xl mx-auto text-center text-black dark:text-white">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    {{ t('home.cta.title') }}
                </h2>
                <p class="text-xl mb-8 text-red-600 dark:text-red-500">
                    {{ t('home.cta.subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link 
                        :href="route('contact')" 
                        class="btn-camera bg-red-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-white hover:text-red-600 dark:bg-red-600 dark:hover:bg-black dark:hover:text-white transition-all duration-300 transform hover:scale-110 hover:shadow-xl"
                    >
                        {{ t('home.cta.contactButton') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white dark:bg-black text-black dark:text-white py-12 px-4 transition-colors duration-300">
            <div class="max-w-7xl mx-auto">
                <div class="grid md:grid-cols-4 gap-8">
                    <div>
                <Link :href="route('home')" class="flex items-center space-x-2">
                    <div class="rounded-lg flex items-center justify-center bg-white dark:bg-black">
                        <img :src="logoSrc" alt="Fotomimi Logo" class="h-14" />
                    </div>
                </Link>
  

                    </div>
                    <div>
                        <h3 class="font-semibold mb-4">{{ t('home.footer.services') }}</h3>
                        <ul class="space-y-2 text-black/70 dark:text-white/70">
                            <li><Link :href="route('gallery', 'wedding')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors cursor-pointer">{{ t('gallery.filter.weddings') }}</Link></li>
                            <li><Link :href="route('gallery', 'baptism')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors cursor-pointer">{{ t('gallery.filter.baptism') }}</Link></li>
                            <li><Link :href="route('gallery', 'video')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors cursor-pointer">{{ t('gallery.filter.video') }}</Link></li>
                            <li><Link :href="route('gallery', 'studio')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors cursor-pointer">{{ t('gallery.filter.studio') }}</Link></li>
                            <li><Link :href="route('gallery', 'modelling')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors cursor-pointer">{{ t('gallery.filter.modelling') }}</Link></li>
                            <li><Link :href="route('gallery', 'concert')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors cursor-pointer">{{ t('gallery.filter.concerts') }}</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-4">{{ t('home.footer.quickLinks') }}</h3>
                        <ul class="space-y-2 text-black/70 dark:text-white/70">
                            <li><Link :href="route('home')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors cursor-pointer">{{ t('navigation.home') }}</Link></li>
                            <li><Link :href="route('gallery')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors cursor-pointer">{{ t('navigation.gallery') }}</Link></li>    
                            <li><Link :href="route('about')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors cursor-pointer">{{ t('navigation.about') }}</Link></li>
                            <li><Link :href="route('contact')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors cursor-pointer">{{ t('navigation.contact') }}</Link></li>                        
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-4">{{ t('home.footer.contactInfo') }}</h3>
                        <ul class="space-y-2 text-black/70 dark:text-white/70">
                            <li>📧 fotomimi@gmail.com</li>
                            <li>📞 + 385 912397004</li>
                            <li>📍 Bjelovarska 34, Križevci</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-black/10 dark:border-white/10 mt-8 pt-8 text-center text-black/70 dark:text-white/70">
                    <p>&copy; 2025 Fotomimi. {{ t('home.footer.copyright') }}</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Hero section buttons with camera cursor and enhanced animations */
.hero-btn {
    cursor: url('/cursor-camera.svg') 16 16, auto;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
    pointer-events: none;
    cursor: inherit;
}

.hero-btn:hover::before {
    left: 100%;
}

.hero-btn:hover {
    transform: scale(1.15);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
}

.hero-btn:active {
    transform: scale(1.05);
}

/* Service cards camera cursor */
.btn-camera {
    cursor: url('/cursor-camera.svg') 16 16, auto;
    position: relative;
    overflow: hidden;
}

.btn-camera::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
    pointer-events: none;
    cursor: inherit;
}

.btn-camera:hover::before {
    left: 100%;
}

.btn-camera:hover {
    transform: scale(1.1);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.arrow-animate {
    transition: transform 0.3s ease;
}

.hero-btn:hover .arrow-animate,
.btn-camera:hover .arrow-animate {
    transform: translateX(4px);
}

/* Additional hover effects for better visual feedback */
.btn-camera:active {
    transform: scale(1.05);
}

/* Ensure smooth transitions */
.btn-camera {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Enhanced service card hover effects */
.service-card {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    transform: translateY(0) scale(1);
}

.service-card:hover {
    transform: scale(1.05) translateY(-5px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}
</style> 