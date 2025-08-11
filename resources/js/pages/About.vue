<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import Navigation from '@/components/Navigation.vue';
import { Camera, Heart, Award, Users, Star, ArrowRight } from 'lucide-vue-next';
import { useTranslation } from '@/composables/useTranslation';

const { t, initLanguage } = useTranslation();

onMounted(() => {
    initLanguage();
});

const stats = [
    { icon: Camera, number: '500+', labelKey: 'about.stats.sessionsCompleted' },
    { icon: Heart, number: '300+', labelKey: 'about.stats.happyCouples' },
    { icon: Award, number: '10+', labelKey: 'about.stats.yearsExperience' },
    { icon: Users, number: '50+', labelKey: 'about.stats.eventsCovered' }
];

const testimonials = [
    {
        name: 'Sarah & Michael',
        eventKey: 'about.testimonials.wedding',
        text: 'Our wedding photos are absolutely stunning! Every moment was captured perfectly. We couldn\'t be happier with the results.',
        rating: 5
    },
    {
        name: 'Maria Rodriguez',
        eventKey: 'about.testimonials.baptism',
        text: 'The baptism photos are beautiful and captured the joy of our family perfectly. Highly recommended!',
        rating: 5
    },
    {
        name: 'John Smith',
        eventKey: 'about.testimonials.studioSession',
        text: 'Professional, creative, and amazing results. The studio session exceeded all my expectations.',
        rating: 5
    }
];
</script>

<template>
    <Head :title="t('about.title')">
        <meta name="description" :content="t('about.subtitle')" />
    </Head>
    <div class="min-h-screen bg-black dark:bg-white transition-colors duration-300">
        <Navigation />
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-4xl mx-auto text-center text-black dark:text-white">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">{{ t('about.title') }}</h1>
                <p class="text-xl text-red-600 dark:text-red-500 max-w-2xl mx-auto">
                    {{ t('about.subtitle') }}
                </p>
            </div>
        </section>
        <section class="py-20 px-4 bg-white dark:bg-black transition-colors duration-300">
            <div class="max-w-5xl mx-auto grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <img src="/1010.jpg" alt="Photographer" class="rounded-xl shadow-2xl" />
                </div>
                <div>
                    <h2 class="text-3xl md:text-4xl font-bold text-black dark:text-white mb-6">{{ t('about.greeting') }}</h2>
                    <p class="text-lg text-black/70 dark:text-white/70 mb-6">
                        {{ t('about.description1') }}
                    </p>
                    <p class="text-lg text-black/70 dark:text-white/70 mb-8">
                        {{ t('about.description2') }}
                    </p>
                    <Link :href="route('contact')" class="inline-flex items-center gap-2 bg-red-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-black hover:text-red-600 dark:bg-red-600 dark:hover:bg-white dark:hover:text-black transition-all duration-200 btn-camera">
                        {{ t('about.contactButton') }}
                    </Link>
                </div>
            </div>
        </section>
        <footer class="bg-white dark:bg-black text-black dark:text-white py-12 px-4 transition-colors duration-300">
            <div class="max-w-7xl mx-auto text-center">
                <p class="text-black/70 dark:text-white/70">&copy; 2025 Fotomimi. {{ t('about.footer.copyright') }}</p>
            </div>
        </footer>
    </div>
</template> 