<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Navigation from '@/components/Navigation.vue';
import { Camera, Heart, Users, Star, ArrowRight } from 'lucide-vue-next';
import { useTranslation } from '@/composables/useTranslation';

const { t, initLanguage } = useTranslation();

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
        icon: Users,
        titleKey: 'home.services.baptism.title',
        descriptionKey: 'home.services.baptism.description',
        image: '/krstenja.jpg'
    },
    {
        icon: Camera,
        titleKey: 'home.services.video.title',
        descriptionKey: 'home.services.video.description',
        image: '/video.jpg'
    },
    {
        icon: Star,
        titleKey: 'home.services.onSet.title',
        descriptionKey: 'home.services.onSet.description',
        image: '/onSet.jpg'
    },
    {
        icon: Camera,
        titleKey: 'home.services.studio.title',
        descriptionKey: 'home.services.studio.description',
        image: '/studio.jpg'
    },
    {
        icon: Users,
        titleKey: 'home.services.modelling.title',
        descriptionKey: 'home.services.modelling.description',
        image: '/modeling.jpg'
    },
    {
        icon: Star,
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
                        class="bg-red-600 text-white border-2 border-black px-8 py-4 rounded-lg font-semibold hover:bg-black hover:text-red-600 dark:bg-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-200 flex items-center justify-center gap-2"
                    >
                        {{ t('home.hero.galleryButton') }}
                        <ArrowRight class="w-5 h-5" />
                    </Link>
                    <Link 
                        :href="route('contact')" 
                        class="bg-black text-white border-2 border-red-600 px-8 py-4 rounded-lg font-semibold hover:bg-red-600 hover:text-white dark:bg-white dark:text-black dark:hover:bg-red-600 dark:hover:text-white transition-all duration-200"
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
                    <h2 class="text-4xl md:text-5xl font-bold text-black dark:text-white mb-4">
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
                        class="group relative overflow-hidden rounded-xl bg-white dark:bg-black shadow-lg hover:shadow-xl transition-all duration-300 border border-black/10 dark:border-white/10"
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
                                    class="w-6 h-6 text-red-600 dark:text-red-500"
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
                            class="inline-flex items-center gap-2 bg-red-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-black hover:text-red-600 dark:bg-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-200"
                        >
                            {{ t('home.about.button') }}
                            <ArrowRight class="w-5 h-5" />
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
                                <div class="text-3xl font-bold text-red-600 dark:text-red-500 mb-1">500+</div>
                                <div class="text-black/70 dark:text-white/70">{{ t('home.about.stats.clients') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 px-4 bg-black dark:bg-white transition-colors duration-300">
            <div class="max-w-4xl mx-auto text-center text-white dark:text-black">
                <h2 class="text-4xl md:text-5xl font-bold mb-6">
                    {{ t('home.cta.title') }}
                </h2>
                <p class="text-xl mb-8 text-red-600 dark:text-red-500">
                    {{ t('home.cta.subtitle') }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link 
                        :href="route('contact')" 
                        class="bg-red-600 text-white px-8 py-4 rounded-lg font-semibold hover:bg-black hover:text-red-600 dark:bg-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-200 btn-camera"
                    >
                        {{ t('home.cta.contactButton') }}
                    </Link>
                    <Link 
                        :href="route('booking')" 
                        class="border-2 border-red-600 text-white dark:text-black px-8 py-4 rounded-lg font-semibold hover:bg-red-600 hover:text-white dark:hover:bg-red-600 dark:hover:text-white transition-all duration-200 btn-camera"
                    >
                        {{ t('home.cta.bookingButton') }}
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
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-white dark:bg-black">
                        <img src="/fotomimi-logo.jpg" alt="Mimi Logo" class="object-contain w-8 h-8" />
                    </div>
                    <span class="text-xl font-bold text-black dark:text-white">Fotomimi</span>
                </Link>
  

                    </div>
                    <div>
                        <h3 class="font-semibold mb-4">{{ t('home.footer.services') }}</h3>
                        <ul class="space-y-2 text-black/70 dark:text-white/70">
                            <li><Link :href="route('gallery', 'wedding')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('gallery.filter.weddings') }}</Link></li>
                            <li><Link :href="route('gallery', 'baptism')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('gallery.filter.baptism') }}</Link></li>
                            <li><Link :href="route('gallery', 'video')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('gallery.filter.video') }}</Link></li>
                            <li><Link :href="route('gallery', 'on-set')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('gallery.filter.onSet') }}</Link></li>
                            <li><Link :href="route('gallery', 'studio')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('gallery.filter.studio') }}</Link></li>
                            <li><Link :href="route('gallery', 'modelling')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('gallery.filter.modelling') }}</Link></li>
                            <li><Link :href="route('gallery', 'concert')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('gallery.filter.concerts') }}</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-4">{{ t('home.footer.quickLinks') }}</h3>
                        <ul class="space-y-2 text-black/70 dark:text-white/70">
                            <li><Link :href="route('home')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('navigation.home') }}</Link></li>
                            <li><Link :href="route('about')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('navigation.about') }}</Link></li>
                            <li><Link :href="route('contact')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('navigation.contact') }}</Link></li>
                            <li><Link :href="route('booking')" class="hover:text-red-600 dark:hover:text-red-500 transition-colors">{{ t('navigation.booking') }}</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-semibold mb-4">{{ t('home.footer.contactInfo') }}</h3>
                        <ul class="space-y-2 text-black/70 dark:text-white/70">
                            <li>📧 info@photostudio.com</li>
                            <li>📞 +1 (555) 123-4567</li>
                            <li>📍 123 Photography St, City</li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-black/10 dark:border-white/10 mt-8 pt-8 text-center text-black/70 dark:text-white/70">
                    <p>&copy; 2024 Fotomimi. {{ t('home.footer.copyright') }}</p>
                </div>
            </div>
        </footer>
    </div>
</template> 